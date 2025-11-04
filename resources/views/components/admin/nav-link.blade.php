@props(['href', 'active'])

@php
$isActive = $active ?? request()->routeIs($href);

// --- CAMBIO DE ESTILO AQUÍ ---
// Clases base para la tarjeta flotante
$baseClasses = 'flex items-center gap-x-3 p-3 rounded-lg bg-white shadow-sm transition-all duration-150';

$classes = $isActive
    // Activo: Texto índigo, fuente más gruesa y una sombra más pronunciada
    ? 'text-indigo-700 font-semibold shadow-md' 
    // Inactivo: Texto gris, hover sutil
    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $classes]) }}>
    
    @if (isset($icon))
        <span class="w-5 h-5 flex-shrink-0">
            {{ $icon }}
        </span>
    @endif
    
    <span class="link-text">{{ $slot }}</span>
</a>