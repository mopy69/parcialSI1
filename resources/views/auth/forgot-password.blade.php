<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer la contraseña que te permitirá elegir una nueva.') }}
    </div>

    <!-- Session Status -->
    <x-inicio.auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-inicio.input-label for="email" :value="__('Email')" />
            <x-inicio.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-inicio.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-inicio.primary-button>
                {{ __('Enviar enlace de restablecimiento de contraseña') }}
            </x-inicio.primary-button>
        </div>
    </form>
</x-guest-layout>
