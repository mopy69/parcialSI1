<x-layouts.admin>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Gestion de grupos</h1>
    <div class="flex gap-2">
        {{-- Convertido a componente de botón --}}
        <x-inicio.primary-button href="{{ route('admin.groups.create') }}">
            Crear un nuevo grupo
        </x-inicio.primary-button>
    </div>
</div>

{{-- Alertas (Estas ya están bien) --}}
@if (session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif
@if (session('error'))
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semestre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accion</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($groups as $group)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $group->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $group->semester }}</td>
                
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    
                    {{-- Convertido a componente de botón (enlace) --}}
                    <x-inicio.primary-button href="{{ route('admin.groups.edit', $group) }}" class="mr-3">
                        Edit
                    </x-inicio.primary-button>

                    <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        
                        {{-- Convertido a componente de botón (submit) --}}
                        <x-inicio.secondary-button type="submit"
                                onclick="return confirm('Are you sure you want to delete this group?')">
                            Eliminar
                        </x-inicio.secondary-button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4">
    {{ $groups->links() }}
</div>
</x-layouts.admin>