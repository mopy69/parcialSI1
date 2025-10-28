<x-app-layout>
    <div class="py-12 admin-page" style="min-height:60vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 admin-content">
                    <div class="flex mb-4">
                        <div class="w-64 flex-shrink-0">
                            <!-- Sidebar -->
                            <div class="space-y-2">
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Panel Principal
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Gestión de Usuarios
                                </a>
                                <a href="{{ route('admin.logs.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.logs.*') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Visualizacion de bitacora
                                </a>
                                <a href="{{ route('admin.classrooms.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.classrooms.*') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Gestión de Aulas
                                </a>
                                <a href="{{ route('admin.groups.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.groups.*') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Gestión de Grupos
                                </a>
                                <a href="{{ route('admin.subjects.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.subjects.*') ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    Gestión de Materias
                                </a>
                            </div>
                        </div>
                        <div class="flex-1 ml-8">
                            <!-- Content -->
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <style>
        /* Admin layout helpers */
        .admin-content { font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }

        /* Sidebar */
        .admin-page .admin-content .space-y-2 a {
            display: block;
            padding: .5rem .75rem;
            color: #0f172a; /* slate-900 */
            border-radius: .375rem;
            text-decoration: none;
            transition: background-color .15s ease;
        }

        .admin-page .admin-content .space-y-2 a:hover { background-color: #f1f5f9; }
        .admin-page .admin-content .space-y-2 a[aria-current='page'],
        .admin-page .admin-content .space-y-2 a.active,
        .admin-page .admin-content .space-y-2 a:focus {
            background-color: #e2e8f0; 
            font-weight: 600;
        }

        /* Tables */
        .admin-content table { width: 100%; border-collapse: collapse; }
        .admin-content thead th { text-align: left; background: #f8fafc; padding: .75rem; font-size: .75rem; color: #475569; }
        .admin-content tbody td { padding: .75rem; border-top: 1px solid #eef2f7; color: #0f172a; }
        .admin-content tr:hover td { background: #f8fafc; }

        /* Primary / secondary actions */
        .admin-primary {
            background-color: #2563eb; /* blue-600 */
            color: #fff !important;
            padding: .5rem .75rem;
            border-radius: .375rem;
            text-decoration: none;
            display: inline-block;
            border: 1px solid transparent;
        }
        .admin-primary:hover { background-color: #1e40af; }

        .admin-secondary {
            background-color: #6b7280; /* gray-500 */
            color: #fff !important;
            padding: .45rem .7rem;
            border-radius: .375rem;
            text-decoration: none;
            display: inline-block;
            border: 1px solid transparent;
        }
        .admin-secondary:hover { background-color: #4b5563; }

        /* Form inputs (simple) */
        .admin-content input[type="text"],
        .admin-content input[type="email"],
        .admin-content input[type="password"],
        .admin-content select,
        .admin-content textarea {
            border: 1px solid #e6edf3; padding: .5rem .6rem; border-radius: .375rem; width: 100%;
        }

        /* Make submit inputs visible too */
        .admin-content input[type="submit"], .admin-content button[type="submit"] { box-sizing: border-box; }
    </style>
</x-app-layout>