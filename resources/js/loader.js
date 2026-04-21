"use script"

document.addEventListener("DOMContentLoaded", () => {
    const loader = document.querySelector(".contenedor_loader");
    if (!loader) return;

    // Mostrar loader al inicio
    loader.style.opacity = 1;
    loader.style.visibility = 'visible';
    console.log("loader ejecutado");
    // Ocultar después de que cargue todo
    setTimeout(() => {
        loader.style.opacity = 0;
        loader.style.visibility = 'hidden';
    }, 500); // podés ajustar el tiempo




})
