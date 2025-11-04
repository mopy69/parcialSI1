<x-layouts.admin>

    {{-- 
      ¡CAMBIO AQUÍ! 
      Envolvemos todo el contenido en un 'div' principal.
      Este div añade el fondo blanco, la sombra y las esquinas redondeadas
      que pediste para que el contenido "flote".
    --}}
    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">

        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Panel de Administración</h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- 
              TARJETA DE ESTADÍSTICA (Estilo Modernizado)
              Cambiamos el fondo a gris claro (bg-gray-50), 
              añadimos sombra interna (shadow-sm) y más padding (p-4 sm:p-6).
            --}}
            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Usuarios</dt>
                    {{-- 'tracking-tight' hace que el número se vea más compacto y moderno --}}
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $usersCount }}</dd>
                </div>
            </div>

            {{-- Puedes añadir más tarjetas aquí siguiendo el mismo estilo --}}
            {{-- 
            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Aulas</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">12</dd>
                </div>
            </div>
            --}}

        </div>

    </div>
</x-layouts.admin>