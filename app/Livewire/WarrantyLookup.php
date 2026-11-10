<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public ?int $activateOrderId = null;
    public ?int $activateVariantId = null;
    public string $activateFullName = '';
    public string $activatePhone = '';
    public string $activateAddress = '';
    public string $activateNotes = '';
    public bool $activateSuccess = false;
    public ?string $activateSuccessCode = null;
    public bool $isAlreadyActivated = false;

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

        // 4. Initial code for activation
        $actCode = trim((string) ($code ?: request()->query('code', request()->query('sku', ''))));
        if ($actCode !== '') {
            $this->activateCode = $actCode;
            $this->lookupActivationCode($actCode);
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
        $notes = (string) ($order?->notes ?? '');
        $phone = trim((string) ($order?->address?->phone ?? ''));
        $fullName = trim((string) ($order?->address?->full_name ?? ''));

        $hasActivationNote = str_contains($notes, 'Đã kích hoạt');
        $isAllZeroPhone = $phone === '' || preg_match('/^0+$/', $phone);
        $isPlaceholderName = str_contains(mb_strtolower($fullName), 'chờ kích hoạt');
        $isPlaceholderNote = str_contains($notes, 'Tạo link kích hoạt');

        $isRealPhone = !$isAllZeroPhone && strlen($phone) >= 9;
        $isPending = ($isPlaceholderName || $isPlaceholderNote || $isAllZeroPhone) && !$hasActivationNote && !$isRealPhone;

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

        if (!$isPhone && mb_strlen($q) < 3) {
            return collect();
        }

        return OrderItem::with(['order.address', 'product_variant.product'])
            ->whereHas('order', fn ($o) => $o->where('status', '!=', 'cancelled'))
            ->where(function ($b) use ($q, $isPhone, $corePhone) {
                if ($isPhone) {
                    $b->whereHas('order.address', fn ($a) => $a->where('phone', 'like', '%' . $corePhone));
                } else {
                    $b->whereHas('order', fn ($o) => $o->where('order_code', $q))
                        ->orWhereHas('product_variant', fn ($v) => $v->where('sku', 'like', '%' . $q . '%'));
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
                        ->orWhere('order_code', 'like', '%' . $cleanCode . '%')
                        ->orWhereHas('items.product_variant', function ($vq) use ($cleanCode) {
                            $vq->where('sku', $cleanCode)
                                ->orWhere('sku', 'like', '%' . $cleanCode . '%');
                        });
                }

                if ($isPhoneQuery && $corePhone) {
                    $builder->orWhereHas('address', function ($addr) use ($corePhone, $digits) {
                        $addr->where('phone', 'like', '%' . $corePhone)
                            ->orWhere('phone', $digits)
                            ->orWhere('phone', '0' . $corePhone)
                            ->orWhere('phone', '+84' . $corePhone);
                    });
                }

                if (Auth::check()) {
                    $builder->orWhere(function ($own) use ($q) {
                        $own->where('user_id', Auth::id())
                            ->whereHas('items.product_variant.product', function ($prod) use ($q) {
                                $prod->where('name', 'like', '%' . $q . '%');
                            });
                    });
                }
            })
            ->latest('created_at')
            ->limit(50)
            ->get();
    }

    // ---------- TAB 3 METHODS (WARRANTY ACTIVATION) ----------
    public function updatedActivateCode(string $value): void
    {
        $this->resetErrorBag('activateLookup');
        $clean = ltrim(trim($value), '#');
        if ($clean === '') {
            $this->activateOrderId = null;
            $this->activateVariantId = null;
            $this->isAlreadyActivated = false;
            return;
        }

        if ($this->activateOrderId) {
            $currentOrder = Order::find($this->activateOrderId);
            if (!$currentOrder || strcasecmp((string) $currentOrder->order_code, $clean) !== 0) {
                $this->activateOrderId = null;
                $this->isAlreadyActivated = false;
            }
        }
    }

    public function clearActivationSearch(): void
    {
        $this->activateCode = '';
        $this->activateOrderId = null;
        $this->activateVariantId = null;
        $this->isAlreadyActivated = false;
        $this->resetErrorBag('activateLookup');
    }

    public function clearSelectedActivationVariant(): void
    {
        $this->activateVariantId = null;
        $this->resetErrorBag('activateLookup');
    }

    public function changeOrderItemVariant(int $orderItemId, int $newVariantId): void
    {
        if (!$this->activateOrderId || $this->isAlreadyActivated) {
            return;
        }

        $order = Order::with('items')->find($this->activateOrderId);
        if (!$order || $this->isOrderAlreadyActivated($order)) {
            return;
        }

        $item = $order->items->firstWhere('id', $orderItemId);
        $newVariant = ProductVariant::with('product')->find($newVariantId);
        if ($item && $newVariant) {
            $unitPrice = (float) ($newVariant->price ?: ($newVariant->discount_price ?? $item->unit_amount));
            $qty = max(1, (int) $item->quantity);
            $item->update([
                'product_variant_id' => $newVariant->id,
                'unit_amount' => $unitPrice,
                'total_amount' => $unitPrice * $qty,
            ]);
            $order->update([
                'grand_total' => $order->items()->sum('total_amount'),
            ]);
        }
    }

    public function searchActivationCode(): void
    {
        $this->resetErrorBag();
        $this->activateSuccess = false;
        $input = trim($this->activateCode);
        if ($input === '') {
            $this->addError('activateLookup', 'Vui lòng nhập mã đơn hàng hoặc mã SKU.');
            return;
        }

        $this->lookupActivationCode($input);
    }

    protected function lookupActivationCode(string $rawInput): void
    {
        $clean = ltrim(trim($rawInput), '#');
        if ($clean === '') {
            return;
        }

        $order = Order::with(['items.product_variant.product.variants', 'address'])
            ->where('order_code', $clean)
            ->first();

        if ($order) {
            $this->bindActivationOrder($order);
            return;
        }

        $matchedVariants = ProductVariant::with(['product.variants'])
            ->where(function ($q) use ($clean) {
                $q->where('sku', 'like', '%' . $clean . '%')
                    ->orWhere('name', 'like', '%' . $clean . '%')
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', '%' . $clean . '%'));
            })
            ->orderByRaw('CASE WHEN sku = ? THEN 0 WHEN sku LIKE ? THEN 1 ELSE 2 END', [$clean, $clean . '%'])
            ->limit(20)
            ->get();

        if ($matchedVariants->isNotEmpty()) {
            $first = $matchedVariants->first();
            $this->activateOrderId = null;
            $this->activateVariantId = $first->id;
            $this->isAlreadyActivated = false;
            return;
        }

        $this->addError('activateLookup', 'Không tìm thấy sản phẩm hoặc đơn hàng với mã "' . $clean . '".');
    }

    public function selectActivationVariant(int $variantId): void
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if ($variant) {
            $this->activateVariantId = $variant->id;
            $this->activateOrderId = null;
            $this->isAlreadyActivated = false;
            $this->resetErrorBag('activateLookup');
        }
    }

    protected function bindActivationOrder(Order $order): void
    {
        $this->activateOrderId = $order->id;
        $this->activateVariantId = null;
        $this->activateCode = (string) $order->order_code;
        $this->isAlreadyActivated = $this->isOrderAlreadyActivated($order);

        if ($order->address) {
            $name = trim((string) $order->address->full_name);
            if ($name !== '' && !str_contains(mb_strtolower($name), 'chờ kích hoạt')) {
                $this->activateFullName = $name;
            }
            $phone = trim((string) $order->address->phone);
            if ($phone !== '' && !preg_match('/^0+$/', $phone)) {
                $this->activatePhone = $phone;
            } else {
                $this->activatePhone = '';
            }
            $addr = trim((string) $order->address->detailed_address);
            if ($addr !== '' && !str_contains(mb_strtolower($addr), 'chờ khách điền')) {
                $this->activateAddress = $addr;
            }
        }
    }

    public function isOrderAlreadyActivated(?Order $order): bool
    {
        if (!$order) {
            return false;
        }

        $notes = (string) ($order->notes ?? '');
        $phone = trim((string) ($order->address?->phone ?? ''));
        $fullName = trim((string) ($order->address?->full_name ?? ''));

        $isAllZeroPhone = $phone === '' || preg_match('/^0+$/', $phone);
        $isPlaceholderName = str_contains(mb_strtolower($fullName), 'chờ kích hoạt');
        $isPlaceholderNote = str_contains($notes, 'Tạo link kích hoạt');

        if (($isAllZeroPhone || $isPlaceholderName || $isPlaceholderNote) && !str_contains($notes, 'Đã kích hoạt')) {
            return false;
        }

        if (str_contains($notes, 'Đã kích hoạt')) {
            return true;
        }

        $hasFutureWarranty = false;
        foreach ($order->items as $item) {
            if ($item->warranty && Carbon::parse($item->warranty)->isFuture()) {
                $hasFutureWarranty = true;
                break;
            }
        }

        return $hasFutureWarranty && !$isAllZeroPhone && strlen($phone) >= 9;
    }

    public function submitActivation(): void
    {
        $this->validate([
            'activateFullName' => ['required', 'string', 'min:2', 'max:120'],
            'activatePhone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 9 || strlen($digits) > 11) {
                        $fail('Số điện thoại phải từ 9 đến 11 chữ số.');
                        return;
                    }
                    if (preg_match('/^0+$/', $digits) || preg_match('/^(\d)\1{7,}$/', $digits)) {
                        $fail('Vui lòng nhập số điện thoại thực tế.');
                        return;
                    }
                    $normalized = preg_replace('/[\s.-]/', '', (string) $value);
                    if (!preg_match('/^(0|\+?84)(3|5|7|8|9)[0-9]{8}$/', $normalized) && !preg_match('/^0[1-9][0-9]{8,9}$/', $digits)) {
                        $fail('Số điện thoại không đúng định dạng di động Việt Nam (VD: 0912345678).');
                    }
                },
            ],
            'activateAddress' => ['nullable', 'string', 'max:255'],
            'activateNotes' => ['nullable', 'string', 'max:500'],
        ], [
            'activateFullName.required' => 'Vui lòng nhập họ và tên của bạn.',
            'activateFullName.min' => 'Họ và tên cần ít nhất 2 ký tự.',
            'activatePhone.required' => 'Vui lòng nhập số điện thoại để kích hoạt bảo hành.',
        ]);

        $cleanPhone = preg_replace('/[\s.-]+/', '', trim($this->activatePhone));
        $cleanName = trim($this->activateFullName);
        $cleanAddress = trim($this->activateAddress) !== '' ? trim($this->activateAddress) : 'Kích hoạt bảo hành điện tử';

        if ($this->activateOrderId) {
            $order = Order::with(['items.product_variant', 'address'])->find($this->activateOrderId);
            if (!$order) {
                $this->addError('activateLookup', 'Đơn hàng không tồn tại.');
                return;
            }

            DB::transaction(function () use ($order, $cleanName, $cleanPhone, $cleanAddress) {
                Address::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'full_name' => $cleanName,
                        'phone' => $cleanPhone,
                        'province' => $order->address?->province ?: '',
                        'district' => $order->address?->district ?: '',
                        'ward' => $order->address?->ward ?: '',
                        'detailed_address' => $cleanAddress,
                    ]
                );

                foreach ($order->items as $item) {
                    $variant = $item->product_variant;
                    $wDays = (int) ($variant?->warranty_days ?: 365);
                    if ($wDays <= 0) $wDays = 365;

                    $currW = $item->warranty ? Carbon::parse($item->warranty) : null;
                    $created = $order->created_at ? Carbon::parse($order->created_at) : now();

                    if (!$currW || $currW->lessThanOrEqualTo($created->copy()->addDay())) {
                        $item->update(['warranty' => now()->addDays($wDays)]);
                    }
                }

                $noteAppend = 'Đã kích hoạt BH bởi ' . $cleanName . ' (' . $cleanPhone . ') lúc ' . now()->format('H:i d/m/Y');
                if (trim($this->activateNotes) !== '') {
                    $noteAppend .= ' | Ghi chú: ' . trim($this->activateNotes);
                }

                $order->update([
                    'status' => $order->status === 'new' ? 'delivered' : $order->status,
                    'notes' => trim(($order->notes ? $order->notes . "\n" : '') . $noteAppend),
                ]);
            });

            $this->activateSuccess = true;
            $this->activateSuccessCode = $order->order_code;
            $this->isAlreadyActivated = true;
            return;
        }

        if ($this->activateVariantId) {
            $variant = ProductVariant::with('product')->find($this->activateVariantId);
            if (!$variant) {
                $this->addError('activateLookup', 'Không tìm thấy sản phẩm.');
                return;
            }

            $order = DB::transaction(function () use ($variant, $cleanName, $cleanPhone, $cleanAddress) {
                $fallbackUserId = Auth::id() ?: (User::query()->value('id') ?: 1);
                $orderCode = 'BH' . now()->format('ymdHis') . random_int(10, 99);
                $unitPrice = (float) ($variant->price ?: ($variant->discount_price ?? 0));
                $costPrice = (float) ($variant->discount_price ?? 0);
                $profit = $costPrice > 0 ? ($unitPrice - $costPrice) : 0;
                $wDays = (int) ($variant->warranty_days ?: 365);
                if ($wDays <= 0) $wDays = 365;

                $newOrder = Order::create([
                    'user_id' => $fallbackUserId,
                    'order_code' => $orderCode,
                    'grand_total' => $unitPrice,
                    'payment_method' => 'cod',
                    'payment_status' => 'paid',
                    'status' => 'delivered',
                    'currency' => 'VND',
                    'shipping_amount' => 0,
                    'shipping_method' => 'in_store_pickup',
                    'profit_loss' => $profit,
                    'deposit_amount' => 0,
                    'deposit_status' => 'paid',
                    'notes' => 'Kích hoạt bảo hành qua SKU [' . $variant->sku . '] lúc ' . now()->format('H:i d/m/Y')
                        . (trim($this->activateNotes) !== '' ? ' | Ghi chú: ' . trim($this->activateNotes) : ''),
                ]);

                Address::create([
                    'order_id' => $newOrder->id,
                    'full_name' => $cleanName,
                    'phone' => $cleanPhone,
                    'province' => '',
                    'district' => '',
                    'ward' => '',
                    'detailed_address' => $cleanAddress,
                ]);

                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => 1,
                    'unit_amount' => $unitPrice,
                    'total_amount' => $unitPrice,
                    'profit_loss' => $profit,
                    'warranty' => now()->addDays($wDays),
                ]);

                return $newOrder;
            });

            $this->activateOrderId = $order->id;
            $this->activateVariantId = null;
            $this->activateCode = $order->order_code;
            $this->activateSuccess = true;
            $this->activateSuccessCode = $order->order_code;
            $this->isAlreadyActivated = true;
        }
    }

    public function resetActivation(): void
    {
        $this->activateOrderId = null;
        $this->activateVariantId = null;
        $this->activateCode = '';
        $this->activateSuccess = false;
        $this->activateSuccessCode = null;
        $this->isAlreadyActivated = false;
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

        // 3. Warranty Activation Data
        $activateOrder = $this->activateOrderId
            ? Order::with(['items.product_variant.product.variants', 'address'])->find($this->activateOrderId)
            : null;

        $activateVariant = (!$activateOrder && $this->activateVariantId)
            ? ProductVariant::with(['product.variants'])->find($this->activateVariantId)
            : null;

        if (!$activateOrder) {
            $term = ltrim(trim($this->activateCode), '#');
            if ($term !== '') {
                $matchingVariants = ProductVariant::with(['product.variants'])
                    ->where(function ($sub) use ($term) {
                        $sub->where('sku', 'like', '%' . $term . '%')
                            ->orWhere('name', 'like', '%' . $term . '%')
                            ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', '%' . $term . '%'));
                    })
                    ->orderByRaw('CASE WHEN sku = ? THEN 0 WHEN sku LIKE ? THEN 1 ELSE 2 END', [$term, $term . '%'])
                    ->limit(16)
                    ->get();
            } else {
                $matchingVariants = ProductVariant::with(['product.variants'])
                    ->where('stock', 'in_stock')
                    ->latest('id')
                    ->limit(40)
                    ->get()
                    ->unique('product_id')
                    ->take(10)
                    ->values();
            }
        } else {
            $matchingVariants = collect();
        }

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
