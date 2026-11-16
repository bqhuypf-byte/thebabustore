<?php

namespace Tests\Feature;

use App\Livewire\Shop;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShopDisplayPriceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sorting_and_ranges_match_the_available_variant_shown_on_cards(): void
    {
        $prefix = 'display-price-'.uniqid();
        $products = [];
        foreach ([[1000, 2100000], [9000000, 120000]] as $index => [$basePrice, $variantPrice]) {
            $product = Product::create([
                'name' => $prefix.' '.$index, 'slug' => $prefix.'-'.$index,
                'category_id' => 1, 'price' => $basePrice, 'discount_price' => 0,
                'images' => [], 'is_active' => true,
            ]);
            foreach ([['out_of_stock', 500], ['in_stock', $variantPrice]] as [$stock, $price]) {
                ProductVariant::create([
                    'product_id' => $product->id, 'name' => $stock,
                    'sku' => uniqid('display-'), 'price' => $price,
                    'discount_price' => 0, 'stock' => $stock,
                ]);
            }
            $products[] = $product;
        }

        $shop = new Shop;
        $shop->mount();
        $shop->search = $prefix;
        $shop->sort = 'price-asc';
        $this->assertSame([$products[1]->id, $products[0]->id], $shop->render()->getData()['products']->pluck('id')->all());
        $shop->sort = 'price-desc';
        $this->assertSame([$products[0]->id, $products[1]->id], $shop->render()->getData()['products']->pluck('id')->all());
        $shop->priceRange = 'under_500';
        $this->assertSame([$products[1]->id], $shop->render()->getData()['products']->pluck('id')->all());
        $shop->priceRange = 'over_2000';
        $this->assertSame([$products[0]->id], $shop->render()->getData()['products']->pluck('id')->all());
    }
}
