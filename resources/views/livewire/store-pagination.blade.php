@if ($paginator->hasPages())
    <nav class="store-pagination" aria-label="Phân trang {{ $itemLabel ?? 'sản phẩm' }}">
        <p>Hiển thị {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} trong {{ $paginator->total() }} {{ $itemLabel ?? 'sản phẩm' }}</p>
        <div class="store-pagination-pages">
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" @disabled($paginator->onFirstPage()) aria-label="Trang trước">‹</button>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="store-pagination-ellipsis">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" @if ($page == $paginator->currentPage()) class="active" aria-current="page" @endif>{{ $page }}</button>
                    @endforeach
                @endif
            @endforeach
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" @disabled(!$paginator->hasMorePages()) aria-label="Trang tiếp">›</button>
        </div>
    </nav>
    <style>
        .store-pagination { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; color:#737373; font-size:13px; }
        .store-pagination p { margin:0; }
        .store-pagination-pages { display:flex; align-items:center; flex-wrap:wrap; gap:6px; }
        .store-pagination-pages button, .store-pagination-ellipsis { display:grid; place-items:center; min-width:36px; height:36px; padding:0 8px; border:1px solid #e5e5e5; border-radius:9px; background:#fff; color:#171717; font:inherit; cursor:pointer; }
        .store-pagination-pages button.active { background:#171717; border-color:#171717; color:#fff; }
        .store-pagination-pages button:disabled { opacity:.4; cursor:not-allowed; }
        .store-pagination-ellipsis { border:0; }
    </style>
@endif
