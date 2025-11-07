<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Grupos</h1>
    <x-inicio.primary-button href="{{ route('admin.groups.create') }}">
        Crear Nuevo Grupo
    </x-inicio.primary-button>
</div>

{{-- Barra de búsqueda --}}
<x-table.search-bar placeholder="Buscar por nombre o semestre..." :value="request('search')" />

<div class="overflow-x-auto -mx-6 px-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <x-table.sortable-header column="name" label="Nombre" />
                <x-table.sortable-header column="semester" label="Semestre" />
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($groups as $group)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $group->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $group->semester }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-inicio.primary-button href="{{ route('admin.groups.edit', $group) }}" class="mr-3">
                        Editar
                    </x-inicio.primary-button>
                    <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-inicio.secondary-button type="submit"
                                onclick="return confirm('¿Está seguro que desea eliminar este grupo?')">
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
    {{ $groups->links() }}
</div>
</x-layouts.admin>
