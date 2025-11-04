<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Esta es un área segura de la aplicación. Por favor, confirme su contraseña antes de continuar.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-inicio.input-label for="password" :value="__('Password')" />

            <x-inicio.text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-inicio.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-inicio.primary-button>
                {{ __('Confirmar') }}
            </x-inicio.primary-button>
        </div>
    </form>
</x-guest-layout>
