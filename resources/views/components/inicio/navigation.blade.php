{{-- 
  CAMBIOS EN <nav>:
  - Móvil (por defecto): 'sticky mt-4 top-4 mx-2 rounded-lg shadow-md' -> Separado de arriba, con margen lateral, redondeado
  - Escritorio ('lg:'): 
    - 'lg:mt-6 lg:top-6' -> Mayor separación en pantallas grandes
    - 'lg:max-w-7xl lg:mx-auto' -> Se centra y limita el ancho
    - 'lg:shadow-md' -> Mantiene la sombra pronunciada
--}}
<nav x-data="{ open: false }"
    class="bg-white sticky mt-4 top-4 mx-2 rounded-lg shadow-md z-50
    lg:mt-6 lg:top-6 lg:max-w-7xl lg:mx-auto">

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
                    @if (Auth::user()->role->name === 'Docente')
                        <x-inicio.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Inicio') }}
                        </x-inicio.nav-link>
                        <x-inicio.nav-link :href="route('attendance.qr.scan')" :active="request()->routeIs('attendance.qr.scan')">
                            {{ __('Registrar Asistencia') }}
                        </x-inicio.nav-link>
                    @endif
                    @if (Auth::user()->role->name === 'Administrador')
                        <x-inicio.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Administración') }}
                        </x-inicio.nav-link>
                        <x-inicio.nav-link :href="route('attendance.qr.admin')" :active="request()->routeIs('attendance.qr.admin')">
                            {{ __('QR Asistencias') }}
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
            @if (Auth::user() && Auth::user()->role->name === 'Docente')
                <x-inicio.responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Panel de control') }}
                </x-inicio.responsive-nav-link>
                <x-inicio.responsive-nav-link :href="route('attendance.qr.scan')" :active="request()->routeIs('attendance.qr.scan')">
                    {{ __('Registrar Asistencia') }}
                </x-inicio.responsive-nav-link>
            @endif
        </div>

        @if (Auth::user() && Auth::user()->role->name === 'Administrador')
            <div class="pt-2 pb-3 space-y-1">
                <x-inicio.responsive-nav-link :href="route('attendance.qr.admin')" :active="request()->routeIs('attendance.qr.admin')">
                    {{ __('QR Asistencias') }}
                </x-inicio.responsive-nav-link>
            </div>
            <div class="pt-2 pb-3 space-y-1" x-data="{ adminOpen: false, openSection: '' }">
                {{-- Botón principal plegable de Administración --}}
                <button @click="adminOpen = !adminOpen"
                    class="w-full flex items-center justify-between px-4 py-2 text-start text-sm font-medium text-gray-600 border-l-4 border-transparent hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <span>{{ __('Administración') }}</span>
                    <svg :class="{ 'rotate-180': adminOpen }" class="h-4 w-4 transform transition-transform"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Contenido plegable --}}
                <div x-show="adminOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1 bg-gray-50">

                    {{-- Panel Principal (no plegable) --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span>{{ __('Panel Principal') }}</span>
                    </a>

                    {{-- Sección: Datos base (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'datos' ? '' : 'datos')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Datos Base') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'datos' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'datos'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.terms.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.terms.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12v-.008zM15 15h.008v.008H15v-.008zM15 18h.008v.008H15v-.008zM9 15h.008v.008H9v-.008zM9 18h.008v.008H9v-.008zM6 15h.008v.008H6v-.008zM6 18h.008v.008H6v-.008z" />
                                </svg>
                                <span>{{ __('Términos Académicos') }}</span>
                            </a>

                            <a href="{{ route('admin.subjects.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.subjects.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                <span>{{ __('Gestión de Materias') }}</span>
                            </a>

                            <a href="{{ route('admin.groups.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.groups.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                </svg>
                                <span>{{ __('Gestión de Grupos') }}</span>
                            </a>

                            <a href="{{ route('admin.classrooms.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.classrooms.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h18M3 21h18" />
                                </svg>
                                <span>{{ __('Gestión de Aulas') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Sección: Programación (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'programacion' ? '' : 'programacion')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Programación') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'programacion' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'programacion'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.course-offerings.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.course-offerings.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 6.878V6a2.25 2.25 0 012.25-2.25h7.5A2.25 2.25 0 0118 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 004.5 9v.878m13.5-3A2.25 2.25 0 0119.5 9v.878m0 0a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.878m12 0A2.25 2.25 0 0019.5 9v-.878m-12 0c.235.083.487.128.75.128h10.5c.263 0 .515.045.75.128m-12 0v9A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25v-9m-19.5 0h19.5" />
                                </svg>
                                <span>{{ __('Ofertas de Cursos') }}</span>
                            </a>

                            <a href="{{ route('admin.class-assignments.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.class-assignments.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.662M10.5 3.75a48.441 48.441 0 00-3.32.09c-1.135.094-1.976 1.057-1.976 2.192v12.15c0 1.135.841 2.098 1.976 2.192a48.427 48.427 0 003.32.09c.229 0 .458-.009.682-.027m-.682-2.042c.229.018.458.027.682.027a48.427 48.427 0 003.32-.09c1.135-.094.84-2.098-1.976-2.192a48.44 48.44 0 00-3.32-.09c-.229 0-.458.009-.682.027m.682 2.042v.008c-.229.018-.458.027-.682.027z" />
                                </svg>
                                <span>{{ __('Asignación de Clases') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Sección: Gestión de Asistencia (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'asistencia' ? '' : 'asistencia')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Gestión de Asistencia') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'asistencia' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'asistencia'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.teacher-attendance.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.teacher-attendance.*') && !request()->routeIs('attendance.qr.admin') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('Asistencia Docente') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Sección: Administración (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'admin' ? '' : 'admin')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Administración') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'admin' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'admin'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.users.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07M15 19.128c.331.18.68.324 1.05.437m-6.578 3.553a11.217 11.217 0 01-1.124.061 11.25 11.25 0 01-1.124-.061m0 0c-1.29.53-2.648.879-4.132.879a11.25 11.25 0 01-4.132-.879m0 0c-1.017-.417-1.976-.924-2.846-1.5T0 16.5v-2.25C0 11.72 4.03 7.5 9 7.5s9 4.22 9 6.75v2.25z" />
                                </svg>
                                <span>{{ __('Gestión de Usuarios') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Sección: Reportes (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'reportes' ? '' : 'reportes')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Reportes') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'reportes' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'reportes'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.reports.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.reports.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>{{ __('Exportar Datos') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Sección: Auditoría (plegable) --}}
                    <div>
                        <button @click="openSection = (openSection === 'auditoria' ? '' : 'auditoria')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ __('Auditoría') }}</span>
                            <svg :class="{ 'rotate-180': openSection === 'auditoria' }"
                                class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openSection === 'auditoria'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-1">

                            <a href="{{ route('admin.logs.index') }}"
                                class="flex items-center gap-2 pl-8 pr-4 py-2 text-sm font-medium border-l-4 {{ request()->routeIs('admin.logs.*') ? 'border-indigo-400 text-gray-900 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-100 hover:border-gray-300' }} transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.125 2.25h-4.5c-1.125 0-2.062.094-2.062 2.063v15.375c0 1.125.938 2.063 2.063 2.063h12.75c1.125 0 2.063-.938 2.063-2.063V8.318c0-.52-.212-1.008-.574-1.37l-4.688-4.688A1.875 1.875 0 0011.813 2.25H10.125v-1.5zM10.125 2.25v3.375c0 .621.504 1.125 1.125 1.125h3.375z" />
                                </svg>
                                <span>{{ __('Visualización de bitácora') }}</span>
                            </a>
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
