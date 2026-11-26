@foreach($classificationGroups as $group)
    @php($isColorGroup = in_array(\Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($group['name'])), ['mau', 'mau sac', 'color', 'colour'], true))
    <div class="step pd-classification-group {{ $isColorGroup ? 'purchase-color-group' : '' }}">
        <div class="step-label">@if($isColorGroup)<span>Màu sắc:</span><strong class="purchase-picked-color">{{ $group['selected'] }}</strong>@else<span>{{ $group['name'] }}:</span><strong class="purchase-picked-value">{{ $group['selected'] }}</strong>@endif</div>
        <div class="opt-grid {{ $isColorGroup ? 'pd-options-color' : 'pd-options-compact' }}" role="group" aria-label="{{ $group['name'] }}">
            @foreach($group['options'] as $option)
                <button type="button"
                    class="opt {{ $isColorGroup && $option['image'] ? 'has-thumb' : '' }} {{ $group['selected'] === $option['label'] ? 'sel' : '' }}"
                    wire:click="{{ $selectionAction }}({{ json_encode($group['name'], JSON_HEX_APOS | JSON_HEX_QUOT) }}, {{ json_encode($option['label'], JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                    aria-label="{{ $option['label'] }}{{ !$option['available'] ? ' — Hết hàng' : '' }}"
                    aria-pressed="{{ $group['selected'] === $option['label'] ? 'true' : 'false' }}"
                    title="{{ !$option['compatible'] ? 'Không có tổ hợp này' : (!$option['available'] ? 'Hết hàng' : $option['label']) }}"
                    @disabled(!$option['available'])>
                    @if($isColorGroup && $option['image'])
                        <img class="opt-thumb" src="{{ asset('storage/'.$option['image']) }}" alt="" loading="lazy">
                    @endif
                    <span class="opt-copy">
                        <span class="on">{{ $option['label'] }}</span>
                        @if(!$option['available'])<span class="oos">Hết hàng</span>@endif
                    </span>
                </button>
            @endforeach
        </div>
    </div>
@endforeach
