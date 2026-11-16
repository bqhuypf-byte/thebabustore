<div class="babu-blog-page">
    @livewire('inc.header')
    <main class="babu-blog-section">
        <nav class="babu-blog-breadcrumb" aria-label="Breadcrumb"><a href="/">Trang chủ</a><span aria-hidden="true">›</span><span>Blog</span></nav>
        <div class="babu-blog-heading"><h1>BLOG</h1></div>
        @if($posts->isNotEmpty())
            <div class="babu-blog-grid">
                @foreach($posts as $post)
                    @include('livewire.partials.blog-card')
                @endforeach
            </div>
            <div class="babu-blog-pagination">{{ $posts->links('livewire.store-pagination', ['itemLabel' => 'bài viết']) }}</div>
        @else
            <p class="babu-blog-empty">Bài viết đang được cập nhật.</p>
        @endif
    </main>
    @livewire('inc.footer')
</div>
