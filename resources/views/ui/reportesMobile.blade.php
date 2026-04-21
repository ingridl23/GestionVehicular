@extends('layout.appOperativo')

@section('title', 'Nuevo reporte')

@section('content')
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

<div class="contenedor_loader">
        <div class="loader"></div>
    </div>


@endsection
