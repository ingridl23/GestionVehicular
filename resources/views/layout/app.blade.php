<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion Vehicular') }}</title>
    <link rel="icon" href="{{ asset('assets/iconos/iconoweb.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="{{ asset('css/operador.css') }}" rel="stylesheet" />

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>


</head>



<body class="bg-gray-50    dark:bg-gray-900 antialiased">
    <div
        x-data="{
            sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
            darkMode: localStorage.getItem('darkMode') === 'true',
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
            },
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode);
            }
        }"
        x-init="
            if (darkMode) {
                document.documentElement.classList.add('dark');
            }
            $watch('darkMode', value => {
                if (value) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })
        "
        class="flex h-screen overflow-hidden"
    >

        @auth
            <!-- Sidebar -->
            @include('layout.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Navbar -->
                @include('layout.navbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">

                    @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 border border-green-400 text-green-800 dark:bg-green-900 dark:border-green-600 dark:text-green-200 flex items-start gap-2">
                        <i class="fas fa-check-circle mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 border border-red-400 text-red-800 dark:bg-red-900 dark:border-red-600 dark:text-red-200 flex items-start gap-2">
                        <i class="fas fa-circle-exclamation mt-0.5"></i>
                        <div>
                            @if(is_array(session('error')))
                                <strong>{{ session('error')['titulo'] ?? 'Error' }}</strong>
                                @if(isset(session('error')['detalle']))
                                    <p class="text-sm mt-0.5">{{ session('error')['detalle'] }}</p>
                                @endif
                            @else
                                {{ session('error') }}
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 border border-red-400 text-red-800 dark:bg-red-900 dark:border-red-600 dark:text-red-200">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-triangle-exclamation"></i>
                            <strong>Corregí los siguientes errores:</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        @else
            <!-- Login Page -->
            <div class="flex-1">
                @yield('content')
            </div>
        @endauth

    </div>

    @stack('scripts')
</body>
</html>
