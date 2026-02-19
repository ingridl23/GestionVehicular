<div
    id="mensajeriaInterna"
    class="flex items-end gap-3"
    data-reporte-id="{{ $reporte_id }}">
    <div class="flex-1">
        <textarea
            id="mensajeInput"
            rows="1"
            placeholder="Escribí un mensaje..."
            class="w-full resize-none px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600
                   bg-gray-50 dark:bg-gray-700 dark:text-white
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
        ></textarea>
    </div>

    <button
        type="button"
        id="btnEnviarMensaje"
        class="flex items-center justify-center w-10 h-10 rounded-full
               bg-blue-600 hover:bg-blue-700 text-white
               focus:outline-none focus:ring-2 focus:ring-blue-500"
        title="Enviar mensaje"
    >
        <i class="fas fa-paper-plane text-sm"></i>
    </button>
</div>

<p id="mensajeError" class="hidden mt-2 text-xs text-red-500">
    El mensaje no puede estar vacío
</p>

