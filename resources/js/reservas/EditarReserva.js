
    let enlaces = document.querySelectorAll(".btn-editar");

    enlaces.forEach(a => {
        const id = a.dataset.id;
        console.log(id);
        const url = window.RESERVAS_CONFIG.routes.editar.replace(':id', id);
        console.log(url);
        a.href = url;
    });

