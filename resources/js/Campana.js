// Este archivo ya NO es necesario porque usamos Alpine.js en navbar.blade.php
// eliminarlo o dejarlo comentado

/*
document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notifBtn');
    const notifPopup = document.getElementById('notifPopup');
    
    if (!notifBtn || !notifPopup) {
        console.log('Elementos de notificación no encontrados');
        return;
    }

    notifBtn.addEventListener('click', async() => {
        notifPopup.classList.toggle('hidden');

        if (!notifPopup.classList.contains('hidden')) {
            try {
                const res = await fetch('/alertas/recientes');
                const alertas = await res.json();

                const list = document.getElementById('notifList');
                list.innerHTML = '';

                if (alertas.length === 0) {
                    list.innerHTML = '<p class="text-gray-500">Sin notificaciones</p>';
                    return;
                }

                alertas.forEach(a => {
                    list.innerHTML += `
                        <div class="border-b pb-2 mb-2">
                            <p class="font-medium">${a.titulo ?? 'Alerta'}</p>
                            <p class="text-sm text-gray-600">${a.mensaje}</p>
                        </div>
                    `;
                });
            } catch (error) {
                console.error('Error cargando alertas:', error);
            }
        }
    });
});
*/

console.log('✅ Campana.js cargado (Alpine.js maneja las notificaciones)');