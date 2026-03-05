{{-- resources/views/components/filtros-viajes-fields.blade.php
     Props:
       $vehiculos_filtros  → Collection<Vehiculo>
       $estados_filtros    → Collection<EstadoViaje>
       $ubicacion          → string|null
--}}

@props([
    'vehiculos_filtros',
    'estados_filtros',
    'ubicacion' => null,
])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-4 shadow-sm">

  {{-- Cabecera --}}
  <div class="flex items-center justify-between mb-4">
    <span class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 flex items-center gap-2">
      <i class="fas fa-sliders"></i> Buscar
    </span>
    <span
      id="vfActiveIndicator"
      style="display:none;"
      class="inline-flex items-center gap-1.5 text-xs font-semibold
             text-blue-600 dark:text-blue-400
             bg-blue-50 dark:bg-blue-900/30
             border border-blue-200 dark:border-blue-700
             px-2.5 py-0.5 rounded-full">
      <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 inline-block"></span>
      <span id="vfActiveCount">0</span> activos
    </span>
  </div>

  <form
    action="/filtrar-reservas"
    data-busqueda="{{ $ubicacion }}"
    method="GET"
    id="formFiltrosViajes"
    onchange="vfCountActive()">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 items-end">

      {{-- Buscar --}}
      <div class="flex flex-col gap-1.5">
        <label for="vf-nombre"
               class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
          <i class="fas fa-search mr-1 opacity-60"></i> Buscar
        </label>
        <input
          type="text"
          name="nombre"
          id="vf-nombre"
          placeholder="Conductor u oficina…"
          value="{{ request('nombre') }}"
          class="w-full px-3 py-2 text-sm rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 transition">
      </div>

      {{-- Fecha inicio --}}
      <div class="flex flex-col gap-1.5">
        <label for="vf-fecha-inicio"
               class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
          <i class="fas fa-calendar-alt mr-1 opacity-60"></i> Fecha inicio
        </label>
        <input
          type="date"
          name="fecha_inicio"
          id="vf-fecha-inicio"
          value="{{ request('fecha_inicio') }}"
          class="w-full px-3 py-2 text-sm rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 transition">
      </div>

      {{-- Fecha fin --}}
      <div class="flex flex-col gap-1.5">
        <label for="vf-fecha-fin"
               class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
          <i class="fas fa-calendar-alt mr-1 opacity-60"></i> Fecha fin
        </label>
        <input
          type="date"
          name="fecha_fin"
          id="vf-fecha-fin"
          value="{{ request('fecha_fin') }}"
          class="w-full px-3 py-2 text-sm rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 transition">
      </div>

      {{-- Estado --}}
      <div class="flex flex-col gap-1.5">
        <label for="vf-estado"
               class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
          <i class="fas fa-circle-half-stroke mr-1 opacity-60"></i> Estado
        </label>
        <select
          name="estado"
          id="vf-estado"
          class="w-full px-3 py-2 text-sm rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 transition">
          <option value="default">Todos los estados</option>
          @foreach ($estados_filtros as $estado)
            <option
              value="{{ $estado->id }}"
              {{ request('estado') == $estado->id ? 'selected' : '' }}>
              {{ $estado->estado }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Vehículo --}}
      <div class="flex flex-col gap-1.5">
        <label for="vf-vehiculo"
               class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
          <i class="fas fa-car mr-1 opacity-60"></i> Vehículo
        </label>
        <select
          name="vehiculo"
          id="vf-vehiculo"
          class="w-full px-3 py-2 text-sm rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 transition">
          <option value="default">Todos los vehículos</option>
          @foreach ($vehiculos_filtros as $vehiculo)
            <option
              value="{{ $vehiculo->id }}"
              {{ request('vehiculo') == $vehiculo->id ? 'selected' : '' }}>
              {{ $vehiculo->dominio }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Botones --}}
      <div class="flex flex-col sm:flex-row gap-2">
        <button
          type="submit"
          class="flex-1 flex items-center justify-center gap-2
                 px-4 py-2 text-sm font-semibold rounded-lg
                 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                 text-white transition">
          <i class="fas fa-filter text-xs"></i> Filtrar
        </button>
        <button
          type="button"
          onclick="vfLimpiar()"
          class="flex-1 flex items-center justify-center gap-2
                 px-4 py-2 text-sm font-semibold rounded-lg border
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-700
                 text-gray-600 dark:text-gray-300
                 hover:bg-gray-100 dark:hover:bg-gray-600
                 transition">
          <i class="fas fa-times text-xs"></i> Limpiar
        </button>
      </div>

    </div>
  </form>
</div>

<script>
(function () {
  window.vfCountActive = function () {
    const fields  = ['vf-nombre', 'vf-fecha-inicio', 'vf-fecha-fin'];
    const selects = ['vf-estado', 'vf-vehiculo'];
    let count = 0;

    fields.forEach(id => {
      const el = document.getElementById(id);
      if (el && el.value.trim() !== '') count++;
    });
    selects.forEach(id => {
      const el = document.getElementById(id);
      if (el && el.value !== 'default') count++;
    });

    const indicator = document.getElementById('vfActiveIndicator');
    const countEl   = document.getElementById('vfActiveCount');
    if (indicator && countEl) {
      countEl.textContent = count;
      indicator.style.display = count > 0 ? 'inline-flex' : 'none';
    }
  };

  window.vfLimpiar = function () {
    const form = document.getElementById('formFiltrosViajes');
    if (!form) return;
    form.querySelectorAll('input').forEach(i => i.value = '');
    form.querySelectorAll('select').forEach(s => s.value = 'default');
    vfCountActive();
    window.location.href = window.location.pathname;
  };

  document.addEventListener('DOMContentLoaded', vfCountActive);
})();
</script>
