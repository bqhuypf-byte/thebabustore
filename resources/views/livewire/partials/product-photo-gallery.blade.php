<div class="gallery pd-gallery pd-photo-grid" x-data>
 <link rel="stylesheet" href="{{ asset('css/product-photo-gallery.css') }}?v={{ filemtime(public_path('css/product-photo-gallery.css')) }}">
 <div class="main-photo pd-main-image">
  @if($displayImage)
   <img src="{{ asset('storage/'.$displayImage) }}" alt="{{ $product->name }}{{ $selected ? ' — '.$selected->name : '' }}" fetchpriority="high">
   <button class="pd-photo-zoom" x-ref="zoomTrigger" type="button" @click="$refs.photoZoom.showModal()" aria-label="Phóng to ảnh {{ $product->name }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5M10.5 7.5v6m-3-3h6"/></svg></button>
  @else<span>Chưa có hình ảnh</span>@endif
  @if($images->count())<span class="pd-image-count">{{ ($images->search($displayImage) !== false ? $images->search($displayImage) : 0) + 1 }}/{{ $images->count() }}</span>@endif
  @if($images->count()>1)<button class="photo-nav prev" type="button" wire:click="changeImage(-1)" aria-label="Ảnh trước">‹</button><button class="photo-nav next" type="button" wire:click="changeImage(1)" aria-label="Ảnh tiếp theo">›</button>@endif
 </div>
 <div class="thumbs pd-thumbnails" aria-label="Thư viện ảnh sản phẩm">@foreach($images as $image)<button type="button" wire:click="selectImage({{ $loop->index }})" class="{{ $displayImage === $image ? 'active' : '' }}" aria-label="Ảnh sản phẩm {{ $loop->iteration }}" aria-pressed="{{ $displayImage === $image ? 'true' : 'false' }}"><img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name }} — ảnh {{ $loop->iteration }}" loading="lazy"></button>@endforeach</div>
 @if($displayImage)<dialog class="pd-photo-dialog" x-ref="photoZoom" @close="$refs.zoomTrigger.focus()" aria-label="Ảnh phóng to {{ $product->name }}" @click="if ($event.target === $el) $el.close()"><button class="pd-photo-dialog-close" type="button" @click="$refs.photoZoom.close()" aria-label="Đóng ảnh phóng to" autofocus>×</button><img src="{{ asset('storage/'.$displayImage) }}" alt="{{ $product->name }} — ảnh phóng to"></dialog>@endif
</div>
