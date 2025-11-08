<x-layouts.admin>
{{-- Título y Botones de Acción --}}
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <h1 class="text-2xl font-semibold text-gray-900">Ofertas de Cursos</h1>
    <div class="flex gap-3">
        {{-- Botón para copiar desde otra gestión --}}
        @if($availableTerms->isNotEmpty())
        <button 
            type="button"
            onclick="document.getElementById('copyModal').style.display='flex'"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Copiar desde otra gestión
        </button>
        @endif
        
        <x-inicio.primary-button href="{{ route('admin.course-offerings.create') }}">
            Crear Nueva Oferta
        </x-inicio.primary-button>
    </div>
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

{{-- Barra de búsqueda --}}
<x-table.search-bar placeholder="Buscar por materia o grupo..." :value="request('search')" />

{{-- Contenedor de la Tabla (Responsivo) --}}
<div class="overflow-x-auto -mx-6 px-6">
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
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $offering->subject->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $offering->group->name }}</td>
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
                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">
                        No hay ofertas de cursos registradas para esta gestión.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="mt-6">
    {{ $courseOfferings->links() }}
</div>

{{-- Modal para copiar desde otra gestión --}}
<div id="copyModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Copiar Ofertas de Curso</h3>
            <button type="button" onclick="document.getElementById('copyModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.course-offerings.copy-from-term') }}">
            @csrf
            <p class="text-sm text-gray-600 mb-4">
                Seleccione la gestión desde la cual desea copiar las ofertas de curso a <strong>{{ $currentTerm->name }}</strong>:
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
                    <strong>Nota:</strong> Solo se copiarán las ofertas que no existan en la gestión actual. Las ofertas duplicadas serán omitidas.
                </p>
            </div>

            <div class="flex items-center justify-end gap-4">
                <button type="button" onclick="document.getElementById('copyModal').style.display='none'" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Copiar Ofertas
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.admin>
