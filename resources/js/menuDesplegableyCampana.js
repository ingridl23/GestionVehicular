const sideMenu = document.getElementById("sideMenu");
const menuBtn = document.getElementById("menuBtn");

const notifBtn = document.getElementById("notifBtn");
const notifPopup = document.getElementById("notifPopup");

const userBtn = document.getElementById("userBtn");
const userMenu = document.getElementById("userMenu");

// MENU LATERAL
menuBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    sideMenu.style.transform =
        sideMenu.style.transform === "translateX(0px)" ?
        "translateX(-100%)" :
        "translateX(0px)";
});

// CAMPANA
notifBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    notifPopup.classList.toggle("hidden");
    userMenu.classList.add("hidden");
});

// USUARIO
userBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    userMenu.classList.toggle("hidden");
    notifPopup.classList.add("hidden");
});

// CERRAR AL HACER CLICK FUERA
document.addEventListener("click", () => {
    sideMenu.style.transform = "translateX(-100%)";
    notifPopup.classList.add("hidden");
    userMenu.classList.add("hidden");
});