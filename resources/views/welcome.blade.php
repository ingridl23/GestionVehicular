<!DOCTYPE html>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/js/app.js', 'resources/css/app.css'])


</head>


<body>
    <div class="p-10 text-center bg-blue-500 text-white">
        Tailwind funciona 🎉
    </div>


    <div class="bg-green-500 text-white p-4 rounded-lg">
        Tailwind v4 OK ✔️
    </div>
    <div class="bg-red-500 text-white p-6">
        Probando Tailwind 🎨
    </div>
    <div class="bg-white dark:bg-gray-800 text-black dark:text-white p-4">
        Modo oscuro funcionando 🌙
    </div>


    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>




</body>

</html>
