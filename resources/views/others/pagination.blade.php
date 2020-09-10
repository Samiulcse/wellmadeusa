@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <div class="column hidden-xs-down"><a class="btn btn-outline-secondary btn-sm disabled"><i class="icon-arrow-left"></i>Previous&nbsp;</a></div>
    @else
        <div class="column hidden-xs-down"><a class="btn btn-outline-secondary btn-sm page-link" href="{{ $paginator->previousPageUrl() }}"><i class="icon-arrow-left"></i>Previous&nbsp;</a></div>
    @endif

    <div class="column text-center">
        <ul class="pages">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>...</li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active"><a href="#">{{ $page }}</a></li>
                        @else
                            <li><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>

    @if ($paginator->hasMorePages())
        <div class="column text-right hidden-xs-down"><a class="btn btn-outline-secondary btn-sm page-link" href="{{ $paginator->nextPageUrl() }}">Next&nbsp;<i class="icon-arrow-right"></i></a></div>
    @else
        <div class="column text-right hidden-xs-down"><a class="btn btn-outline-secondary btn-sm disabled">Next&nbsp;<i class="icon-arrow-right"></i></a></div>
    @endif
@endif