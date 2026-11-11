<?php

namespace Tests\Feature;

use App\Livewire\Inc\Header;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class HeaderMegaMenuTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mega_menu_shows_only_active_in_stock_products_and_compact_lookup(): void
    {
        $category = Category::where('is_active', true)->where('show_in_header', true)->firstOrFail();
        foreach ([['Stocked menu fixture', true, 'in_stock'], ['Soldout menu fixture', true, 'out_of_stock'], ['Inactive menu fixture', false, 'in_stock']] as [$name, $active, $stock]) {
            $product = Product::create(['name' => $name, 'slug' => 'mega-'.uniqid(), 'category_id' => $category->id, 'price' => 120000, 'discount_price' => 0, 'images' => [], 'is_active' => $active]);
            ProductVariant::create(['product_id' => $product->id, 'name' => 'Menu variant', 'sku' => 'MEGA-'.uniqid(), 'price' => 120000, 'discount_price' => 0, 'stock' => $stock, 'classification' => []]);
        }

        $html = Livewire::test(Header::class)->assertSee('Stocked menu fixture')->assertDontSee('Soldout menu fixture')->assertDontSee('Inactive menu fixture')->html();
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($dom);
        $this->assertSame(0, $xpath->query('//nav[contains(@class,"store-nav")]//a[contains(@href,"sort=discount")]')->length);
        $this->assertSame(1, $xpath->query('//a[contains(@class,"store-account")]/following-sibling::*[1][contains(@class,"store-order-lookup")]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="store-product-menu"]')->length);
    }
}