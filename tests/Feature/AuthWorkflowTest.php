<?php

namespace Tests\Feature;

use App\Livewire\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthWorkflowTest extends TestCase
{
    public function test_auth_page_renders_with_target_ui_elements(): void
    {
        Livewire::test(Login::class)
            ->assertSee('Trang chủ')
            ->assertSee('Đăng nhập & Đăng ký')
            ->assertSee('Cổng Tài Khoản Khách Hàng')
            ->assertSee('Chào Mừng Bạn Đến Với BABU')
            ->assertSee('Đăng nhập để theo dõi đơn hàng, nhận ưu đãi thành viên và bảo hành nhanh chóng.')
            ->assertSee('Đăng Nhập')
            ->assertSee('Tạo Tài Khoản')
            ->assertSee('HOẶC DÙNG NHANH')
            ->assertSee('Đăng nhập tài khoản mẫu (Nguyễn Hoàng Nam)')
            ->assertSee('Đăng Nhập Ngay →');
    }

    public function test_email_login_works_with_valid_credentials(): void
    {
        $password = 'secret123456';
        $user = User::factory()->create([
            'email' => 'customer_test_' . uniqid() . '@example.com',
            'password' => Hash::make($password),
            'is_admin' => false,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', $password)
            ->call('login')
            ->assertRedirect('/account');

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_email_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'customer_fail_' . uniqid() . '@example.com',
            'password' => Hash::make('correct_password'),
            'is_admin' => false,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong_password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertFalse(Auth::check());
    }

    public function test_demo_login_button_authenticates_demo_user(): void
    {
        Livewire::test(Login::class)
            ->call('loginDemoUser')
            ->assertRedirect('/account');

        $this->assertTrue(Auth::check());
        $this->assertEquals('hgdigital.vn@gmail.com', Auth::user()->email);
        $this->assertEquals('Nguyễn Hoàng Nam', Auth::user()->name);
    }

    public function test_tab_switching_to_register_and_registration(): void
    {
        $newEmail = 'reg_user_' . uniqid() . '@example.com';

        Livewire::test(Login::class)
            ->call('setTab', 'register')
            ->assertSet('activeTab', 'register')
            ->assertSee('Tạo Tài Khoản Ngay →')
            ->set('reg_name', 'Trần Thị Thu')
            ->set('reg_phone', '0987654321')
            ->set('reg_email', $newEmail)
            ->set('reg_password', 'password123')
            ->call('register')
            ->assertRedirect('/account');

        $this->assertTrue(Auth::check());
        $this->assertEquals($newEmail, Auth::user()->email);
        $this->assertEquals('Trần Thị Thu', Auth::user()->name);
    }

    public function test_account_register_route_redirects_to_login_register_tab(): void
    {
        $response = $this->get('/account/register');
        $response->assertRedirect('/account/login?tab=register');
    }

    public function test_account_orders_tab_renders_horizontal_order_list(): void
    {
        $user = User::factory()->create([
            'email' => 'account_view_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Account::class)
            ->call('setTab', 'orders')
            ->assertSet('activeTab', 'orders')
            ->assertSee('Lịch sử mua hàng')
            ->assertSee('cp-orders-full-list')
            ->assertSee('Tìm theo mã đơn hoặc sản phẩm...');
    }

    public function test_account_warranty_tab_renders_unified_lookup_frame_and_overview_omits_duplicate_block(): void
    {
        $user = User::factory()->create([
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0912345678',
        ]);

        // 1. Overview does not contain duplicate "Sản phẩm đang bảo hành"
        Livewire::actingAs($user)
            ->test(\App\Livewire\Account::class, ['tab' => 'overview'])
            ->assertDontSee('Sản phẩm đang bảo hành')
            ->assertSee('Đơn hàng gần đây')
            ->assertSee('Ưu đãi của bạn');

        // 2. Warranty tab contains the RMA service tracking pipeline matching CellphoneS
        Livewire::actingAs($user)
            ->test(\App\Livewire\Account::class)
            ->call('setTab', 'warranty')
            ->assertSee('Đã tiếp nhận')
            ->assertSee('Đang điều phối')
            ->assertSee('Đang sửa')
            ->assertSee('Đã sửa xong')
            ->assertSee('Đã trả khách')
            ->assertSee('Bạn chưa có đơn bảo hành')
            ->assertSee('Tra cứu thời gian bảo hành');
    }
}

