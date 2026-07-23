@extends('layout.appOperativo')

@section('title', 'Nuevo reporte')

@section('content')
<div class="max-w-md mx-auto mt-6 bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">

    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
            Crear nuevo reporte
        </h2>
    </div>

    <!-- Formulario -->
    <form method="POST" action="{{ route('operativo.reportes.store') }}" class="p-4 space-y-4">
        @csrf

        <!-- Título -->
        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Título
            </label>
            <input
                type="text"
                id="titulo"
                name="titulo"
                value="{{ old('titulo') }}"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Ej: Problema con vehículo"
            >
            @error('titulo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Descripción -->
        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Descripción
            </label>
            <textarea
                id="descripcion"
                name="descripcion"
                rows="6"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Contá brevemente qué pasó..."
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex gap-3 pt-2">
            <a
                href="{{ route('operativo.reportes.index') }}"
                class="flex-1 text-center px-4 py-2 rounded-lg border border-gray-300
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="flex-1 px-4 py-2 rounded-lg bg-blue-600 text-white
                       hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Enviar reporte
            </button>
        </div>
    </form>

</div>

@if(session('success'))
    <div class="max-w-md mx-auto mt-4 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="max-w-md mx-auto mt-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
        {{ session('error') }}
    </div>
@endif

@endsection
