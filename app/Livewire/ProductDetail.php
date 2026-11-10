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

    public function setDefaultVariant()
    {
        if ($this->product->variants->isNotEmpty()) {
            $variant = $this->product->variants->firstWhere('stock', 'in_stock') ?? $this->product->variants->first();
            $this->updateVariantDetails($variant->id);
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
            $this->activeImage = $variant->image ?: collect($this->product->images ?: [])->first();
            $this->stock_status = $variant->stock; // Cập nhật trạng thái tồn kho
        }
    }

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

    private function classificationMap(): array
    {
        $variants = $this->product->variants;
        if ($variants->count() < 2) return [];
        $map = [];
        foreach ($variants as $variant) {
            $attributes = collect($variant->classification ?: [])->filter(fn ($value, $key) => is_string($key) && trim($key) !== '' && is_string($value) && trim($value) !== '')->mapWithKeys(fn ($value, $key) => [trim($key) => trim($value)])->all();
            if (!$attributes) {
                // Derive a model and colour only when every existing variant follows this unambiguous pattern.
                if (!preg_match('/^(.+?)\s+(gray|grey|purple|blue|black|golden|gold|silver|white|green|pink|orange|red)$/iu', trim($variant->name), $match)) return [];
                $attributes = ['Dòng máy' => trim($match[1]), 'Màu sắc' => ucfirst(mb_strtolower($match[2]))];
            }
            $map[$variant->id] = $attributes;
        }
        $keys = array_keys(reset($map));
        // MySQL may reorder JSON object keys. Prefer familiar selection order unless admin overrides it.
        $priority = ['dong may' => 0, 'model' => 0, 'phien ban' => 0, 'loai' => 0, 'mau sac' => 1, 'mau' => 1, 'color' => 1, 'switch' => 2, 'cong tac' => 2, 'mic' => 3, 'cap' => 4];
        usort($keys, fn ($a, $b) => ($priority[\Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($a))] ?? 5) <=> ($priority[\Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($b))] ?? 5));
        $configured = array_column($this->product->option_group_order ?? [], 'name');
        $keys = array_values(array_unique(array_merge(array_intersect($configured, $keys), $keys)));
        if (count($keys) < 1) return [];
        $combinations = [];
        foreach ($map as $attrs) {
            if (count($attrs) !== count($keys) || array_diff($keys, array_keys($attrs))) return [];
            $combination = json_encode(array_map(fn ($key) => $attrs[$key], $keys), JSON_UNESCAPED_UNICODE);
            if (isset($combinations[$combination])) return [];
            $combinations[$combination] = true;
        }
        foreach ($map as $id => $attrs) $map[$id] = array_replace(array_fill_keys($keys, ''), $attrs);
        return $map;
    }

    public function selectClassification(string $group, string $value): void
    {
        $map = $this->classificationMap();
        if (!$map || !in_array($group, array_keys(reset($map)), true)) return;
        $current = $map[$this->selectedVariant] ?? [];
        $keys = array_keys(reset($map));
        $prior = array_slice($keys, 0, array_search($group, $keys, true));
        $candidates = $this->product->variants->filter(function ($variant) use ($map, $group, $value, $prior, $current) {
            if ($variant->stock !== 'in_stock' || ($map[$variant->id][$group] ?? null) !== $value) return false;
            foreach ($prior as $key) if (($map[$variant->id][$key] ?? null) !== ($current[$key] ?? null)) return false;
            return true;
        });
        $best = $candidates->sortByDesc(function ($variant) use ($current, $map, $group) {
            $score = 0;
            foreach ($current as $key => $selected) if ($key !== $group && ($map[$variant->id][$key] ?? null) === $selected) $score++;
            return $score;
        })->first();
        if ($best) {
            $changed = [];
            foreach ($current as $key => $selected) {
                if ($key !== $group && ($map[$best->id][$key] ?? null) !== $selected) $changed[] = $key.': '.$map[$best->id][$key];
            }
            $this->classificationNotice = $changed ? 'Đã chọn phiên bản còn hàng phù hợp: '.implode(', ', $changed).'.' : '';
            $this->updateVariantDetails($best->id);
        }
    }

    private function classificationGroups(): array
    {
        $map = $this->classificationMap();
        if (!$map) return [];
        $fallbackImage = collect($this->product->images ?: [])->filter()->first();
        $current = $map[$this->selectedVariant] ?? reset($map);
        $groups = [];
        $prior = [];
        foreach (array_keys(reset($map)) as $key) {
            $eligible = $this->product->variants->filter(function ($variant) use ($map, $prior, $current) {
                foreach ($prior as $previous) if (($map[$variant->id][$previous] ?? null) !== ($current[$previous] ?? null)) return false;
                return true;
            });
            $values = [];
            foreach ($eligible as $variant) {
                $value = $map[$variant->id][$key];
                if (!isset($values[$value])) $values[$value] = ['label' => $value, 'image' => $variant->image ?: $fallbackImage, 'price' => $variant->price, 'available' => false];
                if ($variant->stock === 'in_stock') {
                    if (!$values[$value]['available'] || $variant->price < $values[$value]['price']) $values[$value]['price'] = $variant->price;
                    $values[$value]['available'] = true;
                    $values[$value]['image'] = $variant->image ?: $values[$value]['image'] ?: $fallbackImage;
                }
            }
            $groups[] = ['name' => $key, 'selected' => $current[$key] ?? '', 'options' => array_values($values)];
            $prior[] = $key;
        }
        return $groups;
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
        $this->quantity = min(99, (int) $this->quantity + 1);
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    public function addtocart()
    {
        if (! $this->product->variants()->where('id', $this->selectedVariant)->where('stock', 'in_stock')->exists()) {
            $this->addError('selectedVariant', 'Vui lòng chọn phiên bản còn hàng.');
            return;
        }
        $cartItemCount = CartManagement::addItemToCart($this->selectedVariant, max(1, min(99, (int) $this->quantity)));
        $this->dispatch('cart-updated');
        $this->dispatch('cart-added');

        $this->quantity = 1;
    }
    public function buyNow()
    {
        if (! $this->product->variants()->where('id', $this->selectedVariant)->where('stock', 'in_stock')->exists()) {
            $this->addError('selectedVariant', 'Vui lòng chọn phiên bản còn hàng.');
            return;
        }
        $cartItemCount = CartManagement::addItemToCart($this->selectedVariant, max(1, min(99, (int) $this->quantity)));
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

        return view('livewire.modern-product-detail', [
            'product' => $this->product,
            'relatedProducts' => $this->relatedProducts,
            'approvedReviews' => $approvedReviews,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating,
            'ratingDistribution' => $ratingDistribution,
            'classificationGroups' => $this->classificationGroups(),
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
