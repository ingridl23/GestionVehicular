<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Operativo')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/operador.css') }}" rel="stylesheet" />

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen pt-12">

    @include('layout.navbarOperativo')

    <main class="max-w-md mx-auto px-4 mt-6">
        @yield('content')
    </main>

    @include('layout.footer')

    @vite(['resources/js/scriptsOperativo.js'])
</body>
</html>
