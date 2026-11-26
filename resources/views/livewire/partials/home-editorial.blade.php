@php $homeSettings = \App\Models\Setting::query()->first(); $homeSections = $homeSettings?->homepage_sections ?? \App\Support\HomepageContent::defaults($homeSettings); @endphp
<div class="mc-editorial-stack">
@foreach(collect($homeSections)->reject(fn ($entry) => in_array($entry['layout'] ?? '', ['setup', 'newsletter'], true))->sortBy(fn ($entry) => ($entry['layout'] ?? '') === 'faq' ? 1 : 0) as $section)
    @if(($section['enabled'] ?? true) && filled($section['title'] ?? null) && !str_contains(mb_strtoupper($section['title']), 'BABU HUB') && mb_strtolower(trim($section['title'])) !== 'cùng kết nối. cùng chia sẻ.' && ($section['layout'] ?? '') !== 'faq')
        @php $layout = in_array($section['layout'] ?? '', ['split', 'banner', 'catalog', 'cards', 'faq'], true) ? $section['layout'] : 'split'; @endphp
        @php $fullLink = in_array($layout, ['split', 'banner', 'catalog'], true) && ($section['full_link'] ?? false) && filled($section['link'] ?? null); @endphp
        <section class="mc-editorial mc-layout-{{ $layout }} {{ $layout === 'catalog' && !empty($section['image']) ? 'mc-catalog-has-image' : '' }} {{ $fullLink ? 'mc-editorial-linked' : '' }}" aria-labelledby="mc-section-{{ $loop->index }}">
            @if(in_array($layout, ['split', 'banner', 'catalog'], true))
                @if(!empty($section['image']))<img class="mc-editorial-image" src="{{ Storage::url($section['image']) }}" alt="{{ $section['title'] }}" loading="lazy">@endif
                @if($layout === 'catalog' && empty($section['image']))<img class="mc-editorial-image" src="{{ asset('assets/100/436/596/themes/834446/assets/evo-page-banner.jpg') }}" alt="" loading="lazy">@endif
                @if($layout === 'catalog' && empty($section['image']))
                    @php $setupFocusProduct = $latestProducts->first(fn ($item) => filled(($item->images ?: [])[0] ?? null)); @endphp
                    @if($setupFocusProduct)<img class="home-build-product" src="{{ Storage::url($setupFocusProduct->images[0]) }}" alt="{{ $setupFocusProduct->name }}" loading="lazy">@endif
                @endif
                <div class="mc-editorial-copy">
                    @if($layout === 'catalog' && !empty($section['eyebrow']))<p class="mc-catalog-eyebrow">{{ $section['eyebrow'] }}</p>@endif
                    <h2 id="mc-section-{{ $loop->index }}">{{ $section['title'] }}</h2>
                    @if(!empty($section['body']))<p>{{ $section['body'] }}</p>@endif
                    @if(!empty($section['link']) && !empty($section['button_label']))
                        @if($fullLink)<span class="mc-button">{{ $section['button_label'] }} <span aria-hidden="true">→</span></span>
                        @else<a class="mc-button" href="{{ $section['link'] }}">{{ $section['button_label'] }} <span aria-hidden="true">→</span></a>@endif
                    @endif
                </div>
            @else
                <div class="mc-editorial-heading"><h2 id="mc-section-{{ $loop->index }}">{{ $section['title'] }}</h2>@if(!empty($section['body']))<p>{{ $section['body'] }}</p>@endif</div>
                <div class="mc-editorial-items">
                    @foreach($section['items'] ?? [] as $item)
                        @if($layout === 'faq')
                            <details><summary>{{ $item['title'] ?? '' }}<span aria-hidden="true">+</span></summary><p>{{ $item['body'] ?? '' }}</p>@if(!empty($item['link']))<a class="mc-faq-link" href="{{ $item['link'] }}">Xem chi tiết →</a>@endif</details>
                        @else
                            <article>
                                @if(!empty($item['image']))
                                    @if(!empty($item['link']))<a href="{{ $item['link'] }}">@endif
                                    <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
                                    @if(!empty($item['link']))</a>@endif
                                @endif
                                <h3>@if(!empty($item['link']))<a href="{{ $item['link'] }}">{{ $item['title'] ?? '' }}</a>@else{{ $item['title'] ?? '' }}@endif</h3>
                                @if(!empty($item['body']))<p>{{ $item['body'] }}</p>@endif
                            </article>
                        @endif
                    @endforeach
                </div>
            @endif
            @if($fullLink)<a class="mc-editorial-full-link" href="{{ $section['link'] }}" aria-label="{{ $section['button_label'] ?? $section['title'] }}"></a>@endif
        </section>
    @endif
@endforeach
</div>
@if($latestPosts->isNotEmpty())
<section class="babu-blog-section babu-blog-home" aria-labelledby="mc-blog-title">
    <div class="babu-blog-heading">
        <div><h2 id="mc-blog-title">Blog</h2><p>Hướng dẫn và chia sẻ về gaming gear.</p></div>
        <a href="/blogs">Xem tất cả <span aria-hidden="true">→</span></a>
    </div>
    <div class="babu-blog-grid">
        @foreach($latestPosts->take(6) as $post)
            @include('livewire.partials.blog-card')
        @endforeach
    </div>
</section>
@endif

@include('livewire.partials.home-community')
