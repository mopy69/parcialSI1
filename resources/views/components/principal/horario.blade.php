@props(['clases' => []])

@php
    // --- ¡AÑADE ESTA LÍNEA! ---
    // Convierte el array (incluso si está vacío) en una Colección de Laravel
    $clases = collect($clases);
    // -------------------------

    // Define los días de la semana
    $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    
    // Define las franjas horarias para la vista de escritorio
    $franjasHorarias = [
        '08:00', '09:00', '10:00', '11:00', '12:00', 
        '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
    ];

    // Agrupa las clases por día para la vista móvil
    // (Ahora $clases es una Colección, así que esto funciona)
    $clasesPorDia = $clases->groupBy('dia');

    // Crea un "mapa" de búsqueda rápida para la vista de escritorio
    $lookupClases = $clases->keyBy(function ($item) {
        return $item->dia . '-' . $item->hora_inicio;
    });
@endphp

<div class="lg:hidden space-y-4">
    @foreach ($dias as $dia)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            {{-- Encabezado del Día --}}
            <div class="bg-gray-50 p-3 border-b border-gray-200">
                <h3 class="font-semibold text-lg capitalize text-gray-800">{{ $dia }}</h3>
            </div>
            
            {{-- Lista de Clases para ese Día --}}
            <div class="p-4 space-y-3">
                @if (isset($clasesPorDia[$dia]) && $clasesPorDia[$dia]->count() > 0)
                    @foreach ($clasesPorDia[$dia]->sortBy('hora_inicio') as $clase)
                        <div class="border-l-4 border-indigo-500 pl-3">
                            <p class="font-semibold text-indigo-700">{{ $clase->nombre }}</p>
                            <p class="text-sm text-gray-600">{{ $clase->hora_inicio }} - {{ $clase->hora_fin }}</p>
                            <p class="text-sm text-gray-500">
                                @if(isset($clase->aula)) Aula: {{ $clase->aula }} @endif
                                @if(isset($clase->docente)) // Docente: {{ $clase->docente }} @endif
                            </p>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400">No hay clases programadas.</p>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="hidden lg:block bg-white shadow-md rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- Columna de Hora --}}
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        Hora
                    </th>
                    {{-- Columnas de Días --}}
                    @foreach ($dias as $dia)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider capitalize">
                            {{ $dia }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($franjasHorarias as $hora)
                    <tr>
                        {{-- Celda de Hora --}}
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top h-24">
                            {{ $hora }}
                        </td>
                        
                        {{-- Celdas de Clases --}}
                        @foreach ($dias as $dia)
                            @php
                                // Busca si existe una clase en esta franja exacta
                                $clase = $lookupClases->get($dia . '-' . $hora);
                            @endphp
                            
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 align-top h-24">
                                {{-- Si se encuentra una clase, se muestra la tarjeta --}}
                                @if ($clase)
                                    <div class="bg-indigo-50 border border-indigo-200 p-2 rounded-lg shadow-sm h-full">
                                        <p class="font-semibold text-indigo-700 text-xs">{{ $clase->nombre }}</p>
                                        <p class="text-xs text-gray-600">{{ $clase->hora_inicio }} - {{ $clase->hora_fin }}</p>
                                        <p class="text-xs text-gray-500">
                                           @if(isset($clase->aula)) Aula: {{ $clase->aula }} @endif
                                        </p>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>