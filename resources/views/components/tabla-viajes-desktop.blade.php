{{-- ============================================================
     COMPONENTE: tabla-viajes-desktop
     Props:
       $viajes          → Collection<Viaje>
       $configEditar    → ['can' => '...', 'route' => '...'] | null
       $ids             → array|null
       $ubicacion       → string|null
       $mostrarAcciones → bool
     ============================================================ --}}
     @props([
         'viajes',
         'configEditar' => null,
         'ids'          => null,
         'ubicacion'    => null,
         'mostrarAcciones',
         ])


@vite('resources/css/viaje-admin.css')
<div class="vt-wrap" id="viajes-wrapper" data-view="tabla">

  {{-- ── Chips de filtro rápido ── --}}
  <div class="vt-chips" id="vtChips">
    <button class="vt-chip active" data-estado="todos" onclick="vtFilter(this)">
      Todos
    </button>
    <button class="vt-chip c-green" data-estado="EN_CURSO" onclick="vtFilter(this)">
      <span class="chip-dot"></span> En curso
    </button>
  {{--    <button class="vt-chip c-amber" data-estado="PAUSADO" onclick="vtFilter(this)">
      <span class="chip-dot"></span> Pausado
    </button>--}}
    <button class="vt-chip" data-estado="APROBADA" onclick="vtFilter(this)">
      Aprobados
    </button>
    <button class="vt-chip c-gray" data-estado="FINALIZADO" onclick="vtFilter(this)">
      Finalizados
    </button>
    <button class="vt-chip c-red" data-estado="CANCELADO" onclick="vtFilter(this)">
      Cancelados
    </button>
  </div>

  {{-- ── Tabla ── --}}
  <div class="vt-table-wrap">



    <div class="overflow-x-auto">
      <table class="vt-table" id="vtTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Vehículo</th>
            <th>Conductor</th>
            <th>KM recorridos</th>
            <th>Fecha reserva</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estado</th>
            @if($ubicacion)
              <th>Ubicación</th>
            @endif
            <th style="text-align:right;">Acciones</th>
          </tr>
        </thead>

        <tbody id="vtBody">
          @forelse ($viajes as $viaje)
          @php
           $estadoRaw = strtoupper($viaje->estado_viaje ?? '');
            $badgeClass = match($estadoRaw) {
                'EN_CURSO'   => 'en-curso',
                'FINALIZADO' => 'finalizado',
                'CANCELADO'  => 'cancelado',
               // 'PAUSADO'    => 'pausado',
                'APROBADA'   => 'aprobada',
                default      => 'aprobada',
            };
            $estadoLabel = match($estadoRaw) {
                'EN_CURSO'   => 'En curso',
                'FINALIZADO' => 'Finalizado',
                'CANCELADO'  => 'Cancelado',
               // 'PAUSADO'    => 'Pausado',
                'APROBADA'   => 'Aprobado',
                default      => $estadoRaw,
            };
            $nombreCompleto = $viaje->reserva?->usuario?->name ?? '—';
            $initials = collect(explode(' ', $nombreCompleto))
                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                ->take(2)->implode('');
            $kmRecorridos = ($viaje->kilometros_fin && $viaje->kilometros_inicio)
                ? ($viaje->kilometros_fin - $viaje->kilometros_inicio)
                : null;
          @endphp

          <tr
            class="vt-row"
            data-estado="{{ $estadoRaw }}"
            data-search="{{ strtolower($viaje->vehiculo?->dominio . ' ' . $nombreCompleto . ' ' . $viaje->id) }}">

            {{-- ID --}}
            <td><span class="vt-id">#{{ $viaje->id }}</span></td>

            {{-- Vehículo --}}
            <td>
              <div style="display:flex; flex-direction:column; gap:2px;">
                <span class="vt-dominio">{{ $viaje->vehiculo?->dominio ?? '—' }}</span>
                <span style="font-size:11px; color:var(--vt-muted);">{{ $viaje->vehiculo?->marca }} {{ $viaje->vehiculo?->modelo }}</span>
              </div>
            </td>

            {{-- Conductor --}}
            <td>
              <div class="vt-user">
                <div class="vt-avatar">{{ $initials ?: '?' }}</div>
                <span>{{ $nombreCompleto }}</span>
              </div>
            </td>

            {{-- KM --}}
            <td>
              @if($kmRecorridos !== null)
                <span class="vt-km">+{{ number_format($kmRecorridos, 0, ',', '.') }} km</span>
              @else
                <span style="color:var(--vt-muted2); font-size:11px;">—</span>
              @endif
            </td>

            {{-- Fecha reserva --}}
            <td>
              <span class="vt-date">
                {{ $viaje->reserva?->created_at?->format('d/m/Y') ?? '—' }}
              </span>
            </td>

            {{-- Inicio --}}
            <td>
              <span class="vt-date">
                {{ $viaje->fecha_inicio?->format('d/m H:i') ?? '—' }}
              </span>
            </td>

            {{-- Fin --}}
            <td>
              <span class="vt-date">
                {{ $viaje->fecha_fin?->format('d/m H:i') ?? '—' }}
              </span>
            </td>

            {{-- Estado --}}
            <td>
              <span class="vt-badge {{ $badgeClass }}">
                <span class="bd"></span>
                {{ $estadoLabel }}
              </span>
            </td>

            {{-- Ubicación (opcional) --}}
            @if($ubicacion)
            <td>
              <span style="font-size:12px; color:var(--vt-muted);">
                {{ $viaje->ubicacion?->descripcion ?? $viaje->id_ultima_ubicacion ?? '—' }}
              </span>
            </td>
            @endif

            {{-- Acciones --}}
            <td>
              <div class="vt-actions">

                {{-- Ver detalle --}}
                @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
                <a href="{{ route('viajes.show', $viaje->id) }}"
                   class="vt-btn b-blue"
                   title="Ver detalle">
                  <i class="fas fa-eye"></i>
                </a>
                @endcanany

  @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
       {{-- ver mapa --}}
<a href="{{ route('viajes.mapa', $viaje->id) }}"
   class="vt-btn b-green" title="Ver mapa">
    <i class="fas fa-map"></i>
</a>
 @endcanany
                {{-- Editar (si config disponible y estado permite) --}}
                @if($configEditar && ($ids === null || in_array($viaje->id, $ids ?? [])))
                  @can($configEditar['can'])      {{-- pausado --}}
                    @if(!in_array($estadoRaw, ['CANCELADO','FINALIZADO']))
                    <a href="{{ $configEditar['route'] }}"
                       data-id="{{ $viaje->id }}"
                       class="vt-btn b-yellow btn-editar"
                       title="Editar">
                      <i class="fas fa-pen"></i>
                    </a>
                    @endif
                  @endcan
                @endif

                @if($mostrarAcciones)


                  {{-- Iniciar viaje (solo Operativo con reserva aprobada) --}}
                  @role('Operativo')
                    @if($viaje->reserva?->estado_reserva?->estado === 'APROBADA')
                    <form method="POST"
                          action="{{ route('operativo.viajes.comenzar', $viaje->reserva->id) }}"
                          class="inline">
                      @csrf
                      <button type="submit"
                              class="vt-btn b-green"
                              title="Iniciar viaje">
                        <i class="fas fa-play"></i>
                      </button>
                    </form>
                    @endif
                  @endrole

                  {{-- Finalizar viaje en curso --}}
                  @if($estadoRaw === 'EN_CURSO')
                  <button
                    type="button"
                    class="vt-btn b-amber btn-finalizar-viaje"
                    data-viaje-id="{{ $viaje->id }}"
                    title="Finalizar viaje">
                    <i class="fas fa-flag-checkered"></i>
                  </button>
                  @endif

                  {{-- Aprobar reserva --}}
                  @canany(['autorizar_reservas_internas'])
                    @if(!in_array($viaje->reserva?->estado_reserva?->estado, ['APROBADA','CANCELADA','RECHAZADA','FINALIZADA','EN_CURSO']))
                    <button
                      type="button"
                      data-id="{{ $viaje->reserva?->id }}"
                      class="vt-btn b-green btn-aprobar-reserva"
                      title="Aprobar reserva">
                      <i class="fa-solid fa-circle-check"></i>
                    </button>
                    @endif
                  @endcanany

                  {{-- Cancelar --}}
                  @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
                    @if(!in_array($viaje->reserva?->estado_reserva?->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                    <button
                      type="button"
                      command="show-modal"
                      commandfor="dialog-cancelar"
                      data-id="{{ $viaje->reserva?->id }}"
                      class="vt-btn b-red btn-cancelar"
                      title="Cancelar viaje">
                      <i class="fas fa-times"></i>
                    </button>
                    @endif
                  @endcanany

                @else
                  {{-- Columna alternativa (préstamos) --}}
                  @can('ver_solicitudes_prestamos')
                  <a href="{{ route('reservas.reserva', $viaje->reserva?->id) }}"
                     class="vt-btn b-blue"
                     title="Ver detalle">
                    <i class="fas fa-eye"></i>
                  </a>
                  @endcan

                  @can('autorizar_prestamos')
                  <button type="button"
                          command="show-modal"
                          commandfor="dialog-autorizar"
                          data-id="{{ $viaje->reserva?->id }}"
                          class="vt-btn b-green btn-autorizar"
                          title="Autorizar préstamo">
                    <i class="fa-solid fa-circle-check"></i>
                  </button>
                  @endcan

                  @can('rechazar_prestamos')
                  <button type="button"
                          command="show-modal"
                          commandfor="dialog-rechazar"
                          data-id="{{ $viaje->reserva?->id }}"
                          class="vt-btn b-red btn-rechazar"
                          title="Rechazar préstamo">
                    <i class="fas fa-times"></i>
                  </button>
                  @endcan
                @endif

              </div>
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="10">
              <div class="vt-empty">
                <i class="fas fa-route"></i>
                <p>No hay viajes registrados</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>{{-- /vt-table-wrap --}}
</div>{{-- /vt-wrap --}}

<script>
(function () {
  let filtroActivo = 'todos';
  let busqueda     = '';

  function vtApply() {
    const rows  = document.querySelectorAll('#vtBody .vt-row');
    let visible = 0;

    rows.forEach(row => {
      const estado     = row.dataset.estado   ?? '';
      const searchData = row.dataset.search   ?? '';
      const matchE = filtroActivo === 'todos' || estado === filtroActivo;
      const matchB = !busqueda    || searchData.includes(busqueda);
      const show   = matchE && matchB;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    const countEl = document.getElementById('vtCount');
    if (countEl) countEl.textContent = visible + ' viaje' + (visible !== 1 ? 's' : '');
  }

  window.vtFilter = function (btn) {
    document.querySelectorAll('.vt-chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    filtroActivo = btn.dataset.estado;
    vtApply();
  };

  window.vtSearch = function (val) {
    busqueda = val.toLowerCase().trim();
    vtApply();
  };

  // Delegar click en botones "finalizar viaje" para abrir modal si existe
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-finalizar-viaje');
    if (!btn) return;
    const id = btn.dataset.viajeId;
    // Si la página tiene el modal de finalizar del partial de operativo, lo abre;
    // si no, redirige al show del viaje
    const modal = document.getElementById('vms-modal-finalizar');
    if (modal) {
      modal.classList.add('open');
      // Actualiza el action del form si existe
      const form = document.getElementById('formFinalizarViaje');
      if (form) {
        form.action = form.action.replace(/\/\d+\/finalizar/, '/' + id + '/finalizar');
      }
    } else {
      window.location = '/viajes/' + id;
    }
  });
})();
</script>
