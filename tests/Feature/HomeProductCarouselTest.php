<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomeProductCarouselTest extends TestCase
{
    use DatabaseTransactions;

    public function test_catalog_tabs_and_cards_use_only_active_products_with_in_stock_variants(): void
    {
        $category = Category::where('is_active', true)->firstOrFail();
        foreach ([['Carousel available fixture', true, 'in_stock'], ['Carousel soldout fixture', true, 'out_of_stock'], ['Carousel inactive fixture', false, 'in_stock']] as [$name, $active, $stock]) {
            $product = Product::create(['name' => $name, 'slug' => 'carousel-'.uniqid(), 'category_id' => $category->id, 'price' => 120000, 'discount_price' => 0, 'images' => [], 'is_active' => $active]);
            ProductVariant::create(['product_id' => $product->id, 'name' => 'Carousel variant', 'sku' => 'CAROUSEL-'.uniqid(), 'price' => 120000, 'discount_price' => 0, 'stock' => $stock, 'classification' => []]);
        }
        $html = view('livewire.partials.home-product-carousel')->render();
        $this->assertStringContainsString('Carousel available fixture', $html);
        $this->assertStringNotContainsString('Carousel soldout fixture', $html);
        $this->assertStringNotContainsString('Carousel inactive fixture', $html);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($dom);
        $tabs = $xpath->query('//*[@role="tab"]');
        $this->assertGreaterThan(1, $tabs->length);
        foreach ($tabs as $tab) {
            $this->assertSame(1, $xpath->query('//*[@id="'.$tab->getAttribute('aria-controls').'"]')->length);
        }
        $this->assertSame($tabs->length, $xpath->query('//button[@aria-label="Sản phẩm tiếp theo"]')->length);
    }
}