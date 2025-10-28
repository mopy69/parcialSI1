<x-layouts.admin>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Crear nueva materia</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf

        {{-- Campo Code --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="code">
                Codigo de materia
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="code" 
                   type="text" 
                   name="code" 
                   value="{{ old('code') }}" 
                   placeholder="Ej: SIS-101"
                   required>
            @error('code')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Campo Name --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Nombre de la materia
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   placeholder="Ej: Introducción a la Programación"
                   required>
            @error('name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="admin-primary" type="submit">Crear materia</button>
            <a href="{{ route('admin.subjects.index') }}" class="admin-secondary">Cancel</a>
        </div>
    </form>
</div>
</x-layouts.admin>