@if(count($productContent['groups']))
    <div class="pd-technical-groups">
        <h2>Thông số kỹ thuật</h2>
        @if($selected)<p class="pd-panel-intro">Phiên bản đang xem: <strong>{{ $selected->name }}</strong></p>@endif
        @foreach($productContent['groups'] as $group)
            <section class="pd-spec-group">
                <h3>{{ $group['title'] }}</h3>
                <table class="spec-table pd-specs"><tbody>
                    @foreach($group['rows'] as $row)
                        <tr><th scope="row">{{ $row['label'] }}</th><td>{{ $row['value'] }}</td></tr>
                    @endforeach
                </tbody></table>
            </section>
        @endforeach
    </div>
@endif
