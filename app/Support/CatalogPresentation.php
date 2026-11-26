<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;

class CatalogPresentation
{
    public static function collection(?Category $category, ?Subcategory $subcategory, $products): array
    {
        $name = trim($subcategory?->name ?: $category?->name ?: 'Gaming gear cho góc máy của bạn');
        $candidates = [$subcategory?->banner, $category?->banner, $category?->image];
        $image = collect($candidates)->first(fn ($path) => filled($path) && Storage::disk('public')->exists($path));
        $productImage = false;
        if (!$image && ($category || $subcategory)) {
            $image = $products->flatMap(fn ($product) => $product->images ?: [])->first(fn ($path) => filled($path) && Storage::disk('public')->exists($path));
            $productImage = filled($image);
        }
        if (!$image && !$category && !$subcategory && Storage::disk('public')->exists('homepage/2026-11/setup.webp')) {
            $image = 'homepage/2026-11/setup.webp';
        }
        return ['name' => $name, 'image' => $image, 'product_image' => $productImage, 'selected' => (bool) ($category || $subcategory)];
    }
}
