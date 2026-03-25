@extends('layout.app')

@section('content')
<div id="wrap">
  <div id="map-col">
    <div id="map-card">
      <svg id="map-svg" viewBox="0 0 420 300"></svg>
    </div>

    <div id="status-card">
      <div id="status-dot" class="idle"></div>
      <div>
        <div id="status-title">Listo para salir</div>
        <div id="status-text">Presioná el botón para comenzar</div>
      </div>
    </div>
  </div>

  <div id="side-col">
    <div class="info-card">
      <div class="info-label">distancia recorrida</div>
      <div>
        <span class="info-value" id="km-val">0.0</span>
        <span class="info-unit">km</span>
      </div>
      <div id="progress-bar-wrap">
        <div id="progress-bar"></div>
      </div>
    </div>

    <div id="route-card">
      <div class="route-row">
        <div class="rdot o"></div>
        <div>
          <div class="rname">Origen</div>
          <div class="raddr" id="origin-addr">direccion de origen</div>
        </div>
      </div>

      <div class="rline"><div class="rline-inner"></div></div>

      <div class="route-row">
        <div class="rdot d"></div>
        <div>
          <div class="rname">Destino</div>
          <div class="raddr" id="dest-addr">nombre del destino</div>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-2">
      <button id="btn-start">Comenzar viaje</button>
      <button id="btn-cancel">Cancelar viaje</button>
    </div>

    <div id="gps-pill">
      <div id="gps-dot"></div>
      <span id="gps-txt">GPS no conectado · modo simulado</span>
    </div>
  </div>
</div>
@endsection
<script>
    const viajeId = @json($viaje->id);
</script>
@vite(['resources/js/mapa/simulador.js', 'resources/css/mapa/simulador.css'])
