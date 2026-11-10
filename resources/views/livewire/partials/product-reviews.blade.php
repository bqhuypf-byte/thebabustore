<div class="review-empty">
    <div class="stars" aria-hidden="true">★★★★★</div>
    <h2>{{ $reviewCount ? number_format($avgRating,1).' / 5 · '.$reviewCount.' đánh giá' : 'Chưa có đánh giá nào cho sản phẩm này' }}</h2>
    <p>Bạn đã sử dụng {{ $product->name }}? Chia sẻ cảm nhận để giúp khách hàng khác lựa chọn.</p>
    <button class="btn btn-dark" type="button" wire:click="$set('showReviewForm', true)">Viết đánh giá</button>
</div>
@if($showReviewForm)
    <form wire:submit="sendReview" class="review-form open">
        <label>Đánh giá của bạn</label>
        <div class="rate-pick" role="radiogroup" aria-label="Chọn số sao">@foreach(range(1,5) as $star)<button type="button" role="radio" aria-checked="{{ (int)$rating === $star ? 'true' : 'false' }}" aria-label="{{ $star }} sao" class="{{ $star <= $rating ? 'on' : '' }}" wire:click="$set('rating', {{ $star }})">★</button>@endforeach</div>
        @error('rating')<p class="field-error">{{ $message }}</p>@enderror
        <label for="review-name">Họ tên</label><input id="review-name" wire:model="name" required maxlength="100" autocomplete="name">
        @error('name')<p class="field-error">{{ $message }}</p>@enderror
        <label for="review-body">Nhận xét</label><textarea id="review-body" wire:model="body" rows="4" minlength="5" maxlength="1000" required></textarea>
        @error('body')<p class="field-error">{{ $message }}</p>@enderror
        <p class="review-note">Đánh giá được hiển thị sau khi cửa hàng duyệt.</p>
        <button class="btn btn-dark" type="submit" wire:loading.attr="disabled">Gửi đánh giá</button>
    </form>
@endif
<div class="review-list">
    @foreach($approvedReviews as $review)<article class="review-item"><div class="rh"><b>{{ $review->name }}</b><span class="stars" aria-label="{{ $review->rating }} trên 5 sao">@foreach(range(1,5) as $star)<span class="{{ $star <= $review->rating ? 'filled' : '' }}">★</span>@endforeach</span><time datetime="{{ $review->created_at?->toDateString() }}">{{ $review->created_at?->format('d/m/Y') }}</time></div><p>{{ $review->comment }}</p></article>@endforeach
</div>
