<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Panel de Administración</h1>

            {{-- Selector de Gestión --}}
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-500">Gestión Actual:</span>
                <form action="{{ route('admin.change-term') }}" method="POST" class="flex items-center">
                    @csrf
                    <select name="term_id" 
                            onchange="this.form.submit()"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" 
                                {{ $currentTerm && $currentTerm->id == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Información de la Gestión Actual --}}
        @if($currentTerm)
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-medium text-indigo-800 mb-2">Información de la Gestión</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm text-indigo-600">Inicio:</span>
                    <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($currentTerm->start_date)->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-sm text-indigo-600">Fin:</span>
                    <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($currentTerm->end_date)->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-sm text-indigo-600">Estado:</span>
                    <span class="text-sm font-medium {{ $currentTerm->asset ? 'text-green-600' : 'text-red-600' }}">
                        {{ $currentTerm->asset ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Estadísticas --}}
            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Usuarios</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $usersCount }}</dd>
                </div>
            </div>

            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Aulas</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $classroomsCount }}</dd>
                </div>
            </div>

            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Asignaciones en la Gestión Actual</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $currentAssignments }}</dd>
                </div>
            </div>
        </div>
</x-layouts.admin>