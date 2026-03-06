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
@vite(['resources/css/app.css','resources/js/app.js'])


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
