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
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Ofertas de Cursos</h1>
            <x-inicio.primary-button href="{{ route('admin.course-offerings.create') }}">
                Crear Nueva Oferta
            </x-inicio.primary-button>
        </div>

        {{-- Mostrar la gestión actual --}}
        @if($currentTerm)
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-indigo-800">
                    Mostrando ofertas de la gestión: <strong>{{ $currentTerm->name }}</strong>
                </span>
            </div>
        </div>
        @endif

        {{-- Contenedor de la Tabla (Responsivo) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($courseOfferings as $offering)
                        <tr>
                            {{-- Usamos las relaciones (eager-loaded) del controlador --}}
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
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No hay ofertas de cursos registradas para esta gestión.
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