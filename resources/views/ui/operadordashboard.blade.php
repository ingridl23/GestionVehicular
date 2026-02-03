<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title> Dashboard-operador </title>
    <link href="{{ asset('css/operador.css') }}" rel="stylesheet" />
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer" />

</head>

<body class="bg-gray-100 min-h-screen pt-12">


@include('layout.navbarOperativo')

<!-- CONTENIDO -->
<main class="max-w-md mx-auto px-4 mt-6">



   {{-- seccion donde se renderiza la alerta segun gravedad del usuario --}}
    {{-- ALERTAS DEL USUARIO --}}
@foreach ($alertas as $alerta)
    <x-alerta-card :alerta="$alerta" />
@endforeach



    <!-- BOTONES RÁPIDOS -->
    <section class="flex flex-col gap-10 mb-10">
        <button class="btn-rapido" href="#">Iniciar reserva</button>
      <button
    class="btn-rapido"
    onclick="window.location='{{ route('operativo.reportes.create') }}'"
>
    Comenzar reporte
</button>

        <button class="btn-rapido">Asignar conductor</button>
    </section>

    <!-- BOTONES DE VIAJE -->
    <section class="flex gap-6">
        <button class="btn-iniciar flex-1">Iniciar viaje</button>

        <!-- este se deshabilita si NO hay viaje -->
       <!-- <button class="btn-finalizar flex-1 disabled:opacity-50"
                disabled>
            Finalizar viaje
        </button>
    -->
<button class="btn-finalizar flex-1"
  :disabled="!auth()->user()->viajeActivo">
    Finalizar viaje
</button>

    </section>

</main>
</body>

@include('layout.footer')




</html>
