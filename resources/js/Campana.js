notifBtn.addEventListener('click', async() => {
    notifPopup.classList.toggle('hidden');
    userMenu.classList.add('hidden');

    if (!notifPopup.classList.contains('hidden')) {
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
                <div class="border-b pb-1">
                    <p class="font-medium">${a.titulo ?? 'Alerta'}</p>
                    <p class="text-gray-600">${a.mensaje}</p>
                </div>
            `;
        });
    }

    const notifDot = document.getElementById('notifDot');

    if (alertas.length > 0) {
        notifDot.classList.remove('hidden');
    } else {
        notifDot.classList.add('hidden');
    }

});
