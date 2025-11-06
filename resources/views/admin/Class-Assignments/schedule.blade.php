<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignar Horario: {{ $docente->name }}
        </h2>
    </x-slot>

    <div x-data="{ 
        showModal: false,
        selectedTimeslots: [],
        isSelecting: false,
        isDeselecting: false,
        startPoint: null,
        touchStartTime: null,
        lastTouchId: null,
        
        startSelection(id, event) {
            // Prevenir el comportamiento por defecto en dispositivos táctiles
            if (event.type === 'touchstart') {
                event.preventDefault();
                this.touchStartTime = Date.now();
                this.lastTouchId = id;
            }

            this.isSelecting = true;
            this.startPoint = id;
            this.isDeselecting = this.selectedTimeslots.includes(id);

            if (this.isDeselecting) {
                this.selectedTimeslots = this.selectedTimeslots.filter(t => t !== id);
            } else if (!this.selectedTimeslots.includes(id)) {
                this.selectedTimeslots.push(id);
            }
        },
        
        updateSelection(id, event) {
            // Para eventos táctiles, actualizar solo si es un movimiento significativo
            if (event.type === 'touchmove') {
                event.preventDefault();
                if (this.lastTouchId === id) return; // Evitar actualizaciones repetidas en la misma celda
                this.lastTouchId = id;
            }

            if (!this.isSelecting) return;
            
            if (this.isDeselecting) {
                this.selectedTimeslots = this.selectedTimeslots.filter(t => t !== id);
            } else if (!this.selectedTimeslots.includes(id)) {
                this.selectedTimeslots.push(id);
            }
        },
        
        endSelection(event) {
            if (event.type === 'touchend') {
                event.preventDefault();
                // Si fue un toque rápido (menos de 200ms), tratarlo como un clic
                if (Date.now() - this.touchStartTime < 200) {
                    this.toggleSingleTimeslot(this.lastTouchId, event);
                }
                this.touchStartTime = null;
                this.lastTouchId = null;
            }

            this.isSelecting = false;
            this.startPoint = null;
        },

        toggleSingleTimeslot(id, event) {
            if (!this.isSelecting || event.type === 'touchend') {
                const index = this.selectedTimeslots.indexOf(id);
                if (index === -1) {
                    this.selectedTimeslots.push(id);
                } else {
                    this.selectedTimeslots.splice(index, 1);
                }
                this.startPoint = id;
                event.stopPropagation();
            }
        }
    }">

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
                @click="showModal = selectedTimeslots.length > 0"
                :disabled="selectedTimeslots.length === 0"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Asignar Horarios
                <span class="ml-2 bg-indigo-500 px-2 py-0.5 rounded-full text-xs" x-show="selectedTimeslots.length > 0">
                    <span x-text="selectedTimeslots.length"></span>
                </span>
            </button>
        </div>

        {{-- Contenido Principal (La cuadrícula) --}}
        <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-4">Horario del Docente</h1>

            {{-- 
              CAMBIO: RESPONSIVE
              - Se eliminó 'hidden lg:block'. 
              - 'overflow-x-auto' ahora se aplica en todas las pantallas.
            --}}
            <div class="overflow-x-auto -mx-6 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            {{-- Columna de Hora --}}
                            <th class="px-2 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20 sm:w-32 sticky left-0 bg-gray-50 shadow-sm z-20">Hora</th>
                            @foreach ($dias as $dia)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider capitalize">{{ $dia }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($franjasHorarias as $hora)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900 align-middle sticky left-0 bg-gray-50/95 shadow-sm">{{ $hora }}</td>
                                
                                @foreach ($dias as $dia)
                                    @php
                                        $key = $dia . '-' . $hora;
                                        $clase = $clasesAsignadas->get($key);
                                        
                                        $timeslotDeCelda = $timeslots->first(function($ts) use ($dia, $hora) {
                                            return $ts->day == $dia && \Carbon\Carbon::parse($ts->start)->format('H:i') == $hora;
                                        });
                                    @endphp
                                    
                                    <td class="px-2 py-1 align-top">
                                        @if ($clase)
                                            {{-- Celda Ocupada --}}
                                            <div class="bg-indigo-100 border border-indigo-200 p-2 rounded-lg shadow-sm text-xs group relative">
                                                {{-- Contenido principal --}}
                                                <div class="space-y-1">
                                                    <p class="font-bold text-indigo-700">{{ $clase->courseOffering->subject->name }}</p>
                                                    <p class="text-gray-600">G: {{ $clase->courseOffering->group->name }}</p>
                                                    <p class="text-gray-600">A: {{ $clase->classroom->nro }}</p>
                                                </div>

                                                {{-- Botones flotantes (aparecen al hacer hover) --}}
                                                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                                    {{-- Botón Editar --}}
                                                    <a href="{{ route('admin.class-assignments.edit', $clase) }}" 
                                                       class="p-1 rounded-md bg-white/80 hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 transition-colors shadow-sm"
                                                       title="Editar asignación">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    {{-- Botón Eliminar --}}
                                                    <form action="{{ route('admin.class-assignments.destroy', $clase) }}" 
                                                          method="POST"
                                                          class="inline-block"
                                                          onsubmit="return confirm('¿Está seguro que desea eliminar esta asignación?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="p-1 rounded-md bg-white/80 hover:bg-red-50 text-red-600 hover:text-red-800 transition-colors shadow-sm"
                                                                title="Eliminar asignación">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            @if ($timeslotDeCelda)
                                                {{-- Celda Vacía (Botón '+') --}}
                                                <button 
                                                    data-timeslot-id="{{ $timeslotDeCelda->id }}"
                                                    @click="toggleSingleTimeslot({{ $timeslotDeCelda->id }}, $event)"
                                                    @mousedown.prevent="startSelection({{ $timeslotDeCelda->id }}, $event)"
                                                    @mouseover="updateSelection({{ $timeslotDeCelda->id }}, $event)"
                                                    @mouseup.prevent="endSelection($event)"
                                                    @touchstart.prevent="startSelection({{ $timeslotDeCelda->id }}, $event)"
                                                    @touchmove.prevent="updateSelection({{ $timeslotDeCelda->id }}, $event)"
                                                    @touchend.prevent="endSelection($event)"
                                                    class="w-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors py-2 cursor-pointer select-none touch-manipulation"
                                                    :class="{ 'bg-indigo-100 text-indigo-600': selectedTimeslots.includes({{ $timeslotDeCelda->id }}) }">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                </button>
                                            @else
                                                <div class="w-full h-full bg-gray-50/50 rounded-lg"></div>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 
          EL MODAL DE CREACIÓN (Ventana Emergente)
          (Con @click.stop para evitar que se cierre al hacer clic)
        --}}
        <div x-show="showModal" x-cloak 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            
            <div @mousedown.self="showModal = false" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
            
            <div @mousedown.stop
                 x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                 class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg z-10 relative">
                
                <h3 class="text-xl font-semibold mb-4">Asignar Nueva Clase</h3>
                
                <form method="POST" action="{{ route('admin.class-assignments.store') }}">
                    @csrf
                    <input type="hidden" name="docente_id" value="{{ $docente->id }}">
                    <input type="hidden" name="coordinador_id" value="{{ auth()->id() }}">
                    
                    <template x-for="timeslotId in selectedTimeslots" :key="timeslotId">
                        <input type="hidden" name="timeslot_ids[]" :value="timeslotId">
                    </template>

                    <p class="text-sm text-gray-600 mb-4">
                        Asignando a: <strong>{{ $docente->name }}</strong>
                    </p>
                    
                    <p class="text-sm text-gray-500 mb-4">
                        Horarios seleccionados: <span x-text="selectedTimeslots.length"></span>
                    </p>

                    {{-- 1. Seleccionar Oferta de Curso --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="course_offering_id" :value="__('Oferta de Curso (Materia/Grupo/Gestión)')" />
                        <select id="course_offering_id" name="course_offering_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                            <option value="">Seleccione una oferta...</option>
                            @foreach($courseOfferings as $oferta)
                                <option value="{{ $oferta->id }}">
                                    {{ $oferta->subject->name }} (Grupo {{ $oferta->group->name }}) - [{{ $oferta->term->name }}]
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
                        <x-inicio.secondary-button @click.prevent="showModal = false">
                            Cancelar
                        </x-inicio.secondary-button>
                        <x-inicio.primary-button type="submit">
                            Guardar Asignación
                        </x-inicio.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>