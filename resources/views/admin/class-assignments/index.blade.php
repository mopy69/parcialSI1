<x-layouts.admin>
<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <h1 class="text-2xl font-semibold text-gray-900">Asignación de Clases - Seleccionar Docente</h1>
    
    {{-- Botón para copiar desde otra gestión --}}
    @if($currentTerm && $availableTerms->isNotEmpty())
    <button 
        type="button"
        onclick="document.getElementById('copyAssignmentsModal').style.display='flex'"
        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        Copiar Asignaciones desde otra gestión
    </button>
    @endif
</div>

@if($currentTerm)
<div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium text-indigo-800">
            Gestión Actual: <strong>{{ $currentTerm->name }}</strong>
            <span class="text-gray-600 ml-2">(Mostrando asignaciones de esta gestión)</span>
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
    Seleccione un docente de la lista para ver, editar o crear su horario de clases.
</p>

<x-table.search-bar placeholder="Buscar por nombre o correo..." :value="request('search')" />

<div class="mt-4 border-t border-gray-200">
    <ul class="divide-y divide-gray-200">
        @forelse ($users as $docente)
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
                        {{ $docente->class_assignments_docente_count }} {{ $docente->class_assignments_docente_count == 1 ? 'asignación' : 'asignaciones' }}
                    </span>
                    <x-inicio.primary-button href="{{ route('admin.class-assignments.schedule', $docente) }}">
                        Ver Horario
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
    {{ $users->links() }}
</div>

{{-- Modal para copiar asignaciones desde otra gestión --}}
@if($currentTerm && $availableTerms->isNotEmpty())
<div id="copyAssignmentsModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Copiar Asignaciones de Clases</h3>
            <button type="button" onclick="document.getElementById('copyAssignmentsModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.class-assignments.copy-from-term') }}">
            @csrf
            <p class="text-sm text-gray-600 mb-4">
                Seleccione la gestión desde la cual desea copiar las asignaciones de clases a <strong>{{ $currentTerm->name }}</strong>:
            </p>

            <div class="mb-4">
                <label for="source_term_id" class="block text-sm font-medium text-gray-700 mb-2">Gestión de Origen</label>
                <select id="source_term_id" name="source_term_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                    <option value="">Seleccione una gestión...</option>
                    @foreach($availableTerms as $term)
                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-yellow-800">
                    <strong>Nota:</strong> Solo se copiarán las asignaciones cuyas ofertas de curso equivalentes existan en la gestión actual. Las asignaciones duplicadas serán omitidas.
                </p>
            </div>

            <div class="flex items-center justify-end gap-4">
                <button type="button" onclick="document.getElementById('copyAssignmentsModal').style.display='none'" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Copiar Asignaciones
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</x-layouts.admin>
