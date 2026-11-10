<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phiếu xác nhận đơn {{ $order->order_code ?: $order->id }}</title>
    <style>
        @font-face { font-family: Geist; src: url('/fonts/Geist-Variable.woff2') format('woff2'); font-weight: 100 900; font-display: swap; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; background: #f3f3f3; color: #171717; font: 14px/1.55 Geist, Arial, sans-serif; }
        .receipt { max-width: 800px; margin: auto; padding: 40px; background: #fff; border: 1px solid #e5e5e5; border-radius: 14px; }
        .top { display: flex; align-items: start; justify-content: space-between; gap: 24px; border-bottom: 1px solid #e5e5e5; padding-bottom: 24px; }
        h1 { margin: 4px 0; font-size: 27px; line-height: 1.2; }
        h2 { margin: 0 0 10px; font-size: 15px; }
        p { margin: 3px 0; }
        .muted { color: #666; }
        .meta { text-align: right; }
        .columns { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 26px 0; }
        .table-wrap { overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 12px 6px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
        th { color: #666; font-size: 12px; font-weight: 600; }
        th:not(:first-child), td:not(:first-child) { text-align: right; white-space: nowrap; }
        .totals { width: min(100%, 330px); margin: 18px 0 0 auto; }
        .totals div { display: flex; justify-content: space-between; gap: 16px; padding: 5px 0; }
        .totals .total { padding-top: 12px; border-top: 1px solid #ddd; font-size: 17px; font-weight: 700; }
        .notice { margin-top: 30px; padding: 13px; border-radius: 8px; background: #f7f7f7; color: #555; font-size: 12px; }
        .toolbar { display: flex; justify-content: center; gap: 10px; max-width: 800px; margin: 20px auto 0; }
        .toolbar button, .toolbar a { display: inline-block; padding: 10px 18px; border: 1px solid #171717; border-radius: 99px; background: #171717; color: #fff; text-decoration: none; font: inherit; cursor: pointer; }
        .toolbar a { background: #fff; color: #171717; }
        @media (max-width: 600px) { .receipt { padding: 24px 18px; } .top { flex-direction: column; } .meta { text-align: left; } .columns { grid-template-columns: 1fr; } h1 { font-size: 23px; } }
        @media print { @page { margin: 15mm; } body { padding: 0; background: #fff; } .receipt { max-width: none; padding: 0; border: 0; } .toolbar { display: none; } }
    </style>
</head>
<body>
    @php
        $money = fn ($value) => number_format((float) $value, 0, ',', '.') . '₫';
        $paymentLabels = ['bank' => 'Chuyển khoản ngân hàng', 'cod' => 'Thanh toán khi nhận hàng', 'appotabank' => 'AppotaBank'];
        $paymentStatuses = ['paid' => 'Đã thanh toán', 'pending' => 'Chờ thanh toán', 'failed' => 'Thanh toán thất bại'];
        $orderStatuses = ['new' => 'Chờ xác nhận', 'processing' => 'Đang xử lý', 'shipped' => 'Đang giao', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy', 'canceled' => 'Đã hủy'];
    @endphp
    <main class="receipt">
        <div class="top">
            <div>
                <strong>{{ $settings?->web_name ?: 'THE BABU STORE' }}</strong>
                <h1>Phiếu xác nhận đơn hàng</h1>
                <p class="muted">{{ $settings?->address }}</p>
                @if($settings?->hotline)<p class="muted">Hotline: {{ $settings->hotline }}</p>@endif
            </div>
            <div class="meta">
                <p><strong>{{ $order->order_code ?: '#'.$order->id }}</strong></p>
                <p class="muted">Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') ?: '—' }}</p>
                <p class="muted">{{ $orderStatuses[$order->status] ?? ($order->status ?: 'Chờ xác nhận') }}</p>
            </div>
        </div>
        <div class="columns">
            <section>
                <h2>Thông tin nhận hàng</h2>
                <p><strong>{{ $order->address?->full_name ?: '—' }}</strong></p>
                <p>{{ $order->address?->phone ?: '—' }}</p>
                <p>{{ collect([$order->address?->detailed_address, $order->address?->ward, $order->address?->district, $order->address?->province])->filter()->join(', ') ?: '—' }}</p>
            </section>
            <section>
                <h2>Thanh toán & giao hàng</h2>
                <p>{{ $paymentLabels[$order->payment_method] ?? ($order->payment_method ?: '—') }}</p>
                <p>{{ $paymentStatuses[$order->payment_status] ?? ($order->payment_status ?: 'Chưa xác định') }}</p>
                <p>{{ $order->shipping_method === 'in_store_pickup' ? 'Nhận tại cửa hàng' : 'Giao hàng tận nơi' }}</p>
            </section>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Sản phẩm</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr><td><strong>{{ $item->product_variant?->product?->name ?? $item->product?->name ?? 'Sản phẩm' }}</strong>@if($item->product_variant?->name)<br><span class="muted">{{ $item->product_variant->name }}</span>@endif </td><td>{{ $item->quantity }}</td><td>{{ $money($item->unit_amount) }}</td><td>{{ $money($item->total_amount) }}</td></tr>
                    @empty
                        <tr><td colspan="4">Không có chi tiết sản phẩm.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="totals">
            <div><span>Tạm tính</span><span>{{ $money($order->grand_total) }}</span></div>
            <div><span>Phí vận chuyển</span><span>{{ $money($order->shipping_amount ?? 0) }}</span></div>
            <div class="total"><span>Tổng đơn hàng</span><span>{{ $money($order->grand_total + ($order->shipping_amount ?? 0)) }}</span></div>
            @if($order->payment_method === 'cod' && $order->deposit_amount > 0)
                <div><span>Tiền cọc ({{ $order->deposit_status === 'paid' ? 'đã thanh toán' : 'chưa thanh toán' }})</span><span>{{ $money($order->deposit_amount) }}</span></div>
            @endif
        </div>
        @if($order->notes)<p><strong>Ghi chú:</strong> {{ $order->notes }}</p>@endif
        <div class="notice">Đây là phiếu xác nhận đơn hàng, không phải hóa đơn điện tử hoặc hóa đơn giá trị gia tăng.</div>
    </main>
    <div class="toolbar"><button type="button" onclick="window.print()">In phiếu xác nhận</button><a href="{{ url('/account/orders') }}">Đơn hàng của tôi</a></div>
</body>
</html>
