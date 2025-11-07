@props(['clases' => [], 'estadisticas' => null])

@php
    // Convierte el array (incluso si está vacío) en una Colección de Laravel
    $clases = collect($clases);
    
    // Define los días de la semana que se mostrarán
    $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    
    // Generar todas las franjas de 15 minutos que necesitamos mostrar
    $todasLasFranjas = collect();
    $clasesRendered = [];
    
    foreach ($clases as $clase) {
        $horaInicio = \Carbon\Carbon::parse($clase->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($clase->hora_fin);
        
        // Agregar todas las franjas de 15 minutos desde inicio hasta fin
        $horaActual = $horaInicio->copy();
        while ($horaActual->lt($horaFin)) {
            $franjaStr = $horaActual->format('H:i');
            if (!$todasLasFranjas->contains($franjaStr)) {
                $todasLasFranjas->push($franjaStr);
            }
            $horaActual->addMinutes(15);
        }
    }
    
    // Ordenar franjas
    if ($todasLasFranjas->isNotEmpty()) {
        $franjasHorarias = $todasLasFranjas->sort()->values()->map(function($inicio) {
            $inicioCarbon = \Carbon\Carbon::parse($inicio);
            $fin = $inicioCarbon->copy()->addMinutes(15);
            return [
                'inicio' => $inicio,
                'fin' => $fin->format('H:i')
            ];
        })->toArray();
    } else {
        $franjasHorarias = [
            ['inicio' => '07:00', 'fin' => '07:15'],
            ['inicio' => '08:00', 'fin' => '08:15'],
            ['inicio' => '09:00', 'fin' => '09:15'],
            ['inicio' => '10:00', 'fin' => '10:15']
        ];
    }
    
    // Preparar datos para el rowspan - cada clase ya viene agrupada del controlador
    $clasesConRowspan = [];
    foreach ($clases as $clase) {
        $horaInicio = \Carbon\Carbon::parse($clase->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($clase->hora_fin);
        $key = $clase->dia . '-' . $horaInicio->format('H:i');
        
        // Calcular rowspan
        $duracionMinutos = $horaInicio->diffInMinutes($horaFin);
        $rowspan = max(1, intval($duracionMinutos / 15));
        
        $clasesConRowspan[$key] = [
            'clase' => $clase,
            'rowspan' => $rowspan,
            'horaInicio' => $horaInicio,
            'horaFin' => $horaFin
        ];
        
        // Marcar todas las celdas ocupadas
        for ($i = 0; $i < $rowspan; $i++) {
            $franjaKey = $clase->dia . '-' . $horaInicio->copy()->addMinutes($i * 15)->format('H:i');
            $clasesRendered[$franjaKey] = true;
        }
    }
@endphp

<div class="space-y-6">
    {{-- Tarjetas de Estadísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Materias --}}
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl shadow-sm p-5 border border-indigo-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Materias</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $estadisticas['total_materias'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Total Grupos --}}
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-sm p-5 border border-purple-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Grupos</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $estadisticas['total_grupos'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Días con Clase --}}
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-sm p-5 border border-blue-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Días</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $estadisticas['dias_clase'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Horas Semanales --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl shadow-sm p-5 border border-emerald-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Horas Semanales</p>
                    @php
                        $minutosSemanales = $estadisticas['horas_semanales'] ?? 0;
                        if ($minutosSemanales >= 60) {
                            $horas = floor($minutosSemanales / 60);
                            $minutos = $minutosSemanales % 60;
                            $textoHoras = $minutos > 0 ? "{$horas}h {$minutos}min" : "{$horas}h";
                        } else {
                            $textoHoras = "{$minutosSemanales}min";
                        }
                    @endphp
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $textoHoras }}</p>
                </div>
            </div>
        </div>

        {{-- Aulas Asignadas --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl shadow-sm p-5 border border-amber-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Aulas</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $estadisticas['aulas_asignadas'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Horario Principal --}}
    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-7 h-7 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Mi Horario de Clases
        </h1>
        
        @if($clases->isEmpty())
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900">No tienes clases asignadas</p>
                <p class="mt-2 text-sm text-gray-500">Contacta al administrador para obtener tu horario</p>
            </div>
        @else
        <div class="overflow-x-auto -mx-6 px-6">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gradient-to-r from-indigo-600 to-blue-600">
                <tr>
                    {{-- Columna de Hora --}}
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-32 border-r border-indigo-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hora
                        </div>
                    </th>
                    {{-- Columnas de Días --}}
                    @foreach ($dias as $dia)
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r last:border-r-0 border-indigo-500">
                            {{ ucfirst($dia) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($franjasHorarias as $franja)
                    <tr class="transition-colors duration-150 hover:bg-indigo-50/50">
                        {{-- Celda de Hora --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 align-middle border-r border-gray-200 bg-gray-50">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $franja['inicio'] }}-{{ $franja['fin'] }}
                            </div>
                        </td>
                        
                        {{-- Celdas de Clases --}}
                        @foreach ($dias as $dia)
                            @php
                                $hora = $franja['inicio'];
                                $key = $dia . '-' . $hora;
                                $claseData = $clasesConRowspan[$key] ?? null;
                                $yaRenderizado = isset($clasesRendered[$key]) && !$claseData;
                            @endphp
                            
                            @if ($claseData)
                                {{-- Primera celda de la clase con rowspan --}}
                                <td class="px-3 py-3 align-top border-r last:border-r-0 border-gray-200 bg-white" rowspan="{{ $claseData['rowspan'] }}">
                                    @php
                                        $clase = $claseData['clase'];
                                        $inicio = $claseData['horaInicio'];
                                        $fin = $claseData['horaFin'];
                                        $duracionMinutos = $inicio->diffInMinutes($fin);
                                        
                                        // Formatear la duración
                                        if ($duracionMinutos >= 60) {
                                            $horas = floor($duracionMinutos / 60);
                                            $minutos = $duracionMinutos % 60;
                                            $duracionTexto = $minutos > 0 ? "{$horas}h {$minutos}min" : "{$horas}h";
                                        } else {
                                            $duracionTexto = "{$duracionMinutos}min";
                                        }
                                    @endphp
                                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-l-4 border-indigo-500 p-3 rounded-lg shadow-sm text-sm transform transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 hover:border-indigo-600 h-full flex flex-col">
                                        <p class="font-bold text-indigo-900 mb-2 text-base">{{ $clase->courseOffering->subject->name }}</p>
                                        <div class="space-y-1.5 text-gray-700 flex-grow">
                                            <p class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-indigo-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-semibold">{{ $inicio->format('H:i') }} - {{ $fin->format('H:i') }}</span>
                                                <span class="ml-2 text-gray-500">({{ $duracionTexto }})</span>
                                            </p>
                                            <p class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-indigo-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span class="font-semibold">Grupo:</span>
                                                <span class="ml-1">{{ $clase->courseOffering->group->name }}</span>
                                            </p>
                                            <p class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-indigo-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-semibold">Aula:</span>
                                                <span class="ml-1">{{ $clase->classroom->nro }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            @elseif (!$yaRenderizado)
                                {{-- Celda vacía --}}
                                <td class="px-3 py-3 align-top border-r last:border-r-0 border-gray-200 bg-white">
                                </td>
                            @endif
                            {{-- Si yaRenderizado es true, no renderizar nada (absorbida por rowspan) --}}
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif