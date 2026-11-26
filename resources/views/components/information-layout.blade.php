@php
 $informationSettings = \App\Models\Setting::query()->first();
 $informationPages = \App\Support\InformationPages::all();
 $informationPath = request()->path();
 $informationTitle = $informationPages[$informationPath] ?? 'Thông tin & hỗ trợ';
@endphp
<div class="ms-root">
 <div class="modern-storefront mc-home info-storefront">
  @livewire('inc.header')
  <main class="info-main">
   <nav class="info-breadcrumb" aria-label="Breadcrumb"><a href="/">Trang chủ</a><span aria-hidden="true">/</span><span>{{ $informationTitle }}</span></nav>
   <header class="info-hero"><span class="info-eyebrow">BABU STORE / Thông tin & hỗ trợ</span><h1>{{ $informationTitle }}</h1><p>Tìm hiểu thông tin mua hàng và chính sách tại The BABU Store.</p></header>
   <div class="info-layout">
    <aside class="info-sidebar"><nav aria-label="Thông tin & hỗ trợ"><h2>Thông tin & hỗ trợ</h2>@foreach($informationPages as $path => $label)<a href="/{{ $path }}" @class(['is-current' => $informationPath === $path]) @if($informationPath === $path) aria-current="page" @endif>{{ $label }}<span aria-hidden="true">&rsaquo;</span></a>@endforeach</nav><div class="info-help"><strong>Bạn cần thêm hỗ trợ?</strong><p>Liên hệ BABU để được giải đáp thắc mắc về sản phẩm và đơn hàng.</p><a href="/contact">Liên hệ BABU</a></div></aside>
    <article class="info-article" aria-label="{{ $informationTitle }}">{{ $slot }}</article>
   </div>
  </main>
  @livewire('inc.footer')
  <link rel="stylesheet" href="{{ asset('css/home-reference.css') }}?v={{ filemtime(public_path('css/home-reference.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/information-pages.css') }}?v={{ filemtime(public_path('css/information-pages.css')) }}">
 </div>
</div>
