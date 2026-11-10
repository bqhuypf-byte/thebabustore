@php
    $warrantySections = \App\Support\ProductContent::warrantySections(
        $settings?->warranty_policy ?: '<p>Liên hệ cửa hàng để được hướng dẫn điều kiện và quy trình bảo hành.</p>'
    );
@endphp
<div class="pd-warranty">
    <div class="warranty-hero">
        <div class="warranty-intro">
            <div class="warranty-intro-head">
                <span class="warranty-shield" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <div>
                    <h2>Chính sách & Hỗ trợ bảo hành</h2>
                    <p>Vui lòng liên hệ kỹ thuật và báo lỗi sản phẩm trước khi gửi về <b>1 trong 2 chi nhánh bảo hành</b> của <b>{{ $settings?->web_name ?: 'The BABU Store' }}</b>.</p>
                </div>
            </div>
        </div>

        <div class="warranty-contact">
            @if($settings?->hotline)
                <a href="tel:{{ preg_replace('/[^0-9+]/','',$settings->hotline) }}" class="warranty-card is-hotline">
                    <span class="warranty-contact-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <span class="warranty-contact-meta">
                        <small>Hotline kỹ thuật</small>
                        <b>{{ $settings->hotline }}</b>
                    </span>
                </a>
            @endif
            @if($settings?->zalo)
                <a href="{{ str_starts_with($settings->zalo, 'https://') ? $settings->zalo : 'https://zalo.me/'.preg_replace('/\D/','',$settings->zalo) }}" target="_blank" rel="noopener noreferrer" class="warranty-card is-zalo">
                    <span class="warranty-contact-icon zalo-logo" aria-hidden="true">
                        <span>Zalo</span>
                    </span>
                    <span class="warranty-contact-meta">
                        <small>Zalo báo lỗi</small>
                        <b>Hỗ trợ kỹ thuật trực tuyến</b>
                    </span>
                </a>
            @endif
            @if($settings?->email)
                <a href="mailto:{{ $settings->email }}" class="warranty-card is-email">
                    <span class="warranty-contact-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <span class="warranty-contact-meta">
                        <small>Email tiếp nhận</small>
                        <b>{{ $settings->email }}</b>
                    </span>
                </a>
            @endif
        </div>

        <div class="warranty-quick" aria-label="Thông tin bảo hành cần biết nhanh">
            <div class="warranty-quick-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                <span>Báo lỗi & hỗ trợ kỹ thuật trước khi gửi</span>
            </div>
            <div class="warranty-quick-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>Tiếp nhận tại 2 chi nhánh bảo hành</span>
            </div>
            <div class="warranty-quick-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Xử lý 7–30 ngày · Đổi mới trong 48h</span>
            </div>
        </div>
    </div>

    @if($selected?->warranty_days)<div class="note-box warranty-duration">Phiên bản <b>{{ $selected->name }}</b>: thời hạn bảo hành <b>{{ $warrantyLabel }}</b>, tính theo thông tin đơn hàng tại cửa hàng.</div>@endif

    <div class="warranty-policy-head">
        <h3>Nội dung chính sách</h3>
    </div>
    <div class="warranty-accordion">
        @forelse($warrantySections as $section)
            <details class="warranty-item" @if($loop->first) open @endif>
                <summary><span class="warranty-index">{{ $loop->iteration }}</span><b>{{ $section['title'] }}</b></summary>
                <div class="warranty-item-body pd-prose">{!! $section['html'] !!}</div>
            </details>
        @empty
            <p>Liên hệ cửa hàng để được hướng dẫn điều kiện và quy trình bảo hành.</p>
        @endforelse
    </div>
    <div class="note-box warranty-reminder">Giữ lại mã đơn hàng và cung cấp đúng tên sản phẩm, phiên bản đã mua để cửa hàng kiểm tra nhanh hơn.</div>
</div>
