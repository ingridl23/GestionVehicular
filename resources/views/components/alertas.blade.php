@extends('layout.app')

@section('page-title', 'Alertas y Notificaciones')
@section('page-description', 'Gestión de alertas del sistema')

@section('content')


<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Alertas Activas</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['total'] }}

                </p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Vencimientos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
            {{ $stats['licencias'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-id-card text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mantenimiento</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                   {{ $stats['mantenimiento'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-wrench text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Vehículos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['vehiculos'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-car-crash text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Alertas -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Todas las Alertas
        </h2>

        <button onclick="resolverSeleccionadas()"
    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
    Resolver seleccionadas
</button>
    </div>


      <dialog id="DialogSinAlertas" class="p-0 backdrop:bg-black/50 rounded-lg">

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md mx-auto">
          <div class="p-6">
            <div class="flex items-center gap-4">
              <!-- Icono -->
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                  class="h-6 w-6 text-blue-600 dark:text-blue-400">
                    <path d="M9 12.75 11.25 15 15 9.75" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>

              <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Sin seleccion
              </h2>
            </div>

            <p id="dialogText" class="mt-4 text-sm text-gray-600 dark:text-gray-300">
              <!-- texto dinámico -->
            </p>
          </div>

          <div class="flex justify-end gap-3 bg-gray-100 dark:bg-gray-800 px-6 py-4 rounded-b-lg">


            <button id="confirmBtn"
              class="botonAceptar px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
              Aceptar
            </button>
          </div>
        </div>
      </dialog>















    <div class="space-y-3">
        @forelse($alertas as $alerta)
            <div class="flex items-start gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
<input type="checkbox" class="alerta-checkbox mt-1" value="{{ $alerta->id }}">
                <!-- Icono -->
        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $alerta->color_class }}">
    <i class="fas {{ $alerta->icono }}"></i>
</div>


                <!-- Contenido -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" onclick= "verDetalle({{ $alerta->id }})">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $alerta->titulo }}

                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $alerta->mensaje }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>
                                {{ $alerta->fecha_generada->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Botón Resolver -->
                        <button
                            onclick="resolver({{ $alerta->id }})"
                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors flex-shrink-0">
                            <i class="fas fa-check mr-1"></i>
                            Resolver
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">No hay alertas activas</p>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $alertas->links() }}
    </div>

</div>
<div id="modalAlerta" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-bold mb-3">Detalle de alerta</h2>

        <p id="detalleMensaje"></p>
         <p class="text-sm mt-2">
            <strong>Dependencia:</strong>
            <span id="detalleDependencia"></span>
        </p>

        <p id="detalleFecha" class="text-sm text-gray-500 mt-2"></p>

        <button onclick="cerrarModal()" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded">
            Cerrar
        </button>
    </div>
</div>



@endsection

