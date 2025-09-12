@if ($paginator->hasPages())
<nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
    <ul class="pagination-list" style="list-style:none;display:flex;gap:6px;padding-left:0;flex-wrap:wrap;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li aria-disabled="true" aria-label="Sebelumnya"><span style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;opacity:.4">«</span></li>
        @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;">«</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li aria-disabled="true"><span style="padding:6px 10px;">{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li aria-current="page"><span style="background:#2563eb;color:#fff;padding:6px 12px;border-radius:6px;font-weight:600;">{{ $page }}</span></li>
        @else
        <li><a href="{{ $url }}" style="padding:6px 12px;border:1px solid #ddd;border-radius:6px;text-decoration:none;">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya" style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;">»</a></li>
        @else
        <li aria-disabled="true" aria-label="Berikutnya"><span style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;opacity:.4">»</span></li>
        @endif
    </ul>
</nav>
@endif