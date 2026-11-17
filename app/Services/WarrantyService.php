<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\WarrantyActivation;
use App\Models\WarrantyRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WarrantyService
{
    public static function phone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        return str_starts_with($digits, '84') ? '0'.substr($digits, 2) : $digits;
    }

    public static function phoneRules(): array
    {
        return ['required', 'regex:/^(?:0|\+?84)[1-9][0-9]{8,9}$/'];
    }

    public function createOffline(array $data): Order
    {
        Validator::make($data, ['phone' => self::phoneRules(), 'name' => 'nullable|string|max:120', 'purchase_date' => 'required|date|before_or_equal:today', 'items' => 'required|array|min:1', 'items.*.product_variant_id' => 'required|integer|exists:product_variants,id', 'items.*.quantity' => 'required|integer|min:1|max:999'])->validate();

        return DB::transaction(function () use ($data) {
            $order = Order::create(['user_id' => null, 'order_code' => 'BH'.Str::upper(Str::random(14)), 'purchase_date' => $data['purchase_date'], 'order_source' => 'offline', 'grand_total' => 0, 'payment_method' => 'cod', 'payment_status' => 'pending', 'status' => 'delivered', 'currency' => 'VND', 'shipping_amount' => 0, 'shipping_method' => 'in_store_pickup', 'deposit_amount' => 0, 'deposit_status' => 'pending', 'notes' => 'Đơn offline; bảo hành chờ kích hoạt.']);
            Address::create(['order_id' => $order->id, 'full_name' => trim($data['name'] ?? '') ?: 'Khách mua tại cửa hàng', 'phone' => self::phone($data['phone']), 'province' => '', 'district' => '', 'ward' => '', 'detailed_address' => 'Nhận tại cửa hàng']);
            // Consolidate duplicate variants so quantity and the activation identity agree.
            $lines = collect($data['items'])->groupBy('product_variant_id');
            foreach ($lines as $variantId => $rows) {
                $variant = ProductVariant::findOrFail($variantId);
                $quantity = $rows->sum('quantity');
                OrderItem::create(['order_id' => $order->id, 'product_variant_id' => $variantId, 'quantity' => $quantity, 'unit_amount' => $variant->price, 'total_amount' => $variant->price * $quantity, 'warranty' => null]);
            }
            $order->update(['grand_total' => $order->items()->sum('total_amount')]);

            return $order;
        });
    }

    public function generateLink(Order $order): string
    {
        return DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            if (! $order->purchase_date) {
                throw ValidationException::withMessages(['purchase_date' => 'Nhân viên cần xác nhận ngày mua trước khi tạo link.']);
            }
            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages(['order' => 'Đơn đã hủy không thể kích hoạt.']);
            }
            $token = Str::random(64);
            $order->update(['activation_token_hash' => hash('sha256', $token), 'activation_token_expires_at' => now()->addDays(30)]);

            return url('/kich-hoat/'.$order->order_code).'?token='.urlencode($token);
        });
    }

    public function authorizeCustomer(Order $order, string $token, string $phone): void
    {
        if ($order->status === 'cancelled') {
            throw ValidationException::withMessages(['activateLookup' => 'Đơn đã hủy không thể kích hoạt.']);
        }
        if ($token !== '') {
            if (! $order->activation_token_hash || ! hash_equals($order->activation_token_hash, hash('sha256', $token)) || ! $order->activation_token_expires_at || $order->activation_token_expires_at->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages(['activateLookup' => 'Link kích hoạt không hợp lệ hoặc đã hết hạn. Vui lòng liên hệ cửa hàng để nhận link mới.']);
            }
        } elseif (! preg_match('/^0[1-9][0-9]{8,9}$/', self::phone($phone)) || self::phone($phone) !== self::phone((string) $order->address?->phone)) {
            throw ValidationException::withMessages(['activateLookup' => 'Mã đơn hoặc số điện thoại chưa khớp. Kiểm tra lại hoặc liên hệ cửa hàng.']);
        }
    }

    public function activate(Order $order, array $variantIds, string $name, string $phone, ?string $serial = null, string $actor = 'staff', ?int $staffId = null, ?string $token = null): array
    {
        Validator::make(compact('name', 'phone', 'serial', 'variantIds'), ['name' => 'required|string|min:2|max:120', 'phone' => self::phoneRules(), 'serial' => 'nullable|string|max:255', 'variantIds' => 'required|array|min:1', 'variantIds.*' => 'integer'])->validate();

        return DB::transaction(function () use ($order, $variantIds, $name, $phone, $serial, $actor, $staffId, $token) {
            $order = Order::with(['items.product_variant', 'address'])->lockForUpdate()->findOrFail($order->id);
            if ($actor === 'customer') {
                $this->authorizeCustomer($order, $token ?? '', $phone);
            }
            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages(['activateLookup' => 'Đơn đã hủy không thể kích hoạt.']);
            }
            $ids = array_unique(array_map('intval', $variantIds));
            $result = [];
            foreach ($ids as $id) {
                $items = $order->items->where('product_variant_id', $id);
                if ($items->isEmpty()) {
                    throw ValidationException::withMessages(['selectedVariants' => 'Sản phẩm không thuộc đơn hàng này.']);
                }
                $existing = WarrantyActivation::where('order_id', $order->id)->where('product_variant_id', $id)->first();
                if ($existing) {
                    $result[] = $existing;

                    continue;
                }
                if (! $order->purchase_date) {
                    throw ValidationException::withMessages(['activateLookup' => 'Đơn cũ chưa có ngày mua được xác nhận. Vui lòng liên hệ cửa hàng.']);
                }
                $days = (int) $items->first()->product_variant?->warranty_days;
                if ($days <= 0) {
                    throw ValidationException::withMessages(['activateLookup' => 'Sản phẩm chưa có thời hạn bảo hành hợp lệ. Nhân viên cần xác nhận chính sách, không tự gán 365 ngày.']);
                }
                $expiry = $order->purchase_date->copy()->addDays($days);
                // Preserve meaningful legacy expiry values; never extend existing warranties.
                $legacy = $items->filter(fn ($item) => $item->warranty && Carbon::parse($item->warranty)->gt($order->created_at->copy()->addDay()));
                if ($legacy->isNotEmpty()) {
                    $expiry = Carbon::parse($legacy->min('warranty'));
                }
                $record = WarrantyActivation::create(['order_id' => $order->id, 'product_variant_id' => $id, 'customer_phone' => self::phone($phone), 'customer_name' => trim($name), 'serial' => $serial, 'quantity' => $items->sum('quantity'), 'purchase_date' => $order->purchase_date, 'warranty_days' => $days, 'expires_at' => $expiry, 'activated_at' => now(), 'activated_by' => $actor, 'staff_id' => $staffId, 'status' => 'active']);
                foreach ($items as $item) {
                    if (! $legacy->contains('id', $item->id)) {
                        $item->update(['warranty' => $expiry]);
                    }
                }
                $result[] = $record;
            }

            return $result;
        });
    }

    public function approve(WarrantyRequest $request, string $purchaseDate, int $staffId, ?string $reviewNotes = null): void
    {
        Validator::make(['purchase_date' => $purchaseDate], ['purchase_date' => 'required|date|before_or_equal:today'])->validate();
        DB::transaction(function () use ($request, $purchaseDate, $staffId, $reviewNotes) {
            $request = WarrantyRequest::lockForUpdate()->findOrFail($request->id);
            if ($request->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Yêu cầu đã được xử lý.']);
            }
            $order = $this->createOffline(['phone' => $request->customer_phone, 'name' => $request->customer_name, 'purchase_date' => $purchaseDate, 'items' => [['product_variant_id' => $request->product_variant_id, 'quantity' => 1]]]);
            $this->activate($order, [$request->product_variant_id], $request->customer_name, $request->customer_phone, $request->serial, 'staff', $staffId);
            $request->update(['status' => 'approved', 'purchase_date' => $purchaseDate, 'review_notes' => $reviewNotes, 'reviewed_by' => $staffId, 'reviewed_at' => now(), 'order_id' => $order->id]);
        });
    }
}
