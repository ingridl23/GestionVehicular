document.addEventListener('DOMContentLoaded', ()=>{
    const form = document.getElementById("formFiltrosDependencias");
    let filtros = {};
    let contenedor = document.getElementById("contenedor-depedencias");
    //let contenedor_lista = document.getElementById("contenedor-reservas-listas");


    let textoNoDependencias = document.getElementById("mensajeNoHayDependencias");
    let contenedorGeneral = document.getElementById("contenedor-general");

    let htmlCopiaContenedorTabla = contenedor.innerHTML;
    //let htmlCopiaContenedorLista = contenedor_lista.innerHTML;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        document.getElementById('contenedor-js').style.display = 'block';
        document.querySelector('.contenedor-servidor').style.display = 'none';
                        
        textoNoDependencias.classList.remove("block");
        textoNoDependencias.classList.add("hidden");
        buscarReservas(1);
    });

    async function buscarReservas(page = 1) {
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
            const res = await fetch(`/admin/dependencias/filtrar`, {
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
        const { permissions: PERMISSIONS , routes: ROUTES} = window.RESERVAS_CONFIG;

        let view = vistaActual();

        if (!dependencias.length) {

            contenedor.innerHTML = "";
            //contenedor_lista.innerHTML = "";
            contenedorGeneral.classList.add("md:hidden");
            contenedorGeneral.classList.remove("md:block");
            textoNoDependencias.classList.add("block");
            textoNoDependencias.classList.remove("hidden");
            textoNoDependencias.innerHTML = `No hay resultados`;
    
            return;
        }

        contenedor.innerHTML = "";
        //contenedor_lista.innerHTML = "";
        document.getElementById("contenedor-general").classList.add("md:block");
        document.getElementById("contenedor-general").classList.remove("md:hidden");

        dependencias.forEach((res) => {

            let acciones = "";

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
                    acciones += `
                    <a href="${ROUTES.editar.replace(":id", res.id)}"
                    class="m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 
                    hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                    title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                `;
            }

            if (PERMISSIONS.eliminar) {
                   acciones += `
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="${res.id}"
                            class="btn-cancelar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                            title="eliminar" >
                            <i class="fa fa-times"></i>
                        </button>
                `;
            }


            // if (view === "lista") {

            //     contenedor_lista.innerHTML += `
            //     <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">
            //         <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-200">
            //             <div>
            //                 <span class="font-semibold">Inicio de uso:</span>
            //                 ${fechaInicioFormateada}
            //             </div>

            //             <div>
            //                 <span class="font-semibold">Fin de uso:</span>
            //                 ${fechaFinFormateada}
            //             </div>
            //         </div>

            //         <div class="mt-2 text-sm">
            //             <span class="font-semibold">Estado:</span>
            //             ${res.estado_reserva.estado}
            //         </div>

            //         <div class="mt-1 text-sm">
            //             <span class="font-semibold">Oficina solicitante:</span>
            //             ${res.dependencia_solicitante.nombre}
            //         </div>

            //         <div class="mt-1 text-sm">
            //             <span class="font-semibold">Conductor:</span>
            //             ${res.usuario.name} ${res.usuario.lastname}
            //         </div>

            //         <div class="mt-1 text-sm">
            //             <span class="font-semibold">Vehículo:</span>
            //             ${res.vehiculo.dominio} ${res.vehiculo.marca} - ${res.vehiculo.anio}
            //         </div>
                    
            //         <div class="mt-4 flex flex-wrap gap-2">
            //             ${acciones}
            //         </div>
            //     </li>
            //     `;
            // }

            if (view === "tabla") {
                contenedor.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.nombre}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.direccion.calle}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.direccion.altura}</td>
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">${res.direccion.ciudad}</td>
                    
                    
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center">
                      <label class="relative inline-flex w-11 h-6 cursor-pointer items-center">
                          <input type="checkbox" class="peer sr-only" ${res.activa ? 'checked' : ''}>

                          <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600">
                          </span>

                          <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5">
                          </span>
                      </label>
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

    // function cambiarEstadoDependencias(){
    //     let dependencias = document.querySelectorAll('.form-check-input');
    //     for (let i = 0; i < dependencias.length; i++) {
    //         const dep = dependencias[i];
    //         dep.addEventListener("change", async () =>{
    //             let id = dep.dataset.id;
    //             try{
    //                 const res = await fetch(`api/cambiar-estado/${id}`, {
    //                     method: "POST",
    //                     headers: {
    //                         "Content-Type": "application/json",
    //                         "Accept":"application/json",
    //                     },
    //                 });
    //             if (!res.ok) {
    //                 throw new Error('Error al cambiar estado');
    //             }
                
    //             }
    //             catch(e){
    //                 console.error(e);
    //             }
    //             }
    //         )
            
    //     }
    // };

    // cambiarEstadoDependencias();


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

})