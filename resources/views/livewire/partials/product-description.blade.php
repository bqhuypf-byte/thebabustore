@php
    $sections = collect($product->content_sections ?? [])->filter(fn ($section) => ($section['visible'] ?? true) && (trim($section['body'] ?? '') !== '' || trim($section['html'] ?? '') !== '' || !empty($section['image'])));
@endphp
<div class="pd-description pd-prose">
    @if($sections->isNotEmpty())
        @foreach($sections as $section)
            <section class="pd-content-section">
                @if(!empty($section['title']))<h2>{{ $section['title'] }}</h2>@endif
                @if(!empty($section['body']))<div class="pd-section-text">{{ $section['body'] }}</div>@endif
                @if(!empty($section['html']))<div class="pd-section-html">{!! \App\Support\ProductContent::sanitize($section['html']) !!}</div>@endif
                @if(!empty($section['image']))
                    <figure><img src="{{ asset('storage/'.$section['image']) }}" alt="{{ $section['image_alt'] ?? $section['title'] ?? $product->name }}" loading="lazy"></figure>
                @endif
            </section>
        @endforeach
    @elseif(!$product->content_standardized_at && (trim(strip_tags($productContent['description'])) !== '' || str_contains($productContent['description'], '<img')))
        {!! $productContent['description'] !!}
    @else
        <p>Thông tin chi tiết đang được cập nhật.</p>
    @endif
    @if(count($productContent['groups']))
        <button class="compare-link" type="button" wire:click="$set('activeTab', 'specifications')">Xem bảng thông số kỹ thuật →</button>
    @endif
</div>
