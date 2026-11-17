<?php

namespace Tests\Feature;

use App\Livewire\WarrantyLookup;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WarrantyActivation;
use App\Models\WarrantyRequest;
use App\Services\WarrantyService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class WarrantyCustomerActivationTest extends TestCase
{
    use DatabaseTransactions;

    private function fixture(): array
    {
        $p = Product::create(['name' => 'Customer Warranty Test', 'slug' => 'customer-warranty-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        $v = ProductVariant::create(['product_id' => $p->id, 'sku' => 'CUSTOMER'.uniqid(), 'name' => 'Test version', 'price' => 100000, 'stock' => 'in_stock', 'warranty_days' => 180]);
        $o = app(WarrantyService::class)->createOffline(['phone' => '0912345678', 'name' => 'Warranty Customer', 'purchase_date' => today()->subDays(10)->toDateString(), 'items' => [['product_variant_id' => $v->id, 'quantity' => 1]]]);

        return [$o, $v];
    }

    public function test_order_code_and_phone_can_activate_and_repeat_without_extending(): void
    {
        [$o,$v] = $this->fixture();
        $c = Livewire::test(WarrantyLookup::class)->call('setTab', 'activate')->set('activateCode', $o->order_code)->set('activatePhone', '0912345678')->call('searchActivationCode')->assertHasNoErrors()->assertSet('activateOrderId', $o->id)->set('activateFullName', 'Warranty Customer')->call('submitActivation')->assertHasNoErrors()->assertSet('activateSuccess', true);
        $expiry = $o->warrantyActivations()->first()->expires_at->toDateString();
        $c->call('submitActivation')->assertHasNoErrors();
        $this->assertSame(1, $o->warrantyActivations()->count());
        $this->assertSame($expiry, $o->warrantyActivations()->first()->expires_at->toDateString());
        $this->assertSame('delivered', $o->fresh()->status);
        $status = WarrantyLookup::getWarrantyStatus($o->items()->first());
        $this->assertSame('active', $status['state']);
    }

    public function test_wrong_phone_does_not_expose_order_items(): void
    {
        [$o,$v] = $this->fixture();
        Livewire::test(WarrantyLookup::class)->set('activateCode', $o->order_code)->set('activatePhone', '0987654321')->call('searchActivationCode')->assertSet('activateOrderId', null)->assertHasErrors('activateLookup');
    }

    public function test_token_prefills_order_and_allows_customer_phone_without_editing_order(): void
    {
        [$o,$v] = $this->fixture();
        $url = app(WarrantyService::class)->generateLink($o);
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        $c = Livewire::withQueryParams(['token' => $q['token']])->test(WarrantyLookup::class, ['code' => $o->order_code])->call('setTab', 'activate')->assertSet('activateOrderId', $o->id)->assertSet('activatePhone', '0912345678')->set('activatePhone', '0987654321')->call('submitActivation')->assertHasNoErrors()->assertSet('activateSuccess', true);
        $this->assertSame('0912345678', $o->address->phone);
        $this->assertSame('0987654321', $o->warrantyActivations()->first()->customer_phone);
        Livewire::test(WarrantyLookup::class)->set('searchInput', '0987654321')->call('checkWarranty')->assertSee($o->order_code);
    }

    public function test_expired_token_is_friendly_and_does_not_load_order(): void
    {
        [$o,$v] = $this->fixture();
        $url = app(WarrantyService::class)->generateLink($o);
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        $o->update(['activation_token_expires_at' => now()->subSecond()]);
        Livewire::withQueryParams(['token' => $q['token']])->test(WarrantyLookup::class, ['code' => $o->order_code])->call('setTab', 'activate')->assertSet('activateOrderId', null)->assertSee('hết hạn');
    }

    public function test_product_fallback_only_creates_pending_request_no_order_or_activation(): void
    {
        [$o,$v] = $this->fixture();
        $orders = Order::count();
        $activations = WarrantyActivation::count();
        $c = Livewire::test(WarrantyLookup::class)->call('setTab', 'activate')->call('selectActivationVariant', $v->id)->set('activateFullName', 'Fallback Customer')->set('activatePhone', '0912345678')->set('activateSerial', 'TEST-SERIAL')->call('submitFallback')->assertHasNoErrors()->assertSet('fallbackSuccess', true);
        $c->call('submitFallback')->assertHasNoErrors();
        $this->assertSame($orders, Order::count());
        $this->assertSame($activations, WarrantyActivation::count());
        $this->assertSame(1, WarrantyRequest::where('product_variant_id', $v->id)->where('status', 'pending')->count());
    }

    public function test_customer_cannot_change_order_item_variant_or_activate_without_proof(): void
    {
        [$o,$v] = $this->fixture();
        Livewire::test(WarrantyLookup::class)->set('activateCode', $o->order_code)->set('activatePhone', '0912345678')->set('activateFullName', 'Test Customer')->set('selectedVariants', [$v->id])->call('submitActivation')->assertHasErrors('activateLookup');
        $this->assertSame(0, $o->warrantyActivations()->count());
        $this->assertFalse(method_exists(WarrantyLookup::class, 'changeOrderItemVariant'));
    }

    public function test_legacy_component_uses_same_verified_workflow(): void
    {
        [$o,$v] = $this->fixture();
        Livewire::test(\App\Livewire\WarrantyActivation::class, ['code' => $o->order_code])->assertSet('activateOrderId', null)->assertSee('Số điện thoại mua hàng');
        $this->assertFalse(method_exists(\App\Livewire\WarrantyActivation::class, 'activateWarranty'));
    }

    public function test_admin_workflow_pages_render_and_customer_cannot_access(): void
    {
        $staff = User::factory()->create(['is_admin' => true]);
        [$o,$v] = $this->fixture();
        $this->actingAs($staff)->get('/hg-digital/orders')->assertOk()->assertSee('Tạo đơn offline');
        $this->get('/hg-digital/orders/'.$o->id)->assertOk()->assertSee('Kích hoạt bảo hành');
        $this->get('/hg-digital/warranty-requests')->assertOk();
        $this->get('/hg-digital/warranty-activations')->assertOk();
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)->get('/hg-digital/warranty-requests')->assertForbidden();
    }

    public function test_admin_actions_activate_and_generate_a_copyable_link(): void
    {
        [$o,$v] = $this->fixture();
        $staff = User::factory()->create(['is_admin' => true]);
        $this->actingAs($staff);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $page = Livewire::test(\App\Filament\Resources\OrderResource\Pages\ViewOrder::class, ['record' => $o->getRouteKey()]);
        $page->callAction('activate_order_warranty', ['purchase_date' => $o->purchase_date->toDateString(), 'name' => 'Staff Confirmed', 'phone' => '0912345678', 'variants' => [$v->id], 'serial' => 'ADMIN-TEST'])->assertHasNoActionErrors();
        $this->assertSame(1, $o->warrantyActivations()->count());
        $page->mountAction('warranty_activation_link')->assertSee('Sao chép tin nhắn và link')->assertSee('/kich-hoat/');
        $this->assertNotNull($o->fresh()->activation_token_hash);
    }

    public function test_admin_can_approve_and_reject_pending_requests(): void
    {
        [$o,$v] = $this->fixture();
        $staff = User::factory()->create(['is_admin' => true]);
        $this->actingAs($staff);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $r = WarrantyRequest::create(['product_variant_id' => $v->id, 'customer_name' => 'Pending Test', 'customer_phone' => '0912345678', 'status' => 'pending']);
        Livewire::test(\App\Filament\Resources\WarrantyRequestResource\Pages\ListWarrantyRequests::class)->callTableAction('approve', $r, ['purchase_date' => today()->subDays(5)->toDateString(), 'review_notes' => 'Confirmed test receipt'])->assertHasNoTableActionErrors();
        $this->assertSame('approved', $r->fresh()->status);
        $other = WarrantyRequest::create(['product_variant_id' => $v->id, 'customer_name' => 'Rejected Test', 'customer_phone' => '0912345678', 'status' => 'pending']);
        Livewire::test(\App\Filament\Resources\WarrantyRequestResource\Pages\ListWarrantyRequests::class)->callTableAction('reject', $other, ['review_notes' => 'No proof'])->assertHasNoTableActionErrors();
        $this->assertSame('rejected', $other->fresh()->status);
        $this->assertNull($other->fresh()->order_id);
    }

    public function test_admin_offline_creation_action_does_not_create_customer_account(): void
    {
        [$existing,$v] = $this->fixture();
        $staff = User::factory()->create(['is_admin' => true]);
        $this->actingAs($staff);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $before = User::count();
        $page = Livewire::test(\App\Filament\Resources\OrderResource\Pages\ListOrders::class)->mountAction('create_offline_warranty_order');
        $key = array_key_first($page->get('mountedActionsData')[0]['items']);
        $page->set('mountedActionsData.0.phone', '0912345678')->set('mountedActionsData.0.name', 'Offline Action Test')->set('mountedActionsData.0.purchase_date', today()->toDateString())->set('mountedActionsData.0.items.'.$key.'.product_variant_id', $v->id)->set('mountedActionsData.0.items.'.$key.'.quantity', 1)->call('callMountedAction')->assertHasNoActionErrors();
        $this->assertSame($before, User::count());
        $created = Order::latest('id')->first();
        $this->assertNull($created->user_id);
        $this->assertNull($created->items->first()->warranty);
    }

    public function test_token_is_rechecked_on_submit_after_order_was_loaded(): void
    {
        [$o,$v] = $this->fixture();
        $url = app(WarrantyService::class)->generateLink($o);
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        $c = Livewire::withQueryParams(['token' => $q['token']])->test(WarrantyLookup::class, ['code' => $o->order_code])->assertSet('activateOrderId', $o->id);
        $o->update(['activation_token_expires_at' => now()->subSecond()]);
        $c->call('submitActivation')->assertHasErrors('activateLookup');
        $this->assertSame(0, $o->warrantyActivations()->count());
    }
}
