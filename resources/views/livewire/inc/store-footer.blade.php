<footer class="store-footer-root">
    @php
        $footerSettings = \App\Models\Setting::query()->first();
        $rawCompanyDesc = trim((string) ($footerSettings?->company_description ?? ''));
        $footerBio = ($rawCompanyDesc !== '' && !str_contains(mb_strtolower($rawCompanyDesc), 'hiệp thành'))
            ? $rawCompanyDesc
            : 'Chuyên cung cấp bàn phím cơ, switch, keycap và phụ kiện setup chính hãng.';
    @endphp
    <div class="store-footer-grid">
        <div>
            <a class="store-footer-logo" href="/"><span>B</span><strong>THE BABU STORE</strong></a>
            <p>{{ $footerBio }}</p>
            @if($footerSettings?->address)
                <p class="store-footer-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $footerSettings->address }}</span></p>
            @endif
            @if($footerSettings?->hotline)
                <p class="store-footer-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg><a href="tel:{{ preg_replace('/\D+/', '', $footerSettings->hotline) }}">{{ $footerSettings->hotline }}</a></p>
            @endif
            @if($footerSettings?->email)
                <p class="store-footer-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg><a href="mailto:{{ $footerSettings->email }}">{{ $footerSettings->email }}</a></p>
            @endif
        </div>
        <div>
            <h3>MUA SẮM & DANH MỤC</h3>
            <a href="/about">Giới thiệu thương hiệu</a>
            <a href="/chinh-sach-bao-hanh">Chính sách bảo hành</a>
            <a href="/chinh-sach-doi-tra-hang-hoan-tien">Chính sách đổi trả</a>
            <a href="/chinh-sach-bao-mat">Chính sách bảo mật</a>
            <a href="/chinh-sach-thanh-toan">Hướng dẫn thanh toán</a>
            <a href="/orders">Tra cứu tình trạng đơn hàng</a>
            <a href="/tra-cuu-bao-hanh">Tra cứu bảo hành điện tử</a>
        </div>
        <div>
            <h3>TÀI KHOẢN & ĐƠN HÀNG</h3>
            <a href="/cart">Giỏ hàng của bạn</a>
            <a href="/checkout">Thanh toán đơn hàng</a>
            <a href="/orders">Quản lý & Tra cứu đơn hàng</a>
            <a href="/account/login">Đăng nhập / Đăng ký tài khoản</a>
            <a href="/blogs">Hướng dẫn mua hàng</a>
        </div>
        <div>
            <h3>HỖ TRỢ & KẾT NỐI</h3>
            <a href="/shop">Tất cả sản phẩm chính hãng</a>
            <a href="/shop?sort=discount">Khuyến mãi Flash Sale</a>
            @if($footerSettings?->facebook)<a href="{{ $footerSettings->facebook }}" target="_blank" rel="noopener">Fanpage Facebook ↗</a>@endif
            @if($footerSettings?->zalo)<a href="{{ $footerSettings->zalo }}" target="_blank" rel="noopener">Hỗ trợ qua Zalo ↗</a>@endif
            <a href="/hg-digital">Trang quản trị (Admin CMS) ↗</a>
        </div>
    </div>
    <div class="store-footer-bottom">
        <span>© {{ now()->year }} The BABU Store.</span>
        <div>
            <a href="/shop">Sản phẩm</a>
            <a href="/cart">Giỏ hàng</a>
            <a href="/orders">Đơn hàng</a>
            <a href="/account">Tài khoản</a>
        </div>
    </div>
    <style>
        .store-footer-root,.store-footer-root *{box-sizing:border-box;font-family:StoreGeist,Geist,Arial,sans-serif!important;letter-spacing:normal}.store-footer-root{background:#fff;color:#6b7280;border-top:1px solid #e5e5e5;padding:42px max(24px,calc((100vw - 1152px)/2)) 24px;font-size:12px;line-height:1.6}.store-footer-root a{color:inherit;text-decoration:none}.store-footer-root a:hover{color:#171717}.store-footer-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:32px}.store-footer-logo{display:flex;gap:9px;align-items:center;color:#171717!important}.store-footer-logo span{display:grid;place-items:center;width:25px;height:25px;color:#fff;background:#171717;border-radius:6px}.store-footer-root h3{font-size:12px;color:#171717;margin:0 0 16px;font-weight:700}.store-footer-root p{margin:12px 0;max-width:280px}.store-footer-item{display:flex;align-items:flex-start;gap:8px}.store-footer-item svg{width:16px;height:16px;flex:0 0 16px;margin-top:3px;color:#1240f7}.store-footer-item a,.store-footer-item span{word-break:break-word}.store-footer-grid>div>a:not(.store-footer-logo){display:block;margin:7px 0}.store-footer-bottom{margin-top:32px;padding-top:20px;border-top:1px solid #eee;display:flex;justify-content:space-between;gap:20px}.store-footer-bottom div{display:flex;gap:18px}@media(max-width:700px){.store-footer-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:25px}.store-footer-bottom{flex-direction:column}.store-footer-bottom div{flex-wrap:wrap}}
    </style>
</footer>
