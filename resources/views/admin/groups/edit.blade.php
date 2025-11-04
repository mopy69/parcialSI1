<x-layouts.admin>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Edtar grupo: {{ $group->name }}</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.groups.update', $group) }}">
        @csrf
        @method('PUT') {{-- Importante para la actualización --}}

        {{-- Campo 'name' --}}
        <div class="mb-4">
            <x-inicio.input-label for="name" :value="__('Nombre del grupo (Nro.)')" />
            <x-inicio.text-input id="name" 
                                 name="name" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('name', $group->name)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Campo 'semester' --}}
        <div class="mb-4">
            <x-inicio.input-label for="semester" :value="__('Semestre')" />
            <x-inicio.text-input id="semester" 
                                 name="semester" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('semester', $group->semester)" 
                                 placeholder="Ej: 2023/1" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('semester')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <x-inicio.primary-button>
                Actualizar grupo
            </x-inicio.primary-button>
            
            <x-inicio.secondary-button :href="route('admin.groups.index')">
                Cancel
            </x-inicio.secondary-button>
        </div>
    </form>
</div>
</x-layouts.admin>