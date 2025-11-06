<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asignación de Clases') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        
        <h1 class="text-2xl font-semibold text-gray-900 mb-4">Seleccionar Docente</h1>
        
        {{-- Mostrar la gestión actual --}}
        @if(session('current_term'))
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-indigo-800">
                    Gestión: <strong>{{ session('current_term')->name }}</strong>
                </span>
            </div>
        </div>
        @endif
        
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