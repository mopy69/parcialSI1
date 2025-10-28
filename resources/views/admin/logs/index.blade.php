@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Historial de la Bitácora</h1>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección IP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>

            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{-- Bucle de datos (Ajustado para Log) --}}
            @foreach ($logs as $log)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{-- Usamos la relación 'user' que definiste en el Modelo --}}
                    {{ $log->user ? $log->user->name : 'Sistema/Invitado' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->action }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->state }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->ip_address }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{-- Formateamos la fecha para mejor lectura --}}
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="px-6 py-4  text-sm text-gray-600">
                    {{ $log->details }}
                </td>
                
                {{-- No hay celda de "Acciones" (editar/borrar) --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection