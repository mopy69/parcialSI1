<x-layouts.admin>
<div class="mb-6">
    {{-- Título --}}
    <h1 class="text-2xl font-semibold text-gray-900">Editar Aula: {{ $classroom->nro }}</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    {{-- Formulario --}}
    <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}">
        @csrf
        @method('PUT') {{-- Importante para la actualización --}}

        {{-- Campo Nro. Aula --}}
        <div class="mb-4">
            <x-inicio.input-label for="nro" :value="__('Nro. Aula')" />
            <x-inicio.text-input id="nro" 
                                 name="nro" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('nro', $classroom->nro)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('nro')" class="mt-2" />
        </div>

        {{-- Campo Tipo --}}
        <div class="mb-4">
            <x-inicio.input-label for="type" :value="__('Tipo')" />
            <x-inicio.text-input id="type" 
                                 name="type" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('type', $classroom->type)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('type')" class="mt-2" />
        </div>

        {{-- Campo Capacidad --}}
        <div class="mb-4">
            <x-inicio.input-label for="capacity" :value="__('Capacidad')" />
            <x-inicio.text-input id="capacity" 
                                 name="capacity" 
                                 type="number" 
                                 class="mt-1 block w-full" 
                                 :value="old('capacity', $classroom->capacity)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('capacity')" class="mt-2" />
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-between">
            <x-inicio.primary-button>
                Actualizar Aula
            </x-inicio.primary-button>
            
            <x-inicio.secondary-button :href="route('admin.classrooms.index')">
                Cancelar
            </x-inicio.secondary-button>
        </div>
    </form>
</div>
</x-layouts.admin>