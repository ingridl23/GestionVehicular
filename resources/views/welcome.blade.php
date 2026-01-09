<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ Vite::asset('resources/imagenes/vehiculos.png') }}');">

        <div class="relative max-w-lg text-center px-4">

            <!-- TÍTULO -->
            <h1 class="text-3xl font-bold text-blue-900 drop-shadow-md">
                Gestión Vehicular
            </h1>
            <h2 class="text-gray-700 mb-6 drop-shadow-md">
                Municipalidad de Tres Arroyos
            </h2>

            <!-- CONTENEDOR FORM -->
            <div class="backdrop-blur-xl bg-white/20 border border-white/40 shadow-2xl rounded-2xl p-6">


                <form method="POST">
                    @csrf

                    <!-- EMAIL -->
                    <label for="email" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" placeholder="Correo Electrónico"
                        class="w-full px-4 py-2 mb-3 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300" />

                    <!-- PASSWORD -->
                    <label for="password" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <input type="password" name="password" placeholder="Contraseña"
                        class="w-full px-4 py-2 mb-4 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300" />

                    <!-- BOTÓN -->
                    <button type="submit"
                        class="w-full bg-blue-700 text-white font-semibold py-2 rounded-lg hover:bg-blue-800 transition">
                        Iniciar Sesión
                    </button>

                    <!-- LINK -->
                    <a href="#" class="block mt-3 text-sm text-blue-700 hover:underline">
                        Olvidé mi contraseña
                    </a>
                </form>

            </div>
        </div>
    </div>

    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
</body>

</html>
