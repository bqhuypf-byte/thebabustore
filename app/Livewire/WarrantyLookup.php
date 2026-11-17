<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\WarrantyRequest;
use App\Services\WarrantyService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class WarrantyLookup extends Component
{
    #[Url(as: 'tab', except: 'warranty', history: true)]
    public string $activeTab = 'warranty'; // 'warranty' | 'orders' | 'activate'

    // ================= TAB 1: TRA CỨU BẢO HÀNH =================
    #[Url(as: 'q', except: '', history: true)]
    public string $query = '';

    public string $searchInput = '';

    public string $searchType = 'phone'; // 'phone' | 'code'

    // ================= TAB 2: TRA CỨU ĐƠN HÀNG =================
    public string $orderSearchInput = '';

    public string $orderQuery = '';

    public string $orderStatusFilter = 'all';

    // ================= TAB 3: KÍCH HOẠT BẢO HÀNH =================
    public string $activateCode = '';

    #[Locked]
    public ?int $activateOrderId = null;

    public ?int $activateVariantId = null;

    public string $activateFullName = '';

    public string $activatePhone = '';

    public string $activateAddress = '';

    public string $activateNotes = '';

    public bool $activateSuccess = false;

    public ?string $activateSuccessCode = null;

    public bool $isAlreadyActivated = false;

    #[Locked]
    public string $activationToken = '';

    #[Locked]
    public string $activationLinkError = '';

    #[Locked]
    public string $verifiedPhone = '';

    public array $selectedVariants = [];

    public string $fallbackSearch = '';

    public string $activateSerial = '';

    public bool $fallbackSuccess = false;

    public function mount(?string $code = null): void
    {
        // 0. Auto-detect active tab based on route path
        if (request()->is('orders*') || request()->is('tra-cuu-don-hang*')) {
            $this->activeTab = 'orders';
        } elseif (request()->is('kich-hoat*')) {
            $this->activeTab = 'activate';
        } else {
            $this->activeTab = 'warranty';
        }

        // 1. Initial tab from query param overrides path if explicitly passed
        $reqTab = trim((string) request()->query('tab', ''));
        if (in_array($reqTab, ['warranty', 'orders', 'activate'], true)) {
            $this->activeTab = $reqTab;
        }

        // 2. Query for warranty lookup
        $rawQ = trim((string) request()->query('q', $this->query));
        if ($rawQ !== '') {
            $this->query = $rawQ;
            $this->searchInput = $rawQ;
            $digits = preg_replace('/\D+/', '', $rawQ);
            if (strlen($digits) >= 9 && strlen($digits) === strlen(preg_replace('/[\s.+-]/', '', $rawQ))) {
                $this->searchType = 'phone';
            } else {
                $this->searchType = 'code';
            }
        }

        // 3. Query for order lookup
        $orderQ = trim((string) request()->query('order_code', request()->query('phone', '')));
        if ($orderQ !== '') {
            $this->orderQuery = $orderQ;
            $this->orderSearchInput = $orderQ;
        }

        $this->activationToken = (string) request()->query('token', '');
        // 4. Initial code for activation
        $actCode = trim((string) ($code ?: request()->query('code', request()->query('sku', ''))));
        if ($actCode !== '') {
            $this->activateCode = $actCode;
            if ($this->activationToken !== '') {
                $this->lookupActivationCode($actCode);
            }
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['warranty', 'orders', 'activate'], true)) {
            $this->activeTab = $tab;
            $targetUrl = match ($tab) {
                'orders' => '/orders',
                'activate' => '/kich-hoat',
                default => '/tra-cuu-bao-hanh',
            };
            $this->js("if (window.history && window.history.pushState) { window.history.pushState({}, '', '{$targetUrl}'); }");
        }
    }

    // ---------- TAB 1 METHODS ----------
    public function setType(string $type): void
    {
        if (in_array($type, ['phone', 'code'], true)) {
            $this->searchType = $type;
        }
    }

    public function checkWarranty(): void
    {
        $this->query = trim($this->searchInput);
    }

    public function clearQuery(): void
    {
        $this->query = '';
        $this->searchInput = '';
    }

    public static function getWarrantyStatus(OrderItem $item): array
    {
        $order = $item->order;
        $activation = $order?->warrantyActivations->firstWhere('product_variant_id', $item->product_variant_id);
        if ($activation) {
            $end = $activation->expires_at->endOfDay();
            $active = $end->isFuture();

            return ['state' => $active ? 'active' : 'expired', 'label' => $active ? 'Đang bảo hành' : 'Hết hạn bảo hành', 'badge_class' => $active ? 'status-active' : 'status-expired', 'end_formatted' => $end->format('d/m/Y'), 'days_left' => max(0, (int) now()->diffInDays($end, false)), 'order_code' => $order->order_code];
        }
        if ($order?->order_source === 'offline') {
            return ['state' => 'pending', 'label' => 'Chưa kích hoạt', 'badge_class' => 'status-pending', 'end_formatted' => null, 'days_left' => 0, 'order_code' => $order->order_code];
        }
        $notes = (string) ($order?->notes ?? '');
        $phone = trim((string) ($order?->address?->phone ?? ''));
        $fullName = trim((string) ($order?->address?->full_name ?? ''));

        $hasActivationNote = str_contains($notes, 'Đã kích hoạt');
        $isAllZeroPhone = $phone === '' || preg_match('/^0+$/', $phone);
        $isPlaceholderName = str_contains(mb_strtolower($fullName), 'chờ kích hoạt');
        $isPlaceholderNote = str_contains($notes, 'Tạo link kích hoạt');

        $isRealPhone = ! $isAllZeroPhone && strlen($phone) >= 9;
        $isPending = ($isPlaceholderName || $isPlaceholderNote || $isAllZeroPhone) && ! $hasActivationNote && ! $isRealPhone;

        $end = $item->warranty ? Carbon::parse($item->warranty) : null;
        $isFuture = $end && $end->isFuture();

        if ($isPending) {
            return [
                'state' => 'pending',
                'label' => 'Chưa kích hoạt',
                'badge_class' => 'status-pending',
                'end_formatted' => null,
                'days_left' => 0,
                'order_code' => $order?->order_code,
            ];
        }

        if ($isFuture) {
            $daysLeft = max(0, (int) now()->diffInDays($end, false));

            return [
                'state' => 'active',
                'label' => 'Đang bảo hành',
                'badge_class' => 'status-active',
                'end_formatted' => $end->format('d/m/Y'),
                'days_left' => $daysLeft,
                'order_code' => $order?->order_code,
            ];
        }

        return [
            'state' => 'expired',
            'label' => 'Hết hạn bảo hành',
            'badge_class' => 'status-expired',
            'end_formatted' => $end ? $end->format('d/m/Y') : '—',
            'days_left' => 0,
            'order_code' => $order?->order_code,
        ];
    }

    protected function fetchWarrantyItems(): Collection
    {
        $q = ltrim(trim($this->query), '#');
        if ($q === '') {
            return collect();
        }

        $digits = preg_replace('/\D+/', '', $q);
        $isPhone = strlen($digits) >= 9 && strlen($digits) === strlen(preg_replace('/[\s.+-]/', '', $q));
        $corePhone = $isPhone ? substr($digits, -9) : null;

        if (! $isPhone && mb_strlen($q) < 3) {
            return collect();
        }

        return OrderItem::with(['order.address', 'order.warrantyActivations', 'product_variant.product'])
            ->whereHas('order', fn ($o) => $o->where('status', '!=', 'cancelled'))
            ->where(function ($b) use ($q, $isPhone, $corePhone) {
                if ($isPhone) {
                    $b->whereHas('order.address', fn ($a) => $a->where('phone', 'like', '%'.$corePhone))->orWhereHas('order.warrantyActivations', fn ($a) => $a->where('customer_phone', 'like', '%'.$corePhone));
                } else {
                    $b->whereHas('order', fn ($o) => $o->where('order_code', $q))
                        ->orWhereHas('product_variant', fn ($v) => $v->where('sku', 'like', '%'.$q.'%'));
                }
            })
            ->latest('id')
            ->limit(50)
            ->get();
    }

    // ---------- TAB 2 METHODS (ORDER TRACKING) ----------
    public function checkOrders(): void
    {
        $this->orderQuery = trim($this->orderSearchInput);
    }

    public function clearOrderSearch(): void
    {
        $this->orderQuery = '';
        $this->orderSearchInput = '';
        $this->orderStatusFilter = 'all';
    }

    public function setOrderStatusFilter(string $status): void
    {
        if (in_array($status, ['all', 'pending', 'shipping', 'completed', 'cancelled'], true)) {
            $this->orderStatusFilter = $status;
        }
    }

    public static function normalizeStatusGroup(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'shipped', 'shipping' => 'shipping',
            'delivered', 'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending', // 'new', 'pending', 'processing'
        };
    }

    protected function fetchMatchedOrders(): Collection
    {
        $q = trim($this->orderQuery);
        $digits = preg_replace('/\D+/', '', $q);
        $isPhoneQuery = strlen($digits) >= 9;
        $corePhone = $isPhoneQuery ? substr($digits, -9) : null;
        $cleanCode = preg_replace('/^#/', '', $q);

        if ($q === '') {
            if (! Auth::check()) {
                return collect();
            }

            return Order::with(['address', 'items.product_variant.product'])
                ->where('user_id', Auth::id())
                ->latest('created_at')
                ->get();
        }

        if (! Auth::check() && mb_strlen($cleanCode) < 3 && ! $isPhoneQuery) {
            return collect();
        }

        return Order::with(['address', 'items.product_variant.product'])
            ->where(function ($builder) use ($cleanCode, $q, $isPhoneQuery, $corePhone, $digits) {
                if (mb_strlen($cleanCode) >= 3) {
                    $builder->where('order_code', $cleanCode)
                        ->orWhere('order_code', 'like', '%'.$cleanCode.'%')
                        ->orWhereHas('items.product_variant', function ($vq) use ($cleanCode) {
                            $vq->where('sku', $cleanCode)
                                ->orWhere('sku', 'like', '%'.$cleanCode.'%');
                        });
                }

                if ($isPhoneQuery && $corePhone) {
                    $builder->orWhereHas('address', function ($addr) use ($corePhone, $digits) {
                        $addr->where('phone', 'like', '%'.$corePhone)
                            ->orWhere('phone', $digits)
                            ->orWhere('phone', '0'.$corePhone)
                            ->orWhere('phone', '+84'.$corePhone);
                    });
                }

                if (Auth::check()) {
                    $builder->orWhere(function ($own) use ($q) {
                        $own->where('user_id', Auth::id())
                            ->whereHas('items.product_variant.product', function ($prod) use ($q) {
                                $prod->where('name', 'like', '%'.$q.'%');
                            });
                    });
                }
            })
            ->latest('created_at')
            ->limit(50)
            ->get();
    }

    // ---------- TAB 3: verified order activation and pending fallback ----------
    public function updatedActivateCode(): void
    {
        $this->activateOrderId = null;
        $this->verifiedPhone = '';
        $this->selectedVariants = [];
        $this->activateSuccess = false;
        $this->resetErrorBag('activateLookup');
    }

    public function openOrderActivation(string $code): void
    {
        $this->resetActivation();
        $this->activateCode = ltrim(trim($code), '#');
        $this->setTab('activate');
    }

    public function searchActivationCode(): void
    {
        $this->resetErrorBag();
        $this->activateSuccess = false;
        $this->lookupActivationCode($this->activateCode);
    }

    protected function lookupActivationCode(string $rawInput): void
    {
        $this->activateOrderId = null;
        $this->selectedVariants = [];
        $this->verifiedPhone = '';
        $key = 'warranty-order-lookup:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $this->addError('activateLookup', 'Bạn đã tra cứu nhiều lần. Vui lòng thử lại sau một phút.');

            return;
        }
        RateLimiter::hit($key, 60);
        $order = Order::with(['items.product_variant.product', 'address'])->where('order_code', ltrim(trim($rawInput), '#'))->first();
        if (! $order) {
            if ($this->activationToken !== '') {
                $this->activationLinkError = 'Link kích hoạt không hợp lệ hoặc đã hết hạn. Vui lòng liên hệ cửa hàng để nhận link mới.';
            }
            $this->addError('activateLookup', $this->activationToken !== '' ? 'Link kích hoạt không hợp lệ hoặc đã hết hạn. Vui lòng liên hệ cửa hàng để nhận link mới.' : 'Không tìm thấy đơn hàng hoặc số điện thoại chưa khớp.');

            return;
        }
        try {
            app(WarrantyService::class)->authorizeCustomer($order, $this->activationToken, $this->activatePhone);
        } catch (ValidationException $e) {
            if ($this->activationToken !== '') {
                $this->activationLinkError = $e->errors()['activateLookup'][0] ?? $e->getMessage();
            } foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

return;
        }
        $this->activateOrderId = $order->id;
        $this->activateCode = $order->order_code;
        $this->verifiedPhone = WarrantyService::phone((string) $order->address?->phone);
        if ($this->activationToken !== '') {
            $this->activateFullName = (string) $order->address?->full_name;
            $this->activatePhone = (string) $order->address?->phone;
        }
        $this->selectedVariants = $order->items->pluck('product_variant_id')->filter()->unique()->values()->all();
    }

    public function selectActivationVariant(int $variantId): void
    {
        if (ProductVariant::find($variantId)) {
            $this->activateVariantId = $variantId;
            $this->fallbackSuccess = false;
            $this->resetErrorBag();
        }
    }

    public function submitActivation(): void
    {
        $this->validate(['activateFullName' => 'required|string|min:2|max:120', 'activatePhone' => WarrantyService::phoneRules(), 'activateSerial' => 'nullable|string|max:255', 'selectedVariants' => 'required|array|min:1', 'selectedVariants.*' => 'integer']);
        $order = $this->activateOrderId ? Order::find($this->activateOrderId) : null;
        if (! $order) {
            $this->addError('activateLookup', 'Tra cứu và xác nhận đơn hàng trước khi kích hoạt.');

            return;
        }
        try {
            app(WarrantyService::class)->activate($order, $this->selectedVariants, $this->activateFullName, $this->activatePhone, $this->activateSerial, 'customer', null, $this->activationToken);
            $this->activateSuccess = true;
            $this->activateSuccessCode = $order->order_code;
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function submitFallback(): void
    {
        $this->validate(['activateVariantId' => 'required|integer|exists:product_variants,id', 'activateFullName' => 'required|string|min:2|max:120', 'activatePhone' => WarrantyService::phoneRules(), 'activateSerial' => 'nullable|string|max:255', 'activateNotes' => 'nullable|string|max:500']);
        $key = 'warranty-request:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('fallback', 'Bạn đã gửi nhiều yêu cầu. Vui lòng liên hệ cửa hàng hoặc thử lại sau.');

            return;
        }
        RateLimiter::hit($key, 3600);
        DB::transaction(function () {
            ProductVariant::lockForUpdate()->findOrFail($this->activateVariantId);
            WarrantyRequest::firstOrCreate(['product_variant_id' => $this->activateVariantId, 'customer_phone' => WarrantyService::phone($this->activatePhone), 'status' => 'pending'], ['customer_name' => trim($this->activateFullName), 'serial' => $this->activateSerial, 'notes' => $this->activateNotes]);
        });
        $this->fallbackSuccess = true;
    }

    public function resetActivation(): void
    {
        $this->activateOrderId = null;
        $this->activateVariantId = null;
        $this->activateCode = '';
        $this->activationToken = '';
        $this->activationLinkError = '';
        $this->verifiedPhone = '';
        $this->selectedVariants = [];
        $this->activateSuccess = false;
        $this->fallbackSuccess = false;
        $this->activateFullName = '';
        $this->activatePhone = '';
        $this->activateSerial = '';
        $this->activateNotes = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $settings = Setting::query()->first();

        // 1. Warranty Lookup Data
        $warrantyItems = $this->fetchWarrantyItems();

        // 2. Order Tracking Data
        $allOrders = $this->fetchMatchedOrders();
        $orderCounts = [
            'all' => $allOrders->count(),
            'pending' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'pending')->count(),
            'shipping' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'shipping')->count(),
            'completed' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'completed')->count(),
            'cancelled' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'cancelled')->count(),
        ];
        $filteredOrders = $this->orderStatusFilter === 'all'
            ? $allOrders
            : $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === $this->orderStatusFilter)->values();

        // Only verified orders are rendered, including after a Livewire update.
        $activateOrder = $this->activateOrderId ? Order::with(['items.product_variant.product', 'address', 'warrantyActivations'])->find($this->activateOrderId) : null;
        if ($activateOrder) {
            try {
                app(WarrantyService::class)->authorizeCustomer($activateOrder, $this->activationToken, $this->verifiedPhone);
            } catch (ValidationException $e) {
                if ($this->activationToken !== '') {
                    $this->activationLinkError = $e->errors()['activateLookup'][0] ?? $e->getMessage();
                } $activateOrder = null;
                $this->activateOrderId = null;
                foreach ($e->errors() as $field => $messages) {
                    $this->addError($field, $messages[0]);
                }
            }
        }
        $activateVariant = $this->activateVariantId ? ProductVariant::with('product')->find($this->activateVariantId) : null;
        $term = trim($this->fallbackSearch);
        $matchingVariants = mb_strlen($term) >= 2 ? ProductVariant::with('product')->whereHas('product', fn ($q) => $q->where('name', 'like', '%'.$term.'%'))->limit(12)->get() : collect();

        return view('livewire.warranty-lookup', [
            'settings' => $settings,
            // Tab 1
            'warrantyItems' => $warrantyItems,
            'hasWarrantyQuery' => trim($this->query) !== '',
            // Tab 2
            'orders' => $filteredOrders,
            'orderCounts' => $orderCounts,
            'hasOrderQuery' => trim($this->orderQuery) !== '',
            // Tab 3
            'activateOrder' => $activateOrder,
            'activateVariant' => $activateVariant,
            'matchingVariants' => $matchingVariants,
        ]);
    }
}
