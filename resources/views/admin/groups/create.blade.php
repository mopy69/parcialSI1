@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Create New Group</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.groups.store') }}">
        @csrf

        {{-- Campo 'name' --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Group Name (Nro.)
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" {{-- CORREGIDO: Sin $group --}}
                   placeholder="Ej: G-1"
                   required>
            @error('name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Campo 'semester' --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="semester">
                Semester
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="semester" 
                   type="text"  {{-- Corregido a 'text' --}}
                   name="semester" 
                   value="{{ old('semester') }}" {{-- CORREGIDO: Sin $group --}}
                   placeholder="Ej: 2023/1"
                   required>
            @error('semester')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="admin-primary" type="submit">Create Group</button>
            <a href="{{ route('admin.groups.index') }}" class="admin-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection