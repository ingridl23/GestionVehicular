
<div class="max-w-md mx-auto mt-6 bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">

    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
       <input
    name="titulo"
    value="{{ old('titulo') }}"
>

    </div>

    <!-- Chat -->
    <div id="chatBody" class="h-96 overflow-y-auto px-4 py-3 bg-gray-50 dark:bg-gray-900">
        {{-- render con JS o blade --}}
    </div>


</div>

<script>

    window.USUARIO_ACTUAL_ID = {{ auth()->id() }};
</script>

@vite(['resources/js/reportes.js'])

