@props(['href', 'active', 'icon' => null])

@php
// Comprueba si la ruta está activa.
// 'active' es una prop opcional que podemos pasar (ej. :active="request()->routeIs('admin.dashboard')")
// Si no se pasa, comprueba si la ruta actual coincide con el href.
$isActive = $active ?? request()->routeIs($href);

$classes = $isActive
    ? 'bg-gray-200 font-semibold text-gray-900' // Estilo Activo
    : 'text-gray-700 hover:bg-gray-100';        // Estilo Inactivo
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block px-4 py-2 rounded transition-colors duration-150 ' . $classes]) }}>
    <span class="link-text">{{ $slot }}</span>
</a>