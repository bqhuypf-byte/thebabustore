<?php

namespace Tests\Feature;

use App\Livewire\ProductDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDetailExperienceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_product_page_has_product_specific_search_metadata(): void
    {
        $product = Product::create([
            'name' => 'Example SEO headphones', 'slug' => 'seo-test-'.uniqid(),
            'category_id' => 1, 'price' => 100000, 'is_active' => 1,
            'description' => '<p>First feature</p><p>Second feature</p>',
            'images' => ['seo-test.jpg'],
        ]);

        $this->get('/product/'.$product->slug)
            ->assertOk()
            ->assertSee('<meta name="description" content="First feature Second feature">', false)
            ->assertSee('<link rel="canonical" href="'.url('/product/'.$product->slug).'">', false)
            ->assertSee('property="og:image"', false);
    }

    public function test_gallery_wraps_and_selected_variant_is_added_to_cart(): void
    {
        $product = Product::create([
            'name' => 'Test product', 'slug' => 'detail-test-'.uniqid(),
            'category_id' => 1, 'price' => 100000, 'images' => ['first.jpg', 'second.jpg'],
        ]);
        $first = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'A-'.uniqid(), 'name' => 'Blue',
            'price' => 100000, 'stock' => 'in_stock', 'image' => 'blue.jpg',
        ]);
        $second = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'B-'.uniqid(), 'name' => 'Silver',
            'price' => 120000, 'stock' => 'in_stock', 'image' => 'silver.jpg',
        ]);

        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->call('updateVariantDetails', $second->id)
            ->assertSet('price', 120000)
            ->assertSet('sku', $second->sku)
            ->assertSet('activeImage', 'silver.jpg')
            ->call('changeImage', 1)
            ->assertSet('activeImage', 'first.jpg')
            ->call('changeImage', -1)
            ->assertSet('activeImage', 'silver.jpg')
            ->call('incrementQuantity')
            ->call('addtocart')
            ->assertDispatched('cart-added');

        $this->assertSame([['product_variant_id' => $second->id, 'quantity' => 2]], session('cart_items'));
    }

    public function test_classification_uses_only_real_available_variants(): void
    {
        $product = Product::create(['name' => 'Grouped product', 'slug' => 'grouped-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        $variants = collect([
            ['Pro', 'Black', 100000, 'in_stock'],
            ['Pro', 'Blue', 110000, 'in_stock'],
            ['Pro X', 'Black', 120000, 'in_stock'],
            ['Pro X', 'Gold', 130000, 'out_of_stock'],
        ])->map(fn ($row) => ProductVariant::create([
            'product_id' => $product->id, 'name' => $row[0].' '.$row[1], 'sku' => 'GROUP-'.uniqid(),
            'price' => $row[2], 'stock' => $row[3], 'classification' => ['Dòng máy' => $row[0], 'Màu sắc' => $row[1]],
        ]));

        $component = Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSee('Chọn phân loại')->assertSee('Dòng máy')->assertSee('Màu sắc')
            ->call('selectClassification', 'Màu sắc', 'Blue')->assertSet('selectedVariant', $variants[1]->id)
            ->call('selectClassification', 'Dòng máy', 'Pro X')->assertSet('selectedVariant', $variants[2]->id)
            ->assertSet('price', 120000)
            ->call('selectClassification', 'Màu sắc', 'Gold')->assertSet('selectedVariant', $variants[2]->id)
            ->call('addtocart')->assertDispatched('cart-added');

        $this->assertSame($variants[2]->id, session('cart_items')[0]['product_variant_id']);
    }

    public function test_one_data_driven_classification_group_is_selectable(): void
    {
        $product = Product::create(['name' => 'One group', 'slug' => 'one-group-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        $first = ProductVariant::create(['product_id' => $product->id, 'name' => 'One', 'sku' => 'ONE-'.uniqid(), 'price' => 100000, 'stock' => 'in_stock', 'classification' => ['Công tắc' => 'Linear']]);
        $second = ProductVariant::create(['product_id' => $product->id, 'name' => 'Two', 'sku' => 'TWO-'.uniqid(), 'price' => 125000, 'stock' => 'in_stock', 'classification' => ['Công tắc' => 'Tactile']]);
        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSee('Công tắc')->assertSet('selectedVariant', $first->id)
            ->call('selectClassification', 'Công tắc', 'Tactile')
            ->assertSet('selectedVariant', $second->id)->assertSet('price', 125000);
    }

    public function test_three_classification_groups_filter_to_existing_combinations(): void
    {
        $product = Product::create(['name' => 'Three groups', 'slug' => 'three-groups-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        $first = ProductVariant::create(['product_id' => $product->id, 'name' => 'A', 'sku' => 'THREE-'.uniqid(), 'price' => 100000, 'stock' => 'in_stock', 'classification' => ['Loại' => 'A', 'Màu' => 'Đen', 'Cáp' => 'Có']]);
        $second = ProductVariant::create(['product_id' => $product->id, 'name' => 'B', 'sku' => 'THREE-'.uniqid(), 'price' => 135000, 'stock' => 'in_stock', 'classification' => ['Loại' => 'B', 'Màu' => 'Trắng', 'Cáp' => 'Không']]);
        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSet('selectedVariant', $first->id)->assertSee('Cáp')
            ->call('selectClassification', 'Loại', 'B')
            ->assertSet('selectedVariant', $second->id)->assertSet('price', 135000)
            ->assertSee('Đã chọn phiên bản còn hàng phù hợp');
    }
    public function test_compact_groups_disable_missing_combinations_and_keep_the_selected_cart_variant(): void
    {
        $product = Product::create([
            'name' => 'Keyboard groups', 'slug' => 'keyboard-groups-'.uniqid(),
            'category_id' => 1, 'price' => 100000,
            'option_group_order' => [['name' => 'Số phím'], ['name' => 'Màu sắc'], ['name' => 'Switch']],
        ]);
        $variants = collect([
            ['68 phím', 'Gray', 'Pulse (Tactile)', 100000, 'in_stock'],
            ['68 phím', 'Silver', 'Surfer (Linear)', 110000, 'in_stock'],
            ['68 phím', 'Silver', 'Void (Silent)', 120000, 'in_stock'],
            ['84 phím', 'Silver', 'Void (Silent)', 140000, 'in_stock'],
            ['100 phím', 'Gray', 'Pulse (Tactile)', 150000, 'out_of_stock'],
        ])->map(fn ($row) => ProductVariant::create([
            'product_id' => $product->id, 'name' => implode(' - ', array_slice($row, 0, 3)),
            'sku' => 'KEY-GROUP-'.uniqid(), 'price' => $row[3], 'stock' => $row[4],
            'classification' => ['Số phím' => $row[0], 'Màu sắc' => $row[1], 'Switch' => $row[2]],
        ]));
        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSee('pd-options-compact', false)->assertSee('pd-options-color', false)
            ->assertSee('Không có tổ hợp này')
            ->call('selectClassification', 'Switch', 'Void (Silent)')
            ->assertSet('selectedVariant', $variants[0]->id)
            ->call('selectClassification', 'Màu sắc', 'Silver')
            ->assertSet('selectedVariant', $variants[1]->id)
            ->call('selectClassification', 'Switch', 'Void (Silent)')
            ->assertSet('selectedVariant', $variants[2]->id)->assertSet('price', 120000)
            ->call('selectClassification', 'Số phím', '84 phím')
            ->assertSet('selectedVariant', $variants[3]->id)->assertSet('price', 140000)
            ->call('selectClassification', 'Số phím', '100 phím')
            ->assertSet('selectedVariant', $variants[3]->id)
            ->call('addtocart')->assertDispatched('cart-added');
        $this->assertSame($variants[3]->id, session('cart_items')[0]['product_variant_id']);
    }

    public function test_unstructured_long_names_use_full_variant_rows(): void
    {
        $name = 'A long existing option name with several words and details';
        $product = Product::create(['name' => 'Long options', 'slug' => 'long-options-'.uniqid(), 'category_id' => 1, 'price' => 100000]);
        ProductVariant::create(['product_id' => $product->id, 'name' => $name, 'sku' => 'LONG-'.uniqid(), 'price' => 100000, 'stock' => 'in_stock']);
        Livewire::test(ProductDetail::class, ['slug' => $product->slug])
            ->assertSee('pd-variant-list', false)->assertSee($name);
    }

    public function test_both_quick_views_share_attribute_selection_and_cart_variant_ids(): void
    {
        $product = Product::create([
            'name' => 'Shared groups', 'slug' => 'shared-groups-'.uniqid(),
            'category_id' => 1, 'price' => 100000, 'is_active' => true,
            'option_group_order' => [['name' => 'Màu sắc'], ['name' => 'Switch']],
        ]);
        $variants = collect([
            ['Gray', 'Pulse (Tactile)', 'in_stock'],
            ['Silver', 'Surfer (Linear)', 'in_stock'],
            ['Silver', 'Void (Silent)', 'in_stock'],
            ['Gray', 'Void (Silent)', 'out_of_stock'],
        ])->map(fn ($row) => ProductVariant::create([
            'product_id' => $product->id, 'name' => $row[0].' '.$row[1],
            'sku' => 'SHARED-'.uniqid(), 'price' => 100000, 'stock' => $row[2],
            'classification' => ['Màu sắc' => $row[0], 'Switch' => $row[1]],
        ]));
        foreach ([
            [\App\Livewire\Home::class, 'openPreview', 'selectPreviewClassification', 'previewVariantId', 'addPreviewToCart'],
            [\App\Livewire\ProductQuickView::class, 'open', 'selectClassification', 'variantId', 'addToCart'],
        ] as [$surface, $open, $select, $property, $add]) {
            session()->forget('cart_items');
            Livewire::test($surface)->call($open, $product->id)
                ->assertSee('store-option-picker', false)->assertSee('Màu sắc')->assertSee('Switch')
                ->call($select, 'Switch', 'Void (Silent)')->assertSet($property, $variants[0]->id)
                ->call($select, 'Màu sắc', 'Silver')->assertSet($property, $variants[1]->id)
                ->call($select, 'Switch', 'Void (Silent)')->assertSet($property, $variants[2]->id)
                ->call($add)->assertDispatched('cart-added');
            $this->assertSame($variants[2]->id, session('cart_items')[0]['product_variant_id']);
        }
    }

}
