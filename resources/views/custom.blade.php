@if ($paginator->hasPages())
<nav class="custom-pagination">
    <ul>
        {{-- First Page Arrow --}}
        <li><a href="{{ $paginator->url(1) }}#room-section">&laquo;</a></li>

        @php
            $total = $paginator->lastPage();
            $current = $paginator->currentPage();
            $maxVisible = 10;
            $start = max(min($current - floor($maxVisible / 2), $total - $maxVisible + 1), 1);
            $end = min($start + $maxVisible - 1, $total);
        @endphp

        {{-- Leading Ellipsis --}}
        @if ($start > 1)
            <li class="ellipsis" data-start="1" data-end="{{ $start - 1 }}">
                ...
                <form class="jump-form">
                    <input type="number" min="1" max="{{ $start - 1 }}" name="page" placeholder="Page" />
                    <button type="submit">Go</button>
                </form>
            </li>
        @endif

        {{-- Page Numbers --}}
        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $current)
                <li class="active"><a href="#">{{ $i }}</a></li>
            @else
                <li><a href="{{ $paginator->url($i) }}#room-section">{{ $i }}</a></li>
            @endif
        @endfor

        {{-- Trailing Ellipsis --}}
        @if ($end < $total)
            <li class="ellipsis" data-start="{{ $end + 1 }}" data-end="{{ $total }}">
                ...
                <form class="jump-form">
                    <input type="number" max="{{ $total }}" name="page" placeholder="Page" />
                    <button type="submit">Go</button>
                </form>
            </li>
        @endif

        {{-- Last Page Arrow --}}
        <li><a href="{{ $paginator->url($total) }}#room-section">&raquo;</a></li>
    </ul>
</nav>
@endif
