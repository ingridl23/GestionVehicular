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
<style>
  /* Tarjeta principal */
  .viaje-card {
    background: #1a1e2a;
    border: 1px solid #252a38;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
    font-family: 'Sora', sans-serif;
  }

  .viaje-card-header {
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #252a38;
  }

  /* Variantes de color del header según estado */
  .viaje-card-header.estado-en-curso {
    background: linear-gradient(135deg, rgba(34,197,94,.12) 0%, rgba(6,182,212,.06) 100%);
    border-bottom-color: rgba(34,197,94,.2);
  }
  .viaje-card-header.estado-aprobado {
    background: linear-gradient(135deg, rgba(59,130,246,.12) 0%, rgba(167,139,250,.06) 100%);
    border-bottom-color: rgba(59,130,246,.2);
  }
  .viaje-card-header.estado-vacio {
    background: rgba(255,255,255,.02);
  }

  .viaje-card-body { padding: 18px; }

  /* Pills de estado */
  .pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .4px;
    padding: 4px 10px;
    border-radius: 20px;
    text-transform: uppercase;
  }
  .pill-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
  }
  .pill.en-curso  { background: rgba(34,197,94,.15); color: #22c55e; }
  .pill.aprobado  { background: rgba(59,130,246,.15); color: #60a5fa; }
  .pill.vacio     { background: rgba(255,255,255,.05); color: #7a8099; }
  .pill.en-curso .pill-dot { background: #22c55e; animation: vms-pulse 1.5s infinite; }
  .pill.aprobado .pill-dot { background: #60a5fa; }
  .pill.vacio    .pill-dot { background: #4a5068; }

  @keyframes vms-pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.4); }
  }

  /* Meta datos */
  .viaje-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 18px;
  }
  .viaje-meta-item label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #4a5068;
    margin-bottom: 3px;
  }
  .viaje-meta-item span {
    font-size: 13px;
    font-weight: 600;
    color: #e8eaf0;
  }
  .viaje-meta-item.full { grid-column: 1 / -1; }

  .dominio-mono {
    font-family: 'DM Mono', monospace;
    background: rgba(255,255,255,.06);
    border: 1px solid #252a38;
    padding: 2px 9px;
    border-radius: 5px;
    font-size: 14px;
    letter-spacing: 2px;
  }

  /* KM strip */
  .km-strip {
    background: #0d0f14;
    border: 1px solid #252a38;
    border-radius: 10px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
  }
  .km-strip .km-num {
    font-family: 'DM Mono', monospace;
    font-size: 20px;
    font-weight: 500;
    color: #e8eaf0;
  }
  .km-strip .km-unit { font-size: 11px; color: #7a8099; margin-left: 3px; }
  .km-strip .km-label { font-size: 11px; color: #7a8099; }
  .km-strip .nafta-val { font-weight: 600; font-size: 13px; }
  .nafta-lleno  { color: #22c55e; }
  .nafta-medio  { color: #f59e0b; }
  .nafta-bajo   { color: #ef4444; }

  /* Botón de acción principal */
  .btn-viaje-main {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-family: 'Sora', sans-serif;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    transition: opacity .15s, transform .1s;
    letter-spacing: .2px;
  }
  .btn-viaje-main:active  { transform: scale(.98); }
  .btn-viaje-main:disabled {
    background: #252a38 !important;
    color: #4a5068 !important;
    box-shadow: none !important;
    cursor: not-allowed;
  }

  .btn-iniciar-viaje {
    background: linear-gradient(135deg, #22c55e 0%, #06b6d4 100%);
    color: #fff;
    box-shadow: 0 4px 20px rgba(34,197,94,.3);
  }
  .btn-iniciar-viaje:hover:not(:disabled) { box-shadow: 0 4px 28px rgba(34,197,94,.45); }

  .btn-finalizar-viaje {
    background: linear-gradient(135deg, #f97316 0%, #ef4444 100%);
    color: #fff;
    box-shadow: 0 4px 20px rgba(239,68,68,.3);
  }
  .btn-finalizar-viaje:hover:not(:disabled) { box-shadow: 0 4px 28px rgba(239,68,68,.45); }

  /* Estado vacío */
  .viaje-empty {
    text-align: center;
    padding: 24px 0 8px;
    color: #4a5068;
  }
  .viaje-empty i { font-size: 28px; margin-bottom: 8px; opacity: .4; }
  .viaje-empty p { font-size: 13px; line-height: 1.5; }

  /* ── MODAL FINALIZAR (bottom-sheet mobile-first) ── */
  #vms-modal-finalizar {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.65);
    backdrop-filter: blur(4px);
    z-index: 200;
    align-items: flex-end;
    justify-content: center;
  }
  #vms-modal-finalizar.open { display: flex; }

  .vms-modal-box {
    background: #1a1e2a;
    border: 1px solid #252a38;
    border-radius: 20px 20px 0 0;
    padding: 20px 22px 28px;
    width: 100%;
    max-width: 480px;
    animation: vms-slideUp .22s ease;
  }
  @keyframes vms-slideUp {
    from { transform: translateY(30px); opacity:0; }
    to   { transform: translateY(0);    opacity:1; }
  }

  .vms-modal-handle {
    width: 38px; height: 4px;
    border-radius: 2px;
    background: #2e3447;
    margin: 0 auto 18px;
  }
  .vms-modal-title {
    font-size: 17px;
    font-weight: 700;
    color: #e8eaf0;
    margin-bottom: 2px;
  }
  .vms-modal-sub {
    font-size: 12px;
    color: #7a8099;
    margin-bottom: 18px;
  }

  .vms-form-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #4a5068;
    margin-bottom: 6px;
  }
  .vms-form-input {
    width: 100%;
    background: #0d0f14;
    border: 1px solid #252a38;
    border-radius: 9px;
    padding: 10px 13px;
    color: #e8eaf0;
    font-family: 'Sora', sans-serif;
    font-size: 14px;
    outline: none;
    transition: border .15s;
    margin-bottom: 13px;
  }
  .vms-form-input:focus { border-color: #3b82f6; }

  /* Opciones de nafta — botones táctiles grandes */
  .nafta-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 6px;
    margin-bottom: 13px;
  }
  .nafta-btn {
    padding: 10px 4px;
    border: 1px solid #252a38;
    border-radius: 8px;
    background: none;
    color: #7a8099;
    font-family: 'Sora', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all .12s;
  }
  .nafta-btn:hover  { border-color: #2e3447; color: #e8eaf0; }
  .nafta-btn.active { border-color: #3b82f6; color: #60a5fa; background: rgba(59,130,246,.1); }

  .vms-btn-confirm {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #f97316, #ef4444);
    border: none;
    border-radius: 11px;
    color: #fff;
    font-family: 'Sora', sans-serif;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 6px;
    box-shadow: 0 4px 18px rgba(239,68,68,.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity .15s;
  }
  .vms-btn-confirm:hover { opacity: .92; }

  .vms-btn-cancel-modal {
    width: 100%;
    margin-top: 8px;
    padding: 10px;
    background: none;
    border: none;
    color: #7a8099;
    font-family: 'Sora', sans-serif;
    font-size: 13px;
    cursor: pointer;
    transition: color .12s;
  }
  .vms-btn-cancel-modal:hover { color: #e8eaf0; }
</style>

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

      {{-- KM finales --}}
      <label class="vms-form-label">Kilómetros finales *</label>
      <input
        type="number"
        name="kilometros_fin"
        id="vmsKmFinal"
        class="vms-form-input"
        placeholder="ej: {{ ($viaje->kilometros_inicio ?? 0) + 50 }}"
        min="{{ $viaje->kilometros_inicio ?? 0 }}"
        required>

      {{-- Estado de nafta — botones táctiles --}}
      <label class="vms-form-label">Estado de nafta al entregar *</label>
      <div class="nafta-grid">
        @foreach($estadosNafta as $estado)
          <button
            type="button"
            class="nafta-btn {{ $loop->last ? 'active' : '' }}"
            data-value="{{ $estado->id }}"
            onclick="vmsSelNafta(this)">
            {{ $estado->descripcion ?? $estado->estado }}
          </button>
        @endforeach
      </div>
      <input type="hidden" name="id_estado_nafta_fin" id="vmsNaftaHidden"
             value="{{ $estadosNafta->last()?->id }}">

      {{-- Observaciones --}}
      <label class="vms-form-label">Observaciones <span style="font-weight:400; text-transform:none; letter-spacing:0;">— opcional</span></label>
      <textarea
        name="observaciones"
        class="vms-form-input"
        rows="2"
        style="resize:none;"
        placeholder="Ej: sin novedades, entregué llaves en recepción…"></textarea>

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
