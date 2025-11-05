<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestion de materias</h1>
    <div class="flex gap-2">
        <x-inicio.primary-button href="{{ route('admin.subjects.create') }}">
            Crear nueva materia
        </x-inicio.primary-button>
    </div>
</div>

@if (session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif
@if (session('error'))
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($subjects as $subject)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $subject->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $subject->name }}</td>
                
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-inicio.primary-button href="{{ route('admin.subjects.edit', $subject) }}">Edit</x-M.primary-button>
                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-inicio.secondary-button type="submit"
                                onclick="return confirm('seguro que quiere eliminar esta materia?')">
                            Eliminar
                        </x-inicio.secondary-button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4">
    {{ $subjects->links() }}
</div>
</x-layouts.admin>