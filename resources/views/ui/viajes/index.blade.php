{{-- resources/views/ui/viajes/index.blade.php --}}
@extends('layout.app')

@section('page-title', 'Viajes')
@section('page-description', 'Gestión de recorridos')

@section('content')

@php
$mostrarAcciones = $mostrarAcciones ?? true;
@endphp

<section class="py-6">
  <div class="container mx-auto px-4">

    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-3">
      <div>
        <div class="flex gap-2 pt-2">
            <a  href="{{ route('operativo.dashboard2') }}"class="text-center px-2 py-1 rounded-lg border border-gray-300 text-white  text-center bg-blue-600 hover:bg-blue-700">
                <-
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recorridos</h1>
        </div>
        @hasanyrole('Administrador General|Administrador de Dependencia|Jefe de Area')
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Total: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $viajes->total() }}</span> viajes
          </p>
        @endhasanyrole
      </div>
    </div>


    @if($viajes->isEmpty())
      <div class="flex flex-col items-center justify-center py-24 text-gray-400 dark:text-gray-600">
        <i class="fas fa-route text-5xl mb-4 opacity-30"></i>
        <p class="text-base">No hay viajes registrados</p>
      </div>

    @else



      {{-- Desktop --}}
      <div class="hidden md:block">
     <x-tabla-viajes-desktop
    :viajes="$viajes"
    :ubicacion="null"
    :mostrarAcciones="$mostrarAcciones"
/>
      </div>
      {{-- Mobile --}}
      <div class="block md:hidden">
      <x-lista-viajes-mobile
    :viajes="$viajes"
    :ubicacion="null"
    :mostrarAcciones="$mostrarAcciones"
/>
      </div>

      <div class="flex justify-center mt-6">
        {{ $viajes->links('vendor.pagination.simple-pagination') }}
      </div>

    @endif

  </div>
</section>

{{-- Dialog cancelar --}}
<dialog id="dialog-cancelar" class="p-0 backdrop:bg-black/50 rounded-lg">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-auto">
    <div class="p-6">
      <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/10">
          <i class="fas fa-triangle-exclamation text-red-500 dark:text-red-400 text-xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancelar viaje</h3>
      </div>
      <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        ¿Estás seguro de cancelar este viaje? La reserva volverá al estado anterior.
      </p>
    </div>
    <div class="flex justify-end gap-3 bg-gray-50 dark:bg-gray-700/30 px-6 py-4 rounded-b-lg">
      <button type="button" onclick="this.closest('dialog').close()"
        class="px-4 py-2 text-sm font-medium rounded-md
               bg-gray-200 dark:bg-white/10
               text-gray-700 dark:text-white
               hover:bg-gray-300 dark:hover:bg-white/20 transition">
        Atrás
      </button>
      <button type="button"
        class="botonCancelar px-4 py-2 text-sm font-medium text-white
               bg-red-500 hover:bg-red-600 rounded-md transition">
        Confirmar Cancelación
      </button>
    </div>
  </div>
</dialog>

<script>

function vfToggle() {
  const panel   = document.getElementById('vfPanel');
  const chevron = document.getElementById('vfChevron');
  const isOpen  = !panel.classList.contains('hidden');
  panel.classList.toggle('hidden', isOpen);
  chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}

// Abre el panel automáticamente si hay filtros activos en la URL
  document.addEventListener('DOMContentLoaded', function () {
  const params = new URLSearchParams(window.location.search);
  const active = ['nombre','fecha_inicio','fecha_fin','estado','vehiculo']
    .filter(k => params.get(k) && params.get(k) !== 'default').length;

  if (active > 0) {
    document.getElementById('vfPanel')?.classList.remove('hidden');
    const chevron = document.getElementById('vfChevron');
    if (chevron) chevron.style.transform = 'rotate(180deg)';
    const badge = document.getElementById('vfBadge');
    if (badge) { badge.textContent = active; badge.classList.remove('hidden'); }
  }
});

</script>

@endsection
