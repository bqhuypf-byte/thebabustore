<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductQuickView extends Component
{
    public $productId = null;
    public $variantId = null;
    public $quantity = 1;
    public $imageIndex = 0;

    #[On('open-product-quick-view')]
    public function open(int $productId): void
    {
        $product = Product::with('variants')->where('is_active', true)->findOrFail($productId);
        $this->productId = $product->id;
        $this->variantId = $product->variants->firstWhere('stock', 'in_stock')?->id;
        $this->quantity = 1;
        $this->imageIndex = 0;
        $this->resetErrorBag();
    }

    public function selectVariant(int $variantId): void
    {
        $product = Product::with('variants')->where('is_active', true)->findOrFail($this->productId);
        $variant = $product->variants->firstWhere('id', $variantId);
        if (! $variant || $variant->stock !== 'in_stock') return;
        $this->variantId = $variant->id;
        $images = collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        if ($variant->image && ($index = $images->search($variant->image)) !== false) $this->imageIndex = $index;
    }

    public function changeQuantity(int $change): void
    {
        $this->quantity = max(1, min(99, (int) $this->quantity + $change));
    }

    public function addToCart(bool $buyNow = false)
    {
        $variant = ProductVariant::where('product_id', $this->productId)->where('stock', 'in_stock')->find($this->variantId);
        if (! $variant) { $this->addError('variantId', 'Vui lòng chọn phiên bản còn hàng.'); return; }
        CartManagement::addItemToCart($variant->id, max(1, min(99, (int) $this->quantity)));
        $this->dispatch('cart-updated');
        $this->productId = null;
        if ($buyNow) return redirect('/checkout');
        $this->dispatch('cart-added');
    }

    public function render()
    {
        return view('livewire.product-quick-view', [
            'quickProduct' => $this->productId ? Product::with(['category', 'variants'])->where('is_active', true)->find($this->productId) : null,
        ]);
    }
}
