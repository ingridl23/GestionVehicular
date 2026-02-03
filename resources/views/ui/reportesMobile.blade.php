<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title> Dashboard-operador </title>
    <link href="{{ asset('css/operador.css') }}" rel="stylesheet" />
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer" />

</head>
<body>
<div class="max-w-md mx-auto mt-8 bg-white dark:bg-gray-800 rounded-xl shadow p-6">

    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
        Crear nuevo reporte
    </h2>

    <form method="POST" action="{{ route('operativo.reportes.store') }}" class="space-y-4">
        @csrf

        <!-- Título -->
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Título</label>
            <input
                type="text"
                name="titulo"
                required
                class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                placeholder="Ej: Problema con vehículo"
            >
        </div>

        <!-- Descripción -->
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Descripción</label>
            <textarea
                name="descripcion"
                rows="4"
                required
                class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                placeholder="Contá brevemente qué pasó..."
            ></textarea>
        </div>

        <!-- Botones -->
        <div class="flex gap-3 pt-2">
            <a
                href="{{ url()->previous() }}"
                class="flex-1 text-center px-4 py-2 rounded-lg border border-gray-300 text-gray-600"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="flex-1 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
            >
                Enviar reporte
            </button>
        </div>
    </form>
</div>

</body>
</html>
