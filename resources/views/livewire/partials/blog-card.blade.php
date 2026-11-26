@php($blogLabel = $post->contentCategory?->name ?: ($post->contentType?->name ?: 'Tin tức'))
<article class="babu-blog-card" wire:key="blog-card-{{ $post->id }}">
    <a class="babu-blog-image" href="/blog/{{ $post->slug }}" aria-label="{{ $post->title }}">
        @if($post->banner)
            <img src="{{ Storage::url($post->banner) }}" alt="{{ $post->title }}" loading="lazy" width="560" height="350">
        @else
            <span class="babu-blog-placeholder">BABU STORE</span>
        @endif
        <span class="babu-blog-label">{{ $blogLabel }}</span>
    </a>
    <time class="babu-blog-date" datetime="{{ ($post->published_at ?? $post->created_at)?->toDateString() }}">{{ ($post->published_at ?? $post->created_at)?->format('d/m/Y') }}</time>
    <h3 class="babu-blog-title"><a href="/blog/{{ $post->slug }}">{{ $post->title }}</a></h3>
    <a class="babu-blog-read" href="/blog/{{ $post->slug }}" aria-label="Đọc tiếp: {{ $post->title }}">Đọc tiếp <span aria-hidden="true">→</span></a>
</article>
