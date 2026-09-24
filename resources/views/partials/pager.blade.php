@php
    $paginator = $paginator ?? null;
@endphp
@if ($paginator && $paginator->hasPages())
    <nav class="pager" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="is-disabled" aria-disabled="true" aria-label="Sebelumnya">&larr;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">&larr;</a>
        @endif

        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="is-current" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">&rarr;</a>
        @else
            <span class="is-disabled" aria-disabled="true" aria-label="Berikutnya">&rarr;</span>
        @endif
    </nav>
@endif
