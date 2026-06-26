document.addEventListener("DOMContentLoaded",()=>{const c=document.getElementById("formFiltrosReservas");let m=document.getElementById("contenedor-reservas"),u=document.getElementById("contenedor-reservas-listas"),o=null,n={};const{permissions:$,routes:T}=window.RESERVAS_CONFIG;let i=document.getElementById("mensajeNoHayReservas"),v=document.getElementById("contenedor-general"),w=m.innerHTML,k=u.innerHTML;if(!c)return;c.addEventListener("submit",t=>{t.preventDefault(),document.getElementById("contenedor-js").style.display="block",document.querySelector(".contenedor-servidor").style.display="none",i.classList.remove("block"),i.classList.add("hidden"),n={nombre:document.getElementById("nombre-filtro").value,fecha_inicio:document.getElementById("fecha-inicio").value,fecha_fin:document.getElementById("fecha-fin").value,estado:document.getElementById("estado-filtro").value,vehiculo:document.getElementById("vehiculo-filtro").value},Object.keys(n).forEach(a=>{(!n[a]||n[a]==="default")&&delete n[a]});let r=c.dataset.busqueda;r=="interna"?o="/filtrar-reservas-internas":r=="autorizar"?o="/admin/filtrar-reservas-externas-autorizar":o="/filtrar-reservas-externas",l(1)});async function l(t=1){n.page=t;try{const r=await fetch(o,{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":window.csrfToken},body:JSON.stringify(n)});if(!r.ok)throw new Error("Error HTTP: "+r.status);const a=await r.json();A(a.data,a.ids?a.ids:[]),_(a.meta)}catch(r){console.error(r)}}function I(){return window.matchMedia("(min-width: 768px)").matches?"tabla":"lista"}function A(t,r=null){const{permissions:a,routes:d}=window.RESERVAS_CONFIG;let b=I();if(!t.length){m.innerHTML="",u.innerHTML="",v.classList.add("md:hidden"),v.classList.remove("md:block"),i.classList.add("block"),i.classList.remove("hidden"),i.innerHTML="No hay resultados";return}m.innerHTML="",u.innerHTML="",document.getElementById("contenedor-general").classList.add("md:block"),document.getElementById("contenedor-general").classList.remove("md:hidden"),t.forEach(e=>{let E=new Date(e.fecha_inicio_reserva).toLocaleString("es-AR",{year:"numeric",month:"2-digit",day:"2-digit",hour:"2-digit",minute:"2-digit",hour12:!1}),L=new Date(e.fecha_fin_reserva).toLocaleString("es-AR",{year:"numeric",month:"2-digit",day:"2-digit",hour:"2-digit",minute:"2-digit",hour12:!1}),s="";a.mostrarAcciones?(a.ver&&(s+=`
                        <a href="${d.ver.replace(":id",e.id)}"
                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                        title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    `),(window.APP_CONFIG.ubicacion=="interna"||r.includes(e.id_dependencia_solicitante))&&(a.editar&&e.estado_reserva.estado!="RECHAZADA"&&e.estado_reserva.estado!="CANCELADA"&&e.estado_reserva.estado!="FINALIZADA"&&(s+=`
                        <a href="${d.editar.replace(":id",e.id)}"
                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                        title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    `),a.cancelar&&e.estado_reserva.estado!="RECHAZADA"&&e.estado_reserva.estado!="CANCELADA"&&e.estado_reserva.estado!="FINALIZADA"&&(s+=`
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="${e.id}"
                                class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                title="Cancelar" >
                                <i class="fa fa-times"></i>
                            </button>
                    `))):(a.ver&&(s+=`
                        <a href="${d.ver.replace(":id",e.id)}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                title="Ver detalles">
                                <i class="fas fa-eye"></i>
                              </a>
                    `),a.autorizar&&(s+=`
                                <button command="show-modal" commandfor="dialog-autorizar" data-id="${e.id}"
                                  class="btn-autorizar text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check"></i>
                              </button>
                        `),a.rechazar&&(s+=`
                                <button command="show-modal" commandfor="dialog-rechazar" data-id="${e.id}"
                                            class="btn-rechazar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Rechazar préstamo" >
                                            <i class="fa fa-times"></i>
                              </button>
                              
                        `)),b==="lista"&&(u.innerHTML+=`
                <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <div>
                            <span class="font-semibold">Inicio de uso:</span>
                            ${E}
                        </div>

                        <div>
                            <span class="font-semibold">Fin de uso:</span>
                            ${L}
                        </div>
                    </div>

                    <div class="mt-2 text-sm">
                        <span class="font-semibold">Estado:</span>
                        ${e.estado_reserva.estado}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Oficina solicitante:</span>
                        ${e.dependencia_solicitante.nombre}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Conductor:</span>
                        ${e.usuario.name} ${e.usuario.lastname}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Vehículo:</span>
                        ${e.vehiculo.dominio} ${e.vehiculo.marca} - ${e.vehiculo.anio}
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${s}
                    </div>
                </li>
                `),b==="tabla"&&(m.innerHTML+=`
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${E}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${L}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${e.estado_reserva.estado=="APROBADA"?"bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":""}
                            ${e.estado_reserva.estado=="EN CURSO"?"bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":""}
                            ${e.estado_reserva.estado=="PENDIENTE"?"bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":""}
                            ${e.estado_reserva.estado=="CANCELADA"?"bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200":""}
                            ${e.estado_reserva.estado=="FINALIZADA"?"bg-gray-300 text-gray-900 dark:bg-gray-100 dark:text-gray-900":""}
                            ${e.estado_reserva.estado=="RECHAZADA"?"bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":""}
                        ">
                        ${e.estado_reserva.estado}</span>
                    </td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${e.dependencia_solicitante.nombre}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">${e.usuario.name} ${e.usuario.lastname}</td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        ${e.vehiculo.dominio} ${e.vehiculo.marca} - ${e.vehiculo.anio}
                    </td>
                    <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    <div class="flex justify-start gap-4">
                        ${s}
                    </div>
                    </td>
                </tr>
            `)})}function _(t){const r=document.getElementById("paginacion");r.innerHTML="",t.current_page>1&&r.appendChild(g("‹",t.current_page-1));for(let a=1;a<=t.last_page;a++){const d=g(a,a);a===t.current_page&&(d.classList.add("activo"),d.disabled=!0),r.appendChild(d)}t.current_page<t.last_page&&r.appendChild(g("›",t.current_page+1))}function g(t,r){const a=document.createElement("button");return a.textContent=t,a.onclick=()=>l(r),a}let f=document.getElementById("mostrarFiltros");f&&f.addEventListener("click",()=>{let t=document.getElementById("filtros");t.classList.contains("hidden")?(t.classList.remove("hidden"),f.innerHTML="Cerrar filtros",requestAnimationFrame(()=>{t.classList.remove("opacity-0","-translate-y-4"),t.classList.add("opacity-100","translate-y-0")})):(t.classList.remove("opacity-100","translate-y-0"),t.classList.add("opacity-0","-translate-y-4"),setTimeout(()=>{t.classList.add("hidden")},300),f.innerHTML="Filtros")});let p=document.getElementById("limpiarFiltros");p&&p.addEventListener("click",()=>{document.querySelectorAll("#formFiltrosReservas input, #formFiltrosReservas select").forEach(t=>{t.tagName==="SELECT"?t.value="default":t.value=""}),document.getElementById("contenedor-js").style.display="none",document.querySelector(".contenedor-servidor").style.display="flex",document.getElementById("contenedor-general").classList.remove("md:hidden"),document.getElementById("contenedor-reservas").innerHTML=w,document.getElementById("contenedor-reservas-listas").innerHTML=k,i.classList.add("hidden"),i.classList.remove("block"),document.getElementById("paginacion").innerHTML=""});let h=document.getElementById("filtroPrestamoExterno"),y=document.getElementById("filtroPrestamoInterno"),x=document.getElementById("filtroPrestamoTodos");x&&x.addEventListener("click",()=>{c.dataset.busqueda=="interna"?o="/filtrar-reservas-internas":o="/filtrar-reservas-externas",l(1)}),y&&y.addEventListener("click",()=>{o="/filtrar-prestamos-internos",n={},l(1)}),h&&h.addEventListener("click",()=>{o="/filtrar-prestamos-externos",n={},l(1)})});
