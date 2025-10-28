<nav id="admin-sidebar" class="space-y-2 sidebar" aria-label="Sidebar de administración">
    
    <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
        Panel Principal
    </x-admin.nav-link>

    <x-admin.nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
        Gestión de Usuarios
    </x-admin.nav-link>

    <x-admin.nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.*')">
        Visualización de bitácora
    </x-admin.nav-link>

    <x-admin.nav-link :href="route('admin.classrooms.index')" :active="request()->routeIs('admin.classrooms.*')">
        Gestión de Aulas
    </x-admin.nav-link>
    
    <x-admin.nav-link :href="route('admin.groups.index')" :active="request()->routeIs('admin.groups.*')">
        Gestión de Grupos
    </x-admin.nav-link>

    <x-admin.nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')">
        Gestión de Materias
    </x-admin.nav-link>

</nav>