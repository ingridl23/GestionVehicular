document.addEventListener("DOMContentLoaded",()=>{const y=document.getElementById("formFiltrosDependencias");let l={},m=document.getElementById("contenedor-dependencias"),p=document.getElementById("contenedor-dependencias-listas"),i=document.getElementById("mensajeNoHayDependencias"),r=document.getElementById("contenedor-general"),b=m.innerHTML;p.innerHTML,y.addEventListener("submit",e=>{e.preventDefault(),document.getElementById("contenedor-js").style.display="block",document.querySelector(".contenedor-servidor").style.display="none",i.classList.remove("block"),i.classList.add("hidden"),f(1)});async function f(e=1){l={nombre:document.getElementById("nombre-filtro").value,ciudad:document.getElementById("ciudad-filtro").value,calle:document.getElementById("calle-filtro").value,activa:document.getElementById("activa-filtro").value,dependencia_padre:document.getElementById("id_dependencia_padre").value,page:e},Object.keys(l).forEach(a=>{(!l[a]||l[a]==="default")&&delete l[a]});try{const n=await(await fetch("/dependencias/filtrar",{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":window.csrfToken},body:JSON.stringify(l)})).json();v(n.data),x(n)}catch(a){console.error(a)}}function h(){return window.matchMedia("(min-width: 768px)").matches?"tabla":"lista"}function v(e){const{permissions:a,routes:n}=window.DEPENDENCIAS_CONFIG;let d=h();if(!e.length){m.innerHTML="",p.innerHTML="",r.classList.add("md:hidden"),r.classList.remove("md:block"),i.classList.add("block"),i.classList.remove("hidden"),i.innerHTML="No hay resultados";return}m.innerHTML="",p.innerHTML="",r.classList.add("md:block"),r.classList.remove("md:hidden"),e.forEach(t=>{let o="";if(a.ver&&(o+=`
                    <a href="${n.ver.replace(":id",t.id)}"
                     class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                `),a.editar&&(o+=`
                    <a href="${n.editar.replace(":id",t.id)}"
                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                `),a.eliminar&&(o+=`
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="${t.id}"
                            class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            title="eliminar" >
                            <i class="fas fa-trash"></i>
                        </button>
                `),d==="lista"){let s;window.DEPENDENCIAS_CONFIG.puedeCambiarActiva?s=`
                        <div class="mt-3 flex">
                            <span class="font-semibold">Activa:</span>
                            <label class="relative inline-flex w-11 h-6 cursor-pointer items-center ml-2">
                                <input type="checkbox" 
                                    class="peer sr-only toggle-activa"
                                    ${t.activa?"checked":""}
                                    data-id="${t.id}"
                                    data-nombre="${t.nombre}">

                                <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>
                                <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                            </label>
                        </div>
                    `:s=`
                        <div class="mt-3 text-sm">
                            <span class="font-semibold">Activa:</span>
                            <span class="ml-2 font-semibold">
                                ${t.activa?"Sí":"No"}
                            </span>
                        </div>
                    `,p.innerHTML+=`
                <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">
                    
                        <div class="mt-2 text-sm">
                            <span class="font-semibold">Nombre:</span>
                            ${t.nombre}
                        </div>

                        <div class="mt-2 text-sm">
                            <span class="font-semibold">Calle:</span>
                            ${t.direccion.calle}
                        </div>


                    <div class="mt-2 text-sm">
                        <span class="font-semibold">Altura:</span>
                        ${t.direccion.altura}
                    </div>

                    <div class="mt-1 text-sm">
                        <span class="font-semibold">Ciudad:</span>
                        ${t.direccion.ciudad}
                    </div>

                    ${s}
                    
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${o}
                    </div>
                </li>
                `}if(d==="tabla"){let s;window.DEPENDENCIAS_CONFIG.puedeCambiarActiva?s=`
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <label class="relative inline-flex w-11 h-6 cursor-pointer items-center">
                                <input type="checkbox" 
                                    class="peer sr-only toggle-activa"
                                    ${t.activa?"checked":""}
                                    data-id="${t.id}"
                                    data-nombre="${t.nombre}">

                                <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>
                                <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                            </label>
                        </td>
                    `:s=`
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${t.activa?"bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":"bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"}}">
                            ${t.activa?"Activa":"Inactiva"}}
                            </span>

                        </td>
                    `,m.innerHTML+=`
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${t.nombre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${t.direccion.calle}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${t.direccion.altura}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${t.direccion.ciudad}</td>
                    
                    ${s}

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        <div class="flex justify-start gap-4">
                            ${o}
                        </div>
                    </td>
                </tr>
            `}})}function x(e){const a=document.getElementById("paginacion");a.innerHTML="",e.current_page>1&&a.appendChild(u("‹",e.current_page-1));for(let n=1;n<=e.last_page;n++){const d=u(n,n);n===e.current_page&&(d.classList.add("activo"),d.disabled=!0),a.appendChild(d)}e.current_page<e.last_page&&a.appendChild(u("›",e.current_page+1))}function u(e,a){const n=document.createElement("button");return n.textContent=e,n.onclick=()=>f(a),n}let g=document.getElementById("mostrarFiltrosDependencia");g.addEventListener("click",()=>{let e=document.getElementById("filtros");e.classList.contains("hidden")?(e.classList.remove("hidden"),g.innerHTML="Cerrar filtros",requestAnimationFrame(()=>{e.classList.remove("opacity-0","-translate-y-4"),e.classList.add("opacity-100","translate-y-0")})):(e.classList.remove("opacity-100","translate-y-0"),e.classList.add("opacity-0","-translate-y-4"),setTimeout(()=>{e.classList.add("hidden")},300),g.innerHTML="Filtros")}),document.getElementById("limpiarFiltros").addEventListener("click",()=>{document.querySelectorAll("#formFiltrosDependencias input, #formFiltrosDependencias select").forEach(e=>{e.tagName==="SELECT"?e.value="default":e.value=""}),document.getElementById("contenedor-js").style.display="none",document.querySelector(".contenedor-servidor").style.display="flex",r.classList.remove("md:hidden"),document.getElementById("contenedor-dependencias").innerHTML=b,i.classList.add("hidden"),i.classList.remove("block"),document.getElementById("paginacion").innerHTML=""}),document.addEventListener("change",function(e){if(e.target.classList.contains("toggle-activa")){const a=e.target,n=a.checked,d=a.dataset.nombre,t=document.getElementById("confirmDialog"),o=document.getElementById("dialogText");o.textContent=n?`¿Querés activar la dependencia "${d}"?`:`¿Seguro que querés desactivar la dependencia "${d}"? No podrá ser utilizada pero en caso de tener dependencias hijas no podrá ser desactivada.`,t.showModal();const s=document.getElementById("confirmBtn"),w=document.getElementById("cancelBtn");s.replaceWith(s.cloneNode(!0)),document.getElementById("confirmBtn").addEventListener("click",()=>{fetch(`/dependencias/${a.dataset.id}/activa`,{method:"PATCH",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":window.csrfToken},body:JSON.stringify({activa:n})}).then(c=>c.json()).then(c=>{if(!c.ok)throw new Error(c.message);alert(c.message),t.close()}).catch(c=>{a.checked=!n,alert(c.message),t.close()})}),w.onclick=()=>{a.checked=!n,t.close()}}})});
