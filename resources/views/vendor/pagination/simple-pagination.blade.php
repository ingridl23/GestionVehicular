@if ($paginator->hasPages())
@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $range = 2; // cantidad de números a cada lado
@endphp

@if ($paginator->total() > 0)
    <div class="pagination-info mb-2">
        Mostrando {{ $paginator->firstItem() }}
        – {{ $paginator->lastItem() }}
        de {{ $paginator->total() }}
    </div>
@endif

<nav class="flex items-center justify-center gap-1">

    {{-- Flecha izquierda --}}
    @if ($paginator->onFirstPage())
        <span class="pagination-btn disabled">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">‹</a>
    @endif

    {{-- Primera página --}}
    @if ($current > $range + 1)
        <a href="{{ $paginator->url(1) }}" class="pagination-btn">1</a>
        @if ($current > $range + 2)
            <span class="pagination-btn disabled">…</span>
        @endif
    @endif

    {{-- Páginas alrededor de la actual --}}
    @for ($i = max(1, $current - $range); $i <= min($last, $current + $range); $i++)
        @if ($i == $current)
            <span class="pagination-btn active">{{ $i }}</span>
        @else
            <a href="{{ $paginator->url($i) }}" class="pagination-btn">{{ $i }}</a>
        @endif
    @endfor

    {{-- Última página --}}
    @if ($current < $last - $range)
        @if ($current < $last - $range - 1)
            <span class="pagination-btn disabled">…</span>
        @endif
        <a href="{{ $paginator->url($last) }}" class="pagination-btn">{{ $last }}</a>
    @endif

    {{-- Flecha derecha --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">›</a>
    @else
        <span class="pagination-btn disabled">›</span>
    @endif

</nav>
@endif
