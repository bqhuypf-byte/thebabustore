<?php
namespace App\Livewire;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Livewire\Concerns\ValidatesPurchases;

class ProductQuickView extends Component
{
    use ValidatesPurchases;
    public $productId=null;
    public $variantId=null;
    public $quantity=1;
    public $imageIndex=0;
    #[On('open-product-quick-view')]
    public function open(int $productId): void
    {
        $product=Product::with('variants')->where('is_active',true)->findOrFail($productId);
        $this->productId=$product->id;$this->quantity=1;$this->imageIndex=0;$this->resetErrorBag();
        $this->initializePurchase($product);
    }
    protected function purchaseProduct(): ?Product { return $this->productId ? Product::with('variants')->where('is_active',true)->find($this->productId) : null; }
    protected function purchaseVariantKey(): string { return 'variantId'; }
    protected function purchaseQuantityKey(): string { return 'quantity'; }
    protected function applyPurchaseVariant($variant): void
    {
        $product=$this->purchaseProduct();if (!$product) return;
        $images=collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        if ($variant?->image && ($index=$images->search($variant->image)) !== false) $this->imageIndex=$index;
    }
    protected function closePurchaseModal(): void { $this->productId=null; }
    public function selectVariant(int $id): void { $this->choosePurchaseVariant($id); }
    public function selectClassification(string $group,string $value): void { $this->choosePurchaseOption($group,$value); }
    public function changeQuantity(int $change): void { $this->quantity=(int)$this->quantity+$change;$this->clampPurchaseQuantity(); }
    public function updatedQuantity(): void { $this->clampPurchaseQuantity(); }
    public function addToCart(bool $buyNow=false) { return $this->submitPurchase($buyNow); }
    public function render() { return view('livewire.product-quick-view',['quickProduct'=>$this->purchaseProduct()]); }
}
