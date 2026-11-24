<div class="ms-root">
<link rel="stylesheet" href="{{ asset('fonts/roboto-care/font.css') }}">
<link rel="stylesheet" href="{{ asset('css/repair-services.css') }}?v={{ filemtime(public_path('css/repair-services.css')) }}">
<div class="modern-storefront gear-repair-storefront">
 @livewire('inc.header')
 <main class="gear-repair-page">
  <nav class="gear-repair-breadcrumb" aria-label="Breadcrumb"><a href="/">Trang chủ</a><span aria-hidden="true">/</span><span>Sửa chữa gear</span></nav>
  @include('livewire.partials.repair-banner')
  <nav class="care-local-nav" aria-label="Nội dung dịch vụ"><a href="#care-service-catalog">Dịch vụ sửa chữa</a><a href="#repair-services-title">Danh sách hạng mục</a><a href="#repair-process-title">Quy trình</a><a href="#care-faq-title">Câu hỏi thường gặp</a><a href="#yeu-cau-sua-chua">Liên hệ tư vấn <span aria-hidden="true">↗</span></a></nav>
  <section id="care-service-catalog" class="care-catalog" x-data="{ group: 'all' }" aria-labelledby="care-catalog-title">
   <header class="gear-repair-section-head"><div><span class="gear-repair-eyebrow">BABU CARE</span><h2 id="care-catalog-title">Sửa chữa & vệ sinh gaming gear</h2></div><p>Chọn hạng mục cần hỗ trợ.<br>Liên hệ để xác nhận phương án và báo giá.</p></header>
   <div class="care-catalog-filters" role="group" aria-label="Lọc nhóm dịch vụ"><button type="button" @click="group = 'all'" :aria-pressed="(group === 'all').toString()">Tất cả dịch vụ</button>@foreach($groups as $group)<button type="button" @click="group = '{{ $group['id'] }}'" :aria-pressed="(group === '{{ $group['id'] }}').toString()">{{ $group['title'] }}</button>@endforeach</div>
   <div class="care-catalog-grid">
    @foreach($groups as $group)
     @foreach(array_slice($group['items'], 0, 2) as $item)
      <article class="care-catalog-card" x-show="group === 'all' || group === '{{ $group['id'] }}'">
       <a class="care-catalog-photo" href="#yeu-cau-sua-chua" aria-label="Tư vấn {{ $item['name'] }}">@if($serviceMedia[$group['id']] ?? null)<img src="{{ Storage::url($serviceMedia[$group['id']]) }}" alt="Minh họa thiết bị — {{ $group['title'] }}" loading="lazy">@else<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="10" y="20" width="60" height="40" rx="8"/><path d="M20 32h40M20 42h40M30 52h20"/></svg>@endif<span aria-hidden="true">↗</span></a>
       <div><small>{{ $group['title'] }}</small><h3><a href="#yeu-cau-sua-chua">{{ $item['name'] }}</a></h3><p>Liên hệ báo giá</p><a class="care-catalog-consult" href="#yeu-cau-sua-chua">Tư vấn hạng mục <span aria-hidden="true">→</span></a></div>
      </article>
     @endforeach
    @endforeach
   </div>
  </section>
  <section aria-labelledby="repair-services-title" class="gear-repair-services">
   <header class="gear-repair-section-head"><div><span class="gear-repair-eyebrow">DỊCH VỤ</span><h2 id="repair-services-title">Gear của bạn cần hỗ trợ gì?</h2></div><p>Chọn nhóm dịch vụ phù hợp.<br>Phương án và chi phí được xác nhận sau khi kiểm tra.</p></header>
   <div class="care-service-directory">
    @foreach($groups as $group)
     <details id="dich-vu-{{ $group['id'] }}" class="care-directory-group" @if($loop->first) open @endif>
      <summary><span class="care-directory-number">0{{ $loop->iteration }}</span><span><strong>{{ $group['title'] }}</strong><small>{{ count($group['items']) }} hạng mục · {{ $group['description'] }}</small></span><i aria-hidden="true">+</i></summary>
      <ul class="care-service-items">@foreach($group['items'] as $item)<li><span>{{ $item['name'] }}</span><small>{{ $item['price_note'] }}</small><a href="#yeu-cau-sua-chua" aria-label="Tư vấn {{ $item['name'] }}">Liên hệ <span aria-hidden="true">↗</span></a></li>@endforeach</ul>
     </details>
    @endforeach
   </div>
  </section>
  <section class="gear-repair-process" aria-labelledby="repair-process-title">
   <header class="gear-repair-section-head"><div><span class="gear-repair-eyebrow">CÁCH BABU TIẾP NHẬN</span><h2 id="repair-process-title">Rõ ràng ở từng bước.</h2></div></header>
   <ol><li><span>01</span><div><h3>Tiếp nhận</h3><p>Liên hệ và mô tả tình trạng thiết bị để được tư vấn.</p></div></li><li><span>02</span><div><h3>Kiểm tra & báo giá</h3><p>Thống nhất phương án, chi phí và thời gian trước khi sửa.</p></div></li><li><span>03</span><div><h3>Sửa chữa & kiểm tra</h3><p>Xử lý lỗi theo phương án đã duyệt và kiểm tra lại.</p></div></li><li><span>04</span><div><h3>Bàn giao</h3><p>Kiểm tra thiết bị cùng khách và xác nhận thông tin bảo hành.</p></div></li></ol>
  </section>
  <section class="care-assurance" aria-labelledby="care-assurance-title"><div><span class="gear-repair-eyebrow">MINH BẠCH KHI SỬA CHỮA</span><h2 id="care-assurance-title">Trao đổi rõ, rồi mới bắt đầu.</h2><p>Babu trao đổi lỗi cần xử lý, linh kiện thay thế và chi phí trước khi thực hiện. Yêu cầu nhận lại linh kiện cũ được ghi nhận khi tiếp nhận.</p></div><div class="care-warranty-note"><span>Bảo hành sau sửa</span><strong>Liên hệ xác nhận</strong><p>Thời hạn, phạm vi và cách ghi nhận bảo hành được thống nhất theo từng hạng mục. Chưa công bố mức áp dụng chung.</p></div></section>
  <section class="care-faq" aria-labelledby="care-faq-title"><header class="gear-repair-section-head"><div><span class="gear-repair-eyebrow">GIẢI ĐÁP TRƯỚC KHI SỬA</span><h2 id="care-faq-title">Bạn cần biết thêm?</h2></div></header><div class="care-faq-list">@foreach($faqs as [$question, $answer])<details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>@endforeach</div></section>
  <section id="yeu-cau-sua-chua" class="care-request" aria-labelledby="care-request-title">
   <div class="care-request-intro"><span class="gear-repair-eyebrow">LIÊN HỆ BABU CARE</span><h2 id="care-request-title">Kể Babu nghe<br>gear đang gặp lỗi gì.</h2><p>Để lại thông tin, shop sẽ liên hệ tư vấn và xác nhận khả năng tiếp nhận thiết bị.</p>
    @if($repairSettings?->hotline)<a class="care-hotline" href="tel:{{ preg_replace('/[^0-9+]/', '', $repairSettings->hotline) }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 3 3 5-2 2a15 15 0 0 0 7 7l2-2 5 3c0 3-2 4-4 3C8 19 4 15 3 7c-1-2 0-4 3-4Z"/></svg><span><small>Gọi shop để được tư vấn</small><strong>{{ $repairSettings->hotline }}</strong></span></a>@endif
    <small class="care-request-note">Gửi form chưa phải lịch hẹn hoặc phiếu sửa đã tiếp nhận. Vui lòng trao đổi với shop trước khi chuyển thiết bị.</small>
   </div>
   <div class="care-request-panel">
    @if($submitted)
     <div class="care-success" role="status" aria-live="polite" tabindex="-1"><span aria-hidden="true">✓</span><h3>Đã gửi yêu cầu tư vấn</h3><p>Babu đã nhận thông tin qua email của shop. Shop sẽ liên hệ theo số điện thoại bạn cung cấp để trao đổi thêm.</p></div>
    @else
     <form wire:submit="submit" class="care-request-form">
      @error('request')<p class="care-form-error care-form-wide" role="alert">{{ $message }}</p>@enderror
      <div><label for="care-name">Họ và tên <span aria-hidden="true">*</span></label><input id="care-name" type="text" wire:model="name" autocomplete="name" required maxlength="120" @error('name') aria-invalid="true" aria-describedby="care-name-error" @enderror>@error('name')<p id="care-name-error" class="care-field-error">{{ $message }}</p>@enderror</div>
      <div><label for="care-phone">Số điện thoại <span aria-hidden="true">*</span></label><input id="care-phone" type="tel" wire:model="phone" autocomplete="tel" inputmode="tel" required maxlength="18" @error('phone') aria-invalid="true" aria-describedby="care-phone-error" @enderror>@error('phone')<p id="care-phone-error" class="care-field-error">{{ $message }}</p>@enderror</div>
      <div class="care-form-wide"><label for="care-device">Loại thiết bị <span aria-hidden="true">*</span></label><select id="care-device" wire:model="deviceType" required @error('deviceType') aria-invalid="true" aria-describedby="care-device-error" @enderror><option value="">Chọn loại thiết bị</option><option value="keyboard">Bàn phím</option><option value="mouse">Chuột</option><option value="headphones">Tai nghe / cáp</option></select>@error('deviceType')<p id="care-device-error" class="care-field-error">{{ $message }}</p>@enderror</div>
      <div class="care-form-wide"><label for="care-description">Mô tả lỗi <span aria-hidden="true">*</span></label><textarea id="care-description" wire:model="description" rows="4" required minlength="10" maxlength="3000" placeholder="Tên thiết bị, lỗi gặp phải và tình trạng hiện tại…" @error('description') aria-invalid="true" aria-describedby="care-description-error" @enderror></textarea>@error('description')<p id="care-description-error" class="care-field-error">{{ $message }}</p>@enderror</div>
      <div class="care-form-wide care-form-submit"><button type="submit" wire:loading.attr="disabled" wire:target="submit"><span wire:loading.remove wire:target="submit">Gửi yêu cầu tư vấn <span aria-hidden="true">↗</span></span><span wire:loading wire:target="submit">Đang gửi yêu cầu…</span></button><small>Thông tin được dùng để liên hệ về yêu cầu sửa chữa của bạn.</small></div>
     </form>
    @endif
   </div>
  </section>
 </main>
 @livewire('inc.footer')
</div></div>
