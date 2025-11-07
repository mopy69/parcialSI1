<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Historial de la Bitácora</h1>
</div>

{{-- Barra de búsqueda --}}
<x-table.search-bar placeholder="Buscar por usuario, acción, estado o IP..." :value="request('search')" />

<div class="overflow-x-auto -mx-6 px-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                <x-table.sortable-header column="action" label="Acción" />
                <x-table.sortable-header column="state" label="Estado" />
                <x-table.sortable-header column="ip_address" label="Dirección IP" />
                <x-table.sortable-header column="created_at" label="Fecha" />
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($logs as $log)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $log->user ? $log->user->name : 'Sistema/Invitado' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->action }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $log->state === 'exitoso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $log->state }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->ip_address }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $log->details }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
</x-layouts.admin>