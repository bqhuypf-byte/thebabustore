<?php
namespace Tests\Feature;
use App\Filament\Resources\PromotionCampaignResource;
use App\Filament\Resources\PromotionCampaignResource\Pages\EditPromotionCampaign;
use App\Livewire\PromotionCampaignPage;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PromotionCampaign;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;
class PromotionCampaignTest extends TestCase {
    use DatabaseTransactions;
    private function campaign(array $attributes=[]): PromotionCampaign {
        return PromotionCampaign::create(array_merge(['name'=>'Campaign '.Str::uuid(),'slug'=>'campaign-'.Str::uuid(),'headline'=>'Black Friday test campaign','is_active'=>true,'show_product_banner'=>true,'product_banner_image'=>'test-campaign-banner.webp','priority'=>1000],$attributes));
    }
    private function product(string $name,string $stock='in_stock',bool $active=true,?int $quantity=null): Product {
        $p=Product::create(['name'=>$name,'slug'=>Str::slug($name).'-'.Str::uuid(),'category_id'=>Category::firstOrFail()->id,'price'=>999999,'discount_price'=>0,'images'=>[],'is_active'=>$active,'on_sale'=>false]);
        ProductVariant::create(['product_id'=>$p->id,'name'=>'Test variant','sku'=>'campaign-'.Str::uuid(),'price'=>123456,'discount_price'=>0,'stock'=>$stock,'stock_quantity'=>$quantity]);return $p;
    }
    public function test_campaign_only_lists_its_enabled_available_products_and_uses_variant_price(): void {
        $c=$this->campaign();$included=$this->product('Campaign included '.Str::uuid());$outside=$this->product('Campaign outside '.Str::uuid());$oos=$this->product('Campaign unavailable '.Str::uuid(),'out_of_stock');$inactive=$this->product('Campaign inactive '.Str::uuid(),'in_stock',false);$zero=$this->product('Campaign zero '.Str::uuid(),'in_stock',true,0);$hidden=$this->product('Campaign hidden '.Str::uuid());
        foreach([$included,$oos,$inactive,$zero] as $i=>$p)$c->items()->create(['product_id'=>$p->id,'is_active'=>true,'sort_order'=>$i]);
        $c->items()->create(['product_id'=>$hidden->id,'is_active'=>false]);
        $response=$this->get('/khuyen-mai/'.$c->slug)->assertOk()->assertSee($included->name)->assertSee('123.456')->assertDontSee('999.999');
        $dom=new \DOMDocument(); @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath=new \DOMXPath($dom);
        $pageRoot=$xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " bf-page ")]')->item(0);
        $this->assertNotEmpty($pageRoot->getAttribute('wire:id'), 'Campaign controls must belong to the Livewire page root.');
        $campaignText=$xpath->query('//*[@id="black-friday-products"]')->item(0)->textContent;
        foreach([$outside,$oos,$inactive,$zero,$hidden] as $p)$this->assertStringNotContainsString($p->name,$campaignText);
        $this->assertFalse((bool)$included->fresh()->on_sale);$this->assertEquals(123456,$included->variants()->first()->price);
    }
    public function test_filter_and_sort_order_work_for_campaign_membership(): void {
        $c=$this->campaign();$a=$this->product('Campaign sort A '.Str::uuid());$b=$this->product('Campaign sort B '.Str::uuid());
        $c->items()->create(['product_id'=>$a->id,'sort_order'=>5]);$c->items()->create(['product_id'=>$b->id,'sort_order'=>1]);
        Livewire::test(PromotionCampaignPage::class,['slug'=>$c->slug])->assertSeeInOrder([$b->name,$a->name])->set('category','category-does-not-exist')->assertSee('Sản phẩm ưu đãi đang được cập nhật')->assertDontSee($a->name)->set('category','')->assertSee($a->name);
    }
    public function test_disabled_future_and_expired_campaigns_are_not_public_and_cannot_supply_banners(): void {
        foreach([['is_active'=>false],['starts_at'=>now()->addDay()],['ends_at'=>now()->subMinute()]] as $attributes){$c=$this->campaign($attributes);$this->get('/khuyen-mai/'.$c->slug)->assertNotFound();$this->assertFalse(PromotionCampaign::live()->whereKey($c->id)->exists());}
        $live=$this->campaign(['ends_at'=>now()->addHour(),'priority'=>5000]);$this->assertSame($live->id,PromotionCampaign::productBanner()?->id);
        $live->update(['show_product_banner'=>false]);$this->assertNotSame($live->id,PromotionCampaign::productBanner()?->id);
    }
    public function test_banner_links_to_campaign_and_replaces_unrelated_slide_on_product_page(): void {
        $c=$this->campaign(['priority'=>9000]);$p=$this->product('Campaign widget '.Str::uuid());
        $this->get('/products/'.$p->slug)->assertOk()->assertSee($c->publicUrl(),false)->assertSee('pd-campaign-banner',false)->assertSee($c->product_banner_image,false);
        PromotionCampaign::query()->update(['show_product_banner'=>false]);
        $this->get('/products/'.$p->slug)->assertOk()->assertDontSee('pd-campaign-banner',false);
    }
    public function test_admin_can_manage_membership_without_changing_product_pricing_or_global_sale_flag(): void {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $admin=User::create(['name'=>'Campaign test administrator','email'=>'campaign-admin-'.Str::uuid().'@example.invalid','password'=>'test-password','is_admin'=>true]);$this->actingAs($admin);
        $c=$this->campaign();$p=$this->product('Campaign admin product '.Str::uuid());
        Livewire::test(EditPromotionCampaign::class,['record'=>$c->id])->fillForm(['headline'=>'Updated campaign heading','items'=>[['product_id'=>$p->id,'badge'=>'Campaign label','is_active'=>true]]])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Updated campaign heading',$c->fresh()->headline);$this->assertSame($p->id,$c->items()->first()?->product_id);
        $this->assertEquals(123456,$p->variants()->first()->price);$this->assertFalse((bool)$p->fresh()->on_sale);
        Livewire::test(EditPromotionCampaign::class,['record'=>$c->id])->fillForm(['items'=>[],'is_active'=>false,'show_product_banner'=>false])->call('save')->assertHasNoFormErrors();
        $this->assertSame(0,$c->items()->count());$this->get('/khuyen-mai/'.$c->slug)->assertNotFound();
    }
    public function test_admin_schedule_allows_end_only_and_rejects_end_before_start(): void {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $admin=User::create(['name'=>'Campaign scheduling test','email'=>'campaign-schedule-'.Str::uuid().'@example.invalid','password'=>'test-password','is_admin'=>true]);$this->actingAs($admin);
        $c=$this->campaign();
        Livewire::test(EditPromotionCampaign::class,['record'=>$c->id])->fillForm(['starts_at'=>null,'ends_at'=>now()->addDay()->format('Y-m-d H:i:s')])->call('save')->assertHasNoFormErrors();
        Livewire::test(EditPromotionCampaign::class,['record'=>$c->id])->fillForm(['starts_at'=>now()->addDays(2)->format('Y-m-d H:i:s'),'ends_at'=>now()->addDay()->format('Y-m-d H:i:s')])->call('save')->assertHasFormErrors(['ends_at']);
    }
    public function test_homepage_preview_uses_campaign_membership_and_hides_when_campaign_stops(): void {
        PromotionCampaign::where('slug','black-friday')->update(['slug'=>'previous-black-friday-'.Str::uuid()]);
        $c=$this->campaign(['slug'=>'black-friday']);$included=$this->product('Homepage campaign member '.Str::uuid());$outside=$this->product('Homepage sale outside '.Str::uuid());$outside->update(['on_sale'=>true]);
        $c->items()->create(['product_id'=>$included->id]);
        $response=$this->get('/')->assertOk()->assertSee('bf-home-event',false);
        $dom=new \DOMDocument();@$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $region=(new \DOMXPath($dom))->query('//*[@aria-labelledby="home-black-friday-title"]')->item(0);
        $this->assertNotNull($region);$this->assertStringContainsString($included->name,$region->textContent);$this->assertStringNotContainsString($outside->name,$region->textContent);
        $this->assertStringContainsString('/black-friday',$dom->saveHTML($region));
        $c->update(['is_active'=>false]);$this->get('/')->assertOk()->assertDontSee('bf-home-event',false);
    }
    public function test_campaign_permissions_follow_existing_admin_access(): void {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $customer=User::create(['name'=>'Campaign test customer','email'=>'campaign-customer-'.Str::uuid().'@example.invalid','password'=>'test-password','is_admin'=>false]);
        $this->assertFalse(Gate::forUser($customer)->allows('viewAny',PromotionCampaign::class));$this->assertFalse(Gate::forUser($customer)->allows('create',PromotionCampaign::class));
        $this->actingAs($customer);$this->get('/hg-digital/promotion-campaigns')->assertForbidden();
    }
}
