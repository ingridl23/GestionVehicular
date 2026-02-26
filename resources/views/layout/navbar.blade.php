<!-- resources/views/layout/navbar.blade.php -->
<header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6">

    <!-- Page Title / Breadcrumb -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
            @yield('page-title', 'Dashboard')
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            @yield('page-description', 'Bienvenido al sistema de gestión vehicular')
        </p>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-4">


        <!-- Dark Mode Toggle -->
        <button
            @click="darkMode = !darkMode"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300"
        >
            <i x-show="!darkMode" class="fas fa-moon"></i>
            <i x-show="darkMode" class="fas fa-sun"></i>
        </button>

        <!-- Notifications -->
  <div x-data="{ open: false }" class="relative">

    @auth
        @php
            $notificaciones = auth()->user()->unreadNotifications->take(5);
        @endphp

        <!-- Botón -->
        <button
            @click="open = !open"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 relative"
        >
            <i class="far fa-bell text-lg"></i>

            @if(auth()->user()->unreadNotifications->count())
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            @endif
        </button>

        <!-- Dropdown -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 shadow-xl rounded-lg z-50 border dark:border-gray-700" >

            <div class="p-3 font-semibold text-gray-700 dark:text-gray-200 border-b">
                Notificaciones
            </div>

            @forelse($notificaciones as $notificacion)
    <div
        class="p-3 border-b hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
        onclick="marcarLeida('{{ $notificacion->id }}')"
    >
        <p class="text-sm text-gray-800 dark:text-gray-200">
            {{ $notificacion->data['mensaje'] ?? '' }}
        </p>
        <p class="text-xs text-gray-500">
            {{ $notificacion->created_at->diffForHumans() }}
        </p>
    </div>
            @empty
                <div class="p-3 text-sm text-gray-500">
                    Sin notificaciones
                </div>
            @endforelse

        </div>
    @endauth

</div>

{{--  para que la campana pueda registrar que el usuario ya leyo la notificacion --}}
<script>
function marcarLeida(id) {
    fetch(`/notificaciones/${id}/leer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        location.reload();
    });
}
</script>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}</p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
            </button>

            <!-- Dropdown Menu -->
            <div
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2 z-50"
                style="display: none;"
            >
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                </div>

                <a href="{{ route('profile.show') }} " class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-user w-4"></i>
                    <span>Mi Perfil</span>
                </a>
{{--
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-cog w-4"></i>
                    <span>Configuración</span>
                </a>
--}}
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
