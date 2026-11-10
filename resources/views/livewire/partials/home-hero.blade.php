@php
    $homeSettings = \App\Models\Setting::query()->first();
    $homeSlides = collect($slides)->filter(fn ($slide) => filled($slide->image))->values();
    if ($homeSlides->isEmpty() && $homeSettings?->banner_web) {
        $homeSlides = collect([(object) ['image' => $homeSettings->banner_web, 'link' => '/shop']]);
    }
@endphp
@if($homeSlides->isNotEmpty())
<section class="mc-hero" aria-label="Bộ sưu tập nổi bật" aria-roledescription="carousel"
    x-data="{
        active: 0, count: {{ $homeSlides->count() }}, timer: null, observer: null, paused: false, userPaused: false,
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
        destroy() { clearInterval(this.timer); this.observer?.disconnect(); }
    }" @mouseenter="paused = true" @mouseleave="paused = false" @focusin="paused = true" @focusout="paused = false"
    @keydown.arrow-right.prevent="active = (active + 1) % count" @keydown.arrow-left.prevent="active = (active + count - 1) % count">
    @foreach($homeSlides as $index => $slide)
        <a class="mc-hero-slide" href="{{ $slide->link ?: '/shop' }}" x-show="active === {{ $index }}" @if($index) x-cloak @endif
           :aria-hidden="active !== {{ $index }}" x-transition.opacity.duration.500ms aria-label="Khám phá bộ sưu tập {{ $index + 1 }}">
            <img src="{{ Storage::url($slide->image) }}" alt="Bộ sưu tập nổi bật {{ $index + 1 }}" @if($index === 0) fetchpriority="high" @else loading="lazy" @endif>
            <span class="mc-hero-cta">Khám phá ngay <span aria-hidden="true">→</span></span>
        </a>
    @endforeach
    @if($homeSlides->count() > 1)
        <div class="mc-hero-controls">
            <button class="mc-pause" type="button" @click="userPaused = !userPaused" :aria-label="userPaused ? 'Tiếp tục trình chiếu' : 'Tạm dừng trình chiếu'" x-text="userPaused ? '▶' : 'Ⅱ'"></button>
            @foreach($homeSlides as $index => $slide)
                <button class="mc-slide-dot" type="button" @click="active = {{ $index }}" :class="{ 'is-active': active === {{ $index }} }" :aria-current="active === {{ $index }} ? 'true' : 'false'" aria-label="Xem banner {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>
@endif