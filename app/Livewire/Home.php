<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Slide;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Helpers\CartManagement;
use Livewire\Component;
use App\Jobs\CheckPayment;
class Home extends Component
{
    public $slides;
    public $categories;
    public $latestPosts;
    public $latestProducts; // Biến để lưu sản phẩm mới nhất
    public $previewProductId = null;
    public $previewVariantId = null;
    public $previewQuantity = 1;
    public $previewImageIndex = 0;
    public $cartCount = 0;

    public function openPreview(int $productId): void
    {
        $product = Product::with('variants')->where('is_active', true)->findOrFail($productId);
        $this->previewProductId = $product->id;
        $this->previewVariantId = $product->variants->firstWhere('stock', 'in_stock')?->id;
        $this->previewQuantity = 1;
        $this->previewImageIndex = 0;
    }

    public function closePreview(): void
    {
        $this->previewProductId = null;
    }

    public function selectPreviewVariant(int $variantId): void
    {
        $product = Product::with('variants')->where('is_active', true)->findOrFail($this->previewProductId);
        $variant = $product->variants->firstWhere('id', $variantId);
        if (! $variant || $variant->stock !== 'in_stock') {
            return;
        }

        $this->previewVariantId = $variant->id;
        $gallery = collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        if ($variant->image && ($index = $gallery->search($variant->image)) !== false) {
            $this->previewImageIndex = $index;
        }
        $this->resetErrorBag('previewVariantId');
    }

    public function selectPreviewClassification(string $group, string $value): void
    {
        $product = Product::with('variants')->where('is_active', true)->findOrFail($this->previewProductId);
        $variant = \App\Support\VariantSelection::select($product, $this->previewVariantId, $group, $value);
        if ($variant) $this->selectPreviewVariant($variant->id);
    }

    public function changePreviewQuantity(int $change): void
    {
        $this->previewQuantity = max(1, min(99, (int) $this->previewQuantity + $change));
    }

    public function addPreviewToCart(bool $buyNow = false)
    {
        $variant = ProductVariant::where('product_id', $this->previewProductId)
            ->where('stock', 'in_stock')->find($this->previewVariantId);
        if (! $variant) {
            $this->addError('previewVariantId', 'Vui lòng chọn phiên bản còn hàng.');
            return;
        }
        $this->cartCount = CartManagement::addItemToCart($variant->id, max(1, min(99, (int) $this->previewQuantity)));
        $this->dispatch('cart-updated');
        $this->closePreview();
        if ($buyNow) {
            return redirect('/checkout');
        }
        $this->dispatch('cart-added');
    }

    public function mount()
    {
        $this->cartCount = count(CartManagement::getRawCartItemsFromSession());
        $this->slides = Slide::get();
        $this->categories = Category::with('subcategories')->get();

        $this->categories->transform(function ($category) {
            $category->random_products = Product::where('category_id', $category->id)
                ->whereHas('variants', function ($query) {
                    $query->where('stock', '!=', 'out_of_stock');
                })
                ->inRandomOrder()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            return $category;
        });
        $this->latestPosts = Blog::with(['contentCategory', 'contentType'])->latest()->take(8)->get();
        $this->latestProducts = Product::whereHas('variants', function ($query) {
            $query->where('stock', '!=', 'out_of_stock');
        })
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get(); // Lấy 4 sản phẩm mới nhất còn hàng
    }

    public function render()
    {
        return view('livewire.modern-home', [
            'categories' => $this->categories,
            'latestPosts' => $this->latestPosts,
            'latestProducts' => $this->latestProducts,
            'previewProduct' => $this->previewProductId
                ? Product::with(['category', 'variants'])->where('is_active', true)->find($this->previewProductId)
                : null,
        ]);
    }
}
