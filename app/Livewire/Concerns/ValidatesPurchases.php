<?php
namespace App\Livewire\Concerns;
use App\Models\Product;
use App\Support\PurchaseValidation as Purchase;
use Livewire\Attributes\Locked;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ValidatesPurchases
{
    public array $purchaseOptions=[];
    #[Locked] public string $purchaseToken='';
    abstract protected function purchaseProduct(): ?Product;
    abstract protected function purchaseVariantKey(): string;
    abstract protected function purchaseQuantityKey(): string;
    abstract protected function applyPurchaseVariant($variant): void;

    protected function initializePurchase(Product $product): void
    {
        $this->purchaseToken=(string)Str::uuid(); $this->purchaseOptions=[];
        $only=$product->variants->count() === 1 ? $product->variants->first() : null;
        if ($only && Purchase::available($only)) $this->purchaseOptions=Purchase::map($product)[$only->id];
        $this->syncPurchase($product);
        $this->resetErrorBag('purchase');
    }
    protected function syncPurchase(Product $product): void
    {
        $variant=Purchase::resolve($product,$this->purchaseOptions);
        $key=$this->purchaseVariantKey(); $this->$key=$variant?->id;
        $quantity=$this->purchaseQuantityKey(); $this->$quantity=Purchase::clamp($this->$quantity,$variant);
        $this->applyPurchaseVariant($variant);
    }
    protected function choosePurchaseVariant(int $id): void
    {
        $product=$this->purchaseProduct(); $variant=$product?->variants->firstWhere('id',$id);
        if (!$variant || !Purchase::available($variant)) return;
        $this->purchaseOptions=Purchase::map($product)[$id]; $this->syncPurchase($product); $this->resetErrorBag('purchase');
    }
    protected function choosePurchaseOption(string $name,string $value): void
    {
        if (!$product=$this->purchaseProduct()) return;
        $this->purchaseOptions=Purchase::choose($product,$this->purchaseOptions,$name,$value);
        $this->syncPurchase($product); $this->resetErrorBag('purchase');
    }
    protected function clampPurchaseQuantity(): void
    {
        if (!$product=$this->purchaseProduct()) return;
        $quantity=$this->purchaseQuantityKey();
        $this->$quantity=Purchase::clamp($this->$quantity,Purchase::resolve($product,$this->purchaseOptions));
    }
    public function changePurchaseQuantity(int $change): void
    {
        $key=$this->purchaseQuantityKey();$this->$key=(int)$this->$key+$change;$this->clampPurchaseQuantity();
    }
    protected function submitPurchase(bool $buyNow=false)
    {
        $product=$this->purchaseProduct();
        if (!$product) { $this->addError('purchase','Sản phẩm không còn khả dụng.'); return; }
        $key=$this->purchaseVariantKey();$quantity=$this->purchaseQuantityKey();
        try {
            if (!$this->purchaseToken) $this->purchaseToken=(string)Str::uuid();
            Purchase::add($product->id,$this->$key,$this->purchaseOptions,$this->$quantity,$this->purchaseToken);
        } catch (ValidationException $e) { $this->addError('purchase',collect($e->errors())->flatten()->first()); return; }
        catch (\Throwable $e) { report($e); $this->addError('purchase','Không thể thêm vào giỏ. Vui lòng thử lại.'); return; }
        $this->dispatch('cart-updated'); $this->dispatch('purchase-toast',message:'Đã thêm sản phẩm vào giỏ hàng.');
        if (method_exists($this,'closePurchaseModal')) $this->closePurchaseModal();
        $this->$quantity=1;
        $this->purchaseToken=(string)Str::uuid();
        if ($buyNow) return redirect('/checkout');
        $this->dispatch('cart-added');
    }
}
