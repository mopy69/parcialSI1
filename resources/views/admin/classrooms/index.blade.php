<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    {{-- Título --}}
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Aulas</h1>
    
    <div class="flex gap-2">
        {{-- Botón Crear --}}
        <a href="{{ route('admin.classrooms.create') }}" class="admin-primary">
            Crear Nueva Aula
        </a>
    </div>
</div>

{{-- Alerta de éxito --}}
@if (session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

{{-- Alerta de error (útil para el borrado) --}}
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
                {{-- Encabezados de Tabla (Ajustados para Classroom) --}}
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nro. Aula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($classrooms as $classroom)
            <tr>

                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->nro }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->type }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->capacity }}</td>

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">

                    <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="admin-primary mr-3">Editar</a>

                    <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="admin-secondary"
                                onclick="return confirm('¿Está seguro que desea eliminar esta aula?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-4">
    {{ $classrooms->links() }}
</div>
</x-layouts.admin>