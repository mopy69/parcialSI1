<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Aulas</h1>
    <x-inicio.primary-button href="{{ route('admin.classrooms.create') }}">
        Crear Nueva Aula
    </x-inicio.primary-button>
</div>

{{-- Barra de búsqueda --}}
<x-table.search-bar placeholder="Buscar por número o tipo..." :value="request('search')" />

<div class="overflow-x-auto -mx-6 px-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <x-table.sortable-header column="nro" label="Nro. Aula" />
                <x-table.sortable-header column="type" label="Tipo" />
                <x-table.sortable-header column="capacity" label="Capacidad" />
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($classrooms as $classroom)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $classroom->nro }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $classroom->type }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $classroom->capacity }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-inicio.primary-button href="{{ route('admin.classrooms.edit', $classroom) }}" class="mr-3">
                        Editar
                    </x-inicio.primary-button>
                    <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
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

<div class="mt-6">
    {{ $classrooms->links() }}
</div>
</x-layouts.admin>