<?php

namespace Tests\Feature;

use App\Livewire\WarrantyLookup;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class WarrantyWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_warranty_lookup_page_renders_correctly(): void
    {
        $response = $this->get('/tra-cuu-bao-hanh');
        $response->assertStatus(200);
        $response->assertSee('TRA CỨU THỜI GIAN BẢO HÀNH');
        $response->assertSee('Theo số điện thoại');
        $response->assertSee('Kiểm tra');

        // Test in-place tab switching without page reload
        Livewire::test(WarrantyLookup::class)
            ->assertSet('activeTab', 'warranty')
            ->assertSee('TRA CỨU THỜI GIAN BẢO HÀNH')
            ->call('setTab', 'orders')
            ->assertSet('activeTab', 'orders')
            ->assertSee('TRA CỨU TIẾN TRÌNH ĐƠN HÀNG')
            ->call('setTab', 'activate')
            ->assertSet('activeTab', 'activate')
            ->assertSee('KÍCH HOẠT BẢO HÀNH ĐIỆN TỬ');
    }

    public function test_delivered_order_shows_checkmark_on_all_steps(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'DELIV' . uniqid(),
            'grand_total' => 500000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'delivered',
        ]);

        Address::create([
            'order_id' => $order->id,
            'full_name' => 'Tran B',
            'phone' => '0912345678',
            'province' => '',
            'district' => '',
            'ward' => '',
            'detailed_address' => 'HCM',
        ]);

        // In WarrantyLookup Tab 2 (orders)
        $component = Livewire::test(WarrantyLookup::class)
            ->call('setTab', 'orders')
            ->set('orderSearchInput', $order->order_code)
            ->call('checkOrders');

        $html = $component->html();
        // Step 4 is 'Giao thành công' - must contain checkmark ✓
        $this->assertStringContainsString('Giao thành công', $html);
        // Ensure 4 checkmarks exist in the stepper circles
        $checkmarkCount = substr_count($html, 'wl-step-circle">✓<');
        $this->assertEquals(4, $checkmarkCount, 'Delivered order must have all 4 steps checked with ✓');
    }

    public function test_three_tabs_share_identical_ui_and_logic(): void
    {
        // 1. Visit /orders
        $respOrders = $this->get('/orders');
        $respOrders->assertStatus(200);
        $respOrders->assertSee('Tra cứu thời gian bảo hành');
        $respOrders->assertSee('Tra cứu đơn hàng');
        $respOrders->assertSee('Kích hoạt bảo hành');
        $respOrders->assertSee('TRA CỨU TIẾN TRÌNH ĐƠN HÀNG');
        $respOrders->assertDontSee('ot-head-actions'); // No duplicate action buttons

        // 2. Visit /tra-cuu-bao-hanh
        $respWarranty = $this->get('/tra-cuu-bao-hanh');
        $respWarranty->assertStatus(200);
        $respWarranty->assertSee('Tra cứu thời gian bảo hành');
        $respWarranty->assertSee('Tra cứu đơn hàng');
        $respWarranty->assertSee('Kích hoạt bảo hành');
        $respWarranty->assertSee('TRA CỨU THỜI GIAN BẢO HÀNH');

        // 3. Visit /kich-hoat
        $respActivate = $this->get('/kich-hoat');
        $respActivate->assertStatus(200);
        $respActivate->assertSee('Tra cứu thời gian bảo hành');
        $respActivate->assertSee('Tra cứu đơn hàng');
        $respActivate->assertSee('Kích hoạt bảo hành');
        $respActivate->assertSee('KÍCH HOẠT BẢO HÀNH ĐIỆN TỬ');
    }
}
