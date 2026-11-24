<section class="care-service-mosaic" x-data aria-label="Babu Care — sửa chữa và vệ sinh gear">
 <div class="care-mosaic-main">
  @if($serviceMedia['cleaning'] ?? null)<img class="care-mosaic-image" src="{{ Storage::url($serviceMedia['cleaning']) }}" alt="" fetchpriority="high">@endif
  <div class="care-mosaic-copy"><span>BABU CARE / SỬA CHỮA GEAR</span><h1>Chăm lại gear.<br>Trọn lại trải nghiệm.</h1><p>Kiểm tra, vệ sinh và sửa chữa bàn phím, chuột cùng phụ kiện của bạn.</p><a class="gear-repair-primary" href="#yeu-cau-sua-chua">Liên hệ tư vấn sửa chữa <span aria-hidden="true">↗</span></a><small>Kiểm tra thiết bị · Báo giá trước khi sửa</small></div>
 </div>
 <a class="care-mosaic-tile care-mosaic-wide" href="#dich-vu-headphones" @click="document.getElementById('dich-vu-headphones').open = true">
  @if($serviceMedia['headphones'] ?? null)<img class="care-mosaic-image" src="{{ Storage::url($serviceMedia['headphones']) }}" alt="" loading="lazy">@endif
  <span><small>ÂM THANH & KẾT NỐI</small><strong>Sửa chữa tai nghe</strong><em>Driver, dây, đệm tai nghe <i aria-hidden="true">↗</i></em></span>
 </a>
 <a class="care-mosaic-tile" href="#dich-vu-keyboard" @click="document.getElementById('dich-vu-keyboard').open = true">
  @if($serviceMedia['keyboard'] ?? null)<img class="care-mosaic-image" src="{{ Storage::url($serviceMedia['keyboard']) }}" alt="" loading="lazy">@endif
  <span><strong>Sửa chữa bàn phím</strong><em>Switch, hotswap, vệ sinh <i aria-hidden="true">↗</i></em></span>
 </a>
 <a class="care-mosaic-tile" href="#dich-vu-mouse" @click="document.getElementById('dich-vu-mouse').open = true">
  @if($serviceMedia['mouse'] ?? null)<img class="care-mosaic-image" src="{{ Storage::url($serviceMedia['mouse']) }}" alt="" loading="lazy">@endif
  <span><strong>Sửa chữa chuột</strong><em>Switch, cuộn lăn, dây <i aria-hidden="true">↗</i></em></span>
 </a>
</section>
