<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>prueba</title>
        <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://use.typekit.net/wjn2blc.css">
    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS y JS Generales -->
    @vite(['resources/js/filtrosDependencias.js'])

</head>
<body>
        @include('dependencias.filtros.filtro', [
            'dependencias_filtro' => $dependencias_filtro ?? collect(),
            'localidades' => $localidades ?? collect()
        ])
        <div class="contenedor-servidor">
         @foreach ($dependencias as $dependencia)
            <p>{{$dependencia -> nombre}} , {{$dependencia->direccion->calle}} {{$dependencia->direccion->altura}} - {{$dependencia->direccion->ciudad}}</p>
            <form action="/dependencias/{{$dependencia->id}}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
            <div class="form-check form-switch">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" data-id="{{$dependencia->id}}"
                        @checked($dependencia->activa)
                        data-id="{{ $dependencia->id }}">
                </div>
            </div>
        @endforeach
    </div>

    <div id="contenedor-js" style="display:none;">
        <div id="lista-dependencias"></div>
        <div id="paginacion"></div>
    </div>

    <div class="contenedor-servidor">
        {{ $dependencias->links() }}
    </div>
</body>

</html>