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
    use \App\Livewire\Concerns\ValidatesPurchases, \App\Livewire\Concerns\PurchasesProductCards;
    public $slides;
    public $categories;
    public $latestPosts;
    public $latestProducts; // Biến để lưu sản phẩm mới nhất
    public $previewProductId = null;
    public $previewVariantId = null;
    public $previewQuantity = 1;
    public $previewImageIndex = 0;
    public $cartCount = 0;

    public string $newsletterEmail = '';
    public bool $newsletterSubscribed = false;

    public function subscribeNewsletter(): void
    {
        $this->validate(['newsletterEmail' => 'required|email|max:254']);
        $key = 'newsletter:' . hash('sha256', request()->ip());
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('newsletterEmail', 'Vui lòng thử lại sau ít phút.');
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
        \Illuminate\Support\Facades\DB::table('newsletter_subscriptions')->insertOrIgnore([
            'email' => mb_strtolower(trim($this->newsletterEmail)),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->newsletterSubscribed = true;
        $this->newsletterEmail = '';
    }
    protected function purchaseProduct(): ?Product { return $this->previewProductId ? Product::with('variants')->where('is_active',true)->find($this->previewProductId) : null; }
    protected function purchaseVariantKey(): string { return 'previewVariantId'; }
    protected function purchaseQuantityKey(): string { return 'previewQuantity'; }
    protected function applyPurchaseVariant($variant): void
    {
        if (!$product=$this->purchaseProduct()) return;
        $images=collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        if ($variant?->image && ($index=$images->search($variant->image)) !== false) $this->previewImageIndex=$index;
    }
    protected function closePurchaseModal(): void { $this->closePreview(); }
    public function openPreview(int $productId): void
    {
        $product=Product::with('variants')->where('is_active',true)->findOrFail($productId);
        $this->previewProductId=$product->id;$this->previewQuantity=1;$this->previewImageIndex=0;
        $this->initializePurchase($product);
    }
    public function closePreview(): void { $this->previewProductId=null; }
    public function selectPreviewVariant(int $id): void { $this->choosePurchaseVariant($id); }
    public function selectPreviewClassification(string $group,string $value): void { $this->choosePurchaseOption($group,$value); }
    public function changePreviewQuantity(int $change): void { $this->previewQuantity=(int)$this->previewQuantity+$change;$this->clampPurchaseQuantity(); }
    public function updatedPreviewQuantity(): void { $this->clampPurchaseQuantity(); }
    public function addPreviewToCart(bool $buyNow=false) { return $this->submitPurchase($buyNow); }

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
