<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Términos Académicos</h1>
    <x-inicio.primary-button href="{{ route('admin.terms.create') }}">
        Crear Nuevo Término
    </x-inicio.primary-button>
</div>

{{-- Barra de búsqueda --}}
<x-table.search-bar placeholder="Buscar por nombre..." :value="request('search')" />

<div class="overflow-x-auto -mx-6 px-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <x-table.sortable-header column="name" label="Nombre" />
                <x-table.sortable-header column="start_date" label="Fecha Inicio" />
                <x-table.sortable-header column="end_date" label="Fecha Fin" />
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($terms as $term)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $term->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $term->start_date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $term->end_date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-inicio.primary-button href="{{ route('admin.terms.edit', $term) }}" class="mr-3">
                        Editar
                    </x-inicio.primary-button>
                    <form class="inline-block" action="{{ route('admin.terms.destroy', $term) }}" method="POST" onsubmit="return confirm('¿Estás seguro que deseas eliminar este término?');">
                        @csrf
                        @method('DELETE')
                        <x-inicio.secondary-button type="submit">
                            Eliminar
                        </x-inicio.secondary-button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">
                    No hay términos académicos registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $terms->links() }}
</div>
</x-layouts.admin>
