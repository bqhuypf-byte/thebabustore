<div>
 <x-information-layout>
  @php $contactSettings = \App\Models\Setting::query()->first(); @endphp
  <section class="info-contact-details"><h2>Thông tin liên hệ</h2><div>@if($contactSettings?->hotline)<a href="tel:{{ $contactSettings->hotline }}">{{ $contactSettings->hotline }}</a>@endif @if($contactSettings?->email)<a href="mailto:{{ $contactSettings->email }}">{{ $contactSettings->email }}</a>@endif @if($contactSettings?->address)<p>{{ $contactSettings->address }}</p>@endif</div></section>
  <h2>Gửi tin nhắn</h2>
  <form class="info-contact-form" wire:submit.prevent="submit">
   <div><label for="contact-name">Họ và tên *</label><input id="contact-name" name="name" autocomplete="name" wire:model="name" required maxlength="255" @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror>@error('name')<p class="info-error" id="contact-name-error">{{ $message }}</p>@enderror</div>
   <div><label for="contact-email">Email *</label><input id="contact-email" name="email" type="email" autocomplete="email" wire:model="email" required @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror>@error('email')<p class="info-error" id="contact-email-error">{{ $message }}</p>@enderror</div>
   <div class="info-form-wide"><label for="contact-phone">Số điện thoại *</label><input id="contact-phone" name="phone" type="tel" autocomplete="tel" wire:model="phone" required maxlength="15" @error('phone') aria-invalid="true" aria-describedby="contact-phone-error" @enderror>@error('phone')<p class="info-error" id="contact-phone-error">{{ $message }}</p>@enderror</div>
   <div class="info-form-wide"><label for="contact-body">Nội dung cần hỗ trợ *</label><textarea id="contact-body" name="body" wire:model="body" rows="6" required @error('body') aria-invalid="true" aria-describedby="contact-body-error" @enderror></textarea>@error('body')<p class="info-error" id="contact-body-error">{{ $message }}</p>@enderror</div>
   <div class="info-form-wide"><button type="submit" wire:loading.attr="disabled" wire:target="submit"><span wire:loading.remove wire:target="submit">Gửi tin nhắn</span><span wire:loading wire:target="submit">Đang gửi...</span></button></div>
  </form>
  @if($contactSettings?->map)<section class="info-contact-map"><h2>Bản đồ cửa hàng</h2><div>{!! $contactSettings->map !!}</div></section>@endif
 </x-information-layout>
</div>
