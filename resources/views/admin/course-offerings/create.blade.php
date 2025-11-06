<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Oferta de Curso') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Crear Nueva Oferta de Curso</h1>

        <div class="max-w-xl"> {{-- Limita el ancho del formulario --}}
            <form method="POST" action="{{ route('admin.course-offerings.store') }}">
                @csrf

                {{-- Campo 'term_id' (Gestión) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="term_id" :value="__('Gestión (Término Académico)')" />
                    <select id="term_id" name="term_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione una gestión</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('term_id')" class="mt-2" />
                </div>

                {{-- Campo 'subject_id' (Materia) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="subject_id" :value="__('Materia')" />
                    <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione una materia</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('subject_id')" class="mt-2" />
                </div>

                {{-- Campo 'group_id' (Grupo) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="group_id" :value="__('Grupo')" />
                    <select id="group_id" name="group_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione un grupo</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('group_id')" class="mt-2" />
                </div>


                <div class="flex items-center justify-start gap-4 mt-6">
                    <x-inicio.primary-button>
                        Crear Oferta
                    </x-inicio.primary-button>
                    
                    <x-inicio.secondary-button :href="route('admin.course-offerings.index')">
                        Cancelar
                    </x-inicio.secondary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>