{{-- 
  CAMBIOS EN <nav>:
  - Móvil (por defecto): 'sticky top-0 shadow-sm' -> Pegado arriba, ancho completo, sombra ligera.
  - Escritorio ('lg:'): 
    - 'lg:mt-6' -> Se separa de arriba.
    - 'lg:top-6' -> Se pega en la nueva posición flotante.
    - 'lg:max-w-7xl lg:mx-auto' -> Se centra y limita el ancho.
    - 'lg:rounded-lg' -> Se redondea.
    - 'lg:shadow-md' -> Se le da una sombra más pronunciada.
--}}
<nav x-data="{ open: false }" class="bg-white sticky top-0 z-50 shadow-sm 
    lg:mt-6 lg:top-6 lg:max-w-7xl lg:mx-auto lg:rounded-lg lg:shadow-md">
    
    {{-- 
      CAMBIOS EN EL DIV INTERNO:
      - Mantenemos el padding interno para todas las vistas.
    --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-inicio.application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-inicio.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Panel de control') }}
                    </x-inicio.nav-link>
                    @if (Auth::user()->role->name === 'Administrador')
                        <x-inicio.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            {{ __('Administracion') }}
                        </x-inicio.nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-inicio.dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-inicio.dropdown-link :href="route('profile.edit', auth()->user())">
                            {{ __('Perfil') }}
                        </x-inicio.dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-inicio.dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-inicio.dropdown-link>
                        </form>
                    </x-slot>
                </x-inicio.dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-inicio.responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Panel de control') }}
            </x-inicio.responsive-nav-link>
        </div>

        @if (Auth::user() && Auth::user()->role->name === 'Administrador')
            <div class="border-t border-gray-200 px-4 pt-4 pb-3 space-y-3 bg-gradient-to-b from-white to-gray-50">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Administración') }}</h3>

                <x-inicio.responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                    class="block w-full rounded-lg border border-indigo-100 bg-white px-4 py-2 text-sm font-medium text-indigo-600 shadow-sm hover:bg-indigo-50">
                    {{ __('Panel principal') }}
                </x-inicio.responsive-nav-link>

                <div class="space-y-2">
                    <div x-data="{ openSection: false }" class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <button @click="openSection = !openSection" :aria-expanded="openSection.toString()"
                            class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-600">
                            <span>{{ __('Datos base') }}</span>
                            <svg :class="{ 'rotate-180': openSection }" class="h-4 w-4 transform transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openSection" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="space-y-1 px-4 pb-3">
                            <x-inicio.responsive-nav-link :href="route('admin.terms.index')" :active="request()->routeIs('admin.terms.*')">
                                {{ __('Términos académicos') }}
                            </x-inicio.responsive-nav-link>
                            <x-inicio.responsive-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')">
                                {{ __('Materias') }}
                            </x-inicio.responsive-nav-link>
                            <x-inicio.responsive-nav-link :href="route('admin.groups.index')" :active="request()->routeIs('admin.groups.*')">
                                {{ __('Grupos') }}
                            </x-inicio.responsive-nav-link>
                            <x-inicio.responsive-nav-link :href="route('admin.classrooms.index')" :active="request()->routeIs('admin.classrooms.*')">
                                {{ __('Aulas') }}
                            </x-inicio.responsive-nav-link>
                        </div>
                    </div>

                    <div x-data="{ openSection: false }" class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <button @click="openSection = !openSection" :aria-expanded="openSection.toString()"
                            class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-600">
                            <span>{{ __('Programación') }}</span>
                            <svg :class="{ 'rotate-180': openSection }" class="h-4 w-4 transform transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openSection" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="space-y-1 px-4 pb-3">
                            <x-inicio.responsive-nav-link :href="route('admin.course-offerings.index')" :active="request()->routeIs('admin.course-offerings.*')">
                                {{ __('Ofertas de cursos') }}
                            </x-inicio.responsive-nav-link>
                            <x-inicio.responsive-nav-link :href="route('admin.class-assignments.index')" :active="request()->routeIs('admin.class-assignments.*')">
                                {{ __('Asignación de clases') }}
                            </x-inicio.responsive-nav-link>
                        </div>
                    </div>

                    <div x-data="{ openSection: false }" class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <button @click="openSection = !openSection" :aria-expanded="openSection.toString()"
                            class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-600">
                            <span>{{ __('Gestión de usuarios') }}</span>
                            <svg :class="{ 'rotate-180': openSection }" class="h-4 w-4 transform transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openSection" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="space-y-1 px-4 pb-3">
                            <x-inicio.responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('Usuarios') }}
                            </x-inicio.responsive-nav-link>
                        </div>
                    </div>

                    <div x-data="{ openSection: false }" class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <button @click="openSection = !openSection" :aria-expanded="openSection.toString()"
                            class="flex w-full items-center justify-between px-4 py-2 text-sm font-semibold text-gray-600">
                            <span>{{ __('Auditoría') }}</span>
                            <svg :class="{ 'rotate-180': openSection }" class="h-4 w-4 transform transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openSection" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="space-y-1 px-4 pb-3">
                            <x-inicio.responsive-nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.*')">
                                {{ __('Bitácora') }}
                            </x-inicio.responsive-nav-link>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-inicio.responsive-nav-link :href="route('profile.edit', auth()->user())">
                    {{ __('Perfil') }}
                </x-inicio.responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-inicio.responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-inicio.responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>