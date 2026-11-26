<?php
namespace Tests\Feature;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Livewire\ProductQuickView;
use App\Livewire\ProductDetail;
use App\Livewire\Home;
use App\Livewire\Shop;
use App\Support\PurchaseValidation as Purchase;
use App\Helpers\CartManagement;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseValidationTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void { parent::setUp();session()->start();session()->put('cart_items',[]); }
    private function product(bool $single=false): Product
    {
        $p=Product::create(['name'=>'Purchase fixture','slug'=>'purchase-'.Str::uuid(),'category_id'=>1,'price'=>100000,'discount_price'=>0,'images'=>[],'is_active'=>true]);
        $variants=$single ? [['A','White',3,'in_stock']] : [['A','White',3,'in_stock'],['A','Black',0,'out_of_stock'],['B','Black',2,'in_stock']];
        foreach ($variants as [$model,$color,$qty,$status]) ProductVariant::create(['product_id'=>$p->id,'name'=>$model.' '.$color,'sku'=>'PUR-'.Str::uuid(),'price'=>100000,'discount_price'=>0,'stock'=>$status,'stock_quantity'=>$qty,'classification'=>['Model'=>$model,'Color'=>$color]]);
        return $p->load('variants');
    }
    private function choose($test) { return $test->call('selectClassification','Model','A')->call('selectClassification','Color','White'); }
    public function test_multi_variant_requires_all_choices_and_rejects_missing_groups(): void
    {
        $p=$this->product();
        $q=Livewire::test(ProductQuickView::class)->call('open',$p->id)->assertSet('variantId',null)->assertSee('Vui lòng chọn Model');
        $q->call('addToCart')->assertHasErrors('purchase')->assertSet('productId',$p->id);
        $q->call('selectClassification','Model','A')->assertSet('variantId',null)->assertSee('Vui lòng chọn Color');
        $this->assertEmpty(CartManagement::getRawCartItemsFromSession());
    }
    public function test_availability_and_explicit_choices_update_without_auto_selecting(): void
    {
        $p=$this->product();
        $groups=Purchase::groups($p,['Model'=>'A']);
        $black=collect(collect($groups)->firstWhere('name','Color')['options'])->firstWhere('label','Black');
        $this->assertFalse($black['available']);
        $q=$this->choose(Livewire::test(ProductQuickView::class)->call('open',$p->id));
        $q->assertSet('variantId',$p->variants->first()->id)->call('selectClassification','Model','B')->assertSet('variantId',null)->assertSet('purchaseOptions',['Model'=>'B']);
        $q->call('selectClassification','Color','Black')->assertSet('variantId',$p->variants->last()->id);
    }
    public function test_typed_quantity_is_clamped_and_low_stock_is_real(): void
    {
        $p=$this->product(true);
        Livewire::test(ProductQuickView::class)->call('open',$p->id)->assertSee('Chỉ còn 3 sản phẩm')->set('quantity',999)->assertSet('quantity',3)->set('quantity',-5)->assertSet('quantity',1)->call('changeQuantity',-1)->assertSet('quantity',1);
    }
    public function test_success_closes_modal_updates_badge_and_emits_toast(): void
    {
        $p=$this->product(true);
        Livewire::test(ProductQuickView::class)->call('open',$p->id)->call('addToCart')->assertHasNoErrors()->assertSet('productId',null)->assertDispatched('cart-updated')->assertDispatched('purchase-toast');
        $this->assertSame(1,CartManagement::getRawCartItemsFromSession()[0]['quantity']);
    }
    public function test_stock_changed_after_selection_blocks_purchase_and_keeps_modal_open(): void
    {
        $p=$this->product(true);$q=Livewire::test(ProductQuickView::class)->call('open',$p->id);
        $p->variants->first()->update(['stock_quantity'=>0]);
        $q->call('addToCart',true)->assertHasErrors('purchase')->assertSet('productId',$p->id)->assertNoRedirect()->assertSee('Hết hàng');
        $this->assertEmpty(CartManagement::getRawCartItemsFromSession());
    }
    public function test_buy_now_uses_same_validation_and_redirects_after_add(): void
    {
        $p=$this->product(true);
        Livewire::test(ProductQuickView::class)->call('open',$p->id)->call('addToCart',true)->assertRedirect('/checkout');
        $this->assertSame($p->variants->first()->id,CartManagement::getRawCartItemsFromSession()[0]['product_variant_id']);
    }
    public function test_duplicate_token_does_not_add_twice_and_existing_cart_counts_toward_stock(): void
    {
        $p=$this->product(true);$v=$p->variants->first();$attrs=Purchase::map($p)[$v->id];$token=(string)Str::uuid();
        Purchase::add($p->id,$v->id,$attrs,2,$token);Purchase::add($p->id,$v->id,$attrs,2,$token);
        $this->assertSame(2,CartManagement::getRawCartItemsFromSession()[0]['quantity']);
        try { Purchase::add($p->id,$v->id,$attrs,2,(string)Str::uuid());$this->fail('Cart total must respect stock.'); }
        catch (ValidationException $e) { $this->assertArrayHasKey('purchase',$e->errors()); }
    }
    public function test_foreign_variant_and_forged_selection_are_rejected(): void
    {
        $p=$this->product(true);$foreign=$this->product(true)->variants->first();
        $this->expectException(ValidationException::class);
        Purchase::add($p->id,$foreign->id,Purchase::map($p)[$p->variants->first()->id],1,(string)Str::uuid());
    }
    public function test_unknown_inventory_is_not_fabricated(): void
    {
        $p=$this->product(true);$p->variants->first()->update(['stock_quantity'=>null]);$p->refresh()->load('variants');
        $this->assertSame(99,Purchase::limit($p->variants->first()));
        Livewire::test(ProductQuickView::class)->call('open',$p->id)->assertDontSee('Chỉ còn');
    }
    public function test_detail_and_home_preview_share_selection_validation(): void
    {
        $p=$this->product();
        Livewire::test(ProductDetail::class,['slug'=>$p->slug])->assertSet('selectedVariant',null)->call('buyNow')->assertHasErrors('purchase')->assertNoRedirect();
        Livewire::test(Home::class)->call('openPreview',$p->id)->assertSet('previewVariantId',null)->call('addPreviewToCart')->assertHasErrors('purchase')->assertSet('previewProductId',$p->id);
    }
    public function test_cards_add_single_variants_and_open_picker_for_multiple(): void
    {
        $single=$this->product(true);$multi=$this->product();
        Livewire::test(Shop::class)->call('purchaseCard',$single->id,(string)Str::uuid())->assertHasNoErrors()->assertDispatched('cart-updated');
        Livewire::test(Shop::class)->call('purchaseCard',$multi->id,(string)Str::uuid())->assertDispatched('open-product-quick-view');
        $this->assertCount(1,CartManagement::getRawCartItemsFromSession());
    }
    public function test_sold_out_card_and_forged_quantity_cannot_purchase(): void
    {
        $p=$this->product(true);$p->variants->first()->update(['stock_quantity'=>0]);
        Livewire::test(Shop::class)->call('purchaseCard',$p->id,(string)Str::uuid())->assertHasErrors('cardPurchase');
        $this->assertEmpty(CartManagement::getRawCartItemsFromSession());
    }
    public function test_duplicate_variant_names_still_select_the_exact_variant(): void
    {
        $p=$this->product();
        foreach ($p->variants as $v) $v->update(['name'=>'Same name','classification'=>[]]);
        $p->refresh()->load('variants');
        $q=Livewire::test(ProductQuickView::class)->call('open',$p->id)->call('selectVariant',$p->variants->last()->id)->assertSet('variantId',$p->variants->last()->id);
        $q->call('addToCart')->assertHasNoErrors();
        $this->assertSame($p->variants->last()->id,CartManagement::getRawCartItemsFromSession()[0]['product_variant_id']);
    }
    public function test_forged_non_integer_quantity_is_rejected_on_server(): void
    {
        $p=$this->product(true);$v=$p->variants->first();
        $this->expectException(ValidationException::class);
        Purchase::add($p->id,$v->id,Purchase::map($p)[$v->id],'1.5',(string)Str::uuid());
    }
    public function test_cart_badge_refreshes_from_validated_purchase(): void
    {
        $p=$this->product(true);$v=$p->variants->first();
        Purchase::add($p->id,$v->id,Purchase::map($p)[$v->id],2,(string)Str::uuid());
        Livewire::test(\App\Livewire\Inc\Header::class)->call('refreshCart')->assertSet('totalQuantity',2);
    }

    public function test_related_product_cards_share_the_direct_add_validator(): void
    {
        $main=$this->product();$related=$this->product(true);
        Livewire::test(ProductDetail::class,['slug'=>$main->slug])->call('purchaseCard',$related->id,(string)Str::uuid())->assertHasNoErrors()->assertDispatched('cart-updated');
        $this->assertSame($related->variants->first()->id,CartManagement::getRawCartItemsFromSession()[0]['product_variant_id']);
    }

    public function test_colour_first_variant_names_split_pro_and_colour_on_all_surfaces(): void
    {
        $p=$this->product();
        foreach ($p->variants as $variant) $variant->delete();
        $variants=collect(['Cam pro','Xanh Pro','Trắng Pro','Silver Pro'])->map(fn($name,$i)=>ProductVariant::create([
            'product_id'=>$p->id,'name'=>$name,'sku'=>'SPLIT-'.Str::uuid(),'price'=>100000+$i*10000,
            'stock'=>$i === 3 ? 'out_of_stock' : 'in_stock','classification'=>null,
        ]));
        $p->refresh()->load('variants');
        $map=\App\Support\VariantSelection::map($p);
        $this->assertSame(['Phiên bản'=>'Pro','Màu sắc'=>'Cam'],$map[$variants[0]->id]);
        $this->assertSame(['Phiên bản'=>'Pro','Màu sắc'=>'Trắng'],$map[$variants[2]->id]);
        $groups=Purchase::groups($p,['Phiên bản'=>'Pro']);
        $this->assertSame(['Phiên bản','Màu sắc'],array_column($groups,'name'));
        $this->assertFalse(collect($groups[1]['options'])->firstWhere('label','Silver')['available']);
        foreach ([
            [ProductQuickView::class,'open','selectClassification','variantId'],
            [Home::class,'openPreview','selectPreviewClassification','previewVariantId'],
        ] as [$surface,$open,$select,$property]) {
            Livewire::test($surface)->call($open,$p->id)->assertSet($property,null)
                ->call($select,'Phiên bản','Pro')->assertSet($property,null)->assertSee('Vui lòng chọn Màu sắc')
                ->call($select,'Màu sắc','Trắng')->assertSet($property,$variants[2]->id)
                ->call($select,'Màu sắc','Silver')->assertSet($property,$variants[2]->id);
        }
        Livewire::test(ProductDetail::class,['slug'=>$p->slug])
            ->call('selectClassification','Phiên bản','Pro')->assertSet('selectedVariant',null)
            ->call('selectClassification','Màu sắc','Xanh')->assertSet('selectedVariant',$variants[1]->id)->assertSet('price',110000);
    }

    public function test_zenith_iceblade_and_compound_colours_are_distinct_valid_choices(): void
    {
        $p=$this->product();
        foreach($p->variants as $v) $v->delete();
        foreach(['Pink Zenith','Black Zenith','Mint Iceblade'] as $name) ProductVariant::create(['product_id'=>$p->id,'name'=>$name,'sku'=>'RS6-'.Str::uuid(),'price'=>1750000,'stock'=>'in_stock']);
        $p->refresh()->load('variants');
        $map=\App\Support\VariantSelection::map($p);
        $this->assertSame(['Phiên bản'=>'Zenith','Màu sắc'=>'Pink'],$map[$p->variants[0]->id]);
        $this->assertSame(['Phiên bản'=>'Iceblade','Màu sắc'=>'Mint'],$map[$p->variants[2]->id]);
        $groups=Purchase::groups($p,['Phiên bản'=>'Zenith']);
        $this->assertFalse(collect($groups[1]['options'])->firstWhere('label','Mint')['available']);
        Livewire::test(ProductDetail::class,['slug'=>$p->slug])->call('selectClassification','Phiên bản','Zenith')->call('selectClassification','Màu sắc','Black')->assertSet('selectedVariant',$p->variants[1]->id)->call('selectClassification','Phiên bản','Iceblade')->assertSet('selectedVariant',null)->call('selectClassification','Màu sắc','Mint')->assertSet('selectedVariant',$p->variants[2]->id);
        $p->variants[0]->update(['name'=>'Ultra+ Rose Red']);$p->variants[1]->update(['name'=>'Ultra+ Pastel Pink']);$p->variants[2]->update(['name'=>'Ultra+ Black Gold']);
        $p->refresh()->load('variants');$map=\App\Support\VariantSelection::map($p);
        $this->assertSame(['Phiên bản'=>'Ultra+','Màu sắc'=>'Rose red'],$map[$p->variants[0]->id]);
        $this->assertSame('Pastel pink',$map[$p->variants[1]->id]['Màu sắc']);
        $this->assertSame('Black gold',$map[$p->variants[2]->id]['Màu sắc']);
    }
    public function test_colour_only_products_use_the_real_product_as_the_version(): void
    {
        $p=$this->product();foreach($p->variants as $v)$v->delete();
        foreach(['Black','White','Blue'] as $name) ProductVariant::create(['product_id'=>$p->id,'name'=>$name,'sku'=>'COLOR-'.Str::uuid(),'price'=>100000,'stock'=>'in_stock']);
        $p->refresh()->load('variants');$map=\App\Support\VariantSelection::map($p);
        $this->assertSame(['Phiên bản'=>$p->name,'Màu sắc'=>'Black'],$map[$p->variants[0]->id]);
    }

    public function test_sale_buy_now_card_redirects_single_variant_and_opens_picker_for_multiple(): void
    {
        $single=$this->product(true);$multi=$this->product();
        $single->update(['on_sale'=>true]);$multi->update(['on_sale'=>true]);
        Livewire::test(Shop::class)->call('purchaseCard',$single->id,(string)Str::uuid(),true)->assertRedirect('/checkout');
        Livewire::test(Shop::class)->call('purchaseCard',$multi->id,(string)Str::uuid(),true)->assertDispatched('open-product-quick-view')->assertNoRedirect();
        $this->assertCount(1,CartManagement::getRawCartItemsFromSession());
    }

    public function test_switch_types_with_case_or_clicky_typo_share_one_version_option(): void
    {
        $p=$this->product();foreach($p->variants as $v)$v->delete();
        foreach(['Black Tactile','Grey Clicky','White Linear','Mint linear','Orange Clikcy','Blue Clicky','Blue Silent - linear','Blue Silent- Tactile'] as $name) ProductVariant::create(['product_id'=>$p->id,'name'=>$name,'sku'=>'SW-'.Str::uuid(),'price'=>14000,'stock'=>'in_stock']);
        $p->refresh()->load('variants');$groups=Purchase::groups($p,[]);
        $this->assertSame(['Tactile','Clicky','Linear','Silent Linear','Silent Tactile'],array_column($groups[0]['options'],'label'));
        $map=\App\Support\VariantSelection::map($p);
        $this->assertSame(['Phiên bản'=>'Clicky','Màu sắc'=>'Orange'],$map[$p->variants[4]->id]);
    }

}
