<div>
    @php
        $settings = \App\Models\Setting::first();
    @endphp

    <head>
        <title>Đăng nhập &amp; Đăng ký tài khoản | {{ $settings->web_name ?? 'The BABU Store' }}</title>
        <link rel="preload" as="style" type="text/css"
            href="/assets/100/436/596/themes/834446/assets/bootstrap.scss.css?1721662888170"
            onload="this.rel='stylesheet'" />
        <link href="/assets/100/436/596/themes/834446/assets/bootstrap.scss.css?1721662888170"
            rel="stylesheet" type="text/css" media="all" />
        <link rel="preload" as="style" type="text/css"
            href="/assets/100/436/596/themes/834446/assets/evo-watch.scss.css?1721662888170"
            onload="this.rel='stylesheet'" />
        <link href="/assets/100/436/596/themes/834446/assets/evo-watch.scss.css?1721662888170"
            rel="stylesheet" type="text/css" media="all" />
    </head>

    @livewire('inc.header')

    <main class="auth-page-main">
        <div class="auth-page-container">
            {{-- BREADCRUMB --}}
            <nav class="auth-breadcrumb" aria-label="Breadcrumb">
                <a href="/" title="Trang chủ">Trang chủ</a>
                <span class="auth-breadcrumb-sep">&gt;</span>
                <span class="auth-breadcrumb-cur">Đăng nhập &amp; Đăng ký</span>
            </nav>

            {{-- HEADER BANNER --}}
            <div class="auth-header-block">
                <div class="auth-portal-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Cổng Tài Khoản Khách Hàng</span>
                </div>
                <h1 class="auth-main-title">Chào Mừng Bạn Đến Với BABU</h1>
                <p class="auth-main-sub">
                    Đăng nhập để theo dõi đơn hàng, nhận ưu đãi thành viên và bảo hành nhanh chóng.
                </p>
            </div>

            {{-- MAIN AUTH CARD --}}
            <div class="auth-card">
                {{-- SEGMENTED TABS --}}
                <div class="auth-tabs-nav">
                    <button
                        type="button"
                        class="auth-tab-btn {{ $activeTab === 'login' ? 'is-active' : '' }}"
                        wire:click="setTab('login')"
                    >
                        Đăng Nhập
                    </button>
                    <button
                        type="button"
                        class="auth-tab-btn {{ $activeTab === 'register' ? 'is-active' : '' }}"
                        wire:click="setTab('register')"
                    >
                        Tạo Tài Khoản
                    </button>
                </div>

                {{-- TAB: ĐĂNG NHẬP --}}
                @if ($activeTab === 'login')
                    <form wire:submit.prevent="login" class="auth-form" novalidate>
                        <div class="auth-field-group">
                            <label for="auth-email">Email</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                </span>
                                <input
                                    id="auth-email"
                                    type="email"
                                    wire:model="email"
                                    placeholder="hgdigital.vn@gmail.com"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                            @error('email')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field-group">
                            <div class="auth-label-row">
                                <label for="auth-password">Mật khẩu</label>
                                <button type="button" class="auth-forgot-link" wire:click="setTab('forgot')">
                                    Quên mật khẩu?
                                </button>
                            </div>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input
                                    id="auth-password"
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    wire:model="password"
                                    placeholder="••••••••••"
                                    autocomplete="current-password"
                                    required
                                >
                                <button
                                    type="button"
                                    class="auth-toggle-pwd"
                                    wire:click="$toggle('showPassword')"
                                    title="{{ $showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu' }}"
                                    tabindex="-1"
                                >
                                    @if ($showPassword)
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            @error('password')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="auth-btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Đăng Nhập Ngay →</span>
                            <span wire:loading>Đang xác thực...</span>
                        </button>
                    </form>

                    {{-- HOẶC DÙNG NHANH --}}
                    <div class="auth-divider">
                        <span>HOẶC DÙNG NHANH</span>
                    </div>

                    {{-- ĐĂNG NHẬP MẪU --}}
                    <button type="button" class="auth-btn-demo" wire:click="loginDemoUser" wire:loading.attr="disabled">
                        <span class="auth-sparkle">✨</span>
                        <span>Đăng nhập tài khoản mẫu (Nguyễn Hoàng Nam)</span>
                    </button>

                    {{-- ĐĂNG NHẬP GOOGLE --}}
                    <div class="auth-social-row">
                        <a href="{{ route('google.login', ['id' => 1]) }}" class="auth-btn-google">
                            <svg width="17" height="17" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
                            </svg>
                            <span>Đăng nhập với Google</span>
                        </a>
                    </div>

                {{-- TAB: TẠO TÀI KHOẢN --}}
                @elseif ($activeTab === 'register')
                    <form wire:submit.prevent="register" class="auth-form" novalidate>
                        <div class="auth-field-group">
                            <label for="reg-name">Họ và tên</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input
                                    id="reg-name"
                                    type="text"
                                    wire:model="reg_name"
                                    placeholder="Nguyễn Văn A"
                                    autocomplete="name"
                                    required
                                >
                            </div>
                            @error('reg_name')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field-group">
                            <label for="reg-phone">Số điện thoại</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                </span>
                                <input
                                    id="reg-phone"
                                    type="tel"
                                    wire:model="reg_phone"
                                    placeholder="0912345678"
                                    autocomplete="tel"
                                    required
                                >
                            </div>
                            @error('reg_phone')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field-group">
                            <label for="reg-email">Email</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                </span>
                                <input
                                    id="reg-email"
                                    type="email"
                                    wire:model="reg_email"
                                    placeholder="example@gmail.com"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                            @error('reg_email')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field-group">
                            <label for="reg-password">Mật khẩu</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input
                                    id="reg-password"
                                    type="{{ $showRegPassword ? 'text' : 'password' }}"
                                    wire:model="reg_password"
                                    placeholder="Tối thiểu 6 ký tự"
                                    autocomplete="new-password"
                                    required
                                >
                                <button
                                    type="button"
                                    class="auth-toggle-pwd"
                                    wire:click="$toggle('showRegPassword')"
                                    title="{{ $showRegPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu' }}"
                                    tabindex="-1"
                                >
                                    @if ($showRegPassword)
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            @error('reg_password')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="auth-btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Tạo Tài Khoản Ngay →</span>
                            <span wire:loading>Đang xử lý...</span>
                        </button>
                    </form>

                    <div class="auth-social-row" style="margin-top: 16px;">
                        <a href="{{ route('google.login', ['id' => 1]) }}" class="auth-btn-google">
                            <svg width="17" height="17" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
                            </svg>
                            <span>Đăng ký nhanh với Google</span>
                        </a>
                    </div>

                {{-- TAB: QUÊN MẬT KHẨU --}}
                @elseif ($activeTab === 'forgot')
                    <form wire:submit.prevent="sendPasswordReset" class="auth-form" novalidate>
                        <div class="auth-field-group">
                            <label for="forgot-email">Email tài khoản của bạn</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                </span>
                                <input
                                    id="forgot-email"
                                    type="email"
                                    wire:model="forgot_email"
                                    placeholder="Nhập email đã đăng ký..."
                                    required
                                >
                            </div>
                            @error('forgot_email')
                                <span class="auth-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($forgot_sent)
                            <div class="auth-alert-info">
                                Nếu email khớp với tài khoản trong hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu đến hộp thư của bạn.
                            </div>
                        @endif

                        <button type="submit" class="auth-btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Gửi Yêu Cầu Khôi Phục</span>
                            <span wire:loading>Đang gửi...</span>
                        </button>

                        <div style="text-align: center; margin-top: 16px;">
                            <button type="button" class="auth-back-text-btn" wire:click="setTab('login')">
                                ← Quay lại Đăng Nhập
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </main>

    @livewire('inc.footer')

    <style>
        .auth-page-main,
        .auth-page-main * {
            font-family: -apple-system, BlinkMacSystemFont, "Roboto", "Segoe UI", Helvetica, Arial, sans-serif !important;
            box-sizing: border-box;
        }
        .auth-page-main {
            min-height: calc(100vh - 420px);
            background: #ffffff;
            padding: 30px 16px 80px;
            color: #0f172a;
        }
        .auth-page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Breadcrumb matching Image 1: "Trang chủ > Đăng nhập & Đăng ký" */
        .auth-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px !important;
            color: #64748b;
            margin-bottom: 28px;
        }
        .auth-breadcrumb a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .auth-breadcrumb a:hover {
            color: #0f172a;
        }
        .auth-breadcrumb-sep {
            color: #94a3b8;
            font-size: 12px;
        }
        .auth-breadcrumb-cur {
            color: #0f172a;
            font-weight: 600 !important;
        }

        /* Header block */
        .auth-header-block {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-portal-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 16px;
            border-radius: 9999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 13px !important;
            font-weight: 600 !important;
            margin-bottom: 14px;
        }
        .auth-main-title {
            margin: 0 0 10px;
            font-size: 24px !important;
            font-weight: 800 !important;
            color: #0f172a;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }
        .auth-main-sub {
            margin: 0 auto;
            max-width: 540px;
            font-size: 14px !important;
            color: #64748b;
            line-height: 1.5;
        }

        /* Main Card */
        .auth-card {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            box-sizing: border-box;
        }

        /* Tabs Nav */
        .auth-tabs-nav {
            display: flex;
            background: #f1f5f9;
            border-radius: 9999px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }
        .auth-tab-btn {
            flex: 1;
            height: 40px;
            border-radius: 9999px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 14px !important;
            font-weight: 500 !important;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-tab-btn:hover {
            color: #0f172a;
        }
        .auth-tab-btn.is-active {
            background: #ffffff;
            color: #0f172a;
            font-weight: 700 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Form Controls */
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .auth-field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .auth-field-group label {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #1e293b;
            margin: 0;
        }
        .auth-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .auth-forgot-link {
            background: none;
            border: none;
            padding: 0;
            font-size: 12.5px !important;
            color: #64748b;
            font-weight: 500 !important;
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .auth-forgot-link:hover {
            color: #0f172a;
            text-decoration: underline;
        }

        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .auth-input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            pointer-events: none;
        }
        .auth-input-wrap input {
            width: 100%;
            height: 46px;
            padding: 0 42px 0 42px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px !important;
            color: #0f172a;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            box-sizing: border-box;
        }
        .auth-input-wrap input:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }
        .auth-input-wrap input::placeholder {
            color: #94a3b8;
            font-size: 13.5px;
        }
        .auth-toggle-pwd {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }
        .auth-toggle-pwd:hover {
            color: #0f172a;
        }
        .auth-error-msg {
            font-size: 12px !important;
            color: #dc2626;
            font-weight: 500 !important;
        }

        /* Primary Submit Button (Solid Black Pill) */
        .auth-btn-primary {
            height: 48px;
            width: 100%;
            border-radius: 9999px;
            background: #0f172a;
            color: #ffffff;
            border: none;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.1s ease;
            margin-top: 4px;
        }
        .auth-btn-primary:hover {
            background: #1e293b;
        }
        .auth-btn-primary:active {
            transform: scale(0.99);
        }
        .auth-btn-primary[disabled] {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            margin: 22px 0;
            width: 100%;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .auth-divider span {
            padding: 0 14px;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #94a3b8;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        /* Demo Login Button */
        .auth-btn-demo {
            width: 100%;
            height: 44px;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .auth-btn-demo:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .auth-sparkle {
            font-size: 15px;
        }

        /* Google Social Button */
        .auth-social-row {
            margin-top: 10px;
        }
        .auth-btn-google {
            width: 100%;
            height: 42px;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 13px !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none !important;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .auth-btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* Forgot password helpers */
        .auth-alert-info {
            padding: 12px 14px;
            border-radius: 8px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 13px !important;
            line-height: 1.45;
        }
        .auth-back-text-btn {
            background: none;
            border: none;
            color: #64748b;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .auth-back-text-btn:hover {
            color: #0f172a;
        }

        @media (max-width: 640px) {
            .auth-page-main {
                padding: 20px 12px 60px;
            }
            .auth-main-title {
                font-size: 24px !important;
            }
            .auth-card {
                padding: 20px 16px;
                border-radius: 16px;
            }
        }
    </style>
</div>
