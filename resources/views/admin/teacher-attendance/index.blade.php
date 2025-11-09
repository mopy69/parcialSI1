<x-layouts.admin>
<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Asistencias - Seleccionar Docente</h1>
</div>

@if($currentTerm)
<div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium text-indigo-800">
            Gestión Actual: <strong>{{ $currentTerm->name }}</strong>
            <span class="text-gray-600 ml-2">({{ $currentTerm->start_date }} - {{ $currentTerm->end_date }})</span>
        </span>
    </div>
</div>
@else
<div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 mb-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span class="text-sm font-medium text-yellow-800">
            No hay gestión seleccionada. Por favor, seleccione una gestión desde el dashboard.
        </span>
    </div>
</div>
@endif

<p class="text-gray-600 mb-4">
    Seleccione un docente de la lista para gestionar sus asistencias.
</p>

<x-table.search-bar placeholder="Buscar por nombre o correo..." :value="request('search')" />

<div class="mt-4 border-t border-gray-200">
    <ul class="divide-y divide-gray-200">
        @forelse ($docentes as $docente)
            <li class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <img class="h-10 w-10 rounded-full" 
                         src="https://ui-avatars.com/api/?name={{ urlencode($docente->name) }}&color=4F46E5&background=EEF2FF" 
                         alt="{{ $docente->name }}">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $docente->name }}</p>
                        <p class="text-sm text-gray-500">{{ $docente->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-3 sm:mt-0">
                    <span class="text-xs text-gray-500">
                        {{ $docente->total_attendances ?? 0 }} {{ ($docente->total_attendances ?? 0) == 1 ? 'asistencia registrada' : 'asistencias registradas' }}
                    </span>
                    <x-inicio.primary-button href="{{ route('admin.teacher-attendance.schedule', $docente) }}">
                        Ver Asistencias
                    </x-inicio.primary-button>
                </div>
            </li>
        @empty
            <li class="p-8 text-center text-gray-500">
                No se encontraron docentes
            </li>
        @endforelse
    </ul>
</div>

<div class="mt-6">
    {{ $docentes->links() }}
</div>
</x-layouts.admin>
