<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>prueba</title>
</head>
<body>
    <div> 

        <p>Nombre: {{$dependencia_padre->nombre}}</p>
        <p>Activa: {{ $dependencia_padre->activa ? 'Sí' : 'No' }}</p>
        <p>Direccion: {{$dependencia_padre->direccion->calle}} {{$dependencia_padre->direccion->altura}} - {{$dependencia_padre->direccion->ciudad}}</p>
        @if($dependencia_padre->id_dependencia_padre != null)
            <p>Dependencia jerárquicamente superior: {{$dependencia_padre->dependenciaPadre->nombre}}</p>
        @else
            <p>No tiene</p>
        @endif

        @if($dependencias_hijas->dependenciasHijas->isNotEmpty())
            <p>Dependencias hijas:</p>
             <ul>
                @foreach($dependencias_hijas->dependenciasHijas as $hija)
                    <li>{{ $hija->nombre }}</li>
                @endforeach
            </ul>
        @else
            <p>No tiene</p>
        @endif
    </div>
   
</body>
</html>