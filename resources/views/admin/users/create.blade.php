<x-layouts.admin>

<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Crear Nuevo Usuario</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="mb-4">
            <x-inicio.input-label for="name" :value="__('Nombre')" />
            <x-inicio.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
            <x-inicio.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-inicio.input-label for="email" :value="__('Correo Electrónico')" />
            <x-inicio.text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-inicio.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-inicio.input-label for="password" :value="__('Contraseña')" />
            <x-inicio.text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-inicio.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-inicio.input-label for="role" :value="__('Rol')" />
            {{-- 
              Aplicamos las clases de 'text-input' al <select>
              para mantener la consistencia visual del formulario.
            --}}
            <select class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    id="role" 
                    name="role_id" 
                    required>
                <option value="">Seleccione un rol</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <x-inicio.input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <x-inicio.primary-button>
                Crear Usuario
            </x-inicio.primary-button>
            <x-inicio.secondary-button href="{{ route('admin.users.index') }}">
                Cancelar
            </x-inicio.secondary-button>
        </div>
    </form>
</div>
</x-layouts.admin>