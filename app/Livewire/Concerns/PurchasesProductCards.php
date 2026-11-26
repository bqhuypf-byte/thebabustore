<?php
namespace App\Livewire\Concerns;
use App\Models\Product;
use App\Support\PurchaseValidation as Purchase;
use Illuminate\Validation\ValidationException;
trait PurchasesProductCards
{
    public function purchaseCard(int $productId,string $token,bool $buyNow=false)
    {
        $product=Product::with('variants')->where('is_active',true)->find($productId);
        if (!$product) { $this->addError('cardPurchase','Sản phẩm không còn khả dụng.'); return; }
        if ($product->variants->count() !== 1) {
            if (!$product->variants->contains(fn($v)=>Purchase::available($v))) { $this->addError('cardPurchase','Hết hàng'); return; }
            if (method_exists($this,'openPreview')) $this->openPreview($productId);
            else $this->dispatch('open-product-quick-view',productId:$productId);
            return;
        }
        $variant=$product->variants->first();
        try { Purchase::add($productId,$variant->id,Purchase::map($product)[$variant->id],1,$token); }
        catch (ValidationException $e) { $this->addError('cardPurchase',collect($e->errors())->flatten()->first()); return; }
        catch (\Throwable $e) { report($e);$this->addError('cardPurchase','Không thể thêm vào giỏ. Vui lòng thử lại.'); return; }
        $this->dispatch('cart-updated');
        if ($buyNow) return redirect('/checkout');
        $this->dispatch('purchase-toast',message:'Đã thêm sản phẩm vào giỏ hàng.');
    }
}
