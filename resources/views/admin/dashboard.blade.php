<x-layouts.admin>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Panel de Administración</h1>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total de Usuarios</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $usersCount }}</dd>
        </div>
    </div>
</div>
</x-layouts.admin>
