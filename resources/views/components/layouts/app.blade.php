<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Parcial') }}</title>

    <!-- Fonts -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <x-inicio.navigation />

        <!-- Page Heading -->
        @isset($header)
            <header class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            {{ $header }}
                    </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

    </div>
</body>

</html>
