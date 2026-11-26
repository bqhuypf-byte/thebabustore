<section class="pd-retail-card">
    <h2>Thông số kỹ thuật</h2>
    <div class="pd-retail-spec-table">
        @if(count($productContent['groups']))
            @foreach($productContent['groups'] as $specGroup)
                <h3>{{ $specGroup['title'] }}</h3>
                <table><tbody>@foreach($specGroup['rows'] as $row)<tr><th scope="row">{{ $row['label'] }}</th><td>{{ $row['value'] }}</td></tr>@endforeach</tbody></table>
            @endforeach
        @else
            <table><tbody><tr><th>Tên sản phẩm</th><td>{{ $product->name }}</td></tr>@if($product->brand)<tr><th>Thương hiệu</th><td>{{ $product->brand->name }}</td></tr>@endif</tbody></table>
        @endif
    </div>
    <a class="pd-retail-spec-link" href="#panel-specifications" wire:click="$set('activeTab', 'specifications')">Xem thông số đầy đủ</a>
</section>
