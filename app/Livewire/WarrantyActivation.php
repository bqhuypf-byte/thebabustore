<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WarrantyActivation extends Component
{
    public string $code = '';
    public string $productSearch = '';

    public ?int $orderId = null;
    public ?int $variantId = null;

    public string $full_name = '';
    public string $phone = '';
    public string $detailed_address = '';
    public string $notes = '';

    public bool $activatedSuccess = false;
    public ?string $activatedOrderCode = null;
    public bool $isAlreadyActivated = false;

    public function mount(?string $code = null): void
    {
        $initialCode = trim((string) ($code ?: request()->query('code', '')));
        $initialSku = trim((string) request()->query('sku', ''));

        if ($initialCode !== '') {
            $this->code = $initialCode;
            $this->productSearch = $initialCode;
            $this->lookupByCodeOrKeyword($initialCode);
        } elseif ($initialSku !== '') {
            $this->code = $initialSku;
            $this->productSearch = $initialSku;
            $this->lookupByCodeOrKeyword($initialSku);
        }
    }

    public function updatedCode(string $value): void
    {
        $this->productSearch = $value;
        $this->resetErrorBag('lookup');
        if (trim($value) !== '') {
            $this->lookupByCodeOrKeyword($value, silent: true);
        }
    }

    public function searchCodeOrSku(): void
    {
        $this->resetErrorBag();
        $this->activatedSuccess = false;

        $input = trim($this->code ?: $this->productSearch);
        if ($input === '') {
            $this->addError('lookup', 'Vui lòng nhập tên sản phẩm, mã SKU (VD: S75, ATK...) hoặc mã đơn hàng.');
            return;
        }

        $this->lookupByCodeOrKeyword($input, silent: false);
    }

    public function selectVariant(int $variantId): void
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if ($variant) {
            $this->variantId = $variant->id;
            $this->orderId = null;
            $this->isAlreadyActivated = false;
            $this->resetErrorBag('lookup');
        }
    }

    protected function lookupByCodeOrKeyword(string $rawInput, bool $silent = false): void
    {
        $clean = ltrim(trim($rawInput), '#');
        if ($clean === '') {
            return;
        }

        // 1. Thử khớp chính xác hoặc gần đúng Mã đơn hàng (WEB..., BH...)
        $order = Order::with(['items.product_variant.product', 'address'])
            ->where('order_code', $clean)
            ->first();

        if ($order) {
            $this->bindOrder($order);
            return;
        }

        // 2. Tìm kiếm Biến thể sản phẩm theo SKU, Tên biến thể hoặc Tên sản phẩm (VD: "s75")
        $matchedVariants = $this->findMatchingVariants($clean);

        if ($matchedVariants->isNotEmpty()) {
            $first = $matchedVariants->first();
            $this->orderId = null;
            $this->variantId = $first->id;
            return;
        }

        if (!$silent) {
            $this->addError('lookup', 'Không tìm thấy sản phẩm hoặc mã "' . $clean . '". Bạn có thể gõ tên sản phẩm hoặc chọn từ danh sách bên dưới.');
        }
    }

    protected function findMatchingVariants(string $keyword): Collection
    {
        $term = trim($keyword);
        if ($term === '') {
            return ProductVariant::with(['product.variants'])
                ->where('stock', 'in_stock')
                ->latest('id')
                ->limit(40)
                ->get()
                ->unique('product_id')
                ->take(10)
                ->values();
        }

        return ProductVariant::with(['product.variants'])
            ->where(function ($q) use ($term) {
                $q->where('sku', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%')
                    ->orWhereHas('product', function ($pq) use ($term) {
                        $pq->where('name', 'like', '%' . $term . '%')
                            ->orWhere('slug', 'like', '%' . $term . '%');
                    });
            })
            ->orderByRaw("CASE WHEN sku = ? THEN 0 WHEN sku LIKE ? THEN 1 ELSE 2 END", [$term, $term . '%'])
            ->limit(16)
            ->get();
    }

    protected function bindOrder(Order $order): void
    {
        $this->orderId = $order->id;
        $this->variantId = null;
        $this->code = (string) $order->order_code;
        $this->isAlreadyActivated = $this->isOrderAlreadyActivated($order);

        if ($order->address) {
            $existingName = trim((string) $order->address->full_name);
            if ($existingName !== '' && !str_contains(mb_strtolower($existingName), 'chờ kích hoạt')) {
                $this->full_name = $existingName;
            }
            $existingPhone = trim((string) $order->address->phone);
            if ($existingPhone !== '' && !preg_match('/^0+$/', $existingPhone)) {
                $this->phone = $existingPhone;
            } else {
                $this->phone = '';
            }
            $existingAddr = trim((string) $order->address->detailed_address);
            if ($existingAddr !== '' && !str_contains(mb_strtolower($existingAddr), 'chờ khách điền')) {
                $this->detailed_address = $existingAddr;
            }
        }
    }

    public function activateWarranty(): void
    {
        $this->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 9 || strlen($digits) > 11) {
                        $fail('Số điện thoại phải có từ 9 đến 11 chữ số.');
                        return;
                    }
                    if (preg_match('/^0+$/', $digits) || preg_match('/^(\d)\1{7,}$/', $digits)) {
                        $fail('Vui lòng nhập số điện thoại thực tế (không sử dụng toàn số 0 hoặc số lặp thử nghiệm).');
                        return;
                    }
                    $normalized = preg_replace('/[\s.-]/', '', (string) $value);
                    if (!preg_match('/^(0|\+?84)(3|5|7|8|9)[0-9]{8}$/', $normalized) && !preg_match('/^0[1-9][0-9]{8,9}$/', $digits)) {
                        $fail('Số điện thoại không đúng định dạng di động Việt Nam (VD: 0912345678).');
                    }
                },
            ],
            'detailed_address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'full_name.min' => 'Họ và tên cần ít nhất 2 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại để kích hoạt và tra cứu bảo hành.',
        ]);

        if (!$this->orderId && !$this->variantId && trim($this->code) !== '') {
            $this->lookupByCodeOrKeyword($this->code, silent: true);
        }

        if (!$this->orderId && !$this->variantId) {
            $this->addError('lookup', 'Vui lòng tìm và chọn Sản phẩm / Mã SKU cần kích hoạt ở cột bên trái trước khi xác nhận.');
            return;
        }

        $cleanPhone = preg_replace('/\s+/', '', trim($this->phone));
        $cleanName = trim($this->full_name);
        $cleanAddress = trim($this->detailed_address) !== ''
            ? trim($this->detailed_address)
            : 'Kích hoạt bảo hành điện tử';

        if ($this->orderId) {
            $order = Order::with(['items.product_variant', 'address'])->find($this->orderId);
            if (!$order) {
                $this->addError('lookup', 'Đơn hàng không tồn tại.');
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
                    $warrantyDays = (int) ($variant?->warranty_days ?: 365);
                    if ($warrantyDays <= 0) {
                        $warrantyDays = 365;
                    }

                    $currentWarranty = $item->warranty ? Carbon::parse($item->warranty) : null;
                    $orderCreated = $order->created_at ? Carbon::parse($order->created_at) : now();

                    if (!$currentWarranty || $currentWarranty->lessThanOrEqualTo($orderCreated->copy()->addDay())) {
                        $item->update([
                            'warranty' => now()->addDays($warrantyDays),
                        ]);
                    }
                }

                $noteAppend = 'Đã kích hoạt BH bởi ' . $cleanName . ' (' . $cleanPhone . ') lúc ' . now()->format('H:i d/m/Y');
                if (trim($this->notes) !== '') {
                    $noteAppend .= ' | Ghi chú KH: ' . trim($this->notes);
                }

                $order->update([
                    'status' => $order->status === 'new' ? 'delivered' : $order->status,
                    'notes' => trim(($order->notes ? $order->notes . "\n" : '') . $noteAppend),
                ]);
            });

            $this->activatedSuccess = true;
            $this->activatedOrderCode = $order->order_code;
            return;
        }

        if ($this->variantId) {
            $variant = ProductVariant::with('product')->find($this->variantId);
            if (!$variant) {
                $this->addError('lookup', 'Không tìm thấy biến thể sản phẩm.');
                return;
            }

            $order = DB::transaction(function () use ($variant, $cleanName, $cleanPhone, $cleanAddress) {
                $fallbackUserId = Auth::id() ?: (User::query()->value('id') ?: 1);
                $orderCode = 'BH' . now()->format('ymdHis') . random_int(10, 99);
                $unitPrice = (float) ($variant->price ?: ($variant->discount_price ?? 0));
                $costPrice = (float) ($variant->discount_price ?? 0);
                $profit = $costPrice > 0 ? ($unitPrice - $costPrice) : 0;
                $warrantyDays = (int) ($variant->warranty_days ?: 365);
                if ($warrantyDays <= 0) {
                    $warrantyDays = 365;
                }

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
                    'notes' => 'Kích hoạt bảo hành qua Link SKU [' . $variant->sku . '] lúc ' . now()->format('H:i d/m/Y')
                        . (trim($this->notes) !== '' ? ' | Ghi chú: ' . trim($this->notes) : ''),
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
                    'warranty' => now()->addDays($warrantyDays),
                ]);

                return $newOrder;
            });

            $this->orderId = $order->id;
            $this->variantId = null;
            $this->code = $order->order_code;
            $this->activatedSuccess = true;
            $this->activatedOrderCode = $order->order_code;
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

        // Đơn đang ở trạng thái giữ chỗ chờ kích hoạt
        $isAllZeroPhone = $phone === '' || preg_match('/^0+$/', $phone);
        $isPlaceholderName = str_contains(mb_strtolower($fullName), 'chờ kích hoạt');
        $isPlaceholderNote = str_contains($notes, 'Tạo link kích hoạt');

        // Nếu là placeholder và chưa có ghi chú kích hoạt -> Chưa kích hoạt
        if (($isAllZeroPhone || $isPlaceholderName || $isPlaceholderNote) && !str_contains($notes, 'Đã kích hoạt')) {
            return false;
        }

        // Nếu đã có ghi chú "Đã kích hoạt BH" -> Đã kích hoạt
        if (str_contains($notes, 'Đã kích hoạt')) {
            return true;
        }

        // Hoặc nếu đơn có khách thật và có hạn bảo hành hợp lệ đang còn hiệu lực
        $hasFutureWarranty = false;
        foreach ($order->items as $item) {
            if ($item->warranty && Carbon::parse($item->warranty)->isFuture()) {
                $hasFutureWarranty = true;
                break;
            }
        }

        return $hasFutureWarranty && !$isAllZeroPhone && strlen($phone) >= 9;
    }

    public function render()
    {
        $order = $this->orderId
            ? Order::with(['items.product_variant.product', 'address'])->find($this->orderId)
            : null;

        $selectedVariant = (!$order && $this->variantId)
            ? ProductVariant::with('product')->find($this->variantId)
            : null;

        $matchingVariants = !$order
            ? $this->findMatchingVariants($this->code ?: $this->productSearch)
            : collect();

        $isAlreadyActivated = $this->isOrderAlreadyActivated($order);

        return view('livewire.warranty-activation', [
            'order' => $order,
            'variant' => $selectedVariant,
            'matchingVariants' => $matchingVariants,
            'isAlreadyActivated' => $isAlreadyActivated,
        ]);
    }
}
