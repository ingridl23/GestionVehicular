<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editar</title>
</head>
<body>
    <form action="/dependencias/{{$dependencia_base_datos->id}}" method="post">
        @csrf
        {{ method_field('PATCH') }}
        @include('dependencias.formulario-crear-editar.form')

        <button>Editar dependencia</button>
    </form>
</body>

<script>
    function toggleNuevaDireccion() {
        const select = document.getElementById('direcciones');
        const bloque = document.getElementById('nueva-direccion');

        if (select.value === 'nueva') {
            bloque.style.display = "flex";
        } else {
             bloque.style.display = "none";
        }
    }
    document.addEventListener('DOMContentLoaded', toggleNuevaDireccion);
</script>
</html>