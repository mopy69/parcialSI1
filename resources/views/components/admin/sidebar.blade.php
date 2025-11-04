<nav id="admin-sidebar" class="sidebar" aria-label="Sidebar de administración">
    
    {{-- SECCIÓN PRINCIPAL --}}
    <div class="space-y-3">
        <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
            </x-slot>
            Panel Principal
        </x-admin.nav-link>
    </div>

    {{-- SECCIÓN DE GESTIÓN (CON TÍTULO) --}}
    <div class="pt-6">
        <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider" id="gestion-heading">
            Gestión
        </h3>
        <div class="mt-2 space-y-3" role="group" aria-labelledby="gestion-heading">
            
            <x-admin.nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07M15 19.128c.331.18.68.324 1.05.437m-6.578 3.553a11.217 11.217 0 01-1.124.061 11.25 11.25 0 01-1.124-.061m0 0c-1.29.53-2.648.879-4.132.879a11.25 11.25 0 01-4.132-.879m0 0c-1.017-.417-1.976-.924-2.846-1.5T0 16.5v-2.25C0 11.72 4.03 7.5 9 7.5s9 4.22 9 6.75v2.25z" /></svg>
                </x-slot>
                Gestión de Usuarios
            </x-admin.nav-link>

            <x-admin.nav-link :href="route('admin.classrooms.index')" :active="request()->routeIs('admin.classrooms.*')">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h18M3 21h18" /></svg>
                </x-slot>
                Gestión de Aulas
            </x-admin.nav-link>
            
            <x-admin.nav-link :href="route('admin.groups.index')" :active="request()->routeIs('admin.groups.*')">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5z" /></svg>
                </x-slot>
                Gestión de Grupos
            </x-admin.nav-link>

            <x-admin.nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                </x-slot>
                Gestión de Materias
            </x-admin.nav-link>
            
            {{-- --- BLOQUE AÑADIDO --- --}}
            <x-admin.nav-link :href="route('admin.timeslots.index')" :active="request()->routeIs('admin.timeslots.*')">
                <x-slot name="icon">
                    {{-- Icono: Clock (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot>
                Gestión de Horarios
            </x-admin.nav-link>
            {{-- --- FIN DEL BLOQUE AÑADIDO --- --}}

        </div>
    </div>

    {{-- SECCIÓN DE AUDITORÍA (CON TÍTULO) --}}
    <div class="pt-6">
        <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider" id="auditoria-heading">
            Auditoría
        </h3>
        <div class="mt-2 space-y-3" role="group" aria-labelledby="auditoria-heading">
            <x-admin.nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.*')">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-1.125 0-2.062.938-2.062 2.063v15.375c0 1.125.938 2.063 2.063 2.063h12.75c1.125 0 2.063-.938 2.063-2.063V8.318c0-.52-.212-1.008-.574-1.37l-4.688-4.688A1.875 1.875 0 0011.813 2.25H10.125v-1.5zM10.125 2.25v3.375c0 .621.504 1.125 1.125 1.125h3.375z" /></svg>
                </x-slot>
                Visualización de bitácora
            </x-admin.nav-link>
        </div>
    </div>
</nav>