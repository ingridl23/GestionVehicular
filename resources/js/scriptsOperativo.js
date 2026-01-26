const userBtn = document.getElementById('userBtn');
const userMenu = document.getElementById('userMenu');

const notifBtn = document.getElementById('notifBtn');
const notifPopup = document.getElementById('notifPopup');

userBtn.addEventListener('click', () => {
    userMenu.classList.toggle('hidden');
    notifPopup.classList.add('hidden');
});

notifBtn.addEventListener('click', () => {
    notifPopup.classList.toggle('hidden');
    userMenu.classList.add('hidden');
});

document.addEventListener('click', (e) => {
    if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.add('hidden');
    }
    if (!notifBtn.contains(e.target) && !notifPopup.contains(e.target)) {
        notifPopup.classList.add('hidden');
    }
});


const menuOperativoBtn = document.getElementById('menuOperativoBtn');
const menuOperativo = document.getElementById('menuOperativo');

menuOperativoBtn.addEventListener('click', () => {
    menuOperativo.classList.toggle('hidden');

    // cerrar otros menús si están abiertos
    userMenu.classList.add('hidden');
    notifPopup.classList.add('hidden');
});