<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva contraseña - Gestión Vehicular</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-flota">
        <div class="relative max-w-lg w-full text-center px-4">

            <!-- TÍTULO -->
            <h1 class="text-3xl font-bold text-blue-900 drop-shadow-md">
                Gestión Vehicular
            </h1>

            <h3 class="text-gray-700 mb-6 drop-shadow-md">
                Municipalidad de Tres Arroyos
            </h3>

            <!-- ERRORES -->
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

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Crear nueva contraseña
                </h2>

                <p class="text-sm text-gray-700 mb-4">
                    Ingresá tu nueva contraseña para completar el proceso de recuperación.
                </p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- TOKEN -->
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <!-- EMAIL -->
                    <input type="hidden" name="email" value="{{ request()->email }}">

                    <!-- NUEVA PASSWORD -->
                    <label for="password" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Nueva contraseña
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="••••••••"
                        required
                        class="w-full px-4 py-2 mb-3 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300 focus:border-blue-500 @error('password') border-red-500 @enderror"
                    />
                    @error('password')
                        <p class="text-red-500 text-xs text-left mb-2">{{ $message }}</p>
                    @enderror

                    <!-- CONFIRMAR PASSWORD -->
                    <label for="password_confirmation" class="block text-left text-sm font-medium text-gray-700 mb-1">
                        Confirmar contraseña
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        placeholder="••••••••"
                        required
                        class="w-full px-4 py-2 mb-4 rounded-lg border border-gray-300 focus:ring focus:ring-blue-300 focus:border-blue-500"
                    />

                    <!-- BOTÓN -->
                    <button
                        type="submit"
                        class="w-full bg-blue-700 text-white font-semibold py-2 rounded-lg hover:bg-blue-800 transition duration-200"
                    >
                        Restablecer contraseña
                    </button>

                    <!-- VOLVER AL LOGIN -->
                    <a href="{{ route('login') }}" class="block mt-3 text-sm text-blue-700 hover:underline">
                        Volver al inicio de sesión
                    </a>

                </form>

            </div>
        </div>
    </div>

    @include('layout.footer')

</body>
</html>

