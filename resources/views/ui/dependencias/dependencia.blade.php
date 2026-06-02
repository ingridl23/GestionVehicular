@extends('layout.app')

@section('content')
<section class="max-w-6xl mx-auto p-6 space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-1 mb-4 transition-colors duration-300 hover:bg-gray-50 dark:hover:bg-gray-700">

        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
            Datos de la Dependencia
        </h2>

        <div class="space-y-2 text-gray-700 dark:text-gray-300 mt-1 mb-4">
            <p>
                <span class="font-medium text-gray-800 dark:text-gray-100"> Nombre: </span>
                {{ $datos_dependencia->nombre }}
            </p>

            <p>
                <span class="font-medium text-gray-800 dark:text-gray-100"> Direccion: </span>
                {{$datos_dependencia->direccion->calle}} {{$datos_dependencia->direccion->altura}} - {{$datos_dependencia->direccion->ciudad}}
            </p>


            <p>
                <span class="font-medium text-gray-800 dark:text-gray-100"> Se encuentra activa:</span>
                <span class="ml-1 px-2 py-1 rounded-full font-semibold
                    {{ $datos_dependencia->activa ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $datos_dependencia->activa ? 'Sí' : 'No' }}
                </span>
            </p>

    </div>
</div>

    <!-- Datos de la estructura de la Dependencia -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mt-1 mb-4 transition-colors duration-300 hover:bg-gray-50 dark:hover:bg-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
            Organización Interna
        </h2>

        @if($datos_dependencia->id_dependencia_padre )
            <p class="text-gray-700 dark:text-gray-300">
                <span class="font-medium text-gray-800 dark:text-gray-100">
                    Dependencia superior:
                </span>
                {{ $datos_dependencia->dependenciaPadre->nombre }}
            </p>
        @else
            <p class="text-gray-500 dark:text-gray-400 italic">
                No posee una dependencia superior.
            </p>
        @endif

        <div class="mt-3">
            @if($dependencias_hijas->isNotEmpty())
                <p class="font-medium text-gray-800 dark:text-gray-100 mb-1">
                    Dependencias hijas:
                </p>

                <ul class=" text-gray-700 dark:text-gray-300 space-y-1">
                    @foreach($dependencias_hijas as $hija)
                        <li class="list-disc list-inside">{{ $hija->nombre }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">
                    No posee dependencias hijas.
                </p>
            @endif
        </div>
    </section>
    <button onclick="window.history.back()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                           <svg class="w-6 h-6 text-blue-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                           </svg>
                       </button>


 <div class="contenedor_loader">
        <div class="loader"></div>
    </div>

@endsection
