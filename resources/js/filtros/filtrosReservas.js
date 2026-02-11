document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formFiltrosReservas");
    let filtros = {};
    let contenedor = document.getElementById("contenedor-reservas");
    let contenedor_lista = document.getElementById("contenedor-reservas-listas");

    const { permissions: PERMISSIONS, routes: ROUTES } = window.RESERVAS_CONFIG;
    let textoNoReservas = document.getElementById("mensajeNoHayReservas");
    let contenedorGeneral = document.getElementById("contenedor-general");

    let htmlCopiaContenedorTabla = contenedor.innerHTML;
    let htmlCopiaContenedorLista = contenedor_lista.innerHTML;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        document.getElementById('contenedor-js').style.display = 'block';
        document.querySelector('.contenedor-servidor').style.display = 'none';
                        
        textoNoReservas.classList.remove("block");
        textoNoReservas.classList.add("hidden");
        buscarReservas(1);
    });

    async function buscarReservas(page = 1) {
        filtros = {
            nombre: document.getElementById("nombre-filtro").value,
            fecha_inicio: document.getElementById("fecha-inicio").value,
            fecha_fin: document.getElementById("fecha-fin").value,
            estado: document.getElementById("estado-filtro").value,
            vehiculo: document.getElementById("vehiculo-filtro").value,
            page,
        };

        // Limpiar vacíos / default
        Object.keys(filtros).forEach((key) => {
            if (!filtros[key] || filtros[key] === "default") {
                delete filtros[key];
            }
        });
        let busqueda = form.dataset.busqueda;
        let url;
        if (busqueda == "interna") {
            url = "/filtrar-reservas-internas";
        }
        else if(busqueda == "autorizar"){
            url = "/admin/filtrar-reservas-externas-autorizar";
        } 
        else {
            url = "/filtrar-reservas-externas";
        }

        try {
            const res = await fetch(`${url}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
                body: JSON.stringify(filtros),
            });
            const data = await res.json();
            mostrarResultado(data.data);
            renderPaginacion(data);
        } catch (err) {
            console.error(err);
        }
    }

    function vistaActual() {
        return window.matchMedia("(min-width: 768px)").matches
            ? "tabla"
            : "lista";
    }

    function mostrarResultado(reservas) {
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
        document.getElementById("contenedor-general").classList.remove("md:hidden");

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
            if(PERMISSIONS.mostrarAcciones){
                if (PERMISSIONS.ver) {
                    acciones += `
                        <a href="${ROUTES.ver.replace(":id", res.id)}"
                        class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 
                        hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                        title="Ver detalles">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    `;
                }

                if (PERMISSIONS.editar) {
                    if (
                        res.estado_reserva.estado != "RECHAZADA" &&
                        res.estado_reserva.estado != "CANCELADA" &&
                        res.estado_reserva.estado != "FINALIZADA"
                    ) {
                        acciones += `
                        <a href="${ROUTES.editar.replace(":id", res.id)}"
                        class="m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 
                        hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                        title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
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
                                class="btn-cancelar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                                title="eliminar" >
                                <i class="fa fa-times"></i>
                            </button>
                    `;
                    }
                }
            }
            else{
                if(PERMISSIONS.ver){
                        acciones += `
                        <a href="${ROUTES.ver.replace(":id", res.id)}"
                                class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                title="Ver detalles">
                                <i class="fa-solid fa-eye"></i>
                              </a>
                    `;
                }
                
                if(PERMISSIONS.autorizar){
                    acciones += `
                              <form action="${ROUTES.autorizar.replace(":id", res.id)}" 
                                    method="post" 
                                    class="inline-block m-1">
                                    <input type="hidden" name="_token" value="${window.csrfToken}">
                                    <input type="hidden" name="_method" value="PATCH">
                                  <button type="submit"
                                      class="rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check text-green-600"></i>
                                  </button>
                              </form>
                        `;
                }

                if(PERMISSIONS.rechazar){
                    acciones += `
                              <form action="${ROUTES.rechazar.replace(":id", res.id)}" 
                                    method="post" 
                                    class="inline-block m-1">
                                    <input type="hidden" name="_token" value="${window.csrfToken}">
                                    <input type="hidden" name="_method" value="PATCH">
                                  <button type="submit"
                                      class="rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check text-green-600"></i>
                                  </button>
                              </form>
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
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${fechaInicioFormateada}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${fechaFinFormateada}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.estado_reserva.estado}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.dependencia_solicitante.nombre}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.usuario.name} ${res.usuario.lastname}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                        ${res.vehiculo.dominio} ${res.vehiculo.marca} - ${res.vehiculo.anio}
                    </td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${acciones}</td>
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
        btn.onclick = () => buscarReservas(page);
        return btn;
    }

    let botonMostrarFiltros = document.getElementById("mostrarFiltros");
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

    // LIMPIAR FILTROS
    
   let botonLimpiar = document.getElementById("limpiarFiltros");

botonLimpiar.addEventListener("click", () => {
    // Limpiar inputs
    document.querySelectorAll("#formFiltrosReservas input, #formFiltrosReservas select")
        .forEach(el => {
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
    document.getElementById("contenedor-general").classList.remove("md:hidden");

    // Limpiar contenedores JS

    document.getElementById("contenedor-reservas").innerHTML = htmlCopiaContenedorTabla;
    document.getElementById("contenedor-reservas-listas").innerHTML = htmlCopiaContenedorLista;

    // Ocultar mensaje “No hay reservas”
    textoNoReservas.classList.add("hidden");
    textoNoReservas.classList.remove("block");

    // Resetear paginación JS
    document.getElementById("paginacion").innerHTML = "";
}); 
});
