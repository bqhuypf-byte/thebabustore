<?php

namespace Tests\Feature;

use App\Livewire\Checkout;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_checkout_rejects_a_variant_that_became_out_of_stock(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::create(['name' => 'Stock test', 'slug' => 'stock-test-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'STOCK-'.uniqid(), 'name' => 'Variant', 'price' => 100000, 'stock' => 'out_of_stock', 'warranty_days' => 0]);
        $this->actingAs($user)->withSession(['cart_items' => [['product_variant_id' => $variant->id, 'quantity' => 1]]]);
        Livewire::test(Checkout::class)
            ->set('shippingAddress.full_name', 'Test User')->set('shippingAddress.phone', '0900000000')
            ->set('shippingAddress.province', 'Hà Nội')->set('shippingAddress.district', 'Ba Đình')
            ->set('shippingAddress.ward', 'Phúc Xá')->set('shippingAddress.detailed_address', 'Test address')
            ->call('placeOrder')->assertHasErrors(['cart']);
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    public function test_checkout_creates_an_order_for_the_signed_in_customer_and_redirects_to_payment(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::create([
            'name' => 'Sản phẩm kiểm thử',
            'slug' => 'checkout-test-' . uniqid(),
            'category_id' => 1,
            'price' => 100000,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'TEST-' . uniqid(),
            'name' => 'Mặc định',
            'price' => 100000,
            'discount_price' => 50000,
            'stock' => 'in_stock',
            'warranty_days' => 0,
        ]);

        $this->actingAs($user)->withSession([
            'cart_items' => [['product_variant_id' => $variant->id, 'quantity' => 2]],
        ]);

        $component = Livewire::test(Checkout::class)
            ->set('shippingAddress.full_name', 'Khách kiểm thử')
            ->set('shippingAddress.phone', '0900000000')
            ->set('shippingAddress.province', 'Hà Nội')
            ->set('shippingAddress.district', 'Ba Đình')
            ->set('shippingAddress.ward', 'Phúc Xá')
            ->set('shippingAddress.detailed_address', 'Địa chỉ kiểm thử')
            ->set('paymentMethod', 'bank')
            ->call('placeOrder')
            ->assertHasNoErrors();

        $order = Order::where('user_id', $user->id)->firstOrFail();
        $component->assertRedirect(route('bank', $order->order_code));
        $this->assertSame('new', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertEquals(200000, $order->grand_total);
        $this->assertEquals(30000, $order->shipping_amount);
        $this->assertEquals(2, $order->items()->firstOrFail()->quantity);
        $this->assertSame('Khách kiểm thử', $order->address->full_name);
        $this->assertSame([], session('cart_items', []));
    }
}
