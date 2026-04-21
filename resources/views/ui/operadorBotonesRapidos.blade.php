{{-- resources/views/operativo/botonesrapidos.blade.php
     Variables esperadas del controller del dashboard:
       $alertas       → Collection<Alerta>
       $reservaActiva → Reserva|null   (reserva APROBADA del usuario, sin viaje activo)
       $estadosNafta  → Collection     (para el modal de finalizar)
--}}

{{-- ALERTAS --}}
@foreach ($alertas as $alerta)
    <x-alerta-card :alerta="$alerta" />
@endforeach

{{-- BOTONES RÁPIDOS --}}
<section class="flex flex-col gap-4 mb-8">
    <a class="btn-rapido text-center" href="{{ route('operativo.mis-reservas') }}">
          <i class="fa-solid fa-car mr-2"></i>+
           <p class="text-xs text-white-500 dark:text-gray-400"> Reservar Vehiculo</p>
    </a>
    <a class="btn-rapido text-center" href="{{ route('operativo.reportes.create') }}">
          <i class="fas fa-file-alt text-gray-300 dark:text-gray-600 text-3xl mb-3"></i>
          <p class="text-xs text-white-500 dark:text-gray-400">  Iniciar reporte</p>
    </a>
    <a class="btn-rapido text-center"
       href="{{ isset($reservaActiva) ? route('operativo.editar-conductor', $reservaActiva->id) : '#' }}"
       @if(!isset($reservaActiva)) aria-disabled="true" @endif>
         <i class="fas fa-user-tag mr-1"></i>
          <p class="text-xs text-white-500 dark:text-gray-400">Asignar conductor</p>
    </a>
</section>

{{-- ══════════════════════════════════════
     BOTONES DE VIAJE
     Lógica:
       - $reservaActiva existe y sin viaje activo → mostrar "Iniciar"
       - viajeActivo existe (sin fecha_fin)       → mostrar "Finalizar"
       - ninguno                                  → ambos deshabilitados
═══════════════════════════════════════ --}}

@php
    $viajeActivo   = auth()->user()->viajeActivo ?? null;
    $puedeIniciar  = isset($reservaActiva) && !$viajeActivo;
    $puedeFinalizar = (bool) $viajeActivo;
@endphp
{{-- Info contextual debajo de los botones --}}
@if($puedeIniciar)
<p class="text-xs text-center text-gray-500 dark:text-gray-400 mb-6">
    Reserva #{{ $reservaActiva->id }} —
    {{ $reservaActiva->vehiculo?->dominio ?? '' }}
    aprobada y lista
</p>
@elseif($puedeFinalizar)
<p class="text-xs text-center text-gray-500 dark:text-gray-400 mb-6">
    Viaje #{{ $viajeActivo->id }} en curso desde
    {{ $viajeActivo->fecha_inicio?->format('H:i') }}
</p>
@else
<p class="text-xs text-center text-gray-400 dark:text-gray-500 mb-6">
    No tenés viajes asignados para hoy
</p>
@endif
<section class="flex gap-3 mb-2">




    {{-- INICIAR VIAJE --}}
    <form method="POST"
          action="{{ $puedeIniciar ? route('operativo.viajes.comenzar', $reservaActiva->id) : '#' }}"
          class="flex-1">
        @csrf
        <button
            type="{{ $puedeIniciar ? 'submit' : 'button' }}"
            class="btn-iniciar w-full {{ !$puedeIniciar ? 'opacity-50 cursor-not-allowed' : '' }}"
            @if(!$puedeIniciar) disabled @endif>
            <i class="fas fa-play mr-2"></i>
             <p class="text-xs text-gray-600 dark:text-gray-400"> Iniciar viaje</p>
        </button>
    </form>

    {{-- FINALIZAR VIAJE --}}
    <button
        type="button"
        id="btnFinalizar"
        class="btn-finalizar flex-1 {{ !$puedeFinalizar ? 'opacity-60 cursor-not-allowed' : '' }}"
        @if(!$puedeFinalizar) disabled @endif>
        <i class="fas fa-flag-checkered mr-2"></i>
     <p class="text-xs text-gray-800 dark:text-gray-400">  Finalizar viaje</p>
    </button>

</section>



{{-- MODAL FINALIZAR —
     Solo se renderiza cuando hay un viaje activo,
     así $estadosNafta se usa solo cuando hace falta --}}
