@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Edit Subject: {{ $subject->name }}</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
        @csrf
        @method('PUT')

        {{-- Campo Code --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="code">
                Subject Code
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="code" 
                   type="text" 
                   name="code" 
                   value="{{ old('code', $subject->code) }}" 
                   required>
            @error('code')
                <p class="text-red-500 text-xs italic">{{ $message }}</D>
            @enderror
        </div>

        {{-- Campo Name --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Subject Name
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name', $subject->name) }}" 
                   required>
            @error('name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="admin-primary" type="submit">Update Subject</button>
            <a href="{{ route('admin.subjects.index') }}" class="admin-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection