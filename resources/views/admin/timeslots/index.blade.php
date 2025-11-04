<x-layouts.admin>
    {{-- Slot de Cabecera (Opcional, pero recomendado) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Franjas Horarias') }}
        </h2>
    </x-slot>

    {{-- Contenido Principal (Tarjeta Flotante) --}}
    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        
        {{-- Título y Botón de Crear --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Lista de Horarios</h1>
            <x-inicio.primary-button href="{{ route('admin.timeslots.create') }}">
                Crear Nueva Franja
            </x-inicio.primary-button>
        </div>

        {{-- Alertas de Éxito/Error --}}
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

        {{-- Contenedor de la Tabla (Responsivo) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Día de la Semana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora de Inicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora de Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($timeslots as $timeslot)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $timeslot->day }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $timeslot->start }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $timeslot->end }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-inicio.primary-button href="{{ route('admin.timeslots.edit', $timeslot) }}" class="mr-3">
                                    Editar
                                </x-inicio.primary-button>

                                <form action="{{ route('admin.timeslots.destroy', $timeslot) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-inicio.secondary-button type="submit"
                                            onclick="return confirm('¿Está seguro que desea eliminar esta franja horaria?')">
                                        Eliminar
                                    </x-inicio.secondary-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No hay franjas horarias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $timeslots->links() }}
        </div>
    </div>
</x-layouts.admin>