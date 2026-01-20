<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crear</title>
</head>
<body>
    <form action="/dependencias/crear-dependencia" method="post">
        @csrf
        @include('dependencias.formulario-crear-editar.form')

        <button>Crear dependencia</button>
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