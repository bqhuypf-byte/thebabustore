<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\ProductContent;
use App\Support\ProductSeoRevision;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductSeoRevisionTest extends TestCase
{
    use DatabaseTransactions;

    private function fixture(string $stock = 'in_stock'): array
    {
        $p = Product::create(['name'=>'SEO test','slug'=>'seo-revision-'.uniqid(),'category_id'=>1,'price'=>123,'discount_price'=>0,'images'=>['test.jpg'],'description'=>'<p>Original preserved</p>']);
        $v = ProductVariant::create(['product_id'=>$p->id,'name'=>'White','sku'=>'SEO-'.uniqid(),'price'=>876543,'discount_price'=>0,'stock'=>$stock,'warranty_days'=>365]);
        $p->refresh();
        $expected=[];foreach(ProductSeoRevision::BASELINE as $field)$expected[$field]=$p->getAttribute($field);
        $entry=['id'=>$p->id,'expected'=>$expected,'changes'=>[
            'name'=>'Reviewed name','content_sections'=>[['title'=>'Intro','body'=>'Reviewed content','visible'=>true]],
            'specification_groups'=>[['title'=>'Specs','rows'=>[['label'=>'Model','value'=>'X1','source_label'=>'Shop record','verification_status'=>'verified']]]],
            'seo_title'=>str_repeat('A',55),'seo_description'=>str_repeat('B',150),'seo_research'=>['primary_keyword'=>'X1'],
        ]];
        return [$p,$v,$entry];
    }

    public function test_currently_out_of_stock_product_is_never_rewritten(): void
    {
        [$p,$v,$entry]=$this->fixture();
        $v->update(['stock'=>'out_of_stock']);
        $before=$p->fresh()->getAttributes();$backups=[];
        $result=ProductSeoRevision::run($entry,true,function($data)use(&$backups){$backups[]=$data;});
        $this->assertSame('out_of_stock',$result['status']);
        $this->assertSame($before,$p->fresh()->getAttributes());
        $this->assertSame([],$backups);
    }

    public function test_mixed_stock_product_preserves_original_and_commercial_data(): void
    {
        [$p,$v,$entry]=$this->fixture();
        ProductVariant::create(['product_id'=>$p->id,'name'=>'Black','sku'=>'SEO-'.uniqid(),'price'=>456,'discount_price'=>0,'stock'=>'out_of_stock']);
        $variants=$p->variants()->get()->map->getAttributes()->all();$before=$p->getAttributes();$backups=[];
        $this->assertSame('ready',ProductSeoRevision::run($entry)['status']);
        $this->assertSame($before,$p->fresh()->getAttributes());
        $this->assertSame('applied',ProductSeoRevision::run($entry,true,function($row)use(&$backups){$backups[]=$row;})['status']);
        $after=$p->fresh();
        $this->assertSame('Reviewed name',$after->name);
        $this->assertSame($before['description'],$after->description);
        $this->assertSame($before['price'],$after->getRawOriginal('price'));
        $this->assertSame($before['slug'],$after->slug);
        $this->assertSame($variants,$p->variants()->get()->map->getAttributes()->all());
        $this->assertSame($before,$backups[0]['product']);
    }

    public function test_concurrent_editorial_change_and_model_hold_are_preserved(): void
    {
        [$p,$v,$entry]=$this->fixture();
        $p->update(['seo_description'=>'Admin changed this while research was in progress']);
        $this->assertSame('changed_since_review',ProductSeoRevision::run($entry,true,fn()=>null)['status']);
        $entry['hold']=true;
        $this->assertSame('needs_model_confirmation',ProductSeoRevision::run($entry,true,fn()=>null)['status']);
        $this->assertSame('Admin changed this while research was in progress',$p->fresh()->seo_description);
    }

    public function test_commercial_field_in_payload_is_rejected(): void
    {
        [$p,$v,$entry]=$this->fixture();$entry['changes']['price']=1;
        $this->expectException(\InvalidArgumentException::class);
        ProductSeoRevision::run($entry,true,fn()=>null);
    }

    public function test_pending_specification_cannot_return_through_legacy_or_variant_override(): void
    {
        $p=new Product(['description'=>'<h3>Thông số</h3><p>Pin: 9000mAh</p>', 'specification_groups'=>[['title'=>'Specs','rows'=>[
            ['label'=>'Pin','value'=>'9000mAh','verification_status'=>'pending'],
            ['label'=>'Model','value'=>'X1','verification_status'=>'verified'],
        ]]]]);
        $v=new ProductVariant(['specifications'=>['Pin'=>'8000mAh']]);
        $content=ProductContent::forProduct($p,$v);
        $this->assertSame([['label'=>'Model','value'=>'X1','verification_status'=>'verified']],$content['groups'][0]['rows']);
        $this->assertCount(1,$content['groups']);
    }
}
