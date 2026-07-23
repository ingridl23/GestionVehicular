<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title')
        @elseif(trim($__env->yieldContent('page-title')) !== '')
            @yield('page-title')
        @else
            Dashboard Operativo
        @endif
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/operador.css') }}" rel="stylesheet" />
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen">

    <div x-data="{ notifOpen: false, userOpen: false }" class="min-h-screen flex flex-col">

        <!-- Top bar -->
        <header class="sticky top-0 z-30 h-14 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4">
            <h1 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                @hasSection('title')
                    @yield('title')
                @elseif(trim($__env->yieldContent('page-title')) !== '')
                    @yield('page-title')
                @else
                    Dashboard Operativo
                @endif
            </h1>

            <div class="flex items-center gap-2">
                <!-- Notificaciones -->
                <div class="relative">
                    <button
                        @click="notifOpen = !notifOpen"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 relative">
                        <i class="far fa-bell text-lg"></i>
                        @if(($alertas ?? collect())->count())
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>

                    <div
                        x-show="notifOpen"
                        @click.away="notifOpen = false"
                        x-transition
                        style="display: none;"
                        class="absolute right-0 mt-2 w-72 max-h-[70vh] overflow-y-auto bg-white dark:bg-gray-800 shadow-xl rounded-xl border dark:border-gray-700 z-40">

                        <div class="p-3 font-semibold text-gray-700 dark:text-gray-200 border-b dark:border-gray-700">
                            Alertas
                        </div>

                        @forelse(($alertas ?? collect()) as $alerta)
                        <div class="p-3 border-b dark:border-gray-700">
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $alerta->mensaje }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $alerta->fecha_generada?->diffForHumans() }}</p>
                        </div>
                        @empty
                        <div class="p-3 text-sm text-gray-500 dark:text-gray-400">
                            Sin alertas
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cuenta -->
                <div class="relative">
                    <button
                        @click="userOpen = !userOpen"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-user-circle text-lg"></i>
                    </button>

                    <div
                        x-show="userOpen"
                        @click.away="userOpen = false"
                        x-transition
                        style="display: none;"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2 z-40">

                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-user w-4"></i>
                            <span>Mi Perfil</span>
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido -->
        <main class="flex-1 max-w-md w-full mx-auto px-4 py-4" style="padding-bottom: 5.5rem;">

            @include('layout.partials.flash-messages')

            @yield('content')
        </main>

        @include('layout.bottomnav')
    </div>

    @stack('scripts')
</body>
</html>