@if($puedeFinalizar)
<div id="modalFinalizar"
     class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-end justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-t-2xl w-full max-w-md p-6 shadow-xl
                border border-gray-200 dark:border-gray-700">

        {{-- Handle --}}
        <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-5"></div>

        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
            Finalizar viaje
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Viaje #{{ $viajeActivo->id }} · KM inicio: {{ number_format($viajeActivo->kilometros_inicio, 0, ',', '.') }}
        </p>

        <form method="POST"
              action="{{ route('operativo.viajes.finalizar', $viajeActivo->id) }}"
              id="formFinalizarViaje">
            @csrf

            {{-- KM finales --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider
                              text-gray-500 dark:text-gray-400 mb-1.5">
                    Kilómetros finales *
                </label>
                <input
                    type="number"
                    name="kilometros_fin"
                    id="kmFinal"
                    required
                    min="{{ $viajeActivo->kilometros_inicio }}"
                    placeholder="{{ $viajeActivo->kilometros_inicio + 10 }}"
                    class="w-full px-3 py-2 text-sm rounded-lg border
                           border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700
                           text-gray-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           transition">
            </div>

            {{-- Estado nafta —  botones táctiles grandes --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider
                              text-gray-500 dark:text-gray-400 mb-1.5">
                    Estado del tanque al entregar *
                </label>
                <div class="grid gap-2"
                     style="grid-template-columns: repeat({{ $estadosNafta->count() }}, 1fr)">
                    @foreach($estadosNafta as $estado)
                    <button
                        type="button"
                        class="nafta-btn py-2.5 text-xs font-semibold rounded-lg border
                               border-gray-300 dark:border-gray-600
                               text-gray-600 dark:text-gray-300
                               hover:border-blue-500 hover:text-blue-600
                               dark:hover:border-blue-400 dark:hover:text-blue-400
                               transition {{ $loop->last ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : '' }}"
                        data-value="{{ $estado->id }}"
                        onclick="selNafta(this)">
                        {{ $estado->descripcion ?? $estado->estado }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="id_estado_nafta_fin"
                       id="estadoNafta"
                       value="{{ $estadosNafta->last()?->id }}">
            </div>

            {{-- Observaciones --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold uppercase tracking-wider
                              text-gray-500 dark:text-gray-400 mb-1.5">
                    Observaciones <span class="font-normal normal-case">(opcional)</span>
                </label>
                <textarea
                    name="observaciones"
                    id="observaciones"
                    rows="2"
                    placeholder="Ej: sin novedades, entregué llaves en recepción…"
                    class="w-full px-3 py-2 text-sm rounded-lg border resize-none
                           border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-700
                           text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           transition"></textarea>
            </div>


            <div class="mt-4">
    <label class="block text-sm mb-1">
        Dirección de estacionamiento
        <span class="text-gray-500">(Completar Solo si no hay GPS conectado)</span>
    </label>

    @if(!$viajeActivo->vehiculo->control_satelital)
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


            <button type="submit"
        onclick="console.log('CLICK')"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                Finalizar viaje
            </button>

        </form>

        <button type="button"
                onclick="document.getElementById('modalFinalizar').classList.add('hidden')"
                class="w-full mt-3 py-2 text-sm text-gray-500 dark:text-gray-400
                       hover:text-gray-700 dark:hover:text-gray-200 transition">
            Cancelar
        </button>
    </div>
</div>
@endif
<div class="contenedor_loader">
        <div class="loader"></div>
    </div>
{{-- Script: abrir/cerrar modal + selección nafta --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnFinalizar = document.getElementById('btnFinalizar');
    const modal        = document.getElementById('modalFinalizar');

    if (btnFinalizar && modal) {
        btnFinalizar.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });
        // Cerrar al click fuera del box
        modal.addEventListener('click', function (e) {
            if (e.target === this) this.classList.add('hidden');
        });
    }
});

function selNafta(btn) {
    document.querySelectorAll('.nafta-btn').forEach(b => {
        b.classList.remove(
            'border-blue-500','text-blue-600',
            'dark:border-blue-400','dark:text-blue-400','bg-blue-50','dark:bg-blue-900/20'
        );
    });
    btn.classList.add(
        'border-blue-500','text-blue-600',
        'dark:border-blue-400','dark:text-blue-400','bg-blue-50','dark:bg-blue-900/20'
    );
    document.getElementById('estadoNafta').value = btn.dataset.value;
}
</script>
