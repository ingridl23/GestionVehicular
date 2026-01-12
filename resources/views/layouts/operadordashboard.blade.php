<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Dashboard- operador </title>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- NAV SUPERIOR -->
    <nav class="w-full bg-blue-600 text-white flex items-center justify-between px-3 h-12">

        <!-- BOTÓN MENU -->
        <button id="menuBtn" class="p-1">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- TÍTULO -->
        <span class="font-medium text-sm">Gestión Vehicular</span>

        <!-- ICONOS -->
        <div class="flex items-center gap-4">

            <!-- CAMPANA -->
            <div class="relative">
                <button id="notifBtn" class="p-1">
                    <i class="fas fa-bell text-lg"></i>
                </button>

                <!-- POPUP NOTIFICACIONES -->
                <div id="notifPopup"
                    class="absolute right-0 top-10 w-56 bg-white text-gray-800 rounded shadow-lg border p-3 text-xs hidden z-50">
                    <p class="font-semibold mb-1 text-gray-700">Notificaciones</p>
                    <div class="space-y-1">
                        <p class="text-gray-600">Hace 2m — Nueva reserva</p>
                        <p class="text-gray-600">Hace 15m — Vehículo fuera de servicio</p>
                    </div>
                </div>
            </div>
            <!-- USUARIO -->
            <div class="relative">
                <button id="userBtn" class="p-1">
                    <i class="fas fa-user text-lg"></i>
                </button>

                <!-- MENU USER -->
                <div id="userMenu"
                    class="absolute right-0 top-10 bg-white text-gray-800 w-40 rounded shadow-lg border text-sm hidden z-50">
                    <button class="block w-full text-left px-3 py-2 hover:bg-gray-100">Perfil</button>
                    <hr>
                    <button class="block w-full text-left px-3 py-2 hover:bg-gray-100">Cerrar sesión</button>
                </div>
            </div>

        </div>
    </nav>


    <!-- MENU LATERAL -->
    <div id="sideMenu"
        class="fixed top-0 left-0 h-full w-48 bg-white shadow-lg border-r transform -translate-x-full transition duration-200 z-50 pt-12 text-sm">
        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Mis reportes</button>
        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Mis viajes</button>
        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">Mis reservas</button>
    </div>


    <script>
        const sideMenu = document.getElementById("sideMenu");
        const menuBtn = document.getElementById("menuBtn");

        const notifBtn = document.getElementById("notifBtn");
        const notifPopup = document.getElementById("notifPopup");

        const userBtn = document.getElementById("userBtn");
        const userMenu = document.getElementById("userMenu");

        // MENU LATERAL
        menuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            sideMenu.style.transform =
                sideMenu.style.transform === "translateX(0px)" ? "translateX(-100%)" : "translateX(0px)";
        });

        // CAMPANA
        notifBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            notifPopup.classList.toggle("hidden");
            userMenu.classList.add("hidden");
        });

        // USUARIO
        userBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            userMenu.classList.toggle("hidden");
            notifPopup.classList.add("hidden");
        });

        // CERRAR AL HACER CLICK FUERA
        document.addEventListener("click", () => {
            sideMenu.style.transform = "translateX(-100%)";
            notifPopup.classList.add("hidden");
            userMenu.classList.add("hidden");
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

</body>

</html>
