
@extends('layout.app')

@push('scripts')
<script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosReservas.js') }}"></script>
<script type="module" src="{{ Vite::asset('resources/js/reservas/accionesReserva.js') }}"></script>
@endpush


@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-[0px]">
  

  @if($dependencias->isEmpty())
    <p class="text-center text-gray-600 ">No hay dependencias cargadas</p>
  @else
  <div class="flex items-end justify-between">
    <button id="mostrarFiltros" type="button"
      class="rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      Filtros
    </button>
    
    @can('crear_dependencias')
    <a href="{{ route('admin.dependencias.create') }}"
      class="inline-block rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-center text-white
                        hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
     Crear dependencia
    </a>
    @endcan
  </div>
  <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">
    {{--ACA VAN LOS FILTROS--}}

  </div>

  <p id="mensajeNoHayDependencias" class="hidden text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"></p>
          
  <div class="mx-auto px-0" id="contenedor-general">
    <div class="-mx-4 flex flex-wrap">
      <div class="w-full">
        <div class="max-w-full overflow-x-auto">
          <div class="hidden md:block">
            <x-tabla-dependencia-desktop :dependencias="$dependencias" />
          </div>

          <div class="block md:hidden">
            {{-- ACA LA VISTA MOBILE --}}
          </div>

        </div>
      </div>
    </div>

    <div class="contenedor-servidor flex flex-col items-center justify-center mt-6">
      {{ $dependencias->links('vendor.pagination.simple-pagination') }}
    </div>

  </div>
  @endif
</section>


@endsection