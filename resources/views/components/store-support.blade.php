@php
    $supportUrl = static function ($value, $phoneService = false) {
        $value = trim((string) $value);
        if ($phoneService && preg_match('/^[0-9+ .-]{8,20}$/', $value)) return 'https://zalo.me/'.preg_replace('/\D+/', '', $value);
        return filter_var($value, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($value, PHP_URL_SCHEME) ?: ''), ['http', 'https'], true) ? $value : null;
    };
    $supportFacebook = $supportUrl($settings?->messenger) ?: $supportUrl($settings?->facebook);
    $supportZalo = $supportUrl($settings?->zalo, true);
    $supportPhone = trim((string) $settings?->hotline);
    $supportPhoneLink = preg_replace('/[^0-9+]/', '', $supportPhone);
@endphp
@if($supportFacebook || $supportZalo || $supportPhoneLink)
    <aside
        class="store-support"
        aria-label="Liên hệ hỗ trợ"
        x-data="{ supportOpen: false }"
        @click.outside="supportOpen = false"
        :class="{ 'is-expanded': supportOpen }"
    >
        <div class="store-support-menu" x-cloak x-show="supportOpen" x-transition.opacity.duration.150ms>
            @if($supportFacebook)
                <a href="{{ $supportFacebook }}" target="_blank" rel="noopener noreferrer">
                    <span class="store-support-icon fb" aria-hidden="true">
                        <svg viewBox="0 0 32 32"><path fill="#fff" d="M4.6 21.7 13 12.8c.9-1 2.3-1.1 3.3-.4l3.7 2.8 6.6-3.6-8.3 8.8c-.9 1-2.3 1.1-3.3.4l-3.7-2.8-6.7 3.7Z"/></svg>
                    </span>
                    <span>
                        <strong>Chat Facebook</strong>
                        @if($settings?->support_hours)<small>({{ $settings->support_hours }})</small>@endif
                    </span>
                </a>
            @endif
            @if($supportZalo)
                <a href="{{ $supportZalo }}" target="_blank" rel="noopener noreferrer">
                    <span class="store-support-icon zalo">Zalo</span>
                    <span>
                        <strong>Chat Zalo</strong>
                        @if($settings?->support_hours)<small>({{ $settings->support_hours }})</small>@endif
                    </span>
                </a>
            @endif
            @if($supportPhoneLink)
                <a href="tel:{{ $supportPhoneLink }}">
                    <span class="store-support-icon phone" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.1.36 2.3.55 3.6.55.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C10.6 21 3 13.4 3 4c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.19 2.46.55 3.59a1 1 0 0 1-.25 1.02l-2.2 2.19Z"/></svg>
                    </span>
                    <span>
                        <strong>{{ $supportPhone }}</strong>
                        @if($settings?->support_hours)<small>({{ $settings->support_hours }})</small>@endif
                    </span>
                </a>
            @endif
        </div>

        <button
            class="store-support-toggle"
            type="button"
            @click="supportOpen = !supportOpen"
            :aria-expanded="supportOpen.toString()"
            aria-label="Mở hoặc đóng liên hệ hỗ trợ"
        >
            <svg x-show="!supportOpen" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <svg x-cloak x-show="supportOpen" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
            <span>Liên hệ</span>
        </button>
    </aside>
    <style>
        .store-support, .store-support * { box-sizing: border-box; font-family: StoreGeist, Geist, Arial, sans-serif; }
        .store-support [x-cloak] { display: none !important; }
        .store-support { position: fixed; z-index: 50; right: 20px; bottom: 22px; display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
        .store-support-menu { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }
        .store-support a { display: flex; align-items: center; gap: 10px; width: 196px; min-height: 54px; padding: 8px 13px; border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; color: #171717; text-decoration: none; box-shadow: 0 8px 26px rgba(15, 23, 42, 0.14); transition: transform .15s ease, border-color .15s ease; }
        .store-support a:hover { border-color: #1240f7; transform: translateY(-1px); }
        .store-support a > span:last-child { display: grid; gap: 2px; min-width: 0; }
        .store-support strong { font-size: 14px; line-height: 1.25; color: #171717; font-weight: 700 !important; }
        .store-support small { font-size: 12px; color: #e52418; }
        .store-support-icon { display: grid; place-items: center; flex: 0 0 38px; width: 38px; height: 38px; border-radius: 50%; font-weight: 800; }
        .store-support-icon.fb { background: linear-gradient(135deg, #ff526d 8%, #ca31de 48%, #146bff 95%); color: #fff; }
        .store-support-icon.fb svg { width: 26px; height: 26px; }
        .store-support-icon.zalo { background: #fff; color: #0068ff; border-left: 3px solid #0068ff; border-bottom: 2px solid #0068ff; font-size: 12.5px; font-weight: 800 !important; }
        .store-support-icon.phone { background: #fff; border: 1px solid #e5e7eb; color: #e52418; }
        .store-support-icon.phone svg { width: 19px; height: 19px; }
        .store-support-toggle { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 48px; padding: 0 18px; border: 0; border-radius: 999px; background: var(--babu-blue, #1240f7); color: #fff; font-size: 14.5px; font-weight: 700 !important; cursor: pointer; box-shadow: 0 8px 24px rgba(18, 64, 247, 0.32); transition: background .15s ease, transform .15s ease; }
        .store-support-toggle:hover { background: var(--babu-blue-dark, #0c2fbf); transform: translateY(-1px); }
        .store-support-toggle span { font-weight: 700 !important; color: #fff; line-height: 1; }
        @media (max-width: 640px) {
            .store-support { right: 14px; bottom: 84px; }
            .store-support-toggle { height: 44px; padding: 0 15px; font-size: 13.5px; }
        }
    </style>
@endif
