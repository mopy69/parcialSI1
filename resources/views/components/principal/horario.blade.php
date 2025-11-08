@props(['clases' => [], 'estadisticas' => null])

@php
    // Convierte el array (incluso si está vacío) en una Colección de Laravel
    $clases = collect($clases);
    
    // Define los días de la semana que se mostrarán
    $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    
    // Generar franjas horarias basadas en las clases existentes
    $todasLasFranjas = collect();
    
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
    
    // Ordenar franjas y generar array
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
        $franjasHorarias = [];
    }
    
    // Preparar datos para agrupación con rowspan
    $clasesAgrupadas = [];
    $clasesRendered = [];
    
    foreach ($clases as $clase) {
        $dia = $clase->dia;
        $horaInicio = \Carbon\Carbon::parse($clase->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($clase->hora_fin);
        $key = $dia . '-' . $horaInicio->format('H:i');
        
        // Si esta celda ya fue renderizada, saltarla
        if (isset($clasesRendered[$key])) {
            continue;
        }
        
        // Calcular rowspan basado en la duración
        $duracionMinutos = $horaInicio->diffInMinutes($horaFin);
        $rowspan = max(1, intval($duracionMinutos / 15));
        
        // Almacenar datos de la clase SOLO en la primera celda
        $clasesAgrupadas[$key] = [
            'clase' => $clase,
            'rowspan' => $rowspan,
            'horaInicio' => $horaInicio,
            'horaFin' => $horaFin
        ];
        
        // Marcar TODAS las celdas ocupadas por esta clase
        $horaActual = $horaInicio->copy();
        for ($i = 0; $i < $rowspan; $i++) {
            $franjaKey = $dia . '-' . $horaActual->format('H:i');
            $clasesRendered[$franjaKey] = true;
            $horaActual->addMinutes(15);
        }
    }
    
    $clasesAgrupadasCollection = collect($clasesAgrupadas);
    $clasesRenderedSet = collect($clasesRendered);
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
    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-3 sm:p-6 border border-gray-100">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        {{-- Vista de escritorio (tabla) --}}
        <div class="hidden lg:block overflow-x-auto -mx-6 px-6">
            @php
                // Agrupar franjas horarias consecutivas sin clases
                $franjasAgrupadasEscritorio = [];
                $i = 0;
                while ($i < count($franjasHorarias)) {
                    $franjaActual = $franjasHorarias[$i];
                    $inicioGrupo = $franjaActual['inicio'];
                    $finGrupo = $franjaActual['fin'];
                    $rowspan = 1;
                    
                    // Verificar si esta franja tiene clases en algún día
                    $tieneClases = false;
                    foreach ($dias as $dia) {
                        $key = $dia . '-' . $franjaActual['inicio'];
                        if ($clasesAgrupadasCollection->has($key)) {
                            $tieneClases = true;
                            break;
                        }
                    }
                    
                    // Si no tiene clases, intentar agrupar con las siguientes franjas vacías
                    if (!$tieneClases) {
                        $j = $i + 1;
                        while ($j < count($franjasHorarias)) {
                            $siguienteFranja = $franjasHorarias[$j];
                            $tieneClasesSiguiente = false;
                            
                            foreach ($dias as $dia) {
                                $key = $dia . '-' . $siguienteFranja['inicio'];
                                if ($clasesAgrupadasCollection->has($key)) {
                                    $tieneClasesSiguiente = true;
                                    break;
                                }
                            }
                            
                            if (!$tieneClasesSiguiente) {
                                $finGrupo = $siguienteFranja['fin'];
                                $rowspan++;
                                $j++;
                            } else {
                                break;
                            }
                        }
                        $i = $j;
                    } else {
                        $i++;
                    }
                    
                    $franjasAgrupadasEscritorio[] = [
                        'inicio' => $inicioGrupo,
                        'fin' => $finGrupo,
                        'rowspan' => $rowspan,
                        'franjaOriginal' => $franjaActual
                    ];
                }
            @endphp
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden" style="table-layout: fixed; width: 100%;">
                <colgroup>
                    <col style="width: 10%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                </colgroup>
                <thead class="bg-gradient-to-r from-indigo-600 to-blue-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-bold text-white uppercase tracking-wider border-r border-indigo-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Hora
                            </div>
                        </th>
                        @foreach ($dias as $dia)
                            <th class="px-3 py-3 text-center text-sm font-bold text-white uppercase tracking-wider border-r last:border-r-0 border-indigo-500">
                                {{ ucfirst($dia) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($franjasHorarias as $franja)
                        <tr class="hover:bg-indigo-50/30 transition-colors" style="height: 90px;">
                            <td class="px-4 py-2 text-sm font-bold text-gray-900 align-middle border-r border-gray-200 bg-gray-50">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="whitespace-nowrap">{{ $franja['inicio'] }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-5">{{ $franja['fin'] }}</span>
                                </div>
                            </td>
                            
                            @foreach ($dias as $dia)
                                @php
                                    $hora = $franja['inicio'];
                                    $key = $dia . '-' . $hora;
                                    $claseData = $clasesAgrupadasCollection->get($key);
                                    $yaRenderizado = $clasesRenderedSet->has($key) && !$claseData;
                                @endphp
                                
                                @if ($claseData)
                                    <td class="p-0 align-top border-r last:border-r-0 border-gray-200 bg-white relative" rowspan="{{ $claseData['rowspan'] }}" style="height: {{ $claseData['rowspan'] * 90 }}px;">
                                        @php
                                            $clase = $claseData['clase'];
                                            $inicio = $claseData['horaInicio'];
                                            $fin = $claseData['horaFin'];
                                            $duracionMinutos = $inicio->diffInMinutes($fin);
                                            $duracionTexto = $duracionMinutos >= 60 ? floor($duracionMinutos/60) . 'h' : $duracionMinutos . 'min';
                                        @endphp
                                        <div class="absolute inset-0 p-1.5">
                                            <div class="relative bg-gradient-to-br from-indigo-50 to-blue-50 border-l-4 border-indigo-600 rounded-lg shadow hover:shadow-lg transition-shadow h-full overflow-hidden p-2.5">
                                                <h4 class="text-sm font-bold text-indigo-900 leading-tight mb-2 line-clamp-2">
                                                    {{ $clase->courseOffering->subject->name }}
                                                </h4>
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center gap-1.5 text-xs">
                                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="font-bold text-indigo-900">{{ $inicio->format('H:i') }}-{{ $fin->format('H:i') }}</span>
                                                        <span class="text-gray-600 text-[10px]">({{ $duracionTexto }})</span>
                                                    </div>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-900 rounded text-[10px] font-bold">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                                            </svg>
                                                            {{ $clase->courseOffering->group->name }}
                                                        </span>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-900 rounded text-[10px] font-bold">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ $clase->classroom->nro }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @elseif (!$yaRenderizado)
                                    <td class="p-1 border-r last:border-r-0 border-gray-200"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Vista móvil/tablet (cards por día) --}}
        <div class="lg:hidden space-y-4">
            @foreach ($dias as $dia)
                @php
                    $clasesDelDia = $clases->where('dia', $dia)->sortBy('hora_inicio');
                @endphp
                
                @if($clasesDelDia->isNotEmpty())
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl overflow-hidden border-2 border-indigo-200">
                        {{-- Header del día --}}
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3">
                            <h3 class="text-base font-bold text-white uppercase flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                {{ ucfirst($dia) }}
                                <span class="ml-auto text-sm font-normal bg-white/20 px-2 py-0.5 rounded-full">{{ $clasesDelDia->count() }} {{ $clasesDelDia->count() == 1 ? 'clase' : 'clases' }}</span>
                            </h3>
                        </div>
                        
                        {{-- Clases del día --}}
                        <div class="p-3 space-y-3">
                            @foreach ($clasesDelDia as $clase)
                                @php
                                    $inicio = \Carbon\Carbon::parse($clase->hora_inicio);
                                    $fin = \Carbon\Carbon::parse($clase->hora_fin);
                                    $duracionMinutos = $inicio->diffInMinutes($fin);
                                    $duracionTexto = $duracionMinutos >= 60 ? floor($duracionMinutos/60) . 'h ' . ($duracionMinutos % 60) . 'min' : $duracionMinutos . 'min';
                                @endphp
                                
                                <div class="bg-white border-l-4 border-indigo-600 rounded-lg shadow-md hover:shadow-xl transition-all p-3">
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <h4 class="text-sm font-bold text-indigo-900 leading-tight flex-1">
                                            {{ $clase->courseOffering->subject->name }}
                                        </h4>
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold whitespace-nowrap">
                                            {{ $duracionTexto }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 rounded-lg border border-indigo-300 flex-1">
                                            <svg class="w-4 h-4 text-indigo-700" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-bold text-indigo-900">{{ $inicio->format('H:i') }} - {{ $fin->format('H:i') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-900 rounded-lg border border-blue-200 text-[11px] font-bold">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                            </svg>
                                            {{ $clase->courseOffering->group->name }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-900 rounded-lg border border-purple-200 text-[11px] font-bold">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                            </svg>
                                            Aula {{ $clase->classroom->nro }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @endif