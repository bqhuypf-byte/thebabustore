<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderReceiptTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receipt_is_available_to_the_owner_and_admin_only(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $other = User::factory()->create(['is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'user_id' => $owner->id,
            'order_code' => 'TEST-RECEIPT-' . uniqid(),
            'grand_total' => 150000,
            'shipping_amount' => 30000,
            'payment_method' => 'bank',
            'payment_status' => 'pending',
            'status' => 'new',
        ]);
        Address::create([
            'order_id' => $order->id,
            'full_name' => 'Khách kiểm thử',
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'district' => 'Ba Đình',
            'ward' => 'Phúc Xá',
            'detailed_address' => 'Địa chỉ kiểm thử',
        ]);

        $this->actingAs($owner)->get(route('orders.receipt', $order))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Khách kiểm thử')
            ->assertSee('180.000₫')
            ->assertSee('Chờ thanh toán');
        $this->actingAs($other)->get(route('orders.receipt', $order))->assertNotFound();
        $this->actingAs($admin)->get(route('orders.receipt', $order))->assertOk();
    }
}
