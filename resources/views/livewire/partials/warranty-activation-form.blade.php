<section class="wl-center-card wa-activation-v2" style="text-align:left">
    <header class="wa-activation-header">
        <span class="wa-header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3L20 6V11C20 16 16 20 12 22C8 20 4 16 4 11V6Z"/><path d="M8 12L11 15L16 9"/></svg></span>
        <p class="wa-header-eyebrow">BABU CARE</p>
        <h1 class="wl-main-title">KÍCH HOẠT BẢO HÀNH ĐIỆN TỬ</h1>
        <p class="wl-main-sub">Xác nhận đơn hàng để kích hoạt. Hạn bảo hành tính từ ngày mua, theo chính sách của từng phiên bản.</p>
    </header>
    @if($errors->any() && $activationLinkError === '')<div class="wa-alert-warning" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    @if($activateSuccess)<div class="wa-success-panel" role="status"><strong>Đã lưu bảo hành cho mặt hàng đã chọn.</strong><p>Mặt hàng đã kích hoạt trước đó được giữ nguyên, không gia hạn khi gửi lại.</p></div>@endif
    @if($activationLinkError !== '')
        <div class="wa-alert-warning" role="alert">{{ $activationLinkError }}</div>
        <button type="button" class="wa-inline-search-btn" wire:click="resetActivation">Tra cứu bằng mã đơn và số điện thoại</button>
    @elseif(!$activateOrder)
        <form wire:submit="searchActivationCode" class="wa-fields-form">
            <div class="wa-step-header"><span class="wa-step-num">1</span><div><h2>Nhập mã đơn hàng</h2><p>Dùng mã đơn và số điện thoại mua hàng. Không cần đăng nhập.</p></div></div>
            <div class="wa-order-search-grid">
                <div class="wa-field-group"><label for="wa-order-code">Mã đơn hàng</label><input id="wa-order-code" wire:model="activateCode" placeholder="#WEB… hoặc #BH…" autocomplete="off" required></div>
                <div class="wa-field-group"><label for="wa-order-phone">Số điện thoại mua hàng</label><input id="wa-order-phone" wire:model="activatePhone" type="tel" autocomplete="tel" placeholder="0912345678" required></div>
            </div>
            <button type="submit" class="wa-submit-action-btn" wire:loading.attr="disabled">Tìm đơn hàng</button>
        </form>
    @else
        <div class="wa-selected-order-box">
            <strong>Đơn #{{ $activateOrder->order_code }}</strong>
            <p>Ngày mua: {{ $activateOrder->purchase_date?->format('d/m/Y') ?? 'Chờ nhân viên xác nhận' }}</p>
            @if(!$activateOrder->purchase_date)<div class="wa-alert-warning">Đơn cũ chưa có ngày mua được xác nhận. Vui lòng liên hệ cửa hàng; hệ thống không lấy ngày kích hoạt làm ngày mua.</div>@endif
        </div>
        <form wire:submit="submitActivation" class="wa-fields-form">
            <h2 class="wa-section-title">Chọn mặt hàng kích hoạt</h2>
            @foreach($activateOrder->items->groupBy('product_variant_id') as $variantId=>$items)
                @php $item=$items->first(); $variant=$item->product_variant; $activation=$activateOrder->warrantyActivations->firstWhere('product_variant_id',$variantId); @endphp
                <label class="wa-activation-item" wire:key="wa-item-{{ $variantId }}">
                    <input type="checkbox" wire:model="selectedVariants" value="{{ $variantId }}">
                    <span><strong>{{ $variant?->product?->name ?? 'Sản phẩm' }}</strong><small>{{ $variant?->name }} · Số lượng {{ $items->sum('quantity') }}</small>
                    @if($activation)<small class="wa-active-expiry">Đã kích hoạt · Hạn đến {{ $activation->expires_at->format('d/m/Y') }}</small>
                    @elseif((int)$variant?->warranty_days>0)<small>Bảo hành {{ $variant->warranty_days }} ngày từ ngày mua</small>
                    @else<small>Thời hạn bảo hành cần nhân viên xác nhận</small>@endif</span>
                </label>
            @endforeach
            <div class="wa-order-search-grid">
                <div class="wa-field-group"><label for="wa-confirm-name">Họ và tên</label><input id="wa-confirm-name" wire:model="activateFullName" autocomplete="name" required maxlength="120"></div>
                <div class="wa-field-group"><label for="wa-confirm-phone">Số điện thoại kích hoạt</label><input id="wa-confirm-phone" wire:model="activatePhone" type="tel" autocomplete="tel" required><span class="wa-field-hint">Nhập đúng số mua hàng. Khi mở bằng link riêng, bạn có thể cập nhật số nhận bảo hành.</span></div>
            </div>
            <div class="wa-field-group"><label for="wa-confirm-serial">Serial / định danh trên hộp (không bắt buộc)</label><input id="wa-confirm-serial" wire:model="activateSerial" maxlength="255"></div>
            <button type="submit" class="wa-submit-action-btn" wire:loading.attr="disabled">Xác nhận kích hoạt bảo hành</button>
            <a class="wa-lookup-result-link" href="/tra-cuu-bao-hanh?q={{ urlencode($activatePhone) }}">Tra cứu bảo hành theo số điện thoại →</a>
        </form>
        <button type="button" class="wa-inline-search-btn" wire:click="resetActivation">Tra cứu đơn khác</button>
    @endif
    @if(!$activateOrder && $activationLinkError === '')
        <details class="wa-fallback-panel" @if($fallbackSuccess) open @endif>
            <summary>Mất mã đơn hàng? Gửi yêu cầu xác minh</summary>
            <p>Tìm sản phẩm và gửi thông tin để nhân viên kiểm tra. Bước này chưa kích hoạt bảo hành.</p>
            @if($fallbackSuccess)<div class="wa-success-panel" role="status">Đã nhận yêu cầu, chờ nhân viên xác minh ngày mua. Bạn có thể liên hệ cửa hàng bằng số điện thoại đã gửi.</div>@else
                <div class="wa-field-group"><label for="wa-fallback-search">Tên sản phẩm</label><input id="wa-fallback-search" wire:model.live.debounce.400ms="fallbackSearch" placeholder="Nhập ít nhất 2 ký tự"></div>
                <div class="wa-fallback-results">
                    @foreach($matchingVariants as $variant)
                        <button type="button" class="wa-fallback-result {{ $activateVariantId===$variant->id ? 'is-selected':'' }}" wire:click="selectActivationVariant({{ $variant->id }})" aria-pressed="{{ $activateVariantId===$variant->id ? 'true':'false' }}"><strong>{{ $variant->product?->name }}</strong><small>{{ $variant->name }}</small></button>
                    @endforeach
                    @if(mb_strlen(trim($fallbackSearch))>=2 && $matchingVariants->isEmpty())<p>Chưa tìm thấy sản phẩm. Thử tên khác hoặc liên hệ cửa hàng.</p>@endif
                </div>
                @if($activateVariant)
                    <p><strong>Đã chọn:</strong> {{ $activateVariant->product?->name }} · {{ $activateVariant->name }}</p>
                    <form wire:submit="submitFallback" class="wa-fields-form">
                        <div class="wa-order-search-grid"><div class="wa-field-group"><label for="wa-fallback-name">Họ và tên</label><input id="wa-fallback-name" wire:model="activateFullName" required maxlength="120" autocomplete="name"></div><div class="wa-field-group"><label for="wa-fallback-phone">Số điện thoại</label><input id="wa-fallback-phone" wire:model="activatePhone" type="tel" required autocomplete="tel"></div></div>
                        <div class="wa-field-group"><label for="wa-fallback-serial">Serial trên hộp (không bắt buộc)</label><input id="wa-fallback-serial" wire:model="activateSerial" maxlength="255"></div>
                        <div class="wa-field-group"><label for="wa-fallback-notes">Thông tin mua hàng / ghi chú</label><textarea id="wa-fallback-notes" wire:model="activateNotes" rows="3" maxlength="500" placeholder="Ngày mua dự kiến, nơi mua hoặc thông tin giúp cửa hàng xác minh"></textarea></div>
                        <button type="submit" class="wa-submit-action-btn" wire:loading.attr="disabled">Gửi yêu cầu chờ duyệt</button>
                    </form>
                @endif
            @endif
        </details>
    @endif
