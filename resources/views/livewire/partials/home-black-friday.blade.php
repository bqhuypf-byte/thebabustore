<section class="ms-section bf-page bf-home-event" aria-labelledby="home-black-friday-title">
    @if($homeCampaign->banner_image)<a class="bf-home-masthead" href="{{ $homeCampaign->publicUrl() }}" aria-label="{{ $homeCampaign->headline }}"><picture>@if($homeCampaign->mobile_banner_image)<source media="(max-width:600px)" srcset="{{ Storage::url($homeCampaign->mobile_banner_image) }}">@endif<img src="{{ Storage::url($homeCampaign->banner_image) }}" alt="{{ $homeCampaign->headline }}" loading="lazy" width="2160" height="720"></picture></a>@endif
    <div class="bf-home-body" x-data="{ saleCategory: 'all' }">
        <div class="bf-home-heading"><div><p>THE BABU STORE / BLACK FRIDAY</p><h2 id="home-black-friday-title">Săn deal gaming gear</h2></div><a href="{{ $homeCampaign->publicUrl() }}">Khám phá chiến dịch</a></div>
        @php $homeSaleCategories=$homeCampaignItems->pluck('product.category')->filter()->unique('id'); @endphp
        <div class="bf-filters" role="group" aria-label="Danh mục Black Friday"><button type="button" @click="saleCategory = 'all'" :class="{ 'active': saleCategory === 'all' }" :aria-pressed="(saleCategory === 'all').toString()">Nổi bật</button>@foreach($homeSaleCategories as $group)<button type="button" @click="saleCategory = '{{ $group->id }}'" :class="{ 'active': saleCategory === '{{ $group->id }}' }" :aria-pressed="(saleCategory === '{{ $group->id }}').toString()">{{ $group->name }}</button>@endforeach</div>
        @if($homeCampaignItems->isNotEmpty())<div class="bf-grid">@include('livewire.partials.campaign-deal-cards',['dealItems'=>$homeCampaignItems,'campaignCardFilter'=>true,'campaignHomeCards'=>true])</div>
        @else<div class="bf-empty"><h3>Ưu đãi Black Friday đang được cập nhật</h3><p>Khám phá thêm các thiết bị đang có tại cửa hàng.</p></div>@endif
        <a class="bf-home-all" href="{{ $homeCampaign->publicUrl() }}">Xem tất cả deal Black Friday</a>
    </div>
    <link rel="stylesheet" href="{{ asset('css/promotion-campaign.css') }}?v={{ filemtime(public_path('css/promotion-campaign.css')) }}">
</section>
