@php $homeSettings = \App\Models\Setting::query()->first(); $homeSections = $homeSettings?->homepage_sections ?? \App\Support\HomepageContent::defaults($homeSettings); @endphp
@foreach($homeSections as $section)
    @if(($section['enabled'] ?? true) && filled($section['title'] ?? null))
        @php $layout = in_array($section['layout'] ?? '', ['split', 'banner', 'cards', 'faq'], true) ? $section['layout'] : 'split'; @endphp
        <section class="mc-editorial mc-layout-{{ $layout }}" aria-labelledby="mc-section-{{ $loop->index }}">
            @if(in_array($layout, ['split', 'banner'], true))
                @if(!empty($section['image']))<img class="mc-editorial-image" src="{{ Storage::url($section['image']) }}" alt="{{ $section['title'] }}" loading="lazy">@endif
                <div class="mc-editorial-copy">
                    <h2 id="mc-section-{{ $loop->index }}">{{ $section['title'] }}</h2>
                    @if(!empty($section['body']))<p>{{ $section['body'] }}</p>@endif
                    @if(!empty($section['link']) && !empty($section['button_label']))<a class="mc-button" href="{{ $section['link'] }}">{{ $section['button_label'] }} <span aria-hidden="true">→</span></a>@endif
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
        </section>
    @endif
@endforeach
@if($latestPosts->isNotEmpty())
<section class="mc-editorial mc-home-blog" aria-labelledby="mc-blog-title">
    <div class="mc-blog-heading"><h2 id="mc-blog-title">Bài viết & tin tức</h2><a href="/blogs">Xem tất cả →</a></div>
    <div class="mc-blog-grid">
        @foreach($latestPosts->take(3) as $post)
            <article><a href="/blog/{{ $post->slug }}">@if($post->banner)<img src="{{ Storage::url($post->banner) }}" alt="{{ $post->title }}" loading="lazy">@endif<h3>{{ $post->title }}</h3></a>@if($post->created_at)<time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d.m.Y') }}</time>@endif</article>
        @endforeach
    </div>
</section>
@endif