<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Asignación de Clase') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Asignación de Clase</h1>
        {{-- Mostramos info de la asignación que se está editando --}}
        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm">
            <p class="mb-2 font-semibold text-gray-700">Estás editando un grupo de clases para:</p>
            <ul class="list-disc list-inside ml-2 text-gray-700">
                <li><strong>Docente:</strong> {{ $classAssignment->userDocente->name }}</li>
                <li><strong>Materia:</strong> {{ $classAssignment->courseOffering->subject->name }}</li>
                <li><strong>Grupo:</strong> {{ $classAssignment->courseOffering->group->name }}</li>
                <li><strong>Gestión:</strong> {{ $classAssignment->courseOffering->term->name }}</li>
                <li><strong>Horario:</strong> {{ Str::ucfirst($classAssignment->timeslot->day) }} ({{ $classAssignment->timeslot->start }} - {{ $classAssignment->timeslot->end }})</li>
            </ul>
            <p class="mt-3 text-xs text-indigo-600 bg-indigo-50 p-2 rounded">
                <strong>Nota:</strong> Los cambios se aplicarán a todas las clases consecutivas del mismo grupo y aula.
            </p>
        </div>

        <div class="max-w-xl"> {{-- Limita el ancho del formulario --}}
            <form method="POST" action="{{ route('admin.class-assignments.update', $classAssignment) }}">
                @csrf
                @method('PUT')
                
                {{-- Campos ocultos necesarios --}}
                <input type="hidden" name="coordinador_id" value="{{ auth()->id() }}">
                <input type="hidden" name="docente_id" value="{{ $classAssignment->docente_id }}">
                <input type="hidden" name="timeslot_id" value="{{ $classAssignment->timeslot_id }}">

                {{-- 1. Cambiar Oferta de Curso --}}
                <div class="mb-4">
                    <x-inicio.input-label for="course_offering_id" :value="__('Oferta de Curso (Materia/Grupo/Gestión)')" />
                    <select id="course_offering_id" name="course_offering_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione una oferta...</option>
                        @foreach($courseOfferings as $oferta)
                            <option value="{{ $oferta->id }}" 
                                {{-- Comprueba el valor antiguo o el actual --}}
                                {{ old('course_offering_id', $classAssignment->course_offering_id) == $oferta->id ? 'selected' : '' }}>
                                {{ $oferta->subject->name }} (Grupo {{ $oferta->group->name }}) - [{{ $oferta->term->name }}]
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('course_offering_id')" class="mt-2" />
                </div>

                {{-- 2. Cambiar Aula --}}
                <div class="mb-4">
                    <x-inicio.input-label for="classroom_id" :value="__('Aula')" />
                    <select id="classroom_id" name="classroom_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione un aula...</option>
                        @foreach($classrooms as $aula)
                            <option value="{{ $aula->id }}" 
                                {{ old('classroom_id', $classAssignment->classroom_id) == $aula->id ? 'selected' : '' }}>
                                {{ $aula->nro }} ({{ $aula->type }})
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                </div>

                <div class="flex items-center justify-start gap-4 mt-6">
                    <x-inicio.primary-button type="submit">
                        Actualizar Asignación
                    </x-inicio.primary-button>
                    
                    {{-- 
                      El botón Cancelar te regresa a la cuadrícula del horario 
                      del docente que estabas editando.
                    --}}
                    <x-inicio.secondary-button :href="route('admin.class-assignments.schedule', $classAssignment->docente_id)">
                        Cancelar
                    </x-inicio.secondary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>