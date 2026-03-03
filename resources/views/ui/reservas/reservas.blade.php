@extends('layout.app')

@push('scripts')
<script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosReservas.js') }}"></script>
<script type="module" src="{{ Vite::asset('resources/js/reservas/accionesReserva.js') }}"></script>
@endpush
@section('page-title', 'Administración de Reservas')
@section('page-description', 'Gestión de reservas del sistema')



@section('content')
<section class="py-10 lg:py-[0px]">

  <div class="container mx-auto px-4 py-6">

    <!-- Header con botón de crear -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">

        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white"> Reservas del Sistema</h1>
       @hasanyrole(['Administrador|Administrador de Dependencia|Jefe de Area'])
       <p class="text-gray-600">
             Total de reservas:
            <span class="font-semibold">{{$total}}</span>
       </p>
@endhasanyrole
        </div>

      @can($configAgregar['can'])
      <a href="{{ $configAgregar['route'] }}"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg
                flex items-center justify-center gap-2 transition-colors
                w-full md:w-auto md:self-auto self-stretch">
        {{ $configAgregar['text'] }}
      </a>
      @endcan
    </div>


    @hasanyrole(['Administrador','Administrador de Dependencia','Jefe de Area'])
    <div class="flex items-end justify-between mb-4">
      <button id="mostrarFiltros" type="button"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
        Filtros
      </button>
    </div>
@endhasanyrole
    @hasanyrole(["Administrador de Dependencia|Jefe de Area"])
    @if($ubicacion ==='externa')
    <div class="flex items-end mb-4 text-xs">
      <button href="#" id="filtroPrestamoTodos" class="bg-gray-400 mr-2 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center gap-2 transition-colors dark:bg-gray-800 dark:hover:bg-gray-700">Todas</button>
      <button href="{{route('filtrar.prestamos.externos')}}" id="filtroPrestamoExterno" class="bg-gray-400 mr-2 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center gap-2 transition-colors dark:bg-gray-800 dark:hover:bg-gray-700">Solicitadas a mi dependencia</button>
      <button href="{{route('filtrar.prestamos.internos')}}" id="filtroPrestamoInterno" class="bg-gray-400 mr-2 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center gap-2 transition-colors dark:bg-gray-800 dark:hover:bg-gray-700">Solicitadas a otra dependencia</button>
    </div>
    @endif
    @endhasanyrole

    @if($reservas->isEmpty())
    <p class="text-center text-gray-600 ">No hay reservas</p>
    @else

    <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">
      <x-filtros-reserva-fields :vehiculos_filtros="$vehiculos_filtros" :estados_filtros="$estados_filtros" :ubicacion="$ubicacion" />

    </div>

    <p id="mensajeNoHayReservas" class="hidden text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"></p>

    <div class="mx-auto px-0" id="contenedor-general">
      <div class="-mx-4 flex flex-wrap">
        <div class="w-full">
          <div class="max-w-full overflow-x-auto">
            <div class="hidden md:block">
              <x-tabla-reservas-desktop :reservas="$reservas" :ubicacion="$ubicacion" :ids="$ids" :configEditar="$configEditar" :mostrarAcciones="$mostrarAcciones" />
            </div>

            <div class="block md:hidden">
              <x-lista-reservas-mobile :reservas="$reservas" :ubicacion="$ubicacion" :ids="$ids" :configEditar="$configEditar" :mostrarAcciones="$mostrarAcciones" />
            </div>


            <dialog id="dialog-cancelar" class="p-0 backdrop:bg-black/50 rounded-lg">

              <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-auto">
                <div class="p-6">
                  <div class="flex items-center gap-4">

                    <!-- Icono -->
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5"
                        class="h-6 w-6 text-red-400">
                        <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                          stroke-linecap="round"
                          stroke-linejoin="round" />
                      </svg>
                    </div>



                    <div>
                      <h3 class="text-lg font-semibold text-white">
                        Cancelar reserva
                      </h3>
                    </div>

                  </div>

                  <p class="mt-4 text-sm text-gray-400">
                    ¿Esta seguro de cancelar esta reserva? Al hacerlo, el día, horario y vehiculo se liberarán y podrían no estar disponibles nuevamente.
                  </p>
                </div>

                <div class="flex justify-end gap-3 bg-gray-700/30 px-6 py-4 rounded-b-lg">
                  <button type="button"
                    onclick="this.closest('dialog').close()"
                    class="px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-md">
                    Cancelar
                  </button>

                  <button type="button"
                    class="botonCancelar px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-400 rounded-md">
                    Desactivar
                  </button>
                </div>
              </div>

            </dialog>

          </div>
        </div>
      </div>

      <div id="contenedor-js">
        <div id="lista-reservas"></div>
        <div id="paginacion"></div>
      </div>

      <div class="contenedor-servidor flex flex-col items-center justify-center mt-6">
        {{ $reservas->links('vendor.pagination.simple-pagination') }}
      </div>

    </div>
    @endif
  </div>
</section>




<script>
  window.RESERVAS_CONFIG = {
    permissions: {
      ver: @json(auth() -> user() -> can('ver_reservas_internas')),
      editar: @json(auth() -> user() -> can($configEditar['can'])),
      cancelar: @json(auth() -> user() -> can('cancelar_reserva_interna')),
      mostrarAcciones: "{{$mostrarAcciones}}",
    },
    routes: {
      ver: "{{ route('reservas.reserva', ':id') }}",
      editar: "{{ $configEditar['route'] }}",
    }
  };

  window.APP_CONFIG = {
    ubicacion: @json($ubicacion),
  };
</script>
@endsection
