document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formFiltrosReservas");

    let contenedor = document.getElementById("contenedor-reservas");
    let contenedor_lista = document.getElementById(
        "contenedor-reservas-listas",
    );
    let currentUrl = null;
    let filtrosActuales = {};

    const { permissions: PERMISSIONS, routes: ROUTES } = window.RESERVAS_CONFIG;
    let textoNoReservas = document.getElementById("mensajeNoHayReservas");
    let contenedorGeneral = document.getElementById("contenedor-general");

    let htmlCopiaContenedorTabla = contenedor.innerHTML;
    let htmlCopiaContenedorLista = contenedor_lista.innerHTML;
    if (!form) {
        return;
    }
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        document.getElementById("contenedor-js").style.display = "block";
        document.querySelector(".contenedor-servidor").style.display = "none";

        textoNoReservas.classList.remove("block");
        textoNoReservas.classList.add("hidden");

        filtrosActuales = {
            nombre: document.getElementById("nombre-filtro").value,
            fecha_inicio: document.getElementById("fecha-inicio").value,
            fecha_fin: document.getElementById("fecha-fin").value,
            estado: document.getElementById("estado-filtro").value,
            vehiculo: document.getElementById("vehiculo-filtro").value,
        };

        Object.keys(filtrosActuales).forEach((key) => {
            if (!filtrosActuales[key] || filtrosActuales[key] === "default") {
                delete filtrosActuales[key];
            }
        });

        let busqueda = form.dataset.busqueda;

        if (busqueda == "interna") {
            currentUrl = "/filtrar-reservas-internas";
        } else if (busqueda == "autorizar") {
            currentUrl = "/admin/filtrar-reservas-externas-autorizar";
        } else {
            currentUrl = "/filtrar-reservas-externas";
        }

        fetchReservas(1);
    });

    async function fetchReservas(page = 1) {
        filtrosActuales.page = page;

        try {
            const res = await fetch(currentUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
                body: JSON.stringify(filtrosActuales),
            });

            if (!res.ok) {
                throw new Error("Error HTTP: " + res.status);
            }

            const data = await res.json();

            mostrarResultado(data.data, data.ids ? data.ids : []);
            renderPaginacion(data.meta);

        } catch (err) {
            console.error(err);
        }
    }

    function vistaActual() {
        return window.matchMedia("(min-width: 768px)").matches ?
            "tabla" :
            "lista";
    }

    function mostrarResultado(reservas, ids = null) {
        const { permissions: PERMISSIONS, routes: ROUTES } =
        window.RESERVAS_CONFIG;

        let view = vistaActual();

        if (!reservas.length) {
            contenedor.innerHTML = "";
            contenedor_lista.innerHTML = "";
            contenedorGeneral.classList.add("md:hidden");
            contenedorGeneral.classList.remove("md:block");
            textoNoReservas.classList.add("block");
            textoNoReservas.classList.remove("hidden");
            textoNoReservas.innerHTML = `No hay resultados`;

            return;
        }

        contenedor.innerHTML = "";
        contenedor_lista.innerHTML = "";
        document.getElementById("contenedor-general").classList.add("md:block");
        document
            .getElementById("contenedor-general")
            .classList.remove("md:hidden");

        reservas.forEach((res) => {
            let fecha_inicio = new Date(res.fecha_inicio_reserva);
            let fechaInicioFormateada = fecha_inicio.toLocaleString("es-AR", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });

            let fecha_fin = new Date(res.fecha_fin_reserva);
            let fechaFinFormateada = fecha_fin.toLocaleString("es-AR", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });

            let acciones = "";
            if (PERMISSIONS.mostrarAcciones) {
                if (PERMISSIONS.ver) {
                    acciones += `
                        <a href="${ROUTES.ver.replace(":id", res.id)}"
                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                        title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    `;
                }

                if (window.APP_CONFIG.ubicacion == "interna" || ids.includes(res.id_dependencia_solicitante)) {

                    if (PERMISSIONS.editar) {
                        if (
                            res.estado_reserva.estado != "RECHAZADA" &&
                            res.estado_reserva.estado != "CANCELADA" &&
                            res.estado_reserva.estado != "FINALIZADA"
                        ) {
                            acciones += `
                        <a href="${ROUTES.editar.replace(":id", res.id)}"
                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                        title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;
                        }
                    }

                    if (PERMISSIONS.cancelar) {
                        if (
                            res.estado_reserva.estado != "RECHAZADA" &&
                            res.estado_reserva.estado != "CANCELADA" &&
                            res.estado_reserva.estado != "FINALIZADA"
                        ) {
                            acciones += `
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="${res.id}"
                                class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                title="Cancelar" >
                                <i class="fa fa-times"></i>
                            </button>
                    `;
                        }
                    }

                }
            } else {
                if (PERMISSIONS.ver) {
                    acciones += `
                        <a href="${ROUTES.ver.replace(":id", res.id)}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                title="Ver detalles">
                                <i class="fas fa-eye"></i>
                              </a>
                    `;
                }

                if (PERMISSIONS.autorizar) {
                    acciones += `
                                <button command="show-modal" commandfor="dialog-autorizar" data-id="${res.id}"
                                  class="btn-autorizar text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check"></i>
                              </button>
                        `;
                }
                if (PERMISSIONS.rechazar) {
                    acciones += `
                                <button command="show-modal" commandfor="dialog-rechazar" data-id="${res.id}"
                                            class="btn-rechazar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Rechazar préstamo" >
                                            <i class="fa fa-times"></i>
                              </button>
                              
                        `;
                }
            }

            if (view === "lista") {
                contenedor_lista.innerHTML += `
                <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <div>
                            <span class="font-semibold">Inicio de uso:</span>
                            ${fechaInicioFormateada}
                        </div>

                        <div>
                            <span class="font-semibold">Fin de uso:</span>
                            ${fechaFinFormateada}
                        </div>
                    </div>

                    <div class="mt-2 text-sm">
                        <span class="font-semibold">Estado:</span>
                        ${res.estado_reserva.estado}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Oficina solicitante:</span>
                        ${res.dependencia_solicitante.nombre}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Conductor:</span>
                        ${res.usuario.name} ${res.usuario.lastname}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Vehículo:</span>
                        ${res.vehiculo.dominio} ${res.vehiculo.marca} - ${res.vehiculo.anio}
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${acciones}
                    </div>
                </li>
                `;
            }

            if (view === "tabla") {
                contenedor.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${fechaInicioFormateada}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${fechaFinFormateada}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${res.estado_reserva.estado == 'APROBADA' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }
                            ${res.estado_reserva.estado == 'EN CURSO' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }
                            ${res.estado_reserva.estado == 'PENDIENTE' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }
                            ${res.estado_reserva.estado == 'CANCELADA' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200' : '' }
                            ${res.estado_reserva.estado == 'FINALIZADA' ? 'bg-gray-300 text-gray-900 dark:bg-gray-100 dark:text-gray-900' : '' }
                            ${res.estado_reserva.estado == 'RECHAZADA' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }
                        ">
                        ${res.estado_reserva.estado}</span>
                    </td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.dependencia_solicitante.nombre}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.usuario.name} ${res.usuario.lastname}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        ${res.vehiculo.dominio} ${res.vehiculo.marca} - ${res.vehiculo.anio}
                    </td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    <div class="flex justify-start gap-4">
                        ${acciones}
                    </div>
                    </td>
                </tr>
            `;
            }
        });
    }

    function renderPaginacion(meta) {
        const contenedor = document.getElementById("paginacion");
        contenedor.innerHTML = "";

        if (meta.current_page > 1) {
            contenedor.appendChild(crearBoton("‹", meta.current_page - 1));
        }

        for (let i = 1; i <= meta.last_page; i++) {
            const btn = crearBoton(i, i);

            if (i === meta.current_page) {
                btn.classList.add("activo");
                btn.disabled = true;
            }

            contenedor.appendChild(btn);
        }

        if (meta.current_page < meta.last_page) {
            contenedor.appendChild(crearBoton("›", meta.current_page + 1));
        }
    }

    function crearBoton(texto, page) {
        const btn = document.createElement("button");
        btn.textContent = texto;
        btn.onclick = () => fetchReservas(page);
        return btn;
    }

    let botonMostrarFiltros = document.getElementById("mostrarFiltros");

    if (botonMostrarFiltros) {
        botonMostrarFiltros.addEventListener("click", () => {
            let filtros = document.getElementById("filtros");
            if (filtros.classList.contains("hidden")) {
                filtros.classList.remove("hidden");
                botonMostrarFiltros.innerHTML = "Cerrar filtros";

                requestAnimationFrame(() => {
                    filtros.classList.remove("opacity-0", "-translate-y-4");
                    filtros.classList.add("opacity-100", "translate-y-0");
                });
            } else {
                filtros.classList.remove("opacity-100", "translate-y-0");
                filtros.classList.add("opacity-0", "-translate-y-4");

                setTimeout(() => {
                    filtros.classList.add("hidden");
                }, 300);
                botonMostrarFiltros.innerHTML = "Filtros";
            }
        });
    }

    // LIMPIAR FILTROS

    let botonLimpiar = document.getElementById("limpiarFiltros");

    if (botonLimpiar) {
        botonLimpiar.addEventListener("click", () => {
            // Limpiar inputs
            document
                .querySelectorAll(
                    "#formFiltrosReservas input, #formFiltrosReservas select",
                )
                .forEach((el) => {
                    if (el.tagName === "SELECT") {
                        el.value = "default";
                    } else {
                        el.value = "";
                    }
                });

            // Ocultar resultados JS
            document.getElementById("contenedor-js").style.display = "none";

            // Mostrar contenido del servidor
            document.querySelector(".contenedor-servidor").style.display = "flex";
            document
                .getElementById("contenedor-general")
                .classList.remove("md:hidden");

            // Limpiar contenedores JS

            document.getElementById("contenedor-reservas").innerHTML =
                htmlCopiaContenedorTabla;
            document.getElementById("contenedor-reservas-listas").innerHTML =
                htmlCopiaContenedorLista;

            // Ocultar mensaje “No hay reservas”
            textoNoReservas.classList.add("hidden");
            textoNoReservas.classList.remove("block");

            // Resetear paginación JS
            document.getElementById("paginacion").innerHTML = "";
        });
    }

    let botonExterno = document.getElementById("filtroPrestamoExterno");
    let botonInterno = document.getElementById("filtroPrestamoInterno");

    let botonTodas = document.getElementById("filtroPrestamoTodos");

    if (botonTodas) {
        botonTodas.addEventListener("click", () => {
            let busqueda = form.dataset.busqueda;

            if (busqueda == "interna") {
                currentUrl = "/filtrar-reservas-internas";
            } else {
                currentUrl = "/filtrar-reservas-externas";
            }

            fetchReservas(1);
        })
    }

    if (botonInterno) {
        botonInterno.addEventListener("click", () => {
            currentUrl = "/filtrar-prestamos-internos";
            filtrosActuales = {};
            fetchReservas(1);
        });
    }

    if (botonExterno) {
        botonExterno.addEventListener("click", () => {
            currentUrl = "/filtrar-prestamos-externos";
            filtrosActuales = {};
            fetchReservas(1);
        });
    }

});