<?php

namespace Tests\Feature;

use App\Livewire\WarrantyActivation;
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

    public function test_unactivated_placeholder_shows_pending_and_can_be_activated(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::create([
            'name' => 'Bàn phím Test',
            'slug' => 'ban-phim-test-' . uniqid(),
            'category_id' => 1,
            'price' => 100000,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'TESTSKU' . uniqid(),
            'name' => 'Bản RGB',
            'price' => 100000,
            'stock' => 'in_stock',
            'warranty_days' => 365,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'BHTEST' . uniqid(),
            'grand_total' => 100000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'new',
            'notes' => 'Tạo link kích hoạt cho SKU: ' . $variant->sku,
        ]);

        Address::create([
            'order_id' => $order->id,
            'full_name' => 'Khách chờ kích hoạt (' . $variant->sku . ')',
            'phone' => '0000000000',
            'province' => '',
            'district' => '',
            'ward' => '',
            'detailed_address' => 'Chờ khách điền',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'unit_amount' => 100000,
            'total_amount' => 100000,
            'warranty' => now(),
        ]);

        // 1. Check lookup when not activated
        $statusBefore = WarrantyLookup::getWarrantyStatus($item);
        $this->assertSame('pending', $statusBefore['state']);
        $this->assertSame('Chưa kích hoạt', $statusBefore['label']);

        // 2. Reject all-zeros phone number
        Livewire::test(WarrantyActivation::class, ['code' => $order->order_code])
            ->set('full_name', 'Nguyễn Văn Test')
            ->set('phone', '0000000000')
            ->call('activateWarranty')
            ->assertHasErrors(['phone']);

        // 3. Activate with valid real phone
        Livewire::test(WarrantyActivation::class, ['code' => $order->order_code])
            ->set('full_name', 'Nguyễn Văn Test')
            ->set('phone', '0912345678')
            ->call('activateWarranty')
            ->assertHasNoErrors()
            ->assertSet('activatedSuccess', true);

        // 4. Verify DB was updated
        $order->refresh();
        $item->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertSame('Nguyễn Văn Test', $order->address->full_name);
        $this->assertSame('0912345678', $order->address->phone);
        $this->assertStringContainsString('Đã kích hoạt BH', $order->notes);

        // 5. Check lookup after activation -> MUST BE ACTIVE
        $statusAfter = WarrantyLookup::getWarrantyStatus($item);
        $this->assertSame('active', $statusAfter['state']);
        $this->assertSame('Đang bảo hành', $statusAfter['label']);
        $this->assertGreaterThan(300, $statusAfter['days_left']);
    }

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

    public function test_already_activated_order_does_not_show_activation_inputs(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::create([
            'name' => 'Bàn phím Test 2',
            'slug' => 'ban-phim-test-2-' . uniqid(),
            'category_id' => 1,
            'price' => 100000,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'TESTSKU2' . uniqid(),
            'name' => 'Bản Pro',
            'price' => 100000,
            'stock' => 'in_stock',
            'warranty_days' => 365,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'BHTEST2' . uniqid(),
            'grand_total' => 100000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'notes' => 'Đã kích hoạt BH bởi Nguyễn Văn A lúc 10:00 09/11/2026',
        ]);

        Address::create([
            'order_id' => $order->id,
            'full_name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'province' => '',
            'district' => '',
            'ward' => '',
            'detailed_address' => 'Hà Nội',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'unit_amount' => 100000,
            'total_amount' => 100000,
            'warranty' => now()->addYear(),
        ]);

        Livewire::test(WarrantyActivation::class, ['code' => $order->order_code])
            ->assertSet('isAlreadyActivated', true)
            ->assertSee('Sản Phẩm Đã Được Kích Hoạt')
            ->assertSee('Đang có hiệu lực')
            ->assertDontSee('Xác Nhận Kích Hoạt Bảo Hành');
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
