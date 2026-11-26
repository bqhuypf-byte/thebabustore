<div>
 <x-information-layout>
  @php $informationSettings = \App\Models\Setting::query()->first(); @endphp
  <div class="info-prose">{!! $informationSettings?->return_policy !!}</div>
 </x-information-layout>
</div>
