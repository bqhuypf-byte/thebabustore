@php
    $communitySettings = \App\Models\Setting::query()->first();
    $communitySections = collect($communitySettings?->homepage_sections ?? \App\Support\HomepageContent::defaults($communitySettings));
    $hub = $communitySections->first(fn ($entry) => str_contains(mb_strtoupper($entry['title'] ?? ''), 'BABU HUB'));
    $faq = $communitySections->firstWhere('layout', 'faq');
    $newsletterArt = $communitySections->firstWhere('layout', 'newsletter');
@endphp
<section class="home-discovery home-community" aria-label="Cộng đồng và ưu đãi">
    @if($hub['enabled'] ?? true)
    <div class="home-community-hub">@if(!empty($hub['image']))<img class="home-community-art" src="{{ Storage::url($hub['image']) }}" alt="" loading="lazy">@endif<h2>{{ $hub['title'] ?? 'BABU HUB' }}</h2><p>{{ $hub['body'] ?? 'Khám phá bàn phím, chuột và phụ kiện cho góc máy của bạn.' }}</p><a class="home-community-button" href="{{ $communitySettings?->facebook ?: ($hub['link'] ?? '/shop') }}">{{ $communitySettings?->facebook ? 'Tham gia' : ($hub['button_label'] ?? 'Khám phá') }}</a></div>
    @endif
    <div class="home-community-newsletter">@if(!empty($newsletterArt['image']))<img class="home-community-art" src="{{ Storage::url($newsletterArt['image']) }}" alt="" loading="lazy">@endif<h2>Nhận ưu đãi sớm</h2><p>Đăng ký email để không bỏ lỡ khuyến mãi và hàng mới.</p>
        @if($newsletterSubscribed)<p class="home-newsletter-success" role="status">Đã nhận đăng ký của bạn. Cảm ơn bạn!</p>
        @else<form wire:submit="subscribeNewsletter" class="home-newsletter-form"><label class="sr-only" for="home-newsletter-email">Email của bạn</label><input id="home-newsletter-email" type="email" wire:model="newsletterEmail" placeholder="Email của bạn" autocomplete="email" maxlength="254" required><button type="submit" wire:loading.attr="disabled" wire:target="subscribeNewsletter">Đăng ký</button></form>@error('newsletterEmail')<p class="home-newsletter-error" role="alert">{{ $message }}</p>@enderror
        @endif
    </div>
</section>
@if($faq && ($faq['enabled'] ?? true))
<section class="home-discovery home-support" aria-labelledby="home-support-title"><div><h2 id="home-support-title">{{ $faq['title'] }}</h2><p>{{ $faq['body'] ?? '' }}</p></div><div class="home-support-questions">@foreach($faq['items'] ?? [] as $item)<details><summary>{{ $item['title'] ?? '' }}<span aria-hidden="true">⌄</span></summary><p>{{ $item['body'] ?? '' }}</p>@if(!empty($item['link']))<a href="{{ $item['link'] }}">Xem chi tiết</a>@endif</details>@endforeach</div></section>
@endif