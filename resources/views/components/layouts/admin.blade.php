<x-app-layout>
    <div class="py-9 admin-page" style="min-height:60vh;">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row mb-4 lg:gap-8">
                
                {{-- 
                  CAMBIO AQUÍ: 
                  - Se eliminaron: 'bg-white', 'rounded-lg', 'shadow-md', 'p-4'
                  - El contenedor ahora es transparente y solo se encarga 
                    de la posición y el tamaño.
                --}}
                <div class="
                    hidden      sm:block    sm:w-full   lg:w-64     flex-shrink-0 
                    sm:mb-6     lg:mb-0
                    sm:px-4     lg:px-0
                    lg:self-start
                ">
                    <x-admin.sidebar />
                </div>
                
                {{-- 
                  Contenido principal (sigue siendo una tarjeta flotante)
                --}}
                <div class="
                    flex-1 
                    mt-6 sm:mt-0 
                    lg:ml-0
                    bg-white rounded-2xl shadow-md p-6
                ">
                    {{ $slot }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>