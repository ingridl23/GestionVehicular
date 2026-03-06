{{-- ============================================================
     COMPONENTE: tabla-viajes-admin
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

<style>
  /* ── Variables ── */
  .vt-wrap * { box-sizing: border-box; }

  .vt-wrap {
    --vt-bg:       #0d0f14;
    --vt-surface:  #141720;
    --vt-card:     #1a1e2a;
    --vt-border:   #252a38;
    --vt-border2:  #2e3447;
    --vt-text:     #e8eaf0;
    --vt-muted:    #7a8099;
    --vt-muted2:   #4a5068;
    --vt-accent:   #3b82f6;
    --vt-accent2:  #60a5fa;
    --vt-green:    #22c55e;
    --vt-amber:    #f59e0b;
    --vt-red:      #ef4444;
    --vt-purple:   #a78bfa;
    --vt-font:     'Sora', sans-serif;
    --vt-mono:     'DM Mono', monospace;

    font-family: var(--vt-font);
    color: var(--vt-text);
  }

  /* ── Chips de filtro rápido ── */
  .vt-chips {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 12px;
  }
  .vt-chip {
    padding: 5px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--vt-border);
    background: none;
    color: var(--vt-muted);
    cursor: pointer;
    transition: all .12s;
    font-family: var(--vt-font);
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .vt-chip:hover { border-color: var(--vt-border2); color: var(--vt-text); }
  .vt-chip.active                { background: var(--vt-accent);  border-color: var(--vt-accent);  color: #fff; }
  .vt-chip.c-green.active        { background: rgba(34,197,94,.15); border-color: var(--vt-green);  color: var(--vt-green); }
  .vt-chip.c-amber.active        { background: rgba(245,158,11,.12); border-color: var(--vt-amber); color: var(--vt-amber); }
  .vt-chip.c-red.active          { background: rgba(239,68,68,.12);  border-color: var(--vt-red);   color: var(--vt-red); }
  .vt-chip.c-gray.active         { background: rgba(100,116,139,.1); border-color: #64748b;          color: #94a3b8; }
  .vt-chip .chip-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    display: inline-block;
  }
  .c-green .chip-dot { background: var(--vt-green); }
  .c-amber .chip-dot { background: var(--vt-amber); }
  .c-red   .chip-dot { background: var(--vt-red);   }

  /* ── Barra superior de la tabla ── */
  .vt-toolbar {
    padding: 12px 16px;
    border-bottom: 1px solid var(--vt-border);
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,.01);
  }
  .vt-search {
    flex: 1;
    background: var(--vt-bg);
    border: 1px solid var(--vt-border);
    border-radius: 8px;
    padding: 7px 12px 7px 34px;
    color: var(--vt-text);
    font-family: var(--vt-font);
    font-size: 13px;
    outline: none;
    transition: border .15s;
  }
  .vt-search:focus { border-color: var(--vt-accent); }
  .vt-search::placeholder { color: var(--vt-muted2); }
  .vt-search-wrap { position: relative; flex: 1; }
  .vt-search-icon {
    position: absolute;
    left: 10px; top: 50%;
    transform: translateY(-50%);
    color: var(--vt-muted2);
    font-size: 12px;
    pointer-events: none;
  }

  /* ── Wrapper tabla ── */
  .vt-table-wrap {
    background: var(--vt-card);
    border: 1px solid var(--vt-border);
    border-radius: 14px;
    overflow: hidden;
  }

  /* ── Tabla ── */
  .vt-table { width: 100%; border-collapse: collapse; }

  .vt-table thead th {
    padding: 10px 14px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--vt-muted2);
    background: rgba(255,255,255,.02);
    border-bottom: 1px solid var(--vt-border);
    white-space: nowrap;
    font-family: var(--vt-font);
  }

  .vt-table tbody tr {
    border-bottom: 1px solid var(--vt-border);
    transition: background .1s;
  }
  .vt-table tbody tr:last-child { border-bottom: none; }
  .vt-table tbody tr:hover { background: rgba(255,255,255,.025); }

  .vt-table tbody td {
    padding: 11px 14px;
    font-size: 13px;
    color: var(--vt-text);
    white-space: nowrap;
    vertical-align: middle;
  }

  /* ── Celda ID ── */
  .vt-id {
    font-family: var(--vt-mono);
    color: var(--vt-muted);
    font-size: 12px;
  }

  /* ── Dominio ── */
  .vt-dominio {
    font-family: var(--vt-mono);
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 1.5px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--vt-border);
    padding: 2px 8px;
    border-radius: 5px;
  }

  /* ── Avatar + nombre ── */
  .vt-user { display: flex; align-items: center; gap: 8px; }
  .vt-avatar {
    width: 27px; height: 27px;
    border-radius: 6px;
    background: linear-gradient(135deg, var(--vt-accent) 0%, var(--vt-purple) 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; color: #fff;
    flex-shrink: 0;
    font-family: var(--vt-font);
  }

  /* ── Fecha ── */
  .vt-date {
    font-family: var(--vt-mono);
    font-size: 11px;
    color: var(--vt-muted);
  }

  /* ── KM ── */
  .vt-km {
    font-family: var(--vt-mono);
    font-size: 12px;
  }

  /* ── Badges de estado ── */
  .vt-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 6px;
    white-space: nowrap;
  }
  .vt-badge .bd { width: 5px; height: 5px; border-radius: 50%; }

  .vt-badge.en-curso   { background: rgba(34,197,94,.12);  color: #22c55e; }
  .vt-badge.en-curso .bd { background: #22c55e; animation: vt-pulse 1.5s infinite; }
  .vt-badge.finalizado { background: rgba(100,116,139,.1); color: #64748b; }
  .vt-badge.finalizado .bd { background: #64748b; }
  .vt-badge.cancelado  { background: rgba(239,68,68,.1);   color: #ef4444; }
  .vt-badge.cancelado .bd { background: #ef4444; }
  .vt-badge.pausado    { background: rgba(245,158,11,.1);  color: #f59e0b; }
  .vt-badge.pausado .bd { background: #f59e0b; }
  .vt-badge.aprobada   { background: rgba(59,130,246,.12); color: #60a5fa; }
  .vt-badge.aprobada .bd { background: #60a5fa; }

  @keyframes vt-pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.5); }
  }

  /* ── Botones de acción en tabla ── */
  .vt-actions { display: flex; align-items: center; gap: 4px; justify-content: flex-end; }

  .vt-btn {
    width: 30px; height: 30px;
    border-radius: 7px;
    border: 1px solid var(--vt-border);
    background: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: var(--vt-muted);
    transition: all .12s;
    text-decoration: none;
    font-family: var(--vt-font);
  }
  .vt-btn:hover {
    border-color: var(--vt-border2);
    color: var(--vt-text);
    background: rgba(255,255,255,.04);
  }
  .vt-btn.b-blue  { color: var(--vt-accent2); border-color: rgba(59,130,246,.3); background: rgba(59,130,246,.05); }
  .vt-btn.b-blue:hover { background: rgba(59,130,246,.12); }
  .vt-btn.b-green:hover { border-color: var(--vt-green); color: var(--vt-green); background: rgba(34,197,94,.08); }
  .vt-btn.b-amber:hover { border-color: var(--vt-amber); color: var(--vt-amber); background: rgba(245,158,11,.08); }
  .vt-btn.b-red:hover   { border-color: var(--vt-red);   color: var(--vt-red);   background: rgba(239,68,68,.08); }
  .vt-btn.b-yellow:hover { border-color: #eab308; color: #eab308; background: rgba(234,179,8,.08); }

  /* ── Empty state ── */
  .vt-empty {
    text-align: center;
    padding: 48px 24px;
    color: var(--vt-muted2);
  }
  .vt-empty i { font-size: 32px; margin-bottom: 10px; opacity: .35; display: block; }
  .vt-empty p { font-size: 13px; }

  /* ── Contador de filas ── */
  .vt-count-badge {
    background: rgba(59,130,246,.12);
    color: var(--vt-accent2);
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    font-family: var(--vt-mono);
    white-space: nowrap;
  }
</style>

<div class="vt-wrap" id="viajes-wrapper" data-view="tabla">

  {{-- ── Chips de filtro rápido ── --}}
  <div class="vt-chips" id="vtChips">
    <button class="vt-chip active" data-estado="todos" onclick="vtFilter(this)">
      Todos
    </button>
    <button class="vt-chip c-green" data-estado="EN_CURSO" onclick="vtFilter(this)">
      <span class="chip-dot"></span> En curso
    </button>
    <button class="vt-chip c-amber" data-estado="PAUSADO" onclick="vtFilter(this)">
      <span class="chip-dot"></span> Pausado
    </button>
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

    {{-- Barra superior: búsqueda + contador --}}
    <div class="vt-toolbar">
      <div class="vt-search-wrap">
        <i class="fas fa-search vt-search-icon"></i>
        <input
          type="text"
          class="vt-search"
          placeholder="Buscar por vehículo, conductor, ID…"
          oninput="vtSearch(this.value)"
          id="vtSearchInput">
      </div>
      <span class="vt-count-badge" id="vtCount">
        {{ $viajes->count() }} viaje{{ $viajes->count() !== 1 ? 's' : '' }}
      </span>
    </div>

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
                'PAUSADO'    => 'pausado',
                'APROBADA'   => 'aprobada',
                default      => 'aprobada',
            };
            $estadoLabel = match($estadoRaw) {
                'EN_CURSO'   => 'En curso',
                'FINALIZADO' => 'Finalizado',
                'CANCELADO'  => 'Cancelado',
                'PAUSADO'    => 'Pausado',
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

                {{-- Editar (si config disponible y estado permite) --}}
                @if($configEditar && ($ids === null || in_array($viaje->id, $ids ?? [])))
                  @can($configEditar['can'])
                    @if(!in_array($estadoRaw, ['CANCELADO', 'PAUSADO', 'FINALIZADO']))
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
