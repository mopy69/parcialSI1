<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    {{-- Título --}}
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Aulas</h1>
    
    <div class="flex gap-2">
        {{-- Botón Crear (Convertido a componente) --}}
        <x-inicio.primary-button href="{{ route('admin.classrooms.create') }}">
            Crear Nueva Aula
        </x-inicio.primary-button>
    </div>
</div>


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

                    {{-- Botón Editar (Convertido a componente) --}}
                    <x-inicio.primary-button href="{{ route('admin.classrooms.edit', $classroom) }}" class="mr-3">
                        Editar
                    </x-inicio.primary-button>

                    <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        
                        {{-- Botón Eliminar (Convertido a componente) --}}
                        <x-inicio.secondary-button type="submit"
                                onclick="return confirm('¿Está seguro que desea eliminar esta aula?')">
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

{{-- Paginación --}}
<div class="mt-4">
    {{ $classrooms->links() }}
</div>
</x-layouts.admin>