<x-layouts.admin>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Editar materia: {{ $subject->name }}</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
        @csrf
        @method('PUT')

        {{-- Campo Code --}}
        <div class="mb-4">
            <x-inicio.input-label for="code" :value="__('Codigo de materia')" />
            <x-inicio.text-input id="code" 
                                 name="code" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('code', $subject->code)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        {{-- Campo Name --}}
        <div class="mb-4">
            <x-inicio.input-label for="name" :value="__('Nombre de materia')" />
            <x-inicio.text-input id="name" 
                                 name="name" 
                                 type="text" 
                                 class="mt-1 block w-full" 
                                 :value="old('name', $subject->name)" 
                                 required />
            <x-inicio.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <x-inicio.primary-button>Actualizar materia</x-inicio.primary-button>
            
            {{-- Aquí estaba el error tipográfico (decía </x-inicio.primary-button>) --}}
            <x-inicio.secondary-button href="{{ route('admin.subjects.index') }}">Cancel</x-inicio.secondary-button>
        </div>
    </form>
</div>
</x-layouts.admin>