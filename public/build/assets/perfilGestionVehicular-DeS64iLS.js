const l=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),d=document.querySelector(".profile-body");parseInt(d.dataset.guardavidaId);let i=window.puedeEditar===!0;function c(r,o,e){const a=document.getElementById("alertContainer"),s=`
                <div class="alert alert-${r}">
                    <i class="fas ${r==="success"?"fa-check-circle":"fa-exclamation-circle"}"></i>
                    <div>
                        <strong>${o}</strong><br>
                        ${e}
                    </div>
                </div>
            `;a.innerHTML=s,window.scrollTo({top:0,behavior:"smooth"}),setTimeout(()=>{a.innerHTML=""},1e4)}console.log(i);i&&document.querySelector(".profile-body").addEventListener("submit",async function(r){r.preventDefault();const o=document.getElementById("selectDependencia");if(console.log("EXISTE SELECT?",document.getElementById("selectDependencia")),o.dataset.requiereNuevo==="1"&&o.value===""){c("error","Dependencia obligatorio","Debe seleccionar una eleccion correcta de la nueva dependencia.");return}const e=document.getElementById("btnGuardar"),a=e.innerHTML;e.innerHTML='<span class="spinner"></span> Guardando...',e.disabled=!0;try{const t=new FormData(this);t.append("_method","PUT");const n=await(await fetch(this.action,{method:"POST",headers:{"X-CSRF-TOKEN":l,"X-Requested-With":"XMLHttpRequest"},body:t})).json();n.success?(c("success",n.titulo,n.detalle),setTimeout(()=>location.reload(),2e3)):c("error",n.titulo,n.detalle)}catch(t){console.error("Error:",t),c("error","¡Error!","Ocurrió un error al actualizar el perfil. Por favor, intente nuevamente.")}finally{e.innerHTML=a,e.disabled=!1}});
