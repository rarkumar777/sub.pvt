@if ($paginator->hasPages())
<div class="row pad">
    <div class="align-center">
        <nav>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <a class="btn grey small" style="margin:2px"><i class="fa-angle-double-left"></i></a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn blue small" style="margin:2px"><i class="fa-angle-double-left"></i></a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <a class="btn grey small" style="margin:2px">{{ $element }}</a>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a class="btn red small" style="margin:2px">{{ $page }}</a>
                        @else
                            <a href="{{ $url }}" class="btn blue small" style="margin:2px">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn blue small" style="margin:2px"><i class="fa-angle-double-right"></i></a>
            @else
                <a class="btn grey small" style="margin:2px"><i class="fa-angle-double-right"></i></a>
            @endif
        </nav>
        <small>Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} results</small>
    </div>
</div>
@endif
