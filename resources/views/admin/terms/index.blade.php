<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Términos Académicos</h1>
    <div class="flex gap-2">
        <x-inicio.primary-button href="{{ route('admin.terms.create') }}">
            Crear nuevo término
        </x-inicio.primary-button>
    </div>
</div>


<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Fin</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($terms as $term)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $term->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $term->start_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $term->end_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        
                        {{-- CAMBIO: Convertido a componente de botón --}}
                        <x-inicio.primary-button href="{{ route('admin.terms.edit', $term) }}" class="mr-3">
                            Editar
                        </x-inicio.primary-button>
                        
                        <form class="inline-block" action="{{ route('admin.terms.destroy', $term) }}" method="POST" onsubmit="return confirm('¿Estás seguro que deseas eliminar este término?');">
                            @csrf
                            @method('DELETE')
                            
                            {{-- CAMBIO: Convertido a componente de botón --}}
                            <x-inicio.secondary-button type="submit">
                                Eliminar
                            </x-inicio.secondary-button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay términos académicos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.admin>