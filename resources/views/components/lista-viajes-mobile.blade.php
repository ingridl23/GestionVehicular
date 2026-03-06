{{-- resources/views/components/lista-viajes-mobile.blade.php
     Props:
       $viajes          → Collection<Viaje>
       $configEditar    → array|null
       $ids             → array|null
       $ubicacion       → string|null
       $mostrarAcciones → bool
--}}

@props([
    'viajes',
    'configEditar'    => null,
    'ids'             => null,
    'ubicacion'       => null,
    'mostrarAcciones' => true,
])

<style>
  .vm-list { display: flex; flex-direction: column; gap: 10px; }

  /* Tarjeta mobile */
  .vm-card {
    background: #1a1e2a;
    border: 1px solid #252a38;
    border-radius: 14px;
    overflow: hidden;
    font-family: 'Sora', sans-serif;
    transition: border-color .15s;
  }
  .vm-card:hover { border-color: #2e3447; }

  /* Franja superior con estado */
  .vm-card-top {
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #252a38;
  }
  .vm-card-top.en-curso   { background: rgba(34,197,94,.08);  border-bottom-color: rgba(34,197,94,.15); }
  .vm-card-top.finalizado { background: rgba(100,116,139,.05); }
  .vm-card-top.cancelado  { background: rgba(239,68,68,.06);  border-bottom-color: rgba(239,68,68,.12); }
  .vm-card-top.pausado    { background: rgba(245,158,11,.06);  border-bottom-color: rgba(245,158,11,.12); }
  .vm-card-top.aprobada   { background: rgba(59,130,246,.08);  border-bottom-color: rgba(59,130,246,.15); }

  .vm-id {
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    color: #7a8099;
  }

  /* Badge de estado */
  .vm-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    padding: 3px 9px; border-radius: 6px;
  }
  .vm-badge .dot { width: 5px; height: 5px; border-radius: 50%; }
  .vm-badge.en-curso   { background: rgba(34,197,94,.15); color: #22c55e; }
  .vm-badge.en-curso .dot { background: #22c55e; animation: vm-pulse 1.5s infinite; }
  .vm-badge.finalizado { background: rgba(100,116,139,.1); color: #64748b; }
  .vm-badge.finalizado .dot { background: #64748b; }
  .vm-badge.cancelado  { background: rgba(239,68,68,.1);  color: #ef4444; }
  .vm-badge.cancelado .dot { background: #ef4444; }
  .vm-badge.pausado    { background: rgba(245,158,11,.1); color: #f59e0b; }
  .vm-badge.pausado .dot { background: #f59e0b; }
  .vm-badge.aprobada   { background: rgba(59,130,246,.12); color: #60a5fa; }
  .vm-badge.aprobada .dot { background: #60a5fa; }

  @keyframes vm-pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.5); }
  }

  /* Cuerpo de la tarjeta */
  .vm-card-body { padding: 14px; }

  .vm-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
  }
  .vm-field label {
    display: block;
    font-size: 10px; font-weight: 700;
    letter-spacing: .7px; text-transform: uppercase;
    color: #4a5068; margin-bottom: 3px;
  }
  .vm-field span {
    font-size: 13px; font-weight: 600; color: #e8eaf0;
  }
  .vm-field.full { grid-column: 1 / -1; }

  .vm-dominio {
    font-family: 'DM Mono', monospace;
    background: rgba(255,255,255,.05);
    border: 1px solid #252a38;
    padding: 2px 8px; border-radius: 5px;
    font-size: 13px; letter-spacing: 1.5px;
  }
  .vm-date {
    font-family: 'DM Mono', monospace;
    font-size: 12px; color: #7a8099;
  }
  .vm-km {
    font-family: 'DM Mono', monospace;
    font-size: 13px; color: #e8eaf0;
  }

  /* Conductor */
  .vm-conductor {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 14px;
    padding: 10px 12px;
    background: rgba(255,255,255,.02);
    border: 1px solid #252a38;
    border-radius: 9px;
  }
  .vm-avatar {
    width: 30px; height: 30px; border-radius: 7px;
    background: linear-gradient(135deg, #3b82f6 0%, #a78bfa 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    flex-shrink: 0; font-family: 'Sora', sans-serif;
  }
  .vm-conductor-name { font-size: 13px; font-weight: 600; color: #e8eaf0; }
  .vm-conductor-sub  { font-size: 11px; color: #7a8099; }

  /* Acciones */
  .vm-actions {
    display: flex; gap: 6px;
    padding-top: 12px;
    border-top: 1px solid #252a38;
    flex-wrap: wrap;
  }
  .vm-btn {
    flex: 1; min-width: 0;
    padding: 9px 8px;
    border-radius: 8px;
    border: 1px solid #252a38;
    background: none;
    cursor: pointer;
    font-family: 'Sora', sans-serif;
    font-size: 12px; font-weight: 600;
    color: #7a8099;
    display: flex; align-items: center; justify-content: center; gap: 5px;
    transition: all .12s;
    text-decoration: none;
    white-space: nowrap;
  }
  .vm-btn:hover { border-color: #2e3447; color: #e8eaf0; background: rgba(255,255,255,.03); }
  .vm-btn.b-blue  { color: #60a5fa; border-color: rgba(59,130,246,.3); background: rgba(59,130,246,.05); }
  .vm-btn.b-blue:hover  { background: rgba(59,130,246,.12); }
  .vm-btn.b-green:hover { border-color: #22c55e; color: #22c55e; background: rgba(34,197,94,.08); }
  .vm-btn.b-amber:hover { border-color: #f59e0b; color: #f59e0b; background: rgba(245,158,11,.08); }
  .vm-btn.b-red:hover   { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,.08); }
  .vm-btn.b-yellow:hover { border-color: #eab308; color: #eab308; background: rgba(234,179,8,.08); }
</style>

<div class="vm-list">
  @forelse ($viajes as $viaje)
  @php
   $estadoRaw = strtoupper($viaje->estado_viaje ?? '');
    $badgeClass = match($estadoRaw) {
        'EN_CURSO'   => 'en-curso',
        'FINALIZADO' => 'finalizado',
        'CANCELADO'  => 'cancelado',
        'PAUSADO'    => 'pausado',
        default      => 'aprobada',
    };
    $estadoLabel = match($estadoRaw) {
        'EN_CURSO'   => 'En curso',
        'FINALIZADO' => 'Finalizado',
        'CANCELADO'  => 'Cancelado',
        'PAUSADO'    => 'Pausado',
        default      => 'Aprobado',
    };
    $nombreCompleto = $viaje->reserva?->usuario?->name ?? '—';
    $initials = collect(explode(' ', $nombreCompleto))
        ->map(fn($p) => strtoupper(substr($p, 0, 1)))
        ->take(2)->implode('');
    $kmRecorridos = ($viaje->kilometros_fin && $viaje->kilometros_inicio)
        ? ($viaje->kilometros_fin - $viaje->kilometros_inicio)
        : null;
  @endphp

  <div class="vm-card">

    {{-- Top: ID + estado --}}
    <div class="vm-card-top {{ $badgeClass }}">
      <span class="vm-id">#{{ $viaje->id }}</span>
      <span class="vm-badge {{ $badgeClass }}">
        <span class="dot"></span> {{ $estadoLabel }}
      </span>
    </div>

    <div class="vm-card-body">

      {{-- Conductor --}}
      <div class="vm-conductor">
        <div class="vm-avatar">{{ $initials ?: '?' }}</div>
        <div>
          <div class="vm-conductor-name">{{ $nombreCompleto }}</div>
          <div class="vm-conductor-sub">Conductor asignado</div>
        </div>
      </div>

      {{-- Datos del viaje --}}
      <div class="vm-grid">
        <div class="vm-field">
          <label>Vehículo</label>
          <span class="vm-dominio">{{ $viaje->vehiculo?->dominio ?? '—' }}</span>
        </div>
        <div class="vm-field">
          <label>KM recorridos</label>
          @if($kmRecorridos !== null)
            <span class="vm-km">+{{ number_format($kmRecorridos, 0, ',', '.') }} km</span>
          @else
            <span style="color:#4a5068; font-size:12px;">—</span>
          @endif
        </div>
        <div class="vm-field">
          <label>Inicio</label>
          <span class="vm-date">{{ $viaje->fecha_inicio?->format('d/m H:i') ?? '—' }}</span>
        </div>
        <div class="vm-field">
          <label>Fin</label>
          <span class="vm-date">{{ $viaje->fecha_fin?->format('d/m H:i') ?? '—' }}</span>
        </div>
        @if($ubicacion && $viaje->ubicacion)
        <div class="vm-field full">
          <label>Última ubicación</label>
          <span style="font-size:12px;">{{ $viaje->ubicacion?->descripcion ?? '—' }}</span>
        </div>
        @endif
      </div>

      {{-- Acciones --}}
      <div class="vm-actions">

        {{-- Ver detalle --}}
        @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
        <a href="{{ route('viajes.show', $viaje->id) }}" class="vm-btn b-blue" title="Ver detalle">
          <i class="fas fa-eye"></i> Ver
        </a>
        @endcanany

        @if($mostrarAcciones)

          {{-- Editar --}}
          @if($configEditar && ($ids === null || in_array($viaje->id, $ids ?? [])))
            @can($configEditar['can'])
              @if(!in_array($estadoRaw, ['CANCELADO','PAUSADO','FINALIZADO']))
              <a href="{{ $configEditar['route'] }}"
                 data-id="{{ $viaje->id }}"
                 class="vm-btn b-yellow btn-editar">
                <i class="fas fa-pen"></i> Editar
              </a>
              @endif
            @endcan
          @endif

          {{-- Iniciar viaje (Operativo) --}}
          @role('Operativo')
            @if($viaje->reserva?->estado_reserva?->estado === 'APROBADA')
            <form method="POST"
                  action="{{ route('operativo.viajes.comenzar', $viaje->reserva->id) }}"
                  class="flex-1">
              @csrf
              <button type="submit" class="vm-btn b-green w-full">
                <i class="fas fa-play"></i> Iniciar
              </button>
            </form>
            @endif
          @endrole

          {{-- Finalizar --}}
          @if($estadoRaw === 'EN_CURSO')
          <button type="button"
                  class="vm-btn b-amber btn-finalizar-viaje"
                  data-viaje-id="{{ $viaje->id }}">
            <i class="fas fa-flag-checkered"></i> Finalizar
          </button>
          @endif

          {{-- Aprobar --}}
          @canany(['autorizar_reservas_internas'])
            @if(!in_array($viaje->reserva?->estado_reserva?->estado, ['APROBADA','CANCELADA','RECHAZADA','FINALIZADA','EN_CURSO']))
            <button type="button"
                    data-id="{{ $viaje->reserva?->id }}"
                    class="vm-btn b-green btn-aprobar-reserva">
              <i class="fa-solid fa-circle-check"></i> Aprobar
            </button>
            @endif
          @endcanany

          {{-- Cancelar --}}
          @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
            @if(!in_array($viaje->reserva?->estado_reserva?->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
            <button type="button"
                    command="show-modal"
                    commandfor="dialog-cancelar"
                    data-id="{{ $viaje->reserva?->id }}"
                    class="vm-btn b-red btn-cancelar">
              <i class="fas fa-times"></i> Cancelar
            </button>
            @endif
          @endcanany

        @else

          {{-- Columna préstamos --}}
          @can('autorizar_prestamos')
          <button type="button"
                  command="show-modal" commandfor="dialog-autorizar"
                  data-id="{{ $viaje->reserva?->id }}"
                  class="vm-btn b-green btn-autorizar">
            <i class="fa-solid fa-circle-check"></i> Autorizar
          </button>
          @endcan

          @can('rechazar_prestamos')
          <button type="button"
                  command="show-modal" commandfor="dialog-rechazar"
                  data-id="{{ $viaje->reserva?->id }}"
                  class="vm-btn b-red btn-rechazar">
            <i class="fas fa-times"></i> Rechazar
          </button>
          @endcan

        @endif

      </div>{{-- /vm-actions --}}
    </div>{{-- /vm-card-body --}}
  </div>{{-- /vm-card --}}

  @empty
  <div style="text-align:center; padding:48px 24px; color:#4a5068; font-family:'Sora',sans-serif;">
    <i class="fas fa-route" style="font-size:32px; opacity:.3; display:block; margin-bottom:10px;"></i>
    <p style="font-size:13px;">No hay viajes registrados</p>
  </div>
  @endforelse

</div>{{-- /vm-list --}}
