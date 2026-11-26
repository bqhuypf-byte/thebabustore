@php
    $homeSettings = \App\Models\Setting::query()->first();
    $campaignPath = resource_path('data/homepage-campaigns.json');
    $campaigns = is_file($campaignPath) ? json_decode(ltrim(file_get_contents($campaignPath), "\xEF\xBB\xBF"), true) : [];
    $homeSlides = collect($slides)->filter(fn ($slide) => filled($slide->image))->values();
    if ($homeSlides->isEmpty() && $homeSettings?->banner_web) {
        $homeSlides = collect([(object) ['image' => $homeSettings->banner_web, 'link' => '/shop']]);
    }
@endphp
@if($homeSlides->isNotEmpty())
<section class="mc-hero" aria-label="Bộ sưu tập nổi bật" aria-roledescription="carousel"
    x-data="{
        active: 0, count: {{ $homeSlides->count() }}, timer: null, observer: null, paused: false, userPaused: false, touchStart: null, suppressClickUntil: 0,
        init() {
            const header = this.$root.closest('.modern-storefront').querySelector('.store-header-root');
            const measure = () => this.$root.style.setProperty('--mc-header-height', `${header?.offsetHeight || 0}px`);
            measure();
            this.observer = new ResizeObserver(measure);
            if (header) this.observer.observe(header);
            if (this.count > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.timer = setInterval(() => { if (!this.paused && !this.userPaused && !document.hidden) this.active = (this.active + 1) % this.count; }, 6500);
            }
        },
        startSwipe(event) {
            this.touchStart = event.touches.length === 1 ? { x: event.touches[0].clientX, y: event.touches[0].clientY, active: this.active } : null;
        },
        endSwipe(event) {
            const start = this.touchStart;
            this.touchStart = null;
            if (!start || !event.changedTouches.length || this.count < 2) return;
            const dx = event.changedTouches[0].clientX - start.x;
            const dy = event.changedTouches[0].clientY - start.y;
            if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy) * 1.3) return;
            this.active = (start.active + (dx < 0 ? 1 : -1) + this.count) % this.count;
            this.suppressClickUntil = Date.now() + 500;
        },
        destroy() { clearInterval(this.timer); this.observer?.disconnect(); }
    }" @mouseenter="paused = true" @mouseleave="paused = false" @focusin="paused = true" @focusout="paused = false"
    @touchstart.passive="startSwipe($event)" @touchend.passive="endSwipe($event)" @touchcancel.passive="touchStart = null" @click.capture="if (Date.now() < suppressClickUntil) { $event.preventDefault(); $event.stopPropagation(); }"
    @keydown.arrow-right.prevent="active = (active + 1) % count" @keydown.arrow-left.prevent="active = (active + count - 1) % count">
    @foreach($homeSlides as $index => $slide)
        @php $campaignAlt = $campaigns[basename($slide->image)]['alt'] ?? ('Bộ sưu tập nổi bật '.($index + 1)); @endphp
        <a class="mc-hero-slide" href="{{ $slide->link ?: '/shop' }}" x-show="active === {{ $index }}" @if($index) x-cloak @endif
           :aria-hidden="active !== {{ $index }}" x-transition.opacity.duration.500ms aria-label="{{ $campaignAlt }}">
            @php $mobileImage = preg_replace('/\\.webp$/', '-mobile.webp', $slide->image); $hasMobileImage = $mobileImage !== $slide->image && Storage::disk('public')->exists($mobileImage); @endphp
            <picture>@if($hasMobileImage)<source media="(max-width: 600px)" srcset="{{ Storage::url($mobileImage) }}">@endif
            <img src="{{ Storage::url($slide->image) }}" alt="{{ $campaignAlt }}" @if($index === 0) fetchpriority="high" @else loading="lazy" @endif></picture>
        </a>
    @endforeach
    @if($homeSlides->count() > 1)
        <div class="mc-hero-dots" role="group" aria-label="Chọn banner">
            @foreach($homeSlides as $index => $slide)
                <button class="mc-hero-dot" type="button" @click="active = {{ $index }}" :class="{ 'is-active': active === {{ $index }} }" :aria-current="active === {{ $index }} ? 'true' : 'false'" aria-label="Xem banner {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>
@endif