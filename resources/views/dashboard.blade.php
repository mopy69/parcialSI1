<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('Horario') }}
        </h2>
    </x-slot>


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-principal.horario 
                :clases="$clases" 
                :estadisticas="$estadisticas"
            />

        </div>

</x-app-layout>
