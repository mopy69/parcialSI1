<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Franja Horaria') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Franja Horaria</h1>

        <div class="max-w-xl"> {{-- Limita el ancho del formulario --}}
            <form method="POST" action="{{ route('admin.timeslots.update', $timeslot) }}">
                @csrf
                @method('PUT')

                {{-- Campo 'day' (Día de la Semana) --}}
                <div class="mb-4">
                    {{-- CAMBIO: for ahora es 'day' --}}
                    <x-inicio.input-label for="day" :value="__('Día de la Semana')" /> 
                    {{-- CAMBIO: id y name ahora son 'day' --}}
                    <select id="day" name="day" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione un día</option>
                        
                        {{-- CAMBIO: old y $errors ahora usan 'day' --}}
                        <option value="lunes" {{ old('day', $timeslot->day) == 'lunes' ? 'selected' : '' }}>Lunes</option>
                        <option value="martes" {{ old('day', $timeslot->day) == 'martes' ? 'selected' : '' }}>Martes</option>
                        <option value="miercoles" {{ old('day', $timeslot->day) == 'miercoles' ? 'selected' : '' }}>Miércoles</option>
                        <option value="jueves" {{ old('day', $timeslot->day) == 'jueves' ? 'selected' : '' }}>Jueves</option>
                        <option value="viernes" {{ old('day', $timeslot->day) == 'viernes' ? 'selected' : '' }}>Viernes</option>
                        <option value="sabado" {{ old('day', $timeslot->day) == 'sabado' ? 'selected' : '' }}>Sábado</option>
                        <option value="domingo" {{ old('day', $timeslot->day) == 'domingo' ? 'selected' : '' }}>Domingo</option>
                    </select>
                    <x-inicio.input-error :messages="$errors->get('day')" class="mt-2" />
                </div>

                {{-- Campo 'start' (Hora Inicio) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="start" :value="__('Hora de Inicio')" />
                    <x-inicio.text-input id="start" 
                                         name="start" 
                                         type="time" 
                                         class="mt-1 block w-full" 
                                         {{-- CAMBIO: Utiliza $timeslot->start y 'start' en old --}}
                                         :value="old('start', $timeslot->start)" 
                                         required />
                    <x-inicio.input-error :messages="$errors->get('start')" class="mt-2" />
                </div>

                {{-- Campo 'end' (Hora Fin) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="end" :value="__('Hora de Fin')" />
                    <x-inicio.text-input id="end" 
                                         name="end" 
                                         type="time" 
                                         class="mt-1 block w-full" 
                                         {{-- CAMBIO: Utiliza $timeslot->end y 'end' en old --}}
                                         :value="old('end', $timeslot->end)" 
                                         required />
                    <x-inicio.input-error :messages="$errors->get('end')" class="mt-2" />
                </div>

                <div class="flex items-center justify-start gap-4 mt-6">
                    <x-inicio.primary-button>
                        Actualizar Horario
                    </x-inicio.primary-button>
                    
                    <x-inicio.secondary-button :href="route('admin.timeslots.index')">
                        Cancelar
                    </x-inicio.secondary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>