document.addEventListener('DOMContentLoaded', ()=>{
    const form = document.getElementById("formFiltrosDependencias");
    let filtros = {};
    let contenedor = document.getElementById("contenedor-dependencias");
    let contenedor_lista = document.getElementById("contenedor-dependencias-listas");


    let textoNoDependencias = document.getElementById("mensajeNoHayDependencias");
    let contenedorGeneral = document.getElementById("contenedor-general");

    let htmlCopiaContenedorTabla = contenedor.innerHTML;
    let htmlCopiaContenedorLista = contenedor_lista.innerHTML;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        document.getElementById('contenedor-js').style.display = 'block';
        document.querySelector('.contenedor-servidor').style.display = 'none';
                        
        textoNoDependencias.classList.remove("block");
        textoNoDependencias.classList.add("hidden");
        buscarDependencias(1);
    });

    async function buscarDependencias(page = 1) {
        filtros = {
            nombre: document.getElementById("nombre-filtro").value,
            ciudad: document.getElementById("ciudad-filtro").value,
            calle: document.getElementById("calle-filtro").value,
            activa: document.getElementById("activa-filtro").value,
            dependencia_padre: document.getElementById("id_dependencia_padre").value,
            page,
        };

        // Limpiar vacíos / default
        Object.keys(filtros).forEach((key) => {
            if (!filtros[key] || filtros[key] === "default") {
                delete filtros[key];
            }
        });
        

        try {
            const res = await fetch(`/dependencias/filtrar`, {
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

    function mostrarResultado(dependencias) {
        const { permissions: PERMISSIONS , routes: ROUTES} = window.DEPENDENCIAS_CONFIG;

        let view = vistaActual();

        if (!dependencias.length) {

            contenedor.innerHTML = "";
            contenedor_lista.innerHTML = "";
            contenedorGeneral.classList.add("md:hidden");
            contenedorGeneral.classList.remove("md:block");
            textoNoDependencias.classList.add("block");
            textoNoDependencias.classList.remove("hidden");
            textoNoDependencias.innerHTML = `No hay resultados`;
    
            return;
        }

        contenedor.innerHTML = "";
        contenedor_lista.innerHTML = "";

        contenedorGeneral.classList.add("md:block");
        contenedorGeneral.classList.remove("md:hidden");

        dependencias.forEach((res) => {

            let acciones = "";

            if (PERMISSIONS.ver) {
                acciones += `
                    <a href="${ROUTES.ver.replace(":id", res.id)}"
                     class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                `;
            }

            if (PERMISSIONS.editar) {
                    acciones += `
                    <a href="${ROUTES.editar.replace(":id", res.id)}"
                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                `;
            }

            if (PERMISSIONS.eliminar) {
                   acciones += `
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="${res.id}"
                            class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            title="eliminar" >
                            <i class="fas fa-trash"></i>
                        </button>
                `;
            }


            if (view === "lista") {

                let bloqueActiva;

                if (window.DEPENDENCIAS_CONFIG.puedeCambiarActiva) {
                    bloqueActiva = `
                        <div class="mt-3 flex">
                            <span class="font-semibold">Activa:</span>
                            <label class="relative inline-flex w-11 h-6 cursor-pointer items-center ml-2">
                                <input type="checkbox" 
                                    class="peer sr-only toggle-activa"
                                    ${res.activa ? 'checked' : ''}
                                    data-id="${res.id}"
                                    data-nombre="${res.nombre}">

                                <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>
                                <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                            </label>
                        </div>
                    `;
                } else {
                    bloqueActiva = `
                        <div class="mt-3 text-sm">
                            <span class="font-semibold">Activa:</span>
                            <span class="ml-2 font-semibold">
                                ${res.activa ? 'Sí' : 'No'}
                            </span>
                        </div>
                    `;
                }

                contenedor_lista.innerHTML += `
                <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">
                    
                        <div class="mt-2 text-sm">
                            <span class="font-semibold">Nombre:</span>
                            ${res.nombre}
                        </div>

                        <div class="mt-2 text-sm">
                            <span class="font-semibold">Calle:</span>
                            ${res.direccion.calle}
                        </div>


                    <div class="mt-2 text-sm">
                        <span class="font-semibold">Altura:</span>
                        ${res.direccion.altura}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Ciudad:</span>
                        ${res.direccion.ciudad}
                    </div>

                    ${bloqueActiva}
                    
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${acciones}
                    </div>
                </li>
                `;
            }

            if (view === "tabla") {
                let columnaActiva; 

                if (window.DEPENDENCIAS_CONFIG.puedeCambiarActiva) {
                    columnaActiva = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <label class="relative inline-flex w-11 h-6 cursor-pointer items-center">
                                <input type="checkbox" 
                                    class="peer sr-only toggle-activa"
                                    ${res.activa ? 'checked' : ''}
                                    data-id="${res.id}"
                                    data-nombre="${res.nombre}">

                                <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>
                                <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                            </label>
                        </td>
                    `;
                } else {
                    columnaActiva = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${ res.activa ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            ${ res.activa ? 'Activa' : 'Inactiva' }}
                            </span>

                        </td>
                    `;
                }
                contenedor.innerHTML += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.nombre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.direccion.calle}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.direccion.altura}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${res.direccion.ciudad}</td>
                    
                    ${columnaActiva}

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
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
        btn.onclick = () => buscarDependencias(page);
        return btn;
    }



    let botonMostrarFiltros = document.getElementById("mostrarFiltrosDependencia");

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
        document
            .querySelectorAll(
                "#formFiltrosDependencias input, #formFiltrosDependencias select",
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

        contenedorGeneral.classList.remove("md:hidden");


        // Limpiar contenedores JS

        document.getElementById("contenedor-dependencias").innerHTML = htmlCopiaContenedorTabla;

        //document.getElementById("contenedor-dependencias-listas").innerHTML =
        //    htmlCopiaContenedorLista;

        // Ocultar mensaje “No hay dependencias"
        textoNoDependencias.classList.add("hidden");
        textoNoDependencias.classList.remove("block");

        // Resetear paginación JS
        document.getElementById("paginacion").innerHTML = "";
    });



    document.addEventListener('change', function (e) {

    if (e.target.classList.contains('toggle-activa')) {

        const checkbox = e.target;
        const nuevoEstado = checkbox.checked;
        const nombre = checkbox.dataset.nombre;

        const dialog = document.getElementById('confirmDialog');
        const dialogText = document.getElementById('dialogText');

        dialogText.textContent = 
            nuevoEstado  ? `¿Querés activar la dependencia "${nombre}"?`
                : `¿Seguro que querés desactivar la dependencia "${nombre}"? No podrá ser utilizada pero en caso de tener dependencias hijas no podrá ser desactivada.`;

        dialog.showModal();

        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        
        //  Clonar botón para evitar múltiples listeners acumulados
        confirmBtn.replaceWith(confirmBtn.cloneNode(true));
        const newConfirmBtn = document.getElementById('confirmBtn');

        newConfirmBtn.addEventListener('click', () => {

            fetch(`/dependencias/${checkbox.dataset.id}/activa`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ activa: nuevoEstado })
            })
            .then(res => res.json())
            .then(data => {

                if (!data.ok) {
                    throw new Error(data.message);
                }

                alert(data.message);
                dialog.close();
            })
            .catch(err => {
                checkbox.checked = !nuevoEstado;
                alert(err.message);
                dialog.close();
            });
        });

        cancelBtn.onclick = () => {
            checkbox.checked = !nuevoEstado;
            dialog.close();
        };
    }
});

})