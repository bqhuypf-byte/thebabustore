<div>
 <x-information-layout>
  @php $informationSettings = \App\Models\Setting::query()->first(); @endphp
  <div class="info-prose">{!! $informationSettings?->warranty_policy !!}</div>
 </x-information-layout>
</div>
