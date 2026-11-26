<div>
 <x-information-layout>
  @php $informationSettings = \App\Models\Setting::query()->first(); @endphp
  <div class="info-prose">{!! $informationSettings?->introduction !!}</div>
 </x-information-layout>
</div>
