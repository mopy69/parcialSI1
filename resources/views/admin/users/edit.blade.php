<x-layouts.admin>

<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Editar Usuario: {{ $user->name }}</h1>
</div>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <x-inicio.input-label for="name" :value="__('Nombre')" />
            <x-inicio.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
            <x-inicio.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-inicio.input-label for="email" :value="__('Correo Electrónico')" />
            <x-inicio.text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-inicio.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-inicio.input-label for="role" :value="__('Rol')" />
            {{-- 
              Usamos las clases de 'text-input' en este 'select' 
              para que el estilo sea consistente con tus componentes.
            --}}
            <select class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    id="role" 
                    name="role_id" 
                    required>
                <option value="">Seleccione un rol</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <x-inicio.input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <x-inicio.primary-button>
                Actualizar Usuario
            </x-inicio.primary-button>
            
            {{-- Convertimos el enlace 'a' en un componente de botón --}}
            <x-inicio.secondary-button :href="route('admin.users.index')">
                Cancelar
            </x-inicio.secondary-button>
        </div>
    </form>
</div>
</x-layouts.admin>