<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Gestión Vehicular</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center"
         style="background-image: url('{{ Vite::asset('resources/assets/imagenes/flotavehicular.png') }}');">
        <div class="relative max-w-lg w-full text-center px-4">

            <!-- TÍTULO -->
            <h1 class="text-3xl font-bold text-blue-900 drop-shadow-md">
                Gestión Vehicular
            </h1>

            <h3 class="text-gray-700 mb-6 drop-shadow-md">
                Municipalidad de Tres Arroyos
            </h3>

            <!-- MENSAJES DE ERROR/ÉXITO -->
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <ul class="list-none text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- CONTENEDOR FORM -->
            <div class="backdrop-blur-lg bg-white/30 shadow-xl rounded-xl p-6 border border-white/40">

                {{-- Fortify usa POST /login automáticamente --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- HONEYPOT (campo oculto anti-bot) -->
                    <input type="text" name="oculto" style="display:none" tabindex="-1" autocomplete="off">

                    <!-- EMAIL -->
                    <label for="email" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                        class="w-full px-4 py-2 mb-3 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300 focus:border-blue-500 @error('email') border-red-500 @enderror"
                    />
                    @error('email')
                        <p class="text-red-500 text-xs text-left mb-2">{{ $message }}</p>
                    @enderror

                    <!-- PASSWORD -->
                    <label for="password" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="••••••••"
                        required
                        class="w-full px-4 py-2 mb-4 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300 focus:border-blue-500 @error('password') border-red-500 @enderror"
                    />
                    @error('password')
                        <p class="text-red-500 text-xs text-left mb-2">{{ $message }}</p>
                    @enderror

                    <!-- RECORDARME -->
                    <div class="flex items-center mb-4">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-700">
                            Recordarme
                        </label>
                    </div>

                    <!-- BOTÓN -->
                    <button
                        type="submit"
                        class="w-full bg-blue-700 text-white font-semibold py-2 rounded-lg hover:bg-blue-800 transition duration-200"
                    >
                        Iniciar Sesión
                    </button>

                    <!-- LINK RECUPERAR CONTRASEÑA -->
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="block mt-3 text-sm text-blue-700 hover:underline">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
</body>
</html>
