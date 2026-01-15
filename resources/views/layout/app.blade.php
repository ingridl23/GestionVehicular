<!DOCTYPE html>
<html lang="es" x-data class="dark:bg-slate-800">
<head>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
@php
    $mockUser = (object)[
        'name' => 'Admin Demo',
        'role' => 'admin'
    ];
@endphp
@php
    $role = auth()->user()->role ?? $mockUser->role;
@endphp

<body class="flex h-screen">

    @include('layout.sidebar')

    <main class="flex-1 flex flex-col">
        @include('layout.navbar')
        <div class="p-4">
            @yield('content')
        </div>
    </main>

</body>
</html>