</section>
<style>
.wa-activation-v2 .wa-order-search-grid { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
.wa-activation-v2 .wa-step-header { margin-bottom:8px; }
.wa-activation-v2 .wa-step-header h2,.wa-section-title { font-size:16px!important;margin:0 0 5px; }
.wa-activation-v2 .wa-step-header p,.wa-activation-v2 p { font-size:13px;line-height:1.7;color:#64748b; }
.wa-activation-v2 .wa-selected-order-box { margin-bottom:20px; }
.wa-activation-item { display:flex;align-items:center;gap:14px;border:1px solid #e2e8f0;padding:14px;border-radius:10px;cursor:pointer; }
.wa-activation-item input { width:18px;height:18px;accent-color:var(--wl-brand);flex-shrink:0; }
.wa-activation-item strong { font-size:14px; }
.wa-activation-item small { display:block;color:#64748b;font-size:12px;margin-top:5px; }
.wa-activation-item .wa-active-expiry { color:#15803d; }
.wa-lookup-result-link { color:var(--wl-brand);text-decoration:underline;font-size:13px; }
.wa-fallback-panel { border-top:1px solid #e2e8f0;margin-top:28px;padding-top:22px; }
.wa-fallback-panel summary { cursor:pointer;font-size:14px;font-weight:700;color:#334155; }
.wa-fallback-panel .wa-field-group { margin-top:14px; }
.wa-fallback-results { display:grid;grid-template-columns:1fr 1fr;gap:8px;max-height:240px;overflow:auto;margin:14px 0; }
.wa-fallback-result { text-align:left;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px;color:#334155;cursor:pointer; }
.wa-fallback-result strong,.wa-fallback-result small { display:block;font-size:12px; }
.wa-fallback-result small { color:#64748b;margin-top:6px; }
.wa-fallback-result.is-selected { border-color:var(--wl-brand);background:var(--wl-soft); }
@media(max-width:600px) { .wa-activation-v2 .wa-order-search-grid,.wa-fallback-results { grid-template-columns:1fr; } }
</style>
