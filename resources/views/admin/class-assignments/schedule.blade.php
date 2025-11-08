<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignar Horario: {{ $docente->name }}
        </h2>
    </x-slot>

    {{-- 
      CAMBIO: x-data corregido.
      - 'shouldOpenOnLoad' se usa para abrir el modal si hay errores de validación.
      - 'selectedTimeslots' ahora es un array de IDs (números).
    --}}
    <div x-data="{ 
            showModal: false, 
            shouldOpenOnLoad: {{ $shouldOpenModal ? 'true' : 'false' }}, {{-- Abre el modal cuando la validación lo requiere --}}
            selectedTimeslots: @json(old('timeslot_ids', [])), {{-- Rellena los slots si la validación falla --}}
            isSelecting: false,
            isDeselecting: false,
            
            init() {
                this.selectedTimeslots = Array.isArray(this.selectedTimeslots)
                    ? this.selectedTimeslots.map(Number)
                    : [];
                if (this.shouldOpenOnLoad) {
                    this.showModal = true;
                }
            },
            
            startSelection(id) {
                this.isSelecting = true;
                this.isDeselecting = this.selectedTimeslots.includes(id); // Decide si estamos seleccionando o deseleccionando
                this.toggleSelection(id);
            },
            
            updateSelection(id) {
                if (!this.isSelecting) return;
                this.toggleSelection(id, false); // 'false' evita deseleccionar al arrastrar
            },

            endSelection() {
                this.isSelecting = false;
            },
            
            toggleSelection(id, allowToggle = true) {
                const index = this.selectedTimeslots.indexOf(id);
                if (index === -1) {
                    // Solo añade si NO estamos deseleccionando
                    if (!this.isDeselecting) {
                        this.selectedTimeslots.push(id);
                    }
                } else {
                    // Solo quita si SÍ estamos deseleccionando (o si es un clic único)
                    if (this.isDeselecting || allowToggle) {
                        this.selectedTimeslots.splice(index, 1);
                    }
                }
            }
        }"
        @mouseup.prevent="endSelection()"
        @mouseleave="endSelection()" {{-- Si el ratón sale de la tabla, detiene la selección --}}
    >

        {{-- Barra de acciones --}}
        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('admin.class-assignments.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
            <button
                type="button"
                @click="showModal = true"
                :disabled="selectedTimeslots.length === 0"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Asignar Horarios
                <span class="ml-2 bg-indigo-500 px-2 py-0.5 rounded-full text-xs" x-show="selectedTimeslots.length > 0">
                    (<span x-text="selectedTimeslots.length"></span>)
                </span>
            </button>
        </div>

        {{-- Información de la Gestión Actual --}}
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-indigo-800">
                    Gestión: <strong>{{ $currentTerm->name }}</strong>
                </span>
            </div>
        </div>

        {{-- 
          Contenido Principal (La cuadrícula) 
          CAMBIO: Eliminadas las etiquetas </span> y </button> sueltas
        --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-7 h-7 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Horario del Docente
            </h1>

            <div class="overflow-x-auto -mx-6 sm:mx-0 rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 sm:px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-24 sm:w-32 sticky left-0 bg-indigo-600 shadow-md z-20">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Hora
                                </div>
                            </th>
                            @foreach ($dias as $dia)
                                <th class="px-4 py-4 text-center text-sm font-bold text-white uppercase tracking-wider min-w-[180px]">
                                    {{ $dia }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($franjasHorarias as $franja)
                            <tr class="hover:bg-indigo-50/30 transition-colors" style="height: 70px;">
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-xs font-bold text-gray-700 align-middle sticky left-0 bg-gray-50 shadow-sm z-10 border-r border-gray-200">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-indigo-700">{{ $franja['inicio'] }}</span>
                                        <span class="text-gray-400 text-[10px]">-</span>
                                        <span class="text-indigo-700">{{ $franja['fin'] }}</span>
                                    </div>
                                </td>
                                
                                @foreach ($dias as $dia)
                                    @php
                                        $hora = $franja['inicio'];
                                        $key = $dia . '-' . $hora;
                                        $grupoClases = $clasesAsignadasAgrupadas->get($key);
                                        $yaRenderizado = $clasesRenderedSet->has($key);
                                        
                                        $timeslotDeCelda = $timeslots->first(function($ts) use ($dia, $hora) {
                                            return $ts->day == $dia && \Carbon\Carbon::parse($ts->start)->format('H:i') == $hora;
                                        });
                                    @endphp
                                    
                                    @if ($grupoClases)
                                        {{-- Esta es la primera celda del grupo, renderizar con rowspan --}}
                                        <td class="px-0 py-0 align-top relative" rowspan="{{ $grupoClases['rowspan'] }}" style="height: {{ $grupoClases['rowspan'] * 70 }}px;">
                                            @php
                                                $clase = $grupoClases['primera'];
                                                $todasLasClases = $grupoClases['clases'];
                                                $horaInicioGrupo = \Carbon\Carbon::parse($todasLasClases->first()->timeslot->start)->format('H:i');
                                                $horaFinGrupo = \Carbon\Carbon::parse($todasLasClases->last()->timeslot->end)->format('H:i');
                                            @endphp
                                            
                                            <div class="absolute inset-0 p-2">
                                                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-l-4 border-indigo-500 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-xs group relative h-full flex flex-col overflow-hidden">
                                                    <div class="p-3 flex-1 flex flex-col">
                                                        <div class="space-y-2 flex-1">
                                                            <div class="flex items-start justify-between">
                                                                <p class="font-bold text-indigo-900 text-sm leading-tight pr-2">{{ $clase->courseOffering->subject->name }}</p>
                                                            </div>
                                                            
                                                            <div class="space-y-1.5">
                                                                <div class="flex items-center text-gray-700">
                                                                    <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                    </svg>
                                                                    <span class="font-semibold">Grupo {{ $clase->courseOffering->group->name }}</span>
                                                                </div>
                                                                
                                                                <div class="flex items-center text-gray-700">
                                                                    <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                    <span class="font-medium">Aula {{ $clase->classroom->nro }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-auto pt-2 border-t border-indigo-200">
                                                            <div class="flex items-center text-indigo-700 font-semibold">
                                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <span class="text-[11px]">{{ $horaInicioGrupo }} - {{ $horaFinGrupo }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1.5">
                                                        <button 
                                                            type="button"
                                                            onclick="editarGrupoClases({{ $todasLasClases->pluck('id') }})"
                                                           class="p-2 rounded-lg bg-white hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 transition-all shadow-md hover:shadow-lg"
                                                           title="Editar asignaciones">
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <form id="delete-form-{{ $clase->id }}" 
                                                              action="{{ route('admin.class-assignments.destroy-group') }}" 
                                                              method="POST"
                                                              class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            @foreach($todasLasClases as $claseGrupo)
                                                                <input type="hidden" name="class_ids[]" value="{{ $claseGrupo->id }}">
                                                            @endforeach
                                                            <button type="submit" 
                                                                    onclick="return confirm('¿Está seguro que desea eliminar todas estas asignaciones ({{ $todasLasClases->count() }} bloques)?');"
                                                                    class="p-2 rounded-lg bg-white hover:bg-red-50 text-red-600 hover:text-red-800 transition-all shadow-md hover:shadow-lg"
                                                                    title="Eliminar asignaciones">
                                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @elseif (!$yaRenderizado)
                                        {{-- Celda vacía normal --}}
                                        <td class="px-2 py-2 align-top bg-gray-50/30">
                                            @if ($timeslotDeCelda)
                                                <button 
                                                    type="button"
                                                    data-timeslot-id="{{ $timeslotDeCelda->id }}"
                                                    @click.self="toggleSelection({{ $timeslotDeCelda->id }})"
                                                    @mousedown.prevent="startSelection({{ $timeslotDeCelda->id }})"
                                                    @mouseover.prevent="updateSelection({{ $timeslotDeCelda->id }})"
                                                    @touchend.prevent="endSelection()"
                                                    @touchstart.prevent="startSelection({{ $timeslotDeCelda->id }})"
                                                    class="w-full h-full min-h-[60px] flex items-center justify-center text-gray-300 hover:text-indigo-400 hover:bg-indigo-50 rounded-lg transition-all duration-150 cursor-pointer select-none touch-manipulation border-2 border-dashed border-transparent hover:border-indigo-300"
                                                    :class="{ 'bg-indigo-100 text-indigo-600 border-indigo-400 shadow-inner': selectedTimeslots.includes({{ $timeslotDeCelda->id }}) }">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                </button>
                                            @else
                                                <div class="w-full h-full min-h-[60px] bg-gray-100/30 rounded-lg"></div>
                                            @endif
                                        </td>
                                    @endif
                                    {{-- Si yaRenderizado es true, no renderizar nada (la celda fue absorbida por rowspan) --}}
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 
          EL MODAL DE CREACIÓN (Ventana Emergente)
          CAMBIO: Usamos '@click.self' para el fondo, para que 
          solo se cierre si haces clic EN EL FONDO, no en el modal.
        --}}
       <div x-show="showModal" x-cloak 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
           @click.self="showModal = false">
            
            <div class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
            
            {{-- 
              CAMBIO: Eliminado '@click.stop'. Ya no es necesario
              porque el fondo usa '@click.self'.
            --}}
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                 class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg z-10 relative">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Asignar Nueva Clase</h3>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('admin.class-assignments.store') }}">
                    @csrf
                    <input type="hidden" name="docente_id" value="{{ $docente->id }}">
                    <input type="hidden" name="coordinador_id" value="{{ auth()->id() }}">
                    
                    {{-- CAMBIO: El bucle <template> envía los IDs seleccionados --}}
                    <template x-for="timeslotId in selectedTimeslots" :key="timeslotId">
                        <input type="hidden" name="timeslot_ids[]" :value="timeslotId">
                    </template>

                    <p class="text-sm text-gray-600 mb-2">
                        Asignando a: <strong>{{ $docente->name }}</strong>
                    </p>
                    <p class="text-sm text-gray-600 mb-4">
                        Gestión: <strong>{{ $currentTerm->name }}</strong>
                    </p>
                    <p class="text-sm text-gray-500 mb-4">
                        Horarios seleccionados: <span x-text="selectedTimeslots.length"></span>
                    </p>

                    {{-- 1. Seleccionar Oferta de Curso --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="course_offering_id" :value="__('Materia y Grupo')" />
                        <select id="course_offering_id" name="course_offering_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                            <option value="">Seleccione una materia y grupo...</option>
                            @foreach($courseOfferings as $oferta)
                                <option value="{{ $oferta->id }}">
                                    {{ $oferta->subject->name }} - Grupo {{ $oferta->group->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-inicio.input-error :messages="$errors->get('course_offering_id')" class="mt-2" />
                    </div>
                    
                    {{-- 3. Seleccionar Aula --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="classroom_id" :value="__('Aula')" />
                        <select id="classroom_id" name="classroom_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                            <option value="">Seleccione un aula...</option>
                            @foreach($classrooms as $aula)
                                <option value="{{ $aula->id }}">
                                    {{ $aula->nro }} ({{ $aula->type }})
                                </option>
                            @endforeach
                        </select>
                        <x-inicio.input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-6">
                        <button type="button" @click="showModal = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancelar
                        </button>
                        <x-inicio.primary-button type="submit">
                            Guardar Asignación
                        </x-inicio.primary-button>
                    </div>
                                </form>
            </div>
        </div>
    </div>

    <script>
        function editarGrupoClases(ids) {
            if (ids.length === 0) return;
            
            // Por ahora, redirigir a la edición de la primera clase del grupo
            // En el futuro se podría implementar un modal de edición masiva
            const primeraId = Array.isArray(ids) ? ids[0] : ids;
            window.location.href = "{{ route('admin.class-assignments.index') }}/" + primeraId + "/edit";
        }
    </script>
</x-layouts.admin>
