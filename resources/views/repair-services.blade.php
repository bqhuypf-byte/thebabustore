<div class="ms-root"><link rel="stylesheet" href="{{ asset('fonts/roboto-care/font.css') }}"><link rel="stylesheet" href="{{ asset('css/repair-services.css') }}?v={{ filemtime(public_path('css/repair-services.css')) }}"><div class="modern-storefront gear-repair-storefront">
 @livewire('inc.header')
 <main class="gear-repair-page">
  <nav class="gear-repair-breadcrumb" aria-label="Breadcrumb"><a href="/">Trang chủ</a><span>/</span><span>Dịch vụ sửa chữa</span></nav>
  <section class="care-v2-hero" aria-labelledby="care-hero-title">
   <div class="care-v2-hero-copy"><span class="care-v2-kicker"><i></i> THE BABU STORE / CARE & REPAIR</span><h1 id="care-hero-title">Chăm lại gear.<br><em>Trọn lại trải nghiệm.</em></h1><p>Từ nút click chập chờn đến phím gõ không còn mượt. Kể Babu nghe tình trạng thiết bị, cùng tìm phương án phù hợp.</p><div class="care-v2-hero-actions"><a href="#care-service-catalog" class="care-v2-primary">Tìm dịch vụ phù hợp <span aria-hidden="true">↗</span></a><a href="#yeu-cau-sua-chua" onclick="document.getElementById('repair-request-details').open = true">Gửi tình trạng thiết bị →</a></div><div class="care-v2-hero-foot"><span>01 / Kiểm tra tình trạng</span><span>02 / Báo giá trước khi sửa</span></div></div>
   <div class="care-v2-visual"><div class="care-v2-orbit" aria-hidden="true"></div><span class="care-v2-visual-label">GEAR DESERVES A SECOND ROUND.</span>@foreach(['mouse'=>'Chuột', 'keyboard'=>'Bàn phím', 'headphones'=>'Tai nghe'] as $device=>$label)<a class="care-v2-device care-v2-device-{{ $device }}" href="#care-service-catalog" x-data @click.prevent="$dispatch('repair-device', { device: '{{ $device }}' })"><span>{{ $label }}</span>@if($serviceMedia[$device] ?? null)<img src="{{ Storage::url($serviceMedia[$device]) }}" alt="{{ $label }} — ảnh minh họa thiết bị" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>@endif<span aria-hidden="true">↗</span></a>@endforeach<div class="care-v2-visual-note"><span aria-hidden="true">✳</span><div><strong>Kiểm tra trước. Sửa đúng lỗi.</strong><small>Phương án được thống nhất cùng bạn.</small></div></div></div>
  </section>
  <header class="repair-shop-heading"><h2>Dịch vụ sửa chữa & vệ sinh gaming gear</h2></header>

  @php
   $shopEntries = []; foreach ($groups as $group) foreach ($group['items'] as $index => $item) $shopEntries[] = ['id' => $group['id'].'-'.$index, 'group' => $group['id'], 'index' => $index, 'device' => $group['device'], 'name' => $item['name'], 'text' => $item['name'].' '.$item['symptoms'], 'symptoms' => $item['symptoms'], 'scope' => $item['scope'], 'price' => $item['display_price'], 'tiers' => $item['kind'] === 'repair' ? [] : ($item['tiers'] ?? []), 'image' => !empty($item['image']) ? asset($item['image']) : null];
  @endphp
  <script src="{{ asset('js/repair-services.js') }}?v={{ filemtime(public_path('js/repair-services.js')) }}"></script>
  <section id="care-service-catalog" x-data="repairShop(@js($shopEntries))" @repair-device.window="device = $event.detail.device; limit = 100; $el.scrollIntoView({ behavior: 'smooth' })" aria-label="Danh mục dịch vụ">
   <div class="repair-shop-tools"><div class="repair-shop-filters" role="group" aria-label="Chọn thiết bị">@foreach(['all'=>'Tất cả', 'keyboard'=>'Bàn phím cơ', 'mouse'=>'Chuột', 'headphones'=>'Tai nghe'] as $key=>$label)<button type="button" @click="device = '{{ $key }}'; limit = 100" :aria-pressed="device === '{{ $key }}'">{{ $label }}</button>@endforeach</div><label class="repair-shop-search"><span class="sr-only">Tìm dịch vụ</span><input type="search" placeholder="Tìm dịch vụ…" x-model="query" @input="limit = 100"></label></div>
   <p class="repair-shop-note">Giá dịch vụ tham khảo. Lỗi mạch, chập cháy và sensor được báo giá sau khi kiểm tra.</p>
   <div class="repair-shop-grid">
    @php $flat = []; foreach ($groups as $group) foreach ($group['items'] as $index => $item) $flat[] = ['group' => $group, 'index' => $index, 'item' => $item];
      $priority = ['Thay cổng kết nối bàn phím', 'Thay pin bàn phím', 'Hàn switch bàn phím cơ', 'Kiểm tra lỗi mạch / đứt via chuột', 'Thay cổng kết nối / cổng sạc chuột', 'Thay cuộn lăn / encoder', 'Thay pin chuột', 'Thay switch chuột', 'Rã hàn switch bàn phím cơ', 'Thay switch MX', 'Vệ sinh chuột', 'Lube switch'];
      usort($flat, fn ($a,$b) => (array_search($a['item']['name'], $priority) === false ? 100 : array_search($a['item']['name'], $priority)) <=> (array_search($b['item']['name'], $priority) === false ? 100 : array_search($b['item']['name'], $priority)));
     @endphp
    @foreach($flat as $position => $entry)
     @php $group = $entry['group']; $item = $entry['item']; $index = $entry['index']; $serviceImage = $item['image'] ?? null; @endphp
     <article class="repair-shop-card" x-data="{ variant: 0, variantPrices: @js(array_column($item['tiers'] ?? [], 'price')) }" x-show="matches(@js($group['device']), @js($item['name'].' '.$item['symptoms'])) &amp;&amp; (limit > {{ $position }} || device !== 'all' || query !== '')" wire:key="repair-{{ $group['id'] }}-{{ $index }}">
      <a class="repair-shop-photo" href="#yeu-cau-sua-chua" @click.prevent="openService('{{ $group['id'] }}-{{ $index }}', $event.currentTarget, variant)" aria-label="Tư vấn {{ $item['name'] }}">@if($serviceImage)<img src="{{ asset($serviceImage) }}" alt="{{ $item['name'] }} — ảnh tham khảo RightFix" loading="lazy">@elseif($serviceMedia[$group['id']] ?? null)<img src="{{ Storage::url($serviceMedia[$group['id']]) }}" alt="{{ $item['name'] }} — ảnh minh họa thiết bị" loading="lazy">@endif</a>
      <h2><a href="#yeu-cau-sua-chua" @click.prevent="openService('{{ $group['id'] }}-{{ $index }}', $event.currentTarget, variant)">{{ $item['name'] }}</a></h2>
      @if($item['display_price'] && count($item['tiers'] ?? []) > 1)<label class="repair-variant"><span class="sr-only">Biến thể {{ $item['name'] }}</span><select x-model.number="variant">@foreach($item['tiers'] as $tier)<option value="{{ $loop->index }}">{{ $tier['label'] }} — {{ $tier['condition'] }}</option>@endforeach</select></label>@endif
      <div class="repair-shop-price">@if($item['display_price'])<span x-text="variantPrices.length ? money(variantPrices[variant]) : money({{ $item['display_price'] }})">{{ number_format($item['display_price'],0,',','.') }}đ</span>@else{{ $item['kind'] === 'repair' ? 'Báo giá sau kiểm tra' : 'Liên hệ báo giá' }}@endif</div>
      @if(!empty($item['price_unit']) && count($item['tiers'] ?? []) < 2)<small class="repair-shop-unit">{{ $item['price_unit'] }}</small>@endif
     </article>
    @endforeach
   </div>
   <p class="repair-shop-empty" x-show="!entries.some(item => matches(item.device, item.text))" x-cloak>Không tìm thấy dịch vụ. Thử từ khóa khác hoặc liên hệ Babu.</p>
   <div class="repair-shop-more" x-show="limit < {{ count($flat) }} &amp;&amp; device === 'all' &amp;&amp; query === ''"><button type="button" @click="limit += 12">Xem thêm dịch vụ ↓</button></div>
   <dialog class="repair-price-dialog" x-ref="serviceDialog" @close="lastTrigger?.focus()" @click="if ($event.target === $el) $el.close()" aria-labelledby="repair-dialog-title">
    <button class="repair-dialog-close" type="button" @click="$refs.serviceDialog.close()" aria-label="Đóng bảng giá">×</button>
    <template x-if="active"><div><div class="repair-dialog-intro"><img x-show="active.image" :src="active.image" alt="Ảnh minh họa dịch vụ"><div><span class="repair-dialog-eyebrow">BABU CARE / GIÁ THAM KHẢO</span><h2 id="repair-dialog-title" x-text="active.name"></h2><strong class="repair-dialog-price" x-text="active.price ? money(active.tiers[active.variant]?.price || active.price) : 'Báo giá sau kiểm tra'"></strong><p x-text="active.symptoms"></p></div></div>
     <div class="repair-tier-wrap" x-show="active.tiers.length"><table class="repair-tier-table"><thead><tr><th>Hạng mục</th><th>Giá tham khảo</th><th>Áp dụng</th></tr></thead><tbody><template x-for="(tier,index) in active.tiers" :key="index"><tr><td x-text="tier.label"></td><td x-text="money(tier.price)"></td><td x-text="tier.condition"></td></tr></template></tbody></table></div>
     <p class="repair-dialog-scope" x-text="active.scope"></p><p class="repair-dialog-note">Chi phí phụ thuộc model, số lượng và tình trạng thực tế. Shop xác nhận phương án và tổng giá trước khi thực hiện.</p>
     <a href="#yeu-cau-sua-chua" class="repair-dialog-cta" @click="$wire.chooseService(active.group,active.index,active.variant); $refs.serviceDialog.close(); document.getElementById('repair-request-details').open = true">Tư vấn dịch vụ này ↗</a>
    </div></template>
   </dialog>
  </section>
  <p class="repair-shop-source">Ảnh và giá tham khảo từ RightFix; không phải ảnh sửa chữa tại Babu. Chi phí thực tế được xác nhận theo thiết bị.</p>
  <details id="repair-request-details" class="repair-contact-details"><summary>Gửi tình trạng thiết bị để được tư vấn <span>+</span></summary>
  <section id="yeu-cau-sua-chua" class="care-request" aria-labelledby="care-request-title">
   <div class="care-request-intro"><span class="gear-repair-eyebrow">LIÊN HỆ BABU CARE</span><h2 id="care-request-title">Kể Babu nghe<br>gear đang gặp lỗi gì.</h2><p>Để lại thông tin, shop sẽ liên hệ tư vấn và xác nhận khả năng tiếp nhận thiết bị.</p>
    @if($repairSettings?->hotline)<a class="care-hotline" href="tel:{{ preg_replace('/[^0-9+]/', '', $repairSettings->hotline) }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 3 3 5-2 2a15 15 0 0 0 7 7l2-2 5 3c0 3-2 4-4 3C8 19 4 15 3 7c-1-2 0-4 3-4Z"/></svg><span><small>Gọi shop để được tư vấn</small><strong>{{ $repairSettings->hotline }}</strong></span></a>@endif
    <small class="care-request-note">Gửi form chưa phải lịch hẹn hoặc phiếu sửa đã tiếp nhận. Vui lòng trao đổi với shop trước khi chuyển thiết bị.</small>
   </div>
   <div class="care-request-panel">
    @if($submitted)
     <div class="care-success" role="status" aria-live="polite" tabindex="-1"><span aria-hidden="true">✓</span><h3>Đã gửi yêu cầu tư vấn</h3><p>Babu đã nhận thông tin qua email của shop. Shop sẽ liên hệ theo số điện thoại bạn cung cấp để trao đổi thêm.</p></div>
    @else
     <form wire:submit="submit" class="care-request-form" id="care-request-form">
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
  </details>
 </main>
 @livewire('inc.footer')
</div></div>
