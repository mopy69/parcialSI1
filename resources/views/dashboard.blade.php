<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('Horario') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Información de la Gestión Actual --}}
            @if(isset($currentTerm) && $currentTerm)
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-indigo-800">
                        <strong>{{ $currentTerm->name }}</strong> ({{ \Carbon\Carbon::parse($currentTerm->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($currentTerm->end_date)->format('d/m/Y') }})
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
                        No hay una gestión académica activa en este momento
                    </span>
                </div>
            </div>
            @endif
            
            <x-principal.horario 
                :clases="$clases" 
                :estadisticas="$estadisticas"
            />
        </div>
    </div>
</x-app-layout>
