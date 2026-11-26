<div>
 <x-information-layout>
  @php $informationSettings = \App\Models\Setting::query()->first(); @endphp
  <div class="info-prose">{!! $informationSettings?->privacy_policy !!}</div>
 </x-information-layout>
</div>
