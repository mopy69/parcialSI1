<x-layouts.admin>
    {{-- Slot de Cabecera (Opcional, pero recomendado) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ofertas de Cursos') }}
        </h2>
    </x-slot>

    {{-- Contenido Principal (Tarjeta Flotante) --}}
    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        
        {{-- Título y Botón de Crear --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Ofertas de Cursos</h1>
            <x-inicio.primary-button href="{{ route('admin.course-offerings.create') }}">
                Crear Nueva Oferta
            </x-inicio.primary-button>
        </div>

        {{-- Contenedor de la Tabla (Responsivo) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gestión (Término)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($courseOfferings as $offering)
                        <tr>
                            {{-- Usamos las relaciones (eager-loaded) del controlador --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ $offering->term->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $offering->subject->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $offering->group->name }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-inicio.primary-button href="{{ route('admin.course-offerings.edit', $offering) }}" class="mr-3">
                                    Editar
                                </x-inicio.primary-button>

                                <form action="{{ route('admin.course-offerings.destroy', $offering) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-inicio.secondary-button type="submit"
                                            onclick="return confirm('¿Está seguro? Esto eliminará la oferta y todas sus clases asignadas.')">
                                        Eliminar
                                    </x-inicio.secondary-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No hay ofertas de cursos registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $courseOfferings->links() }}
        </div>
    </div>
</x-layouts.admin>