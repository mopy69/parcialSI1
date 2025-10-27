@extends('layouts.admin')

@section('content')
<div class="mb-6">
    {{-- Título --}}
    <h1 class="text-2xl font-semibold text-gray-900">Crear Nueva Aula</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    {{-- Formulario --}}
    <form method="POST" action="{{ route('admin.classrooms.store') }}">
        @csrf

        {{-- Campo Nro. Aula --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nro">
                Nro. Aula
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="nro" 
                   type="text" 
                   name="nro" 
                   value="{{ old('nro') }}" 
                   required>
            @error('nro')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Campo Tipo --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                Tipo
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="type" 
                   type="text" 
                   name="type" 
                   value="{{ old('type') }}" 
                   required>
            @error('type')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Campo Capacidad --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="capacity">
                Capacidad
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="capacity" 
                   type="number" 
                   name="capacity" 
                   value="{{ old('capacity') }}" 
                   required>
            @error('capacity')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-between">
            <button class="admin-primary" type="submit">Crear Aula</button>
            <a href="{{ route('admin.classrooms.index') }}" class="admin-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection