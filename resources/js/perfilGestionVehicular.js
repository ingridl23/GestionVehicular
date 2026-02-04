// Configurar CSRF token para peticiones AJAX
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const form = document.querySelector(".profile-body");
const guardavidaId = parseInt(form.dataset.guardavidaId); // Esto te da el ID

let puedeEditar = window.puedeEditar === true;

// Función para mostrar alertas
function mostrarAlerta(tipo, titulo, mensaje) {
    const alertContainer = document.getElementById("alertContainer");
    const iconClass =
        tipo === "success" ? "fa-check-circle" : "fa-exclamation-circle";

    const alertHTML = `
                <div class="alert alert-${tipo}">
                    <i class="fas ${iconClass}"></i>
                    <div>
                        <strong>${titulo}</strong><br>
                        ${mensaje}
                    </div>
                </div>
            `;

    alertContainer.innerHTML = alertHTML;

    // Scroll suave hacia arriba
    window.scrollTo({ top: 0, behavior: "smooth" });

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        alertContainer.innerHTML = "";
    }, 10000);
}

console.log(puedeEditar);

// Manejar envío del formulario
if (puedeEditar) {
    document
        .querySelector(".profile-body")
        .addEventListener("submit", async function(e) {
            e.preventDefault();

            // VALIDACIÓN: si cambió la dependencia → detener envío
            const dependenciaSelect = document.getElementById("selectDependencia");

            console.log("EXISTE SELECT?", document.getElementById("selectDependencia"));

            if (dependenciaSelect.dataset.requiereNuevo === "1" && dependenciaSelect.value === "") {
                mostrarAlerta(
                    "error",
                    "Dependencia obligatorio",
                    "Debe seleccionar una eleccion correcta de la nueva dependencia."
                );
                return; // ← Bloquea el envío
            }

            const btnGuardar = document.getElementById("btnGuardar");
            const textoOriginal = btnGuardar.innerHTML;

            btnGuardar.innerHTML = '<span class="spinner"></span> Guardando...';
            btnGuardar.disabled = true;

            try {
                const formData = new FormData(this);
                formData.append("_method", "PUT"); // necesario para Laravel PUT

                const response = await fetch(this.action, {
                    method: "POST", // Laravel interpreta _method como PUT
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    mostrarAlerta("success", result.titulo, result.detalle);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta("error", result.titulo, result.detalle);
                }
            } catch (error) {
                console.error("Error:", error);
                mostrarAlerta(
                    "error",
                    "¡Error!",
                    "Ocurrió un error al actualizar el perfil. Por favor, intente nuevamente."
                );
            } finally {
                btnGuardar.innerHTML = textoOriginal;
                btnGuardar.disabled = false;
            }
        });
}
