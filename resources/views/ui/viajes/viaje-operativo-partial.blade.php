{{-- ============================================================
     SECCIÓN DE VIAJES — Dashboard Operativo
     Variables esperadas:
       $reservaActiva  → Reserva|null  (reserva aprobada pendiente de iniciar)
       $estadosNafta   → Collection    (estados de nafta para el select)
       auth()->user()->viajeActivo → Viaje|null (viaje en curso)
     ============================================================ --}}

@php
    $viaje      = auth()->user()->viajeActivo ?? null;
    $reserva    = $reservaActiva ?? null;
    $puedeIniciar   = $reserva && !$viaje;
    $puedeFinalizar = (bool) $viaje;
    $sinAccion      = !$puedeIniciar && !$puedeFinalizar;

    // Estado para la tarjeta
    $estadoLabel = match(true) {
        $puedeFinalizar => 'En curso',
        $puedeIniciar   => 'Aprobado — listo para iniciar',
        default         => 'Sin viaje asignado',
    };
    $estadoClass = match(true) {
        $puedeFinalizar => 'estado-en-curso',
        $puedeIniciar   => 'estado-aprobado',
        default         => 'estado-vacio',
    };
@endphp

{{-- ── ESTILOS (scoped a esta sección) ── --}}

@vite(['resources/css/app.css','resources/js/app.js'])
{{-- ══════════════════════════════════════════════════
     TARJETA PRINCIPAL DE VIAJE
═══════════════════════════════════════════════════ --}}
<div class="viaje-card">

  {{-- Header con estado dinámico --}}
  <div class="viaje-card-header {{ $estadoClass }}">
    <div>
      <div style="font-size:10px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#4a5068; margin-bottom:3px;">
        Viaje activo
      </div>
      <div style="font-size:15px; font-weight:700; color:#e8eaf0;">
        @if($viaje)
          Reserva #{{ $viaje->reserva->id }}
        @elseif($reserva)
          Reserva #{{ $reserva->id }}
        @else
          Sin viaje asignado
        @endif
      </div>
    </div>

    {{-- Pill de estado --}}
    @if($puedeFinalizar)
      <span class="pill en-curso">
        <span class="pill-dot"></span> En curso
      </span>
    @elseif($puedeIniciar)
      <span class="pill aprobado">
        <span class="pill-dot"></span> Aprobado
      </span>
    @else
      <span class="pill vacio">
        <span class="pill-dot"></span> Sin viaje
      </span>
    @endif
  </div>

  <div class="viaje-card-body">

    {{-- Datos del viaje si existe reserva o viaje activo --}}
    @if($viaje || $reserva)
      @php $r = $viaje?->reserva ?? $reserva; $v = $viaje?->vehiculo ?? $r->vehiculo; @endphp

      <div class="viaje-meta">
        <div class="viaje-meta-item">
          <label>Vehículo</label>
          <span class="dominio-mono">{{ $v->dominio ?? '—' }}</span>
        </div>
        <div class="viaje-meta-item">
          <label>Horario</label>
          <span>{{ \Carbon\Carbon::parse($r->fecha_desde)->format('H:i') }} — {{ \Carbon\Carbon::parse($r->fecha_hasta)->format('H:i') }}</span>
        </div>
        @if($r->motivo ?? $r->descripcion ?? null)
        <div class="viaje-meta-item full">
          <label>Motivo</label>
          <span>{{ $r->motivo ?? $r->descripcion }}</span>
        </div>
        @endif
      </div>

      {{-- KM y nafta actuales del vehículo --}}
      <div class="km-strip">
        <div>
          <div class="km-label">KM del vehículo</div>
          <div style="margin-top:1px;">
            <span class="km-num">{{ number_format($v->kilometros ?? 0, 0, ',', '.') }}</span>
            <span class="km-unit">km</span>
          </div>
        </div>
        <div style="text-align:right;">
          <div class="km-label">Nafta</div>
          <div class="nafta-val nafta-lleno" style="margin-top:1px;">
            {{ $v->estadoNafta->descripcion ?? '—' }}
          </div>
        </div>
      </div>

    @else
      {{-- Estado vacío --}}
      <div class="viaje-empty">
        <i class="fas fa-route"></i>
        <p>No tenés ningún viaje<br>aprobado para hoy</p>
      </div>
    @endif

    {{-- ══ BOTÓN PRINCIPAL DINÁMICO ══ --}}

    @if($puedeFinalizar)
      {{-- Viaje en curso → Finalizar --}}
      <button
        type="button"
        id="btnFinalizarViaje"
        class="btn-viaje-main btn-finalizar-viaje">
        <i class="fas fa-flag-checkered"></i> Finalizar viaje
      </button>

    @elseif($puedeIniciar)
      {{-- Reserva aprobada → Iniciar --}}
      <form method="POST" action="{{ route('operativo.viajes.comenzar', $reserva->id) }}">
        @csrf
        <button type="submit" class="btn-viaje-main btn-iniciar-viaje">
          <i class="fas fa-play"></i> Iniciar viaje
        </button>
      </form>

    @else
      {{-- Sin acción disponible --}}
      <button class="btn-viaje-main" disabled>
        <i class="fas fa-clock"></i> Sin viajes pendientes
      </button>
    @endif

  </div>
</div>{{-- /viaje-card --}}


{{-- ══════════════════════════════════════════════════
     MODAL FINALIZAR VIAJE (bottom-sheet)
     Solo se renderiza si hay un viaje activo
═══════════════════════════════════════════════════ --}}
@if($puedeFinalizar)
<div id="vms-modal-finalizar">
  <div class="vms-modal-box">
    <div class="vms-modal-handle"></div>

    <div class="vms-modal-title">Finalizar viaje</div>
    <div class="vms-modal-sub">Completá los datos para cerrar el recorrido</div>

    <form
      id="formFinalizarViaje"
      method="POST"
      action="{{ route('operativo.viajes.finalizar', $viaje->id) }}">
      @csrf

   <div class="space-y-4">

                <div>
                    <label class="block text-sm">Kilómetros finales</label>
                    <input type="number"
                           name="kilometros_fin"
                           required
                           class="w-full border rounded-lg p-2">
                </div>

                  <div>
<label class="block text-sm">Estado nafta final</label>
 <select name="id_estado_nafta_fin" class="form-control" required>
    <option value="">Seleccione estado</option>

    @foreach($estadosNafta as $estado)
        <option value="{{ $estado->id }}">
            {{ $estado->estado }}
        </option>
    @endforeach
</select>

                </div>
  <div>
    {{-- Observaciones --}}
    <label class="vms-form-label">Observaciones  <span class="text-gray-500">*Opcional</span></label>
    <br>
    <textarea
    name="observaciones"
    class="w-full border rounded-lg p-2"
    rows="2"
    style="resize:none;"
    placeholder="Ej: sin novedades, entregué llaves en recepción…"></textarea>


</div>
            </div>
<div class="mt-4">
    <label class="block text-sm mb-1">
        Dirección de estacionamiento
        <span class="text-gray-500">(Completar Solo si no hay GPS conectado)</span>
    </label>

    @if(!$viaje->vehiculo->control_satelital)
        <select name="id_direccion"
                class="w-full border rounded-lg p-2 bg-white">
            <option value="">Seleccionar dirección</option>

            @foreach($direcciones as $direccion)
                <option value="{{ $direccion->id }}">
                    {{ $direccion->calle }} {{ $direccion->altura }} - {{ $direccion->ciudad }}
                </option>
            @endforeach
        </select>
    @endif
</div>


      <button type="submit" class="vms-btn-confirm">
        <i class="fas fa-flag-checkered"></i> Confirmar finalización
      </button>
    </form>

    <button type="button" class="vms-btn-cancel-modal" onclick="vmsModalClose()">
      Cancelar
    </button>
  </div>
</div>

<script>
  // Abrir modal desde botón principal
  document.getElementById('btnFinalizarViaje')
    .addEventListener('click', () => {
      document.getElementById('vms-modal-finalizar').classList.add('open');
    });

  // Cerrar
  function vmsModalClose() {
    document.getElementById('vms-modal-finalizar').classList.remove('open');
  }

  // Cerrar al click fuera del box
  document.getElementById('vms-modal-finalizar')
    .addEventListener('click', function(e) {
      if (e.target === this) vmsModalClose();
    });

  // Selección nafta
  function vmsSelNafta(btn) {
    document.querySelectorAll('.nafta-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('vmsNaftaHidden').value = btn.dataset.value;
  }
</script>
@endif

