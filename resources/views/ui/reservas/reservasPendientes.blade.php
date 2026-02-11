@extends('layout.app')

@push('scripts')
  <script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosReservas.js') }}"></script>
@endpush

@section('content')

<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-[0px]">
  

  @if($reservas->isEmpty())
    <p class="text-center text-gray-600 ">No hay reservas</p>
  @else
  <div class="flex items-end justify-between">
    <button id="mostrarFiltros" type="button"
      class="rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      Filtros
    </button>
  </div>
  <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">
    <x-filtros-reserva-fields :vehiculos_filtros="$vehiculos_filtros" :estados_filtros="$estados_filtros" :ubicacion="$ubicacion" />

  </div>

  <p id="mensajeNoHayReservas" class="hidden text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"></p>
          
  <div class="mx-auto px-0" id="contenedor-general">
    <div class="-mx-4 flex flex-wrap">
      <div class="w-full">
        <div class="max-w-full overflow-x-auto">
          <div class="hidden md:block">
            <x-tabla-reservas-desktop :reservas="$reservas" :mostrarAcciones="$mostrarAcciones"  />
          </div>

          <div class="block md:hidden">
            <x-lista-reservas-mobile :reservas="$reservas" :mostrarAcciones="$mostrarAcciones"  />
          </div>

        </div>
      </div>
    </div>

    <div id="contenedor-js" style="display:none;">
      <div id="lista-reservas"></div>
      <div id="paginacion"></div>
    </div>

    <div class="contenedor-servidor flex flex-col items-center justify-center mt-6">
      {{ $reservas->links('vendor.pagination.simple-pagination') }}
    </div>

  </div>
  @endif
</section>

<script>
  window.RESERVAS_CONFIG = {
    permissions: {
      ver: @json(auth() -> user() -> can('ver_solicitudes_prestamos')),
      autorizar: @json(auth() -> user() -> can('autorizar_prestamos')),
      rechazar: @json(auth() -> user() -> can('rechazar_prestamos')),
      mostrarAcciones: "{{$mostrarAcciones}}",
    },
    routes: {
      ver: "{{ route('reservas.reserva', ':id') }}",
      autorizar: "{{ route('admin.reservas.autorizar', ':id') }}",
      rechazar: "{{ route('admin.reservas.rechazar', ':id') }}",
    }
  };

  window.APP_CONFIG = {
    ubicacion: @json($ubicacion),
  };
</script>


@endsection