<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignar Horario: {{ $docente->name }}
        </h2>
    </x-slot>

    @php
        // Definir arrays directamente en la vista para evitar cookies grandes
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $franjasHorarias = [
            '07:00', '07:15', '07:30', '07:45',
            '08:00', '08:15', '08:30', '08:45',
            '09:00', '09:15', '09:30', '09:45',
            '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45',
            '12:00', '12:15', '12:30', '12:45',
            '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45',
            '15:00', '15:15', '15:30', '15:45',
            '16:00', '16:15', '16:30', '16:45',
            '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45',
            '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45',
            '21:00', '21:15', '21:30', '21:45',
            '22:00', '22:15', '22:30', '22:45',
            '23:00'
        ];
    @endphp

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
        <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-4">Horario del Docente</h1>

            <div class="overflow-x-auto -mx-6 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-2 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20 sm:w-32 sticky left-0 bg-gray-50 shadow-sm z-20">Hora</th>
                            @foreach ($dias as $dia)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $dia }}</th>
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
                                                <div class="space-y-1">
                                                    <p class="font-bold text-indigo-700">{{ $clase->courseOffering->subject->name }}</p>
                                                    <p class="text-gray-600">G: {{ $clase->courseOffering->group->name }}</p>
                                                    <p class="text-gray-600">A: {{ $clase->classroom->nro }}</p>
                                                </div>
                                                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                                    <a href="{{ route('admin.class-assignments.edit', $clase) }}" 
                                                       class="p-1 rounded-md bg-white/80 hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 transition-colors shadow-sm"
                                                       title="Editar asignación">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
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
                                                {{-- 
                                                  Celda Vacía (Botón '+')
                                                  CAMBIO: Opacidad (text-gray-200) y Lógica de Clic/Arrastrar
                                                --}}
                                                <button 
                                                    type="button"
                                                    data-timeslot-id="{{ $timeslotDeCelda->id }}"
                                                    @click.self="toggleSelection({{ $timeslotDeCelda->id }})" {{-- Clic único --}}
                                                    @mousedown.prevent="startSelection({{ $timeslotDeCelda->id }})" {{-- Inicio de arrastre --}}
                                                    @mouseover.prevent="updateSelection({{ $timeslotDeCelda->id }})" {{-- Arrastre --}}
                                                    @touchend.prevent="endSelection()"
                                                    @touchstart.prevent="startSelection({{ $timeslotDeCelda->id }})"
                                                    class="w-full flex items-center justify-center text-gray-200 hover:text-gray-400 hover:bg-gray-100 rounded-lg transition-colors py-2 cursor-pointer select-none touch-manipulation"
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
</x-layouts.admin>