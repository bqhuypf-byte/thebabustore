<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Livewire\ProductDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Support\ProductContent;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class ProductContentManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge(['name' => 'Content test', 'slug' => 'content-'.uniqid(), 'category_id' => 1, 'price' => 100000, 'discount_price' => 0, 'images' => ['test.jpg']], $attributes));
    }

    private function variant(Product $product, array $attributes = []): ProductVariant
    {
        return ProductVariant::create(array_merge(['product_id' => $product->id, 'name' => 'Default', 'sku' => 'CONTENT-'.uniqid(), 'price' => 100000, 'discount_price' => 0, 'stock' => 'in_stock', 'warranty_days' => 0], $attributes));
    }

    public function test_legacy_specification_extraction_preserves_images_and_does_not_change_original(): void
    {
        $html = '<p><span style="font-size:24px">Giới thiệu bàn phím</span></p><p>Mô tả gốc</p><p><strong>Thông Số Kỹ Thuật</strong></p><p>- Layout: 75%<br style="color:red">- Pin: 10.000mAh</p><p><img src="https://example.com/keyboard.jpg" alt="Bàn phím"></p>';
        $product = $this->product(['description' => $html]);
        $content = ProductContent::forProduct($product);
        $this->assertCount(2, $content['groups'][0]['rows']);
        $this->assertSame('Pin', $content['groups'][0]['rows'][1]['label']);
        $this->assertStringNotContainsString('font-size', $content['description']);
        $this->assertStringNotContainsString('10.000mAh', $content['description']);
        $this->assertStringContainsString('keyboard.jpg', $content['description']);
        $this->assertStringContainsString('<h2>', $content['description']);
        $this->assertSame($html, $product->fresh()->description);
        [$unparsed, $rows] = ProductContent::legacy('<h3>Thông số kỹ thuật</h3><p>Nội dung chưa chia trường</p>');
        $this->assertSame([], $rows);
        $this->assertStringContainsString('Nội dung chưa chia trường', $unparsed);
    }

    public function test_model_specific_legacy_specs_keep_their_model_context(): void
    {
        $product = $this->product(['description' => '<p>KZ ZSN PRO</p><p>Mô tả: Bản gốc</p><p>Driver: Hybrid</p><p>Trở kháng: 24Ω</p><p>KZ ZSN PRO X</p><p>Mô tả: Bản nâng cấp</p><p>Driver: Hybrid mới</p><p>Trở kháng: 25Ω</p>']);
        $content = ProductContent::forProduct($product);
        $this->assertSame('KZ ZSN PRO', $content['groups'][0]['title']);
        $this->assertSame('24Ω', $content['groups'][0]['rows'][1]['value']);
        $this->assertSame('KZ ZSN PRO X', $content['groups'][1]['title']);
        $this->assertSame('25Ω', $content['groups'][1]['rows'][1]['value']);
        $this->assertStringContainsString('Bản nâng cấp', $content['description']);
        $this->assertStringNotContainsString('25Ω', $content['description']);
    }

    public function test_structured_content_and_variant_specs_work_across_products(): void
    {
        foreach (['Bàn phím', 'Tai nghe'] as $name) {
            $product = $this->product(['name' => $name, 'content_sections' => [['title' => 'Điểm nổi bật', 'body' => 'Nội dung riêng '.$name, 'visible' => true], ['title' => 'Ẩn', 'body' => 'Không xuất bản', 'visible' => false]], 'specification_groups' => [['title' => 'Kết nối', 'rows' => [['label' => 'Cổng', 'value' => 'USB']]]]]);
            $first = $this->variant($product, ['specifications' => ['Cổng' => 'USB-C']]);
            $second = $this->variant($product, ['specifications' => ['Cổng' => '3.5mm']]);
            Livewire::test(ProductDetail::class, ['slug' => $product->slug])
                ->assertSee('Nội dung riêng '.$name)->assertDontSee('Không xuất bản')
                ->set('activeTab', 'specifications')->assertSee('USB-C')
                ->call('updateVariantDetails', $second->id)->assertSee('3.5mm')->assertDontSee('USB-C');
        }
    }

    public function test_four_groups_use_admin_order_and_cannot_change_earlier_selection(): void
    {
        $product = $this->product(['option_group_order' => [['name' => 'Model'], ['name' => 'Màu'], ['name' => 'Mic'], ['name' => 'Cáp']]]);
        $first = $this->variant($product, ['classification' => ['Cáp' => 'A', 'Mic' => 'Có', 'Màu' => 'Đen', 'Model' => 'Pro']]);
        $second = $this->variant($product, ['classification' => ['Cáp' => 'B', 'Mic' => 'Không', 'Màu' => 'Trắng', 'Model' => 'Max']]);
        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSeeInOrder(['Model', 'Màu', 'Mic', 'Cáp'])
            ->call('selectClassification', 'Màu', 'Trắng')->assertSet('selectedVariant', $first->id)
            ->call('selectClassification', 'Model', 'Max')->assertSet('selectedVariant', $second->id)
            ->assertSee('Đã chọn phiên bản còn hàng phù hợp');
    }

    public function test_cart_rechecks_stock_and_variant_without_photo_uses_product_image(): void
    {
        $product = $this->product();
        $first = $this->variant($product, ['image' => 'variant.jpg']);
        $second = $this->variant($product);
        $component = Livewire::test(ProductDetail::class, ['slug' => $product->slug]);
        $component->call('updateVariantDetails', $second->id)->assertSet('activeImage', 'test.jpg');
        $second->update(['stock' => 'out_of_stock']);
        $component->call('addtocart')->assertHasErrors('selectedVariant');
        $this->assertEmpty(session('cart_items', []));
    }

    public function test_admin_can_save_content_and_customer_cannot_manage_products(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('test.jpg', 'test image placeholder');
        $product = $this->product();
        $variant = $this->variant($product);
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer);
        $this->assertFalse(Gate::allows('update', $product));
        $this->assertFalse(Gate::allows('create', ProductVariant::class));
        Livewire::test(EditProduct::class, ['record' => $product->id])->assertForbidden();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $this->assertTrue(Gate::allows('update', $product));
        $this->assertTrue(Gate::allows('update', $variant));
        Livewire::test(EditProduct::class, ['record' => $product->id])
            ->fillForm(['content_sections' => [['title' => 'Giới thiệu', 'body' => 'Nội dung quản trị', 'visible' => true]], 'specification_groups' => [['title' => 'Thiết kế', 'rows' => [['label' => 'Layout', 'value' => '75%']]]], 'option_group_order' => [['name' => 'Màu']]])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Nội dung quản trị', $product->fresh()->content_sections[0]['body']);
        $this->assertSame('75%', $product->fresh()->specification_groups[0]['rows'][0]['value']);
    }
}
