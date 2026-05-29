
<div class=relative">

<div
    id="mensajeriaInterna"
    class="flex items-end gap-3

    fixed bottom-20 left-3 right-3
    z-40

    bg-white dark:bg-gray-800
    p-3 rounded-2xl shadow-2xl
    border border-gray-200 dark:border-gray-700

        md:relative
        md:bottom-auto md:left-auto md:right-auto
        md:bg-transparent
        md:border-0
        md:shadow-none
        md:p-0"
    data-reporte-id="{{ $reporte_id }}">
    <div class="flex-1">
        <textarea
            id="mensajeInput"
            rows="1"
            placeholder="Escribí un mensaje..."
            class="w-full resize-none max-h-24 overflow-y-auto px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600
                   bg-gray-50 dark:bg-gray-700 dark:text-white
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
    ></textarea>
    </div>

    <button
        type="button"
        id="btnEnviarMensaje"
        class="flex items-center justify-center w-10 h-10 rounded-full
               bg-blue-600 hover:bg-blue-700 text-white
               focus:outline-none focus:ring-2 focus:ring-blue-500 flex-shrink-0"
        title="Enviar mensaje">
        <i class="fas fa-paper-plane text-sm"></i>
    </button>

</div>
<p id="mensajeError"
  class="hidden fixed bottom-36 left-5 right-5 z-50
               text-xs text-red-500
               md:static md:mt-2">
    El mensaje no puede estar vacío
</p>

</div>
