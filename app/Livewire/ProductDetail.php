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
    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadProduct();
        $this->setDefaultVariant();
        $this->loadRelatedProducts();
    }

    public function loadProduct()
    {
        $this->product = Product::with('variants')
            ->where('slug', $this->slug)
            ->firstOrFail();
    }

    public function setDefaultVariant()
    {
        if ($this->product->variants->isNotEmpty()) {
            $this->selectedVariant = $this->product->variants->first()->id;
            $this->updateVariantDetails($this->product->variants->first()->id);
        }
    }

    public function updateVariantDetails($variantId)
    {
        $variant = $this->product->variants->find($variantId);

        if ($variant) {
            $this->selectedVariant = $variantId;
            $this->price = $variant->price;
            $this->discount_price = $variant->discount_price;
            $this->sku = $variant->sku;
            $this->variant_img = $variant->image;
            $this->stock_status = $variant->stock; // Cập nhật trạng thái tồn kho
        }
    }

    public function loadRelatedProducts()
    {
        $this->relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();
    }
    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    public function addtocart()
    {
        $cartItemCount = CartManagement::addItemToCart($this->selectedVariant, $this->quantity);

        $this->alert('success', 'Sản phẩm đã được thêm vào giỏ hàng!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->quantity = 1;
    }
    public function buyNow()
    {
        $cartItemCount = CartManagement::addItemToCart($this->selectedVariant, $this->quantity);
        $this->quantity = 1;
        return redirect('/checkout');
    }

    public function render()
    {
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

        return view('livewire.product-detail', [
            'product' => $this->product,
            'relatedProducts' => $this->relatedProducts,
            'approvedReviews' => $approvedReviews,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating,
            'ratingDistribution' => $ratingDistribution,
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
