{{-- resources/views/ui/viajes/index.blade.php --}}
@extends('layout.app')

@section('page-title', 'Viajes')
@section('page-description', 'Gestión de recorridos')

@section('content')

@php
$mostrarAcciones = $mostrarAcciones ?? true;
@endphp

@if($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
<section class="py-6">
  <div class="container mx-auto px-4">

    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-3">
      <div>
        <div class="flex gap-2 pt-2">
           @hasanyrole('Operativo')
                <a  href="{{ route('operativo.dashboard2') }}"class="text-center px-2 py-1 rounded-lg border border-gray-300 text-white  text-center bg-blue-600 hover:bg-blue-700">
                       <-
                   </a>
  @endhasanyrole
            @hasanyrole('Administrador General|Administrador de Dependencia|Jefe de Area')
                   <a href="{{ url()->previous() }}"class="text-center px-2 py-1 rounded-lg border border-gray-300 text-white  text-center bg-blue-600 hover:bg-blue-700">
                       <-
                   </a>

           @endhasanyrole
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recorridos</h1>
        </div>
        @hasanyrole('Administrador General|Administrador de Dependencia|Jefe de Area')
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Total: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $viajes->total() }}</span> viajes
          </p>
        @endhasanyrole
      </div>
    </div>

    {{-- ADMINS --}}
@hasanyrole('Administrador General|Administrador de Dependencia|Jefe de Area')

    {{-- Tarjeta: mi viaje activo cuando el admin es conductor --}}
    @if(isset($viajePropio) && $viajePropio)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-orange-500 mb-6 overflow-hidden">
        <div class="flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-900/20">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-orange-600 dark:text-orange-400">Mi viaje en curso</p>
                <p class="font-bold text-gray-900 dark:text-white">Reserva #{{ $viajePropio->reserva->id ?? '—' }}</p>
            </div>
            <span class="flex items-center gap-1.5 text-xs font-semibold text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/40 px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse inline-block"></span> En curso
            </span>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm mb-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Vehículo</p>
                    <p class="font-bold font-mono text-gray-900 dark:text-white">{{ $viajePropio->vehiculo?->dominio ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Inicio del viaje</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $viajePropio->fecha_inicio->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">KM al salir</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($viajePropio->kilometros_inicio ?? 0, 0, ',', '.') }} km</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('viajes.mapa', $viajePropio->id) }}"
                   class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium
                          bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                          hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 transition">
                    <i class="fas fa-map-marked-alt"></i> Regresar al mapa
                </a>
                <button type="button"
                        onclick="document.getElementById('modal-finalizar-admin').style.display='flex'"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium
                               bg-orange-500 hover:bg-orange-600 text-white transition">
                    <i class="fas fa-flag-checkered"></i> Detener viaje
                </button>
            </div>
        </div>
    </div>

    {{-- Modal bottom-sheet: Finalizar viaje (admin) --}}
    <div id="modal-finalizar-admin"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:flex-end;justify-content:center;"
         onclick="if(event.target===this) this.style.display='none'">
        <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-t-2xl p-6"
             style="max-height:85vh;overflow-y:auto;">
            <div class="w-8 h-1 bg-gray-200 dark:bg-gray-600 rounded-full mx-auto mb-4"></div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Finalizar viaje</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Completá los datos para cerrar el recorrido</p>
            <form method="POST" action="{{ route('viajes.finalizaradmin', $viajePropio->id) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kilómetros finales</label>
                        <input type="number" name="kilometros_fin" required min="0"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado nafta final</label>
                        <select name="id_estado_nafta_fin" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm">
                            <option value="">Seleccione estado</option>
                            @foreach($estadosNafta as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Observaciones <span class="text-gray-400 font-normal">*Opcional</span>
                        </label>
                        <textarea name="observaciones" rows="2" style="resize:none"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm"
                                  placeholder="Ej: sin novedades, entregué llaves en recepción…"></textarea>
                    </div>
                    @if($viajePropio->vehiculo && !$viajePropio->vehiculo->control_satelital)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dirección de estacionamiento <span class="text-gray-400 font-normal">(solo sin GPS)</span>
                        </label>
                        <select name="id_direccion"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm">
                            <option value="">Seleccionar dirección</option>
                            @foreach($direcciones as $dir)
                                <option value="{{ $dir->id }}">{{ $dir->calle }} {{ $dir->altura }} - {{ $dir->ciudad }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                <button type="submit"
                        class="w-full mt-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition">
                    <i class="fas fa-flag-checkered mr-1"></i> Confirmar finalización
                </button>
            </form>
            <button type="button"
                    onclick="document.getElementById('modal-finalizar-admin').style.display='none'"
                    class="w-full mt-2 py-2.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                Cancelar
            </button>
        </div>
    </div>
    @endif

    @if($reservasPendientesAdmin->isNotEmpty())
        <div class="mb-6">
            <x-tabla-reservas-admin-pendientes
                :reservas="$reservasPendientesAdmin"
            />
        </div>
    @endif

    @if($viajesEnCurso->isNotEmpty())
        <div class="mb-6">
            <x-tabla-viajes-en-curso-admin
                :viajes="$viajesEnCurso"
            />
        </div>
    @endif

    {{-- Historial de viajes (incluye finalizados, cancelados, en curso) --}}
    @if($viajes->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 mb-4">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Historial de viajes</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $viajes->total() }}</span> registros
                </p>
            </div>
        </div>
        <div class="p-5">
            <div class="hidden md:block">
                <x-tabla-viajes-desktop
                    :viajes="$viajes"
                    :ubicacion="null"
                    :mostrarAcciones="true"
                />
            </div>
            <div class="block md:hidden">
                <x-lista-viajes-mobile
                    :viajes="$viajes"
                    :ubicacion="null"
                    :mostrarAcciones="true"
                />
            </div>
        </div>
    </div>

    @if($viajes->hasPages())
    <div class="flex justify-center mt-4 mb-2">
        {{ $viajes->links('vendor.pagination.simple-pagination') }}
    </div>
    @endif
    @endif

    {{-- Mensaje vacío solo si no hay absolutamente nada --}}
    @if((!isset($viajePropio) || !$viajePropio) && $reservasPendientesAdmin->isEmpty() && $viajesEnCurso->isEmpty() && $viajes->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-gray-400 dark:text-gray-600">
            <i class="fas fa-route text-5xl mb-4 opacity-30"></i>
            <p class="text-base">No hay viajes registrados</p>
        </div>
    @endif

@endhasanyrole

{{-- Operativos --}}
@hasrole('Operativo')
    @if($viajes->isEmpty() )
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
 @endhasrole

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
