document.addEventListener('DOMContentLoaded', ()=>{
    const form = document.getElementById('formFiltros');
    let filtros = {};

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const contenedores = document.getElementsByClassName('contenedor-servidor');

        for (let i = 0; i < contenedores.length; i++) {
            contenedores[i].style.display = 'none';
        }

        document.getElementById('contenedor-js').style.display = 'block';

        buscarDependencias(1);
    });

    async function buscarDependencias(page = 1){
        let dependencia_padre = document.getElementById("dependencias-filtros").value;
        filtros = {
            nombre: document.getElementById("nombre-filtro").value,
            dependencia_padre: dependencia_padre,
            activa: document.getElementById("activa-filtro").value,
            localidad: document.getElementById("localidad-filtro").value,
            page
        };


         // Limpiar vacíos / default
        Object.keys(filtros).forEach(key => {
            if (!filtros[key] || filtros[key] === 'default') {
                delete filtros[key];
            }
        });

        try {
            const res = await fetch("api/filtrar-dependencias", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    //Authorization: `Bearer ${user.token}`,
                },
                body: JSON.stringify(filtros),
            });
            const data = await res.json();
            
            mostrarResultado(data.data , dependencia_padre );
            renderPaginacion(data);
            
            //return data.data;
        } catch (err) {
            console.error(err);
        }
    }

    function mostrarResultado(dependencias , id_dependencia_padre) {
        const contenedor = document.getElementById('lista-dependencias');
        let esElPadre = false;
        contenedor.innerHTML = '';

        if (!dependencias.length) {
            contenedor.innerHTML = '<p>No hay resultados</p>';
            return;
        }

        dependencias.forEach(dep => {
            if(dep.id == id_dependencia_padre){
                esElPadre = true;
            }
            contenedor.innerHTML += `<p>${dep.nombre}</p>`;
        });

        if(dependencias.length == 1 && esElPadre){
            contenedor.innerHTML += `<p>¡No tiene dependencias hijas!</p>`;
        }
    }

    function renderPaginacion(meta) {
         const contenedor = document.getElementById('paginacion');
        contenedor.innerHTML = '';

        if (meta.current_page > 1) {
            contenedor.appendChild(crearBoton('«', meta.current_page - 1));
        }

        for (let i = 1; i <= meta.last_page; i++) {
            const btn = crearBoton(i, i);
            if (i === meta.current_page) {
                btn.classList.add('activo');
            }
            contenedor.appendChild(btn);
        }

        if (meta.current_page < meta.last_page) {
            contenedor.appendChild(crearBoton('»', meta.current_page + 1));
        }
    }


    function crearBoton(texto, page) {
        const btn = document.createElement('button');
        btn.textContent = texto;
        btn.onclick = () => buscarDependencias(page);
        return btn;
    }

    function cambiarEstadoDependencias(){
        let dependencias = document.querySelectorAll('.form-check-input');
        for (let i = 0; i < dependencias.length; i++) {
            const dep = dependencias[i];
            dep.addEventListener("change", async () =>{
                let id = dep.dataset.id;
                try{
                    const res = await fetch(`api/cambiar-estado/${id}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept":"application/json",
                        },
                    });
                if (!res.ok) {
                    throw new Error('Error al cambiar estado');
                }
                
                }
                catch(e){
                    console.error(e);
                }
                }
            )
            
        }
    };

    cambiarEstadoDependencias();

})