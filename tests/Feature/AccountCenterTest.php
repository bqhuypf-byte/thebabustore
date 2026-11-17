<?php

namespace Tests\Feature;

use App\Livewire\Account;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AccountCenterTest extends TestCase
{
    use DatabaseTransactions;

    private function user(): User
    {
        return User::factory()->create(['phone' => '0912345689', 'password' => Hash::make('TestPassword!')]);
    }

    private function order(User $user, string $status, string $code): Order
    {
        $o = Order::create(['user_id' => $user->id, 'order_code' => $code, 'status' => $status, 'grand_total' => 100000, 'payment_method' => 'cod', 'payment_status' => 'pending']);
        Address::create(['order_id' => $o->id, 'full_name' => $user->name, 'phone' => $user->phone, 'province' => '', 'district' => '', 'ward' => '', 'detailed_address' => 'Test']);

        return $o;
    }

    public function test_repeated_tab_changes_keep_distinct_dom_roots_and_expected_content(): void
    {
        $this->actingAs($this->user());
        $c = Livewire::test(Account::class);
        foreach (['orders', 'warranty', 'profile', 'password', 'overview', 'orders', 'profile', 'warranty', 'orders'] as $tab) {
            $c->call('setTab', $tab)->assertSet('activeTab', $tab)->assertSee('wire:key="account-tab-'.$tab.'"', false)->assertSee('account-center.css');
            $this->assertSame(1, substr_count($c->html(), 'class="cp-main"'));
        }
        $c->assertDontSee('class="cp-notif-box"', false);
    }

    public function test_order_filters_search_dates_and_empty_results_are_consistent(): void
    {
        $u = $this->user();
        $this->actingAs($u);
        $a = $this->order($u, 'delivered', 'ACCT-DEL-'.uniqid());
        $b = $this->order($u, 'cancelled', 'ACCT-CANCEL-'.uniqid());
        $other = User::factory()->create(['phone' => '0987654312']);
        $foreign = $this->order($other, 'delivered', 'ACCT-PRIVATE-'.uniqid());
        $c = Livewire::test(Account::class)->call('setTab', 'orders')->assertSee($a->order_code)->assertSee($b->order_code)->assertDontSee($foreign->order_code);
        $c->call('setOrderStatusFilter', 'delivered')->assertSee($a->order_code)->assertDontSee($b->order_code);
        $c->call('setOrderStatusFilter', 'all')->set('orderSearch', $b->order_code)->assertSee($b->order_code)->assertDontSee($a->order_code);
        $c->set('orderSearch', '')->set('dateFrom', today()->addDay()->toDateString())->assertSee('Không có đơn hàng nào phù hợp');
        $c->set('dateFrom', '')->call('setTab', 'overview')->call('setTab', 'orders')->assertSee('wire:key="purchase-order-'.$a->id.'"', false)->assertSee('wire:key="purchase-order-'.$b->id.'"', false);
    }

    public function test_invalid_filters_and_tab_values_do_not_blank_the_account(): void
    {
        $this->actingAs($this->user());
        $c = Livewire::withQueryParams(['tab' => 'invalid', 'status' => 'invalid', 'claim_status' => 'invalid'])->test(Account::class)->assertSet('activeTab', 'overview')->assertSet('orderStatusFilter', 'all')->assertSet('warrantyClaimFilter', 'all');
        $c->call('setTab', 'invalid')->assertSet('activeTab', 'overview')->call('setOrderStatusFilter', 'invalid')->assertSet('orderStatusFilter', 'all');
    }

    public function test_profile_validation_and_tab_changes_do_not_write_invalid_data(): void
    {
        $u = $this->user();
        $name = $u->name;
        $this->actingAs($u);
        Livewire::test(Account::class)->call('setTab', 'profile')->set('name', '')->call('updateProfile')->assertHasErrors('name')->call('setTab', 'orders')->assertHasNoErrors();
        $this->assertSame($name, $u->fresh()->name);
    }

    public function test_password_validation_rejects_wrong_current_password(): void
    {
        $u = $this->user();
        $hash = $u->password;
        $this->actingAs($u);
        Livewire::test(Account::class)->call('setTab', 'password')->set('current_password', 'incorrect')->set('new_password', 'NewPassword!')->set('new_password_confirmation', 'NewPassword!')->call('updatePassword')->assertHasErrors('current_password');
        $this->assertSame($hash, $u->fresh()->password);
    }

    public function test_account_routes_require_login_and_each_tab_can_render_directly(): void
    {
        $this->get('/account')->assertRedirect('/account/login');
        $this->actingAs($this->user());
        foreach (['overview', 'orders', 'warranty', 'profile', 'password'] as $tab) {
            $this->get('/account?tab='.$tab)->assertOk()->assertSee('data-account-tab="'.$tab.'"', false);
        }
    }
}
