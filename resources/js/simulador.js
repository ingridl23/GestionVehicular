const svg = document.getElementById('map-svg');

const WAYPOINTS = [
    { x: 52, y: 240 },
    { x: 100, y: 200 },
    { x: 200, y: 140 },
    { x: 368, y: 52 }
];

let animId = null;
let startTs = null;

const DURATION = 5000;

function drawBase() {
    svg.innerHTML = '';

    const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    rect.setAttribute("width", "420");
    rect.setAttribute("height", "300");
    rect.setAttribute("fill", "#f8fafc");

    svg.appendChild(rect);
}

function setStatus(title, text) {
    document.getElementById('status-title').textContent = title;
    document.getElementById('status-text').textContent = text;
}

function frame(ts) {
    if (!startTs) startTs = ts;

    const progress = Math.min((ts - startTs) / DURATION, 1);

    document.getElementById('km-val').textContent = (progress * 8.4).toFixed(1);

    if (progress < 1) {
        animId = requestAnimationFrame(frame);
    } else {
        finishTrip();
    }
}

function startTrip() {
    startTs = null;

    document.getElementById('btn-start').disabled = true;
    document.getElementById('btn-cancel').style.display = 'block';

    setStatus('En camino', 'Viaje en progreso...');
    animId = requestAnimationFrame(frame);
}

function finishTrip() {
    cancelAnimationFrame(animId);

    document.getElementById('btn-start').disabled = false;
    document.getElementById('btn-cancel').style.display = 'none';

    setStatus('Finalizado', 'Viaje terminado');
}

function cancelTrip() {
    cancelAnimationFrame(animId);

    document.getElementById('btn-start').disabled = false;
    document.getElementById('btn-cancel').style.display = 'none';

    setStatus('Cancelado', 'Viaje cancelado');
}

// EVENTOS (clave para Vite)
document.addEventListener('DOMContentLoaded', () => {
    drawBase();

    document.getElementById('btn-start').addEventListener('click', startTrip);
    document.getElementById('btn-cancel').addEventListener('click', cancelTrip);
});
