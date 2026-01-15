<!-- resources/views/layout/navbar.blade.php -->
<header class="flex items-center justify-between h-12 px-4 border-b bg-white dark:bg-slate-800">

    <div></div>

    <div class="flex items-center gap-4">
        <!-- Notificaciones -->
        <button class="relative text-slate-600 dark:text-slate-200">
            <i class="fa-regular fa-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">3</span>
        </button>

        <!-- Perfil -->
        <div class="relative">
            <button @click="openUser = !openUser" class="flex items-center gap-2">
                <i class="fa-solid fa-user-circle text-xl text-slate-600 dark:text-slate-200"></i>
                <span class="text-sm text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</span>
            </button>

            <div x-show="openUser" @click.away="openUser=false" class="absolute right-0 mt-2 bg-white dark:bg-slate-700 border rounded shadow">
                <a href="#" class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600">Perfil</a>
                <form action="/logout" method="POST">
                    @csrf
                    <button class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</header>
