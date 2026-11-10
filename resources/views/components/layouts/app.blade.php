<!DOCTYPE html>
@php
$settings = App\Models\Setting::first(); // Truy vấn model Settings
$seoProduct = request()->is('product/*', 'products/*') && request()->route('slug')
    ? App\Models\Product::with(['category', 'variants'])->where('slug', request()->route('slug'))->first()
    : null;
if ($seoProduct) {
    $productSeo = \App\Support\ProductSeo::metadata($seoProduct, $settings);
    $seoTitle = $productSeo['title'];
    $seoDescription = $productSeo['description'];
    $seoImage = ($seoProduct->images ?: [])[0] ?? null;
}
@endphp
<html lang="{{ $seoProduct ? 'vi' : str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ Storage::url($settings->web_icon)}}" type="image/png">
    <meta name="google" content="notranslate">

    @if($seoProduct)
        <link rel="stylesheet" href="{{ asset('css/product-muse.css') }}?v={{ filemtime(public_path('css/product-muse.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/product-muse-bridge.css') }}?v={{ filemtime(public_path('css/product-muse-bridge.css')) }}">
        <title>{{ $seoTitle }}</title>
        <meta name="description" content="{{ $seoDescription }}">
        <link rel="canonical" href="{{ url('/product/'.$seoProduct->slug) }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        @if($seoImage)<meta property="og:image" content="{{ asset('storage/'.$seoImage) }}">@endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        @if($seoImage)<meta name="twitter:image" content="{{ asset('storage/'.$seoImage) }}">@endif
        @if(!$seoProduct->is_active)<meta name="robots" content="noindex,follow">@endif
        <script type="application/ld+json">{!! json_encode(\App\Support\ProductSeo::schema($seoProduct, $settings), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif


    <meta property="og:type" content="{{ $seoProduct ? 'product' : 'website' }}">
    <meta property="og:url" content="{{ $seoProduct ? url('/product/'.$seoProduct->slug) : request()->fullUrl() }}">
    <meta name="twitter:url" content="{{ $seoProduct ? url('/product/'.$seoProduct->slug) : request()->fullUrl() }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="google-site-verification" content="Otn01gb0LRdxp72pl915reM6YtB358zdpnUdocErXtY" />
    @livewireStyles
</head>

<body>
    <script>
        if (!['localhost', '127.0.0.1', '[::1]'].includes(window.location.hostname)) {
            fetch('https://hgdigital.vn/traffic/' + window.location.hostname).catch(() => {});
        }
    </script>
   
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Loading Screen Styles */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* Ensure it is on top of other content */
        }

        .loading-spinner {
            border: 8px solid #f3f3f3;
            /* Light grey */
            border-top: 8px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        document.addEventListener('livewire:navigated', function() {
            // Re-enable scroll after loading
            document.getElementById('loadingScreen')?.remove();
        });
    
    </script>
    <livewire:search-palette />
    {{ $slot }}

    @livewireScripts
    <div class="gtranslate_wrapper"></div>
    <script>
        window.gtranslateSettings = {
        "default_language": "vi",
        "wrapper_selector": ".gtranslate_wrapper",
        "flag_style":  "3d",
        "alt_flags": { 
            "en": "usa",
        },
        "languages": ["vi", "en"], // Chỉ định các ngôn ngữ cần hỗ trợ
        "language_codes": { "vi": "vi", "en": "en" }, // Đảm bảo tiếng Việt là mặc định
    };

    // Đặt ngôn ngữ mặc định là tiếng Việt
    document.documentElement.lang = "vi";
    </script>
    <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>


    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets_client/frontend/common/js/slick.min.js?v=1721231848" defer></script>
    <x-livewire-alert::scripts />

    <style>
        .section_product .mobile-viewmore {
            margin-top: 15px;
            font-weight: bold;
            font-family: "Roboto", sans-serif;
        }

        * {
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            letter-spacing: inherit;
        }

        header .nav-item>a {
            color: #1c1c1c;
            font-size: 14px;
            padding: 10px;
            font-weight: bold;
            text-transform: initial;
            letter-spacing: inherit;
            text-align: center;
        }

        .content-box__row:last-child .content-box__row__desc {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            border-bottom-width: 1px;
            text-transform: uppercase;
        }

        .section_product .mobile-viewmore {
            margin-top: 15px;

        }

        .section_product .section-head .title_blog a {
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .section_blogs .mobile-viewmore {
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .section_product .mobile-viewmore {
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        footer.footer .site-footer .footer-widget .list-menu li a {
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .section_product .mobile-viewmore {
            font-weight: 400;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .btn-blues::before,
        #btnnewreview::before,
        .evo-button::before {
            position: absolute;
            content: '';
            display: block;
            left: 0;
            top: 0;
            border-radius: 10px;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            -webkit-transform: scale(1, 1);
            transform: scale(1, 1);
            -webkit-transform-origin: left center;
            transform-origin: left center;
            z-index: -1;
            -webkit-transition: -webkit-transform 0.45s cubic-bezier(0.785, 0.135, 0.15, 0.86);
            transition: -webkit-transform 0.45s cubic-bezier(0.785, 0.135, 0.15, 0.86);
            transition: transform 0.45s cubic-bezier(0.785, 0.135, 0.15, 0.86);
            transition: transform 0.45s cubic-bezier(0.785, 0.135, 0.15, 0.86), -webkit-transform 0.45s cubic-bezier(0.785, 0.135, 0.15, 0.86);
            background-color: #ffffff;
            color: black;
        }

        .section_product .mobile-viewmore {
            font-weight: 400;
            border-radius: 10px;
            font-family: "Roboto", sans-serif;
            color: black;
            text-transform: inherit;
            letter-spacing: initial;
        }

        .section_blogs .mobile-viewmore {
            font-weight: 400;
            border-radius: 10px;
            color: black;
            text-transform: inherit;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        footer.footer .site-footer .footer-widget.footer-subcrible form button {
            width: 100%;
            padding-top: 0;
            padding-bottom: 0;
            /* background-color: black; */
            color: black;
        }

        * {
            font-weight: 400;
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .product-card form .product-card__actions .ajax_addtocart {
            height: 46px;
            font-size: 13px;
            letter-spacing: 1px;
            font-weight: 500;
            flex: 1;
            padding: 0 10px;
            justify-content: center;
            border: 1px solid #1c1c1c;
            background: #fff;
            color: #1c1c1c;
            line-height: 44px;
            font-family: "Roboto", sans-serif;
            border-radius: 10px;
            letter-spacing: initial;
            font-weight: 400;
        }

        .product-card .product-card__inner .product-card__title {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
            font-weight: bold;
        }

        .section_blogs a h3 {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
            font-weight: bold;
        }

        .details-product .details-pro .product-top .title-head {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .details-product .details-pro .product-top .vendor-product {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .details-product .product-tab .tabs-title li {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .evo-blog-header .evo-blog-header-content h1 {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .evo-article .title-head {
            font-family: "Roboto", sans-serif;
            letter-spacing: initial;
        }

        .page-login #login .btn-blues,
        .page-login #customer-reset-password .btn-blues,
        .page-login #recover-password .btn-blues {

            color: black;
        }
        .section_product .section-head .title_blog a strong {
    font-weight: bold;
}
.section_blogs h2 a strong {
    font-weight: bold;
}
button.button.btn.btn-large.btn-block.btn-danger.btn-checkout.evo-button {
    color: black;
    font-family: "Roboto", sans-serif;
    letter-spacing: initial;
}
#right-affix .btn-checkout:hover, #right-affix .btn-checkout:focus {
    background-color: #000000;
    color: #ffffff;
    border-color: #000000;
}
    </style>
    <style>
        /* Điểm nhấn xanh theo nhận diện BABU; giữ nền sáng và chữ dễ đọc. */
        :root { --babu-blue: #1240f7; --babu-blue-dark: #0c2fbf; --babu-blue-light: #eef1ff; }
        .store-logo-mark, .store-footer-logo span { background: var(--babu-blue) !important; }
        .store-nav a:hover, .store-mobile-nav a:hover, .store-footer-root a:hover { color: var(--babu-blue) !important; }
        .store-cart, .ms-product-cart, .ms-products-more a, .ss-card-bottom button,
        .ss-chips button.active, .store-pagination-pages button.active,
        .pd-actions button:last-child, .pd-review-form button,
        .qv-actions button:last-child, .ms-preview-actions button:last-child { background: var(--babu-blue) !important; border-color: var(--babu-blue) !important; color: #fff !important; }
        .store-cart:hover, .ms-product-cart:hover, .ms-products-more a:hover,
        .ss-card-bottom button:hover, .pd-actions button:last-child:hover,
        .qv-actions button:last-child:hover, .ms-preview-actions button:last-child:hover { background: var(--babu-blue-dark) !important; }
        .ms-category-label, .ms-products-label, .ss-pill { color: var(--babu-blue) !important; background: var(--babu-blue-light); border-color: #dedbff !important; }
        .ss-layout aside button.active, .pd-related > div:first-child a, .qv-top a { color: var(--babu-blue) !important; }
        .qv-variants button.selected, .ms-preview-variants button.selected, .pd-variants button.selected,
        .qv-thumbs button.selected, .ms-preview-thumbnails button.selected, .pd-thumbnails button.selected { border-color: var(--babu-blue) !important; }
        .ms-flash { background: linear-gradient(115deg, #10164d, var(--babu-blue)) !important; }
        .ms-flash-kicker { color: #c9d5ff !important; }
        .ms-flash-body strong { color: var(--babu-blue) !important; }
        .ms-flash-body button { background: var(--babu-blue) !important; }
        .ms-flash-heading p, .ms-flash-empty span { color: #e0e4ff !important; }

        /* Tăng độ dễ đọc trong cửa hàng, giữ nguyên phông chữ sẵn có. */
        .store-promo, .store-logo small, .store-search-label, .store-footer-root,
        .ss-breadcrumb, .sc-breadcrumb, .pd-breadcrumb { font-size: 13px !important; }
        .store-logo strong, .store-nav a, .store-header-actions a, .store-mobile-nav a,
        .store-footer-root h3, .store-drawer-item-info, .store-drawer-name,
        .store-drawer-item-total, .store-drawer-footer p, .store-drawer-submit,
        .ss-heading p, .ss-controls input, .ss-controls select, .ss-chips button,
        .ss-layout aside button, .ss-layout aside label, .ss-pill,
        .store-pagination, .sc-muted, .sc-info, .sc-checkout,
        .pd-page, .pd-tabs button, .pd-benefit, .qv-info,
        .ms-category-label, .ms-products-label, .ms-section-head > a,
        .ms-preview-details p, .ms-preview-details h3, .ms-preview-variants button,
        .ms-preview-top, .ms-flash-heading p, .ms-flash-empty span { font-size: 14px !important; }
        .ms-product-info > a, .ms-product-price b, .ss-name, .ss-card-bottom strong,
        .store-drawer-footer > div, .store-drawer-heading h2 { font-size: 16px !important; }
        .ms-category-description { font-size: 14px !important; }
        .store-footer-root { line-height: 1.7; }
        .store-drawer-name, .ss-name, .ms-product-info > a { line-height: 1.45; }
        @media (min-width: 851px) and (max-width: 1200px) {
            .store-nav, .store-header-actions { gap: 12px; }
            .store-search { width: 36px; justify-content: center; padding: 0; }
            .store-search-label, .store-search kbd { display: none; }
        }
        /* Cỡ chữ đọc và điều khiển thống nhất trên các trang dành cho khách hàng. */
        body { font-size: 16px; }
        main :is(p, label, input:not([type=radio]):not([type=checkbox]), textarea, select, li, small),
        .store-header-root :is(.store-nav a, .store-header-actions a, .store-mobile-nav a, .store-search-label),
        .store-footer-root, .store-footer-root h3,
        .ss-layout aside :is(button, label), .ss-chips button, .ss-controls :is(input, select),
        .sc-container :is(button, a, span), .co-container :is(button, label, input, select, textarea, p, span),
        .pd-container :is(button, label, input, select, textarea, p),
        .qv-dialog :is(button, p, small), .ms-preview :is(button, p, small),
        .store-drawer :is(p, a, button, span), .store-pagination,
        .palette-card :is(input, a, kbd),
        .palette-card [style*="font-size: 11px"], .palette-card [style*="font-size: 12px"],
        .palette-card [style*="font-size: 13px"], .palette-card [style*="font-size: 14px"] { font-size: 16px !important; }
        .store-drawer-quantity button { font-size: 18px !important; }
        .store-search-label { font-size: 15px !important; }
        .store-logo small, .store-promo, .store-tag, .ms-category-count,
        .ss-layout aside h3, .ss-breadcrumb, .sc-breadcrumb, .pd-breadcrumb,
        .co-breadcrumb { font-size: 14px !important; }
        .ss-name, .ms-product-info > a, .ss-card-bottom strong, .ms-product-price b { font-size: 17px !important; }
        .store-drawer-name { font-size: 16px !important; }
        .store-header-root .store-drawer-item-total, .store-drawer-footer > div { font-size: 16px !important; }
        .palette-card > div:last-child { flex-wrap: wrap; gap: 8px; }
        /* Giá bán và tổng tiền dùng cùng một màu để khách dễ nhận biết. */
        .ms-product-price b, .ms-flash-body strong, .ms-preview-price,
        .ms-preview-variants button small, .ss-card-bottom strong,
        .pd-price, .pd-variants button strong, .pd-related-grid a > span,
        .qv-price, .qv-variants button small, .store-drawer-item-info > span,
        .store-drawer-item-total, .store-drawer-footer strong,
        .sc-info > span, .sc-item-total strong, .sc-summary > div strong,
        .co-item > span, .co-row strong { color: #d92332 !important; font-weight: 800 !important; }
        .ms-product-price del { color: #777 !important; }
    </style>
    @include('components.store-support')
</body>

</html>
