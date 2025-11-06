@props(['clases' => [], 'estadisticas' => null])

@php
    // Convierte el array (incluso si está vacío) en una Colección de Laravel
    $clases = collect($clases);
    
    // Define los días de la semana que se mostrarán
    $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    
    // Obtener todas las horas únicas de las clases asignadas
    $horasClases = $clases->map(function ($clase) {
        return \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i');
    })->unique()->sort()->values()->all();

    // Si no hay clases, mostrar algunas horas por defecto
    $franjasHorarias = !empty($horasClases) ? $horasClases : ['07:00', '08:00', '09:00', '10:00'];

    // Agrupa las clases por día y hora
    $clasesAsignadas = $clases->keyBy(function ($clase) {
        return $clase->dia . '-' . \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i');
    });
@endphp

<div class="space-y-6">
    {{-- Tarjetas de Estadísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Materias --}}
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-sm p-4 border border-indigo-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Materias</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $estadisticas['total_materias'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Grupos --}}
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow-sm p-4 border border-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Grupos Asignados</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $estadisticas['total_grupos'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Días con Clase --}}
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-sm p-4 border border-blue-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Días con Clase</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $estadisticas['dias_clase'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Horas Semanales --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg shadow-sm p-4 border border-emerald-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Horas Semanales</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ $estadisticas['horas_semanales'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aulas Asignadas --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-sm p-4 border border-amber-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aulas Asignadas</p>
                        <p class="text-2xl font-bold text-amber-600">{{ $estadisticas['aulas_asignadas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Horario Principal --}}
    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-4">Mi Horario de Clases</h1>
        
        @if($clases->isEmpty())
            <div class="text-gray-500 text-center py-8">
                No tienes clases asignadas en este momento.
            </div>
        @endif
        
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-sm border border-gray-200 rounded-lg">
            <thead class="bg-gradient-to-r from-indigo-50 to-blue-50">
                <tr>
                    {{-- Columna de Hora --}}
                    <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider w-32 border-r border-gray-200">
                        Hora
                    </th>
                    {{-- Columnas de Días --}}
                    @foreach ($dias as $dia)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider border-r last:border-r-0 border-gray-200">
                            {{ $dia }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($franjasHorarias as $hora)
                    <tr class="transition-colors duration-200 hover:bg-indigo-50/30">
                        {{-- Celda de Hora --}}
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900 align-middle border-r border-gray-200 bg-gray-50/50">
                            {{ $hora }}
                        </td>
                        
                        {{-- Celdas de Clases --}}
                        @foreach ($dias as $dia)
                            @php
                                $key = $dia . '-' . $hora;
                                $clase = $clasesAsignadas->get($key);
                            @endphp
                            
                            <td class="px-2 py-2 align-top border-r last:border-r-0 border-gray-200">
                                @if ($clase)
                                    <div class="bg-white border border-indigo-200 p-2.5 rounded-lg shadow-sm text-xs transform transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                                        <p class="font-bold text-indigo-700 mb-1">{{ $clase->courseOffering->subject->name }}</p>
                                        <div class="flex flex-col space-y-1 text-gray-600">
                                            <p class="flex items-center">
                                                <span class="font-medium">Grupo:</span>
                                                <span class="ml-1">{{ $clase->courseOffering->group->name }}</span>
                                            </p>
                                            <p class="flex items-center">
                                                <span class="font-medium">Aula:</span>
                                                <span class="ml-1">{{ $clase->classroom->nro }}</span>
                                            </p>
                                        </div>
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