<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\ReviewsProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ProductDetail extends Component
{
    use LivewireAlert;
    use \App\Livewire\Concerns\ValidatesPurchases, \App\Livewire\Concerns\PurchasesProductCards;

    public $slug;
    public $quantity = 1;
    public $product;
    public $selectedVariant;
    public $price;
    public $discount_price;
    public $variant_img;
    public $sku;
    public $relatedProducts;
    public $stock_status; // Thêm biến trạng thái tồn kho
    public $name;
    public $rating = 5;
    public $email;
    public $phone;
    public $body;
    public $activeTab = 'description';
    public bool $showReviewForm = false;
    public string $classificationNotice = '';
    public $activeImage = null;
    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadProduct();
        $this->setDefaultVariant();
        if (request()->filled('variant')) $this->updateVariantDetails((int) request('variant'));
        $this->loadRelatedProducts();
    }

    public function loadProduct()
    {
        $this->product = Product::with('variants')
            ->where('slug', $this->slug)
            ->firstOrFail();
    }

    protected function purchaseProduct(): ?Product
    {
        return Product::with('variants')->where('is_active',true)->find($this->product->id);
    }
    protected function purchaseVariantKey(): string { return 'selectedVariant'; }
    protected function purchaseQuantityKey(): string { return 'quantity'; }
    protected function applyPurchaseVariant($variant): void
    {
        $this->price=$variant?->price;
        $this->discount_price=$variant?->discount_price;
        $this->sku=$variant?->sku;
        $this->variant_img=$variant?->image;
        $this->activeImage=$variant?->image ?: collect($this->product->images ?: [])->first();
        $this->stock_status=$variant && \App\Support\PurchaseValidation::available($variant) ? 'in_stock' : ($variant ? 'out_of_stock' : null);
    }
    public function setDefaultVariant() { $this->initializePurchase($this->product); }
    public function updateVariantDetails($variantId) { $this->choosePurchaseVariant((int)$variantId); }

    public function selectImage(int $index): void
    {
        $images = collect($this->product->images ?: [])->merge($this->product->variants->pluck('image'))->filter()->unique()->values();
        if ($images->has($index)) {
            $this->activeImage = $images->get($index);
        }
    }

    public function changeImage(int $direction): void
    {
        $images = collect($this->product->images ?: [])->merge($this->product->variants->pluck('image'))->filter()->unique()->values();
        if ($images->isEmpty()) return;
        $current = $images->search($this->activeImage);
        $index = $current === false ? 0 : $current;
        $this->activeImage = $images->get(($index + ($direction < 0 ? -1 : 1) + $images->count()) % $images->count());
    }

    public function selectClassification(string $group,string $value): void { $this->choosePurchaseOption($group,$value); }

    public function loadRelatedProducts()
    {
        $this->relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();
    }
    public function incrementQuantity() { $this->quantity=(int)$this->quantity+1;$this->clampPurchaseQuantity(); }
    public function decrementQuantity() { $this->quantity=(int)$this->quantity-1;$this->clampPurchaseQuantity(); }
    public function updatedQuantity(): void { $this->clampPurchaseQuantity(); }
    public function addtocart() { return $this->submitPurchase(); }
    public function buyNow() { return $this->submitPurchase(true); }

    public function render()
    {
        $this->product->load('variants');
        $current=$this->product->variants->firstWhere('id',$this->selectedVariant);
        $this->stock_status=$current && \App\Support\PurchaseValidation::available($current) ? 'in_stock' : ($current ? 'out_of_stock' : null);
        $this->price=$current?->price;$this->sku=$current?->sku;

        $approvedReviews = Review::where('product_id', $this->product->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        $reviewCount = $approvedReviews->count();
        $avgRating = $reviewCount > 0 ? round($approvedReviews->avg('rating'), 1) : 0;

        $ratingDistribution = [
            5 => $approvedReviews->where('rating', 5)->count(),
            4 => $approvedReviews->where('rating', 4)->count(),
            3 => $approvedReviews->where('rating', 3)->count(),
            2 => $approvedReviews->where('rating', 2)->count(),
            1 => $approvedReviews->where('rating', 1)->count(),
        ];

        return view('livewire.modern-product-detail', [
            'product' => $this->product,
            'relatedProducts' => $this->relatedProducts,
            'approvedReviews' => $approvedReviews,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating,
            'ratingDistribution' => $ratingDistribution,
            'classificationGroups' => (\App\Support\VariantSelection::map($this->product) ? \App\Support\PurchaseValidation::groups($this->product, $this->purchaseOptions) : []),
            'productContent' => \App\Support\ProductContent::forProduct($this->product, $this->product->variants->firstWhere('id', $this->selectedVariant)),
            'settings' => \App\Models\Setting::query()->first(),
        ]);
    }

    public function sendReview()
    {
        // Chống spam: giới hạn 5 lần gửi mỗi giờ cho mỗi IP
        $throttleKey = 'review-throttle:' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->alert('error', 'Bạn đã gửi đánh giá quá số lần cho phép. Vui lòng thử lại sau!', [
                'position' => 'top-end',
                'timer' => 4000,
                'toast' => true,
            ]);
            return;
        }

        // Validate dữ liệu đánh giá
        $this->validate([
            'name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:5|max:1000',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Vui lòng nhập họ tên của bạn.',
            'rating.required' => 'Vui lòng chọn số sao đánh giá (1-5 sao).',
            'rating.min' => 'Số sao đánh giá tối thiểu là 1.',
            'rating.max' => 'Số sao đánh giá tối đa là 5.',
            'body.required' => 'Vui lòng nhập nội dung đánh giá.',
            'body.min' => 'Nội dung đánh giá tối thiểu 5 ký tự.',
        ]);

        RateLimiter::hit($throttleKey, 3600);

        // Kiểm tra xem người dùng đã mua sản phẩm này chưa (is_verified_purchase)
        $isVerifiedPurchase = false;
        if (auth()->check()) {
            $isVerifiedPurchase = Order::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->whereHas('items.product_variant', function ($q) {
                    $q->where('product_id', $this->product->id);
                })
                ->exists();
        }

        // Lưu đánh giá vào Database ở trạng thái pending
        Review::create([
            'product_id' => $this->product->id,
            'name' => trim($this->name),
            'email' => $this->email ? trim($this->email) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'rating' => (int) $this->rating,
            'comment' => trim($this->body),
            'status' => 'pending',
            'is_verified_purchase' => $isVerifiedPurchase,
        ]);

        // Gửi email thông báo cho Admin (nếu cấu hình mail hợp lệ)
        try {
            $settings = \App\Models\Setting::first();
            $recipientEmail = $settings?->email;
            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new ReviewsProduct([
                    'reviewData' => [
                        'product_name' => $this->product->name,
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'body' => $this->body,
                        'sku' => $this->sku,
                        'rating' => $this->rating,
                    ]
                ]));
            }
        } catch (\Throwable $e) {
            // Không làm gián đoạn trải nghiệm người dùng nếu gửi mail lỗi
        }

        // Reset form đánh giá
        $this->reset(['name', 'rating', 'body', 'email', 'phone']);
        $this->rating = 5;

        $this->alert('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt!', [
            'position' => 'top-end',
            'timer' => 4000,
            'toast' => true,
        ]);
    }
}
