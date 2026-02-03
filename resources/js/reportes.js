document.addEventListener('DOMContentLoaded', () => {

    // ===============================
    // Datos inyectados desde Blade
    // ===============================
    const reportesData = window.REPORTES_DATA || [];
    const usuarioActualId = window.USUARIO_ACTUAL_ID || null;

    let reporteActivo = null;

    // ===============================
    // Click en reporte (lista izquierda)
    // ===============================
    document.querySelectorAll('.reporte-item').forEach(item => {
        item.addEventListener('click', () => seleccionarReporte(item));
    });

    // ===============================
    // Filtrado en tiempo real
    // ===============================
    function filtrar() {
        const searchInput = document.getElementById('searchInput');
        const filterEstado = document.getElementById('filterEstado');
        const filterDependencia = document.getElementById('filterDependencia');
        const filterUsuario = document.getElementById('filterUsuario');

        const search = searchInput ? searchInput.value.toLowerCase() : '';
        const estado = filterEstado ? filterEstado.value : '';
        const dependencia = filterDependencia ? filterDependencia.value : '';
        const usuario = filterUsuario ? filterUsuario.value.toLowerCase() : '';


        document.querySelectorAll('.reporte-item').forEach(item => {
            const titulo = item.dataset.titulo.toLowerCase();
            const usuarioNombre = item.dataset.usuarioNombre.toLowerCase();
            const itemEstado = item.dataset.estado;
            const entidadId = item.dataset.entidadId;

            let mostrar = true;

            if (search && !titulo.includes(search) && !usuarioNombre.includes(search)) {
                mostrar = false;
            }
            if (estado && itemEstado !== estado) {
                mostrar = false;
            }
            if (dependencia && entidadId !== dependencia) {
                mostrar = false;
            }
            if (usuario && !usuarioNombre.includes(usuario)) {
                mostrar = false;
            }

            item.style.display = mostrar ? 'block' : 'none';
        });
    }

    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.addEventListener('input', filtrar);

    const filterEstado = document.getElementById('filterEstado');
    if (filterEstado) filterEstado.addEventListener('change', filtrar);

    const filterDependencia = document.getElementById('filterDependencia');
    if (filterDependencia) filterDependencia.addEventListener('change', filtrar);

    const filterUsuario = document.getElementById('filterUsuario');
    if (filterUsuario) filterUsuario.addEventListener('input', filtrar);


    // ===============================
    // Seleccionar reporte
    // ===============================
    window.seleccionarReporte = function(el) {

        document.querySelectorAll('.reporte-item').forEach(i =>
            i.classList.remove('bg-white', 'dark:bg-gray-800', 'ring-2', 'ring-blue-500')
        );

        el.classList.add('bg-white', 'dark:bg-gray-800', 'ring-2', 'ring-blue-500');

        const id = parseInt(el.dataset.id);
        reporteActivo = reportesData.find(r => r.id === id);
        if (!reporteActivo) return;



        const sinSeleccion = document.getElementById('sinSeleccion');
        sinSeleccion.classList.add('hidden');

        const reporteSeleccionado = document.getElementById('reporteSeleccionado')
        reporteSeleccionado.classList.remove('hidden');

        document.getElementById('chatAvatar').textContent =
            reporteActivo.usuario_nombre.substring(0, 2).toUpperCase();
        document.getElementById('chatUsuario').textContent = reporteActivo.usuario_nombre;
        document.getElementById('chatTitulo').textContent = reporteActivo.titulo;
        document.getElementById('chatEntidad').textContent =
            `${reporteActivo.entidad_tipo} #${reporteActivo.entidad_id}`;

        document.getElementById('selectEstado').value = reporteActivo.estado;

        renderMensajes();
    };

    // ===============================
    // Renderizar mensajes
    // ===============================
    function renderMensajes() {
        const body = document.getElementById('chatBody');
        if (!body || !reporteActivo) return;

        body.innerHTML = '';

        // Mensaje inicial
        body.innerHTML += `
            <div class="flex justify-center">
                <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-lg px-3 py-1.5 max-w-sm text-center">
                    <strong>${reporteActivo.usuario_nombre}</strong> abrió este reporte el ${reporteActivo.fecha}
                </div>
            </div>

            <div class="flex justify-start">
                <div class="max-w-[75%]">
                    <div class="flex items-end gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                            ${reporteActivo.usuario_nombre.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="bg-white dark:bg-gray-800 border rounded-t-lg rounded-br-lg px-4 py-2.5">
                            <p class="text-xs font-semibold text-blue-600">${reporteActivo.usuario_nombre}</p>
                            <p class="text-sm">${reporteActivo.descripcion}</p>
                            <p class="text-xs text-gray-400 text-right">${reporteActivo.fecha}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Comentarios
        reporteActivo.comentarios.forEach(c => {
            const esPropio = c.usuario_id === usuarioActualId;

            body.innerHTML += esPropio ? `
                <div class="flex justify-end">
                    <div class="bg-blue-600 text-white rounded-t-lg rounded-bl-lg px-4 py-2.5 max-w-[75%]">
                        <p class="text-xs font-semibold">Vos</p>
                        <p class="text-sm">${c.comentario}</p>
                        <p class="text-xs text-right">${c.fecha}</p>
                    </div>
                </div>
            ` : `
                <div class="flex justify-start">
                    <div class="bg-white dark:bg-gray-800 border rounded-t-lg rounded-br-lg px-4 py-2.5 max-w-[75%]">
                        <p class="text-xs font-semibold text-blue-600">${c.nombre}</p>
                        <p class="text-sm">${c.comentario}</p>
                        <p class="text-xs text-right">${c.fecha}</p>
                    </div>
                </div>
            `;
        });

        body.scrollTop = body.scrollHeight;
    }

    // ===============================
    // Cambiar estado
    // ===============================
    window.cambiarEstado = function(select) {
        if (!reporteActivo) return;

        const nuevoEstado = select.value;

        fetch(`/admin/reportes/${reporteActivo.id}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(res => {
                if (!res.ok) throw new Error();
                reporteActivo.estado = nuevoEstado;
            })
            .catch(() => {
                select.value = reporteActivo.estado;
                alert('No se pudo actualizar el estado');
            });
    };

    // ===============================
    // Mensajería interna (hook externo)
    // ===============================
    window.agregarMensajeAlChat = function(comentario) {
        if (!reporteActivo) return;
        reporteActivo.comentarios.push(comentario);
        renderMensajes();
    };

    const textarea = document.getElementById('mensajeInput');
    const btnEnviar = document.getElementById('btnEnviarMensaje');
    const errorMsg = document.getElementById('mensajeError');

    btnEnviar.addEventListener('click', enviarMensaje);

    textarea.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensaje();
        }
    });

    function enviarMensaje() {
        const mensaje = textarea.value.trim();

        if (!mensaje) {
            errorMsg.classList.remove('hidden');
            return;
        }

        errorMsg.classList.add('hidden');

        // EJEMPLO: simulamos mensaje agregado
        window.agregarMensajeAlChat({
            comentario: mensaje,
            nombre: 'Vos',
            usuario_id: window.USUARIO_ACTUAL_ID,
            fecha: new Date().toLocaleString('es-AR', {
                day: '2-digit',
                month: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            })
        });

        textarea.value = '';
    }


});