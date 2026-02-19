@extends('layout.app')

@section('title', 'Mis reportes')

@section('content')
<div class="space-y-4">

 <div class="flex gap-5 justify-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('dashboard') }}"
                   class="px-6 py-3 text-center bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </a>


    <!-- Botón para crear nuevo reporte -->
    <a href="{{ route('operativo.reportes.create') }}"
       class=" text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-sm">
        <i class="fas fa-plus mr-2"></i>
        Nuevo reporte
    </a>
 </div>
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Lista de reportes -->
    @forelse ($reportes as $reporte)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="p-4">
                <!-- Título y estado -->
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex-1">
                        {{ $reporte->titulo }}
                    </h3>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full
                        @if($reporte->estado === 'pendiente') bg-yellow-100 text-yellow-800
                        @elseif($reporte->estado === 'en_revision') bg-blue-100 text-blue-800
                        @elseif($reporte->estado === 'atendido') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                      {{ ucfirst(str_replace('_', ' ', $reporte->estado->value)) }}

                    </span>
                </div>

                <!-- Descripción -->
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                    {{ $reporte->descripcion }}
                </p>

                <!-- Fecha -->
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        <i class="far fa-clock mr-1"></i>
                        {{ $reporte->created_at->format('d/m/Y H:i') }}
                    </span>

                    @if($reporte->comentarios && $reporte->comentarios->count() > 0)
                        <span>
                            <i class="far fa-comment mr-1"></i>
                            {{ $reporte->comentarios->count() }}
                            {{ $reporte->comentarios->count() === 1 ? 'comentario' : 'comentarios' }}
                        </span>
                    @endif
                </div>
            </div>


            <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                <a href=" {{ route('operativo.reportes.detalles') }}"    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    Ver detalles →
                </a>
            </div>

        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">
                No tenés reportes todavía
            </p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                Creá tu primer reporte usando el botón de arriba
            </p>
        </div>
    @endforelse

</div>
@endsection
