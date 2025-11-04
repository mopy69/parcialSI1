@props(['type' => 'submit', 'href' => null])

@php
    $classes = '
        inline-block px-2 py-2
        bg-indigo-600 
        border border-transparent 
        rounded-lg 
        font-semibold text-xs text-white 
        uppercase tracking-widest 
        shadow-sm 
        hover:bg-indigo-700 
        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
        transition ease-in-out duration-150
    ';
@endphp

@if ($href)
    <a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif