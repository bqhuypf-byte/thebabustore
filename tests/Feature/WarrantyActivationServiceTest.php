<?php
namespace Tests\Feature;
use App\Models\{Product,ProductVariant,Order,WarrantyActivation,WarrantyRequest,User};
use App\Services\WarrantyService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
class WarrantyActivationServiceTest extends TestCase {
    use DatabaseTransactions;
    private function variant(int $days=180): ProductVariant {
        $p=Product::create(['name'=>'Warranty workflow test','slug'=>'warranty-workflow-'.uniqid(),'category_id'=>1,'price'=>100000]);
        return ProductVariant::create(['product_id'=>$p->id,'sku'=>'WTEST'.uniqid(),'name'=>'Test variant','price'=>100000,'stock'=>'in_stock','warranty_days'=>$days]);
    }
    private function order(ProductVariant $v): Order {
        return app(WarrantyService::class)->createOffline(['phone'=>'0912345678','name'=>'Test Customer','purchase_date'=>today()->subDays(40)->toDateString(),'items'=>[['product_variant_id'=>$v->id,'quantity'=>2]]]);
    }
    public function test_offline_order_needs_no_account_and_does_not_activate_automatically(): void {
        $before=User::count();$v=$this->variant();$o=$this->order($v);
        $this->assertNull($o->user_id);$this->assertSame($before,User::count());
        $this->assertNull($o->items->first()->warranty);$this->assertCount(0,$o->warrantyActivations);
        $this->assertSame('pending',$o->payment_status);
    }
    public function test_expiry_is_purchase_based_and_repeated_activation_is_idempotent(): void {
        $v=$this->variant();$o=$this->order($v);$s=app(WarrantyService::class);
        $a=$s->activate($o,[$v->id],'Test Customer','0912345678')[0];
        $this->assertSame($o->purchase_date->copy()->addDays(180)->toDateString(),$a->expires_at->toDateString());
        $this->assertSame(2,$a->quantity);
        $again=$s->activate($o,[$v->id],'Changed Name','0987654321')[0];
        $this->assertSame($a->id,$again->id);$this->assertSame('0912345678',$again->customer_phone);
        $this->assertSame(1,$o->warrantyActivations()->count());
    }
    public function test_zero_warranty_days_never_becomes_365(): void {
        $v=$this->variant(0);$o=$this->order($v);
        try { app(WarrantyService::class)->activate($o,[$v->id],'Test Customer','0912345678');$this->fail('Expected policy confirmation'); }
        catch (ValidationException $e) { $this->assertStringContainsString('365',$e->getMessage()); }
        $this->assertNull($o->items()->first()->warranty);$this->assertSame(0,$o->warrantyActivations()->count());
    }
    public function test_unconfirmed_legacy_purchase_date_is_not_guessed(): void {
        $v=$this->variant();$o=$this->order($v);$o->update(['purchase_date'=>null]);
        $this->expectException(ValidationException::class);
        app(WarrantyService::class)->activate($o,[$v->id],'Test Customer','0912345678');
    }
    public function test_token_is_hashed_rotated_and_expires(): void {
        $v=$this->variant();$o=$this->order($v);$s=app(WarrantyService::class);
        parse_str(parse_url($s->generateLink($o),PHP_URL_QUERY),$first);$o->refresh();
        $this->assertNotSame($first['token'],$o->activation_token_hash);
        $s->authorizeCustomer($o,$first['token'],'0987654321');
        parse_str(parse_url($s->generateLink($o),PHP_URL_QUERY),$second);$o->refresh();
        $this->assertNotSame($first['token'],$second['token']);
        try { $s->authorizeCustomer($o,$first['token'],'');$this->fail('Old token accepted'); } catch (ValidationException $e) { $this->assertArrayHasKey('activateLookup',$e->errors()); }
        $o->update(['activation_token_expires_at'=>now()->subSecond()]);
        $this->expectException(ValidationException::class);$s->authorizeCustomer($o,$second['token'],'');
    }
    public function test_code_requires_matching_phone_and_only_order_variants_can_activate(): void {
        $v=$this->variant();$other=$this->variant();$o=$this->order($v);$s=app(WarrantyService::class);
        try { $s->authorizeCustomer($o,'','0987654321');$this->fail('Wrong phone accepted'); } catch (ValidationException $e) { $this->assertArrayHasKey('activateLookup',$e->errors()); }
        $s->authorizeCustomer($o,'','+84912345678');
        $this->expectException(ValidationException::class);$s->activate($o,[$other->id],'Test Customer','0912345678',null,'customer');
    }
    public function test_existing_warranty_is_not_extended(): void {
        $v=$this->variant();$o=$this->order($v);$old=today()->addDays(10);
        $o->items()->first()->update(['warranty'=>$old]);
        $a=app(WarrantyService::class)->activate($o,[$v->id],'Test Customer','0912345678')[0];
        $this->assertSame($old->toDateString(),$a->expires_at->toDateString());
    }
    public function test_staff_approval_is_required_and_can_only_run_once(): void {
        $v=$this->variant();$staff=User::factory()->create(['is_admin'=>true]);
        $r=WarrantyRequest::create(['product_variant_id'=>$v->id,'customer_name'=>'Test Customer','customer_phone'=>'0912345678','status'=>'pending']);
        $this->assertNull($r->order_id);
        app(WarrantyService::class)->approve($r,today()->subDays(20)->toDateString(),$staff->id);
        $r->refresh();$this->assertSame('approved',$r->status);$this->assertNotNull($r->order_id);
        $this->assertSame(1,$r->order->warrantyActivations()->count());
        $this->expectException(ValidationException::class);app(WarrantyService::class)->approve($r,today()->toDateString(),$staff->id);
    }
}
