@props(['type' => 'button', 'href' => null])

@php
    $classes = '
        inline-block px-2 py-2
        bg-white 
        border border-gray-300 
        rounded-lg
        font-semibold text-xs text-gray-700 
        uppercase tracking-widest 
        shadow-sm 
        hover:bg-gray-50 
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