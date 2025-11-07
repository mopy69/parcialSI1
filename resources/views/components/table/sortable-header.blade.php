@props(['column', 'label'])

@php
    $currentSort = request('sort');
    $currentDirection = request('direction', 'asc');
    $newDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
    $isSorted = $currentSort === $column;
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider']) }}>
    <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection]) }}" 
       class="flex items-center gap-1 hover:text-gray-700 group">
        <span>{{ $label }}</span>
        <span class="inline-flex flex-col">
            @if($isSorted)
                @if($currentDirection === 'asc')
                    <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                    </svg>
                @else
                    <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/>
                    </svg>
                @endif
            @else
                <svg class="w-3 h-3 text-gray-300 group-hover:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                </svg>
            @endif
        </span>
    </a>
</th>
