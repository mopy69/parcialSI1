<x-layouts.admin>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Gestión de Usuarios</h1>
        <div class="flex gap-2">
            <x-inicio.primary-button href="{{ route('admin.users.create') }}">
                Crear Nuevo Usuario
            </x-inicio.primary-button>
            <form action="{{ route('admin.users.createMassive') }}" method="POST" enctype="multipart/form-data"
                id="massive-upload-form">

                @csrf

                <input type="file" name="import_file" id="file_input" class="hidden"
                    onchange="document.getElementById('massive-upload-form').submit();" />

                <x-inicio.primary-button onclick="document.getElementById('file_input').click();">
                    Crear Usuarios Masivamente
                </x-inicio.primary-button>

            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Correo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $user->role->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-inicio.primary-button
                                    href="{{ route('admin.users.edit', $user) }}">Editar</x-inicio.primary-button>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-inicio.secondary-button type="submit"
                                        onclick="return confirm('¿Está seguro que desea eliminar este usuario?')">
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
        {{ $users->links() }}
    </div>
</x-layouts.admin>
