<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Oferta de Curso') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Oferta de Curso</h1>
        <p class="text-sm text-gray-600 mb-4">
            Editando la oferta: 
            <strong>{{ $courseOffering->term->name }}</strong> / 
            <strong>{{ $courseOffering->subject->name }}</strong> / 
            Grupo <strong>{{ $courseOffering->group->name }}</strong>
        </p>

        <div class="max-w-xl"> {{-- Limita el ancho del formulario --}}
            <form method="POST" action="{{ route('admin.course-offerings.update', $courseOffering) }}">
                @csrf
                @method('PUT')

                {{-- Campo oculto para term_id --}}
                <input type="hidden" name="term_id" value="{{ $currentTerm->id }}">

                {{-- Mostrar la gestión actual (solo informativo) --}}
                <div class="mb-4">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <span class="text-sm font-medium text-gray-500">Gestión Actual:</span>
                        <span class="ml-2 text-sm font-semibold text-gray-900">{{ $currentTerm->name }}</span>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($currentTerm->start_date)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($currentTerm->end_date)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                {{-- Campo 'subject_id' (Materia) --}}
                <div class="mb-4">
                    <x-inicio.input-label for="subject_id" :value="__('Materia')" />
                    <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                        <option value="">Seleccione una materia</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $courseOffering->subject_id) == $subject->id ? 'selected' : '' }}>
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
                            <option value="{{ $group->id }}" {{ old('group_id', $courseOffering->group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-inicio.input-error :messages="$errors->get('group_id')" class="mt-2" />
                </div>


                <div class="flex items-center justify-start gap-4 mt-6">
                    <x-inicio.primary-button>
                        Actualizar Oferta
                    </x-inicio.primary-button>
                    
                    <x-inicio.secondary-button :href="route('admin.course-offerings.index')">
                        Cancelar
                    </x-inicio.secondary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>