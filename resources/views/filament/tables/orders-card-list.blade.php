@php
    use App\Filament\Resources\OrderResource;
@endphp

<style>
    .babu-order-cards-wrap {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding: 18px 20px;
        background: #f8fafc;
    }
    .dark .babu-order-cards-wrap {
        background: #141414;
    }
    .babu-order-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        overflow: hidden;
    }
    .babu-order-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .dark .babu-order-card {
        background: #1e1e1e;
        border-color: #2e2e2e;
    }
    .dark .babu-order-card:hover {
        border-color: #3f3f46;
    }
    .babu-order-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }
    .dark .babu-order-card-header {
        border-bottom-color: #2c2c2c;
    }
    .babu-order-card-body {
        display: grid;
        grid-template-columns: minmax(290px, 5fr) minmax(380px, 7fr);
        gap: 36px;
        padding: 20px 24px 22px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .babu-order-card-body {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }
    .babu-order-section-label {
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 10px;
    }
    .dark .babu-order-section-label {
        color: #94a3b8;
    }
    .babu-order-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        text-decoration: none;
    }
    .babu-order-title:hover {
        color: #2563eb;
    }
    .dark .babu-order-title {
        color: #f8fafc;
    }
    .babu-order-time {
        font-size: 13px;
        color: #64748b;
        margin-top: 3px;
    }
    .dark .babu-order-time {
        color: #94a3b8;
    }
    .babu-order-customer-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .dark .babu-order-customer-name {
        color: #f8fafc;
    }
    .babu-order-meta-line {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        color: #475569;
        line-height: 1.45;
        margin-bottom: 6px;
    }
    .dark .babu-order-meta-line {
        color: #cbd5e1;
    }
    .babu-order-select-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 16px;
    }
    @media (max-width: 540px) {
        .babu-order-select-grid {
            grid-template-columns: 1fr;
        }
    }
    .babu-order-select-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .dark .babu-order-select-label {
        color: #e2e8f0;
    }
    .babu-order-select {
        width: 100%;
        height: 40px;
        padding: 0 32px 0 12px;
        font-size: 14px;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .babu-order-select:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.15);
    }
    .dark .babu-order-select {
        background-color: #262626;
        border-color: #3f3f46;
        color: #f8fafc;
    }
    .babu-order-items-scroll {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 6px;
    }
    .babu-order-items-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .babu-order-items-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 99px;
    }
    .babu-order-items-scroll::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 99px;
    }
    .dark .babu-order-items-scroll::-webkit-scrollbar-track {
        background: #262626;
    }
    .dark .babu-order-items-scroll::-webkit-scrollbar-thumb {
        background: #52525b;
    }
    .babu-order-item-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .babu-order-item-row:last-child {
        border-bottom: none;
    }
    .dark .babu-order-item-row {
        border-bottom-color: #2c2c2c;
    }
    .babu-order-item-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dark .babu-order-item-title {
        color: #f8fafc;
    }
    .babu-order-item-sku {
        font-size: 12.5px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .dark .babu-order-item-sku {
        color: #94a3b8;
    }
    .babu-order-item-price {
        font-size: 15.5px;
        font-weight: 700;
        color: #0f172a;
        min-width: 105px;
        text-align: right;
        white-space: nowrap;
    }
    .dark .babu-order-item-price {
        color: #f8fafc;
    }
    .babu-order-footer-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        margin-top: 4px;
        border-top: 1px solid #e2e8f0;
    }
    .dark .babu-order-footer-total {
        border-top-color: #2c2c2c;
    }
</style>

<div class="babu-order-cards-wrap">
    @foreach ($records as $record)
        @php
            $recordKey = $this->getTableRecordKey($record);
            $editUrl = OrderResource::getUrl('edit', ['record' => $record]);
            $viewUrl = OrderResource::getUrl('view', ['record' => $record]);

            $statusMeta = match ($record->status) {
                'new' => ['label' => 'Chờ xác nhận', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'color' => '#2563eb'],
                'processing' => ['label' => 'Xác nhận & Đóng gói', 'bg' => '#fffbeb', 'border' => '#fde68a', 'color' => '#d97706'],
                'shipped' => ['label' => 'Đang vận chuyển', 'bg' => '#eef2ff', 'border' => '#c7d2fe', 'color' => '#4f46e5'],
                'delivered' => ['label' => 'Giao thành công', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'color' => '#16a34a'],
                'cancelled' => ['label' => 'Đã hủy', 'bg' => '#fef2f2', 'border' => '#fecaca', 'color' => '#dc2626'],
                default => ['label' => ucfirst((string) $record->status), 'bg' => '#f1f5f9', 'border' => '#e2e8f0', 'color' => '#475569'],
            };

            $paymentMeta = match ($record->payment_status) {
                'paid' => ['label' => 'Đã Thanh Toán', 'bg' => '#ecfdf5', 'border' => '#a7f3d0', 'color' => '#059669'],
                'failed' => ['label' => 'Thanh Toán Lỗi', 'bg' => '#fef2f2', 'border' => '#fecaca', 'color' => '#dc2626'],
                default => ['label' => 'Chưa Thanh Toán', 'bg' => '#f1f5f9', 'border' => '#e2e8f0', 'color' => '#334155'],
            };

            $address = $record->address;
            $customerName = trim((string) ($address?->full_name ?: ($record->user?->name ?: 'Khách hàng')));
            $customerPhone = trim((string) ($address?->phone ?: 'Chưa có số điện thoại'));

            $addressParts = array_filter([
                $address?->detailed_address,
                $address?->ward,
                $address?->district,
                $address?->province,
            ], fn ($part) => filled($part));

            $fullAddress = !empty($addressParts)
                ? implode(', ', $addressParts)
                : ($record->shipping_method === 'in_store_pickup' ? 'Nhận trực tiếp tại cửa hàng' : 'Chưa cập nhật địa chỉ giao hàng');

            $items = $record->items ?? collect();
            $itemsCount = $items->count();

            $paymentMethodLabel = match ($record->payment_method) {
                'cod' => 'COD (Thu hộ)',
                'bank' => 'Chuyển khoản NH',
                'appotabank' => 'AppotaBank',
                default => strtoupper((string) $record->payment_method),
            };
        @endphp

        <div class="babu-order-card" wire:key="order-card-{{ $record->id }}">
            {{-- HEADER --}}
            <div class="babu-order-card-header">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <input
                        type="checkbox"
                        value="{{ $recordKey }}"
                        x-model="selectedRecords"
                        style="width: 18px; height: 18px; margin-top: 3px; border-radius: 4px; border: 1px solid #94a3b8; cursor: pointer; accent-color: #2563eb;"
                    />
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <a href="{{ $editUrl }}" class="babu-order-title">
                                Đơn Hàng <span style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #2563eb;">{{ $record->order_code ?: ('#' . $record->id) }}</span>
                            </a>
                            @if ($record->order_code)
                                <button
                                    type="button"
                                    x-data="{ copied: false }"
                                    x-on:click="navigator.clipboard.writeText('{{ e($record->order_code) }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                    style="font-size: 11.5px; padding: 2px 8px; border-radius: 6px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-weight: 700; cursor: pointer;"
                                    title="Sao chép mã đơn hàng"
                                >
                                    <span x-show="!copied">Copy mã</span>
                                    <span x-show="copied" x-cloak>Đã copy!</span>
                                </button>
                            @endif
                            <span style="font-size: 12px; padding: 2px 8px; border-radius: 6px; background: rgba(100,116,139,0.1); color: #64748b; font-weight: 600;">
                                {{ $paymentMethodLabel }}
                            </span>
                        </div>
                        <div class="babu-order-time">
                            {{ $record->created_at ? $record->created_at->format('H:i:s j/n/Y') : '—' }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="padding: 4px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 600; background: {{ $statusMeta['bg'] }}; border: 1px solid {{ $statusMeta['border'] }}; color: {{ $statusMeta['color'] }};">
                        {{ $statusMeta['label'] }}
                    </span>

                    <span style="padding: 4px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 600; background: {{ $paymentMeta['bg'] }}; border: 1px solid {{ $paymentMeta['border'] }}; color: {{ $paymentMeta['color'] }};">
                        {{ $paymentMeta['label'] }}
                    </span>

                    @if ($record->order_code)
                        @php
                            $activationUrl = url('/kich-hoat/' . $record->order_code);
                        @endphp
                        <button
                            type="button"
                            x-data="{ copiedLink: false }"
                            x-on:click="navigator.clipboard.writeText('{{ e($activationUrl) }}'); copiedLink = true; setTimeout(() => copiedLink = false, 1800)"
                            title="Copy link gửi khách điền thông tin kích hoạt sản phẩm & bảo hành"
                            style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 8px; font-size: 12.5px; font-weight: 700; color: #059669; border: 1px solid #a7f3d0; background: #ecfdf5; cursor: pointer;"
                        >
                            <span x-show="!copiedLink">🔗 Link Kích Hoạt</span>
                            <span x-show="copiedLink" x-cloak>✓ Đã copy link!</span>
                        </button>
                    @endif

                    <a
                        href="{{ $viewUrl }}"
                        title="Xem chi tiết & In phiếu"
                        style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 8px; font-size: 12.5px; font-weight: 600; color: #475569; border: 1px solid #e2e8f0; text-decoration: none; background: #f8fafc;"
                    >
                        Xem
                    </a>
                    <a
                        href="{{ $editUrl }}"
                        title="Chỉnh sửa đơn hàng"
                        style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 8px; font-size: 12.5px; font-weight: 600; color: #2563eb; border: 1px solid #bfdbfe; text-decoration: none; background: #eff6ff;"
                    >
                        Sửa
                    </a>
                </div>
            </div>

            {{-- STEPPER: giống trang tra cứu đơn hàng ngoài web --}}
            @php
                $stepNow = match ($record->status) {
                    'processing' => 2,
                    'shipped', 'shipping' => 3,
                    'delivered', 'completed' => 4,
                    'cancelled', 'canceled' => 0,
                    default => 1,
                };
            @endphp
            @if ($stepNow > 0)
                <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 12px 24px; border-bottom: 1px solid rgba(148,163,184,0.18);">
                    @foreach ([1 => 'Đặt hàng', 2 => 'Xác nhận & Đóng gói', 3 => 'Đang vận chuyển', 4 => 'Giao thành công'] as $n => $label)
                        @php
                            $done = $stepNow >= $n;
                            $current = $stepNow === $n;
                        @endphp
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: {{ $done ? 'inherit' : '#98a0ad' }};">
                            <span style="width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; font-size: 11px; font-weight: 800; flex: none; background: {{ $current ? '#1240f7' : ($done ? '#eef2ff' : '#f1f3f7') }}; color: {{ $current ? '#fff' : ($done ? '#1240f7' : '#68707d') }};">
                                {{ $stepNow > $n ? '✓' : $n }}
                            </span>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 10px 24px; border-bottom: 1px solid rgba(148,163,184,0.18); color: #dc2626; font-size: 13px; font-weight: 700;">
                    Đơn hàng đã hủy
                </div>
            @endif

            {{-- BODY --}}
            <div class="babu-order-card-body">
                {{-- LEFT COLUMN: NGƯỜI ĐẶT & TRẠNG THÁI --}}
                <div>
                    <div class="babu-order-section-label">NGƯỜI ĐẶT</div>

                    <div class="babu-order-customer-name">{{ $customerName }}</div>

                    <div class="babu-order-meta-line">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px; color: #64748b;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>{{ $customerPhone }}</span>
                    </div>

                    <div class="babu-order-meta-line">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px; color: #64748b;">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>{{ $fullAddress }}</span>
                    </div>

                    <div class="babu-order-select-grid">
                        <div>
                            <label class="babu-order-select-label">Trạng Thái Đơn Hàng</label>
                            <select
                                class="babu-order-select"
                                wire:change="updateOrderStatus({{ $record->id }}, $event.target.value)"
                            >
                                <option value="new" @selected($record->status === 'new')>Chờ xác nhận</option>
                                <option value="processing" @selected($record->status === 'processing')>Xác nhận &amp; Đóng gói</option>
                                <option value="shipped" @selected($record->status === 'shipped')>Đang vận chuyển</option>
                                <option value="delivered" @selected($record->status === 'delivered')>Giao thành công</option>
                                <option value="cancelled" @selected($record->status === 'cancelled')>Đã hủy</option>
                            </select>
                        </div>

                        <div>
                            <label class="babu-order-select-label">Thanh Toán</label>
                            <select
                                class="babu-order-select"
                                wire:change="updatePaymentStatus({{ $record->id }}, $event.target.value)"
                            >
                                <option value="pending" @selected($record->payment_status === 'pending')>Chưa Thanh Toán</option>
                                <option value="paid" @selected($record->payment_status === 'paid')>Đã Thanh Toán</option>
                                <option value="failed" @selected($record->payment_status === 'failed')>Thất Bại</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: SẢN PHẨM & TỔNG CỘNG --}}
                <div>
                    <div class="babu-order-section-label">SẢN PHẨM ({{ $itemsCount }})</div>

                    <div class="babu-order-items-scroll">
                        @forelse ($items as $item)
                            @php
                                $variant = $item->product_variant;
                                $productName = $variant?->product?->name ?? 'Sản phẩm không xác định';
                                $variantLabel = OrderResource::formatVariantClassification($variant);
                                $displayTitle = ($variantLabel && $variantLabel !== 'Mặc định' && $variantLabel !== 'Tiêu chuẩn')
                                    ? "{$productName} - {$variantLabel}"
                                    : $productName;
                                $sku = $variant?->sku ?: 'CHƯA CÓ MÃ SKU';
                                $imgUrl = OrderResource::resolveVariantImageUrl($variant);
                                $qty = (int) ($item->quantity ?: 1);
                                $lineAmount = (float) ($item->total_amount ?: ($item->unit_amount * $qty));
                                $warrantyDate = $item->warranty ? \Illuminate\Support\Carbon::parse($item->warranty) : null;
                                $isWarrantyActive = $warrantyDate && $warrantyDate->isFuture();
                            @endphp

                            <div class="babu-order-item-row">
                                @if ($imgUrl)
                                    <img
                                        src="{{ $imgUrl }}"
                                        alt="{{ $displayTitle }}"
                                        style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; flex-shrink: 0;"
                                    />
                                @else
                                    <div style="width: 52px; height: 52px; border-radius: 8px; background: #f1f5f9; color: #64748b; display: grid; place-items: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                        IMG
                                    </div>
                                @endif

                                <div style="flex: 1; min-width: 0;">
                                    <div class="babu-order-item-title" title="{{ $displayTitle }}">
                                        {{ $displayTitle }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 3px;">
                                        <span class="babu-order-item-sku">{{ $sku }}</span>
                                        @if ($variantLabel && $variantLabel !== 'Mặc định' && $variantLabel !== 'Tiêu chuẩn')
                                            <span style="font-size: 11.5px; padding: 1px 7px; border-radius: 5px; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600;">
                                                Loại: {{ $variantLabel }}
                                            </span>
                                        @endif
                                        @if ($isWarrantyActive)
                                            <span style="font-size: 11.5px; padding: 1px 7px; border-radius: 5px; background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 700;">
                                                🛡️ BH đến: {{ $warrantyDate->format('d/m/Y') }}
                                            </span>
                                        @elseif ($record->address?->phone === '0000000000')
                                            <span style="font-size: 11.5px; padding: 1px 7px; border-radius: 5px; background: rgba(245, 158, 11, 0.14); color: #d97706; font-weight: 700;">
                                                ⏳ Chờ khách kích hoạt
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div style="font-size: 14px; color: #64748b; font-weight: 600; min-width: 34px; text-align: center;">
                                    x{{ $qty }}
                                </div>

                                <div class="babu-order-item-price">
                                    {{ number_format($lineAmount, 0, ',', '.') }} ₫
                                </div>
                            </div>
                        @empty
                            <div style="padding: 16px 0; color: #94a3b8; font-size: 13.5px;">
                                Đơn hàng chưa có sản phẩm nào.
                            </div>
                        @endforelse
                    </div>

                    <div class="babu-order-footer-total">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span style="font-size: 15.5px; font-weight: 700; color: inherit;">Tổng Cộng</span>
                            @if ((float) $record->shipping_amount > 0)
                                <span style="font-size: 12px; color: #64748b;">
                                    (Gồm ship: {{ number_format((float) $record->shipping_amount, 0, ',', '.') }} ₫)
                                </span>
                            @endif
                            @if ($record->profit_loss !== null)
                                <span style="font-size: 12px; padding: 2px 8px; border-radius: 6px; background: rgba(22, 163, 74, 0.12); color: #16a34a; font-weight: 700;">
                                    Lợi nhuận: {{ number_format((float) $record->profit_loss, 0, ',', '.') }} ₫
                                </span>
                            @endif
                        </div>

                        <div style="font-size: 17px; font-weight: 800; color: inherit;">
                            {{ number_format((float) $record->grand_total, 0, ',', '.') }} ₫
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

