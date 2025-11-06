<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asignación de Clases') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Seleccionar Docente</h1>
        <p class="text-gray-600 mb-4">
            Seleccione un docente de la lista para ver, editar o crear su horario de clases.
        </p>
        
        <div class="mt-4 border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                @forelse ($users as $docente)
                    <li class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($docente->name) }}&color=4F46E5&background=EEF2FF" alt="">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $docente->name }}</p>
                                <p class="text-sm text-gray-500">{{ $docente->email }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 flex-shrink-0 flex gap-2">
                            
                            {{-- 
                              ¡CAMBIO AQUÍ! 
                              El 'href' ahora apunta a la nueva ruta, 
                              pasándole el ID del docente.
                            --}}
                            <x-inicio.primary-button :href="route('admin.class-assignments.schedule', $docente)">
                                Asignar Horario
                            </x-inicio.primary-button>
                        </div>
                    </li>
                @empty
                    <li class="p-4 text-center text-gray-500">
                        No se encontraron docentes.
                    </li>
                @endforelse
            </ul>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>