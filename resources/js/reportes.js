console.log("JS cargado");

function setText(id, value) {
    const el = document.getElementById(id);

    if (el) {
        el.textContent = value;
    }
}
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
    // Auto-selección para vista show()
    // ===============================
    if (document.querySelectorAll('.reporte-item').length === 0 &&
        reportesData.length === 1) {

        reporteActivo = reportesData[0];

        const sinSeleccion = document.getElementById('sinSeleccion');
        if (sinSeleccion) sinSeleccion.classList.add('hidden');

        const reporteSeleccionado = document.getElementById('reporteSeleccionado');
        if (reporteSeleccionado) reporteSeleccionado.classList.remove('hidden');

        const avatar = document.getElementById('chatAvatar');
        if (avatar) {
            avatar.textContent =
                reporteActivo.usuario_nombre.substring(0, 2).toUpperCase();
        }

        const usuario = document.getElementById('chatUsuario');
        if (usuario) usuario.textContent = reporteActivo.usuario_nombre;

        const titulo = document.getElementById('chatTitulo');
        if (titulo) titulo.textContent = reporteActivo.titulo;

        const entidad = document.getElementById('chatEntidad');
        if (entidad) {
            entidad.textContent =
                `${reporteActivo.entidad_tipo} #${reporteActivo.entidad_id}`;
        }

        const selectEstado = document.getElementById('selectEstado');
        if (selectEstado) selectEstado.value = reporteActivo.estado;

        renderMensajes();
    }

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
        if (sinSeleccion) {
            sinSeleccion.classList.add('hidden');
        }


        const reporteSeleccionado = document.getElementById('reporteSeleccionado')

        if (reporteSeleccionado) {

            reporteSeleccionado.classList.remove('hidden');
        }


        setText(
            'chatAvatar',
            reporteActivo.usuario_nombre.substring(0, 2).toUpperCase()
        );

        setText('chatUsuario', reporteActivo.usuario_nombre);

        setText('chatTitulo', reporteActivo.titulo);

        setText(
            'chatEntidad',
            `${reporteActivo.entidad_tipo} #${reporteActivo.entidad_id}`
        );


        /*
        document.getElementById('chatAvatar').textContent =
            reporteActivo.usuario_nombre.substring(0, 2).toUpperCase();
        document.getElementById('chatUsuario').textContent = reporteActivo.usuario_nombre;
        document.getElementById('chatTitulo').textContent = reporteActivo.titulo;
        document.getElementById('chatEntidad').textContent =
            `${reporteActivo.entidad_tipo} #${reporteActivo.entidad_id}`;
*/
        //  document.getElementById('selectEstado').value = reporteActivo.estado;
        const selectEstado = document.getElementById('selectEstado');

        if (selectEstado) {
            selectEstado.value = reporteActivo.estado;
        }
        renderMensajes();
        scrollChatAbajo();
    };

    /**********************************/
    //// scrol chat  //////////////
    /**********************************/


    function scrollChatAbajo() {
        const chat = document.getElementById('chatBody');

        if (chat) {
            chat.scrollTop = chat.scrollHeight;
        }
    }
    /*
        function scrollChatAbajo() {
            const chat = document.getElementById('chatBody');
            chat.scrollTop = chat.scrollHeight;
        }
    */

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

            const esPropio = Number(c.id_usuario) === Number(usuarioActualId);

            const claseContenedor = esPropio ?
                'flex justify-end' :
                'flex justify-start';

            const claseBurbuja = esPropio ?
                'bg-blue-600 text-white rounded-t-lg rounded-bl-lg' :
                'bg-white dark:bg-gray-800 border rounded-t-lg rounded-br-lg';

            body.innerHTML += `
        <div class="${claseContenedor}">
            <div class="${claseBurbuja} px-4 py-2.5 max-w-[75%]">
                <p class="text-xs font-semibold ${esPropio ? '' : 'text-blue-600'}">
                    ${esPropio ? 'Vos' : c.nombre}
                </p>
                <p class="text-sm">${c.comentario}</p>
                <p class="text-xs text-right">${c.fecha}</p>
            </div>
        </div>
    `;
        });
        /*
                requestAnimationFrame(() => {
                    body.scrollTop = body.scrollHeight;
                });
        */
        requestAnimationFrame(() => {
            if (body) {
                body.scrollTop = body.scrollHeight;
            }
        });

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
                console.log('Estado actualizado:', nuevoEstado);
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

        console.log("push desde agregarmensajealchat");

        if (!reporteActivo) return;
        reporteActivo.comentarios.push(comentario);
        renderMensajes();
    };

    const textarea = document.getElementById('mensajeInput');
    const btnEnviar = document.getElementById('btnEnviarMensaje');
    const errorMsg = document.getElementById('mensajeError');

    if (btnEnviar) {
        btnEnviar.addEventListener('click', enviarMensaje);
    }

    if (textarea) {

        textarea.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensaje();
            }
        });
    }

    function enviarMensaje() {
        if (!reporteActivo) {
            alert('Seleccioná un reporte primero');
            return;
        }
        console.log("push desde enviarMensaje");

        const mensaje = textarea.value.trim();

        if (!mensaje) {
            if (errorMsg) {
                errorMsg.classList.remove('hidden');
            }
            return;
        }

        if (errorMsg) {
            errorMsg.classList.add('hidden');
        }
        btnEnviar.disabled = true;

        fetch(`/${window.BASE_REPORTES_URL}/reportes/${reporteActivo.id}/comentarios`, {

                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    comentario: mensaje
                })
            })
            .then(async res => {
                if (!res.ok) {
                    const text = await res.text();
                    console.error('ERROR BACKEND:', text);
                    throw new Error(text);
                }
                return res.json();
            })

        .then(data => {
            console.log("comentarios antes:", reporteActivo.comentarios);
            console.log("nuevo comentario:", data.comentario);

            reporteActivo.comentarios.push(data.comentario);
            renderMensajes();
            scrollChatAbajo();
            textarea.value = ''; //  limpiar input
            textarea.focus(); //  volver a enfocar
        })

        .catch(() => {
                alert('No se pudo enviar el mensaje');
            })
            .finally(() => {
                btnEnviar.disabled = false;
            });
    }




    /******************************************************************************************************* */
    /********************* FUNCIONALIDAD DE ASIGNAR BOTON DE ELIMINAR REPORTE SOLO SI ESTA CERRADO ******** */
    //**************************************************************************************************** */

    let reporteAEliminar = null;
    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        e.stopPropagation();

        reporteAEliminar = btn.dataset.id;

        const dialog = document.getElementById('dialog-confirmar-reporte');
        if (dialog) dialog.showModal();
    });


    // ===============================
    // CONFIRMAR ELIMINACIÓN
    // ===============================
    const btnConfirmar = document.getElementById('dialog-confirmar-btn');

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', async() => {

            if (!reporteAEliminar) return;

            try {
                const res = await fetch(`/admin/reportes/${reporteAEliminar}/eliminar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await res.json();

                document.getElementById('dialog-confirmar-reporte').close();

                if (data.success) {

                    //  eliminar del DOM
                    const item = document.querySelector(`.reporte-item[data-id="${reporteAEliminar}"]`);
                    if (item) item.remove();

                    //  eliminar del array
                    const index = reportesData.findIndex(r => r.id == reporteAEliminar);
                    if (index !== -1) {
                        reportesData.splice(index, 1);
                    }

                    //  limpiar panel derecho
                    if (reporteActivo && reporteActivo.id == reporteAEliminar) {
                        reporteActivo = null;
                        document.getElementById('reporteSeleccionado').classList.add('hidden');
                        document.getElementById('sinSeleccion').classList.remove('hidden');
                    }

                    console.log("Reporte eliminado correctamente");

                } else {
                    alert(data.message || 'No se pudo eliminar el reporte');
                }

            } catch (error) {
                console.error(error);
                alert('Error al eliminar el reporte');
            }

            reporteAEliminar = null;
        });
    }


});
