<?php
namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\CatalogNormalizer;
use App\Support\ProductContent;
use App\Support\ProductSeo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductMuseCatalogTest extends TestCase
{
    use DatabaseTransactions;

    private function product(array $data=[]): Product
    {
        return Product::create(array_merge(['name'=>'Kiểm tra Muse','slug'=>'muse-'.uniqid(),'category_id'=>1,'price'=>100000,'discount_price'=>0,'images'=>['test.jpg'],'is_active'=>true],$data));
    }

    public function test_normalization_retains_embedded_heading_images_and_repairs_short_description(): void
    {
        $html='<h2><strong>Giới thiệu</strong><br>Nội dung dài được đặt sai trong tiêu đề và vẫn cần giữ lại khi sửa định dạng.<img src=\"https://example.com/photo.jpg\"></h2><p>Model | X75<br>Pin | 8000mAh<br>Tính năng giữ lại</p><h3>Đặc điểm cuối bài</h3>';
        $product=$this->product(['description'=>$html,'short_description'=>'<p>123</p>']);
        $data=CatalogNormalizer::prepare($product);
        $output=json_encode($data['content_sections'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $this->assertStringContainsString('photo.jpg',$output);
        $this->assertStringContainsString('Đặc điểm cuối bài',$output);
        $this->assertStringContainsString('Tính năng giữ lại',$output);
        $this->assertStringNotContainsString('<h2>',$output);
        $this->assertGreaterThan(30,mb_strlen($data['seo_description']));
        $this->assertSame('8000mAh',$data['specification_groups'][0]['rows'][1]['value']);
        $this->assertSame($html,$product->fresh()->description);
    }

    public function test_sanitizer_preserves_raster_images_but_removes_script_and_inline_fonts(): void
    {
        $output=ProductContent::sanitize('<h1>Policy</h1><p style="font-size:70px" onclick="bad()">Text<img src="data:image/png;base64,AAAA"><a href="javascript:bad()">link</a><script>bad()</script></p>');
        $this->assertStringContainsString('data:image/png;base64,AAAA',$output);
        foreach(['font-size','onclick','javascript:','<script','<h1'] as $bad) $this->assertStringNotContainsString($bad,$output);
    }

    public function test_schema_uses_actual_variant_offers_without_fabricated_ratings(): void
    {
        $product=$this->product(['seo_title'=>'Tiêu đề riêng','seo_description'=>'Mô tả tìm kiếm riêng']);
        foreach(['in_stock','out_of_stock'] as $index=>$stock) ProductVariant::create(['product_id'=>$product->id,'name'=>'Màu '.$index,'sku'=>'MUSE-'.uniqid(),'price'=>100000+$index*10000,'discount_price'=>0,'stock'=>$stock,'classification'=>['Màu'=>$index ? 'Trắng':'Đen']]);
        $schema=ProductSeo::schema($product,null)['@graph'][0];
        $this->assertSame('ProductGroup',$schema['@type']);
        $this->assertSame(['https://schema.org/color'],$schema['variesBy']);
        $this->assertArrayNotHasKey('aggregateRating',$schema);
        $this->assertSame(110000.0,(float)$schema['hasVariant'][1]['offers']['price']);
        $this->assertSame('https://schema.org/OutOfStock',$schema['hasVariant'][1]['offers']['availability']);
        $variant=$product->variants->last();
        $response=$this->get('/product/'.$product->slug.'?variant='.$variant->id)->assertOk();
        $response->assertSee('Tiêu đề riêng')->assertSee('panel-description',false)->assertSee('panel-specifications',false)->assertSee('panel-warranty',false)->assertSee('panel-reviews',false);
        $response->assertSee('110.000₫')->assertSee('<link rel="canonical" href="'.url('/product/'.$product->slug).'"',false);
    }

    public function test_removing_all_standardized_sections_and_specs_does_not_restore_legacy_content(): void
    {
        $product=$this->product(['description'=>'<p>Nội dung gốc đã bỏ</p><h3>Thông số</h3><p>Pin: 9000mAh</p>','content_sections'=>[],'specification_groups'=>[],'content_standardized_at'=>now()]);
        $this->assertSame([],ProductContent::forProduct($product)['groups']);
        $html=$this->get('/product/'.$product->slug)->assertOk()->getContent();
        $dom=new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $this->assertStringNotContainsString('Nội dung gốc đã bỏ',$dom->getElementById('panel-description')->textContent);
        $this->assertStringNotContainsString('9000mAh',$dom->getElementById('panel-specifications')->textContent);
        $this->assertStringNotContainsString('Nội dung gốc đã bỏ',ProductSeo::metadata($product,null)['description']);
    }

    public function test_product_sitemap_only_contains_active_products(): void
    {
        $active=$this->product();
        $hidden=$this->product(['is_active'=>false]);
        $this->get('/sitemap-products.xml')->assertOk()->assertSee('/product/'.$active->slug)->assertDontSee('/product/'.$hidden->slug);
    }
}
