/**
 * simulador.js
 * Lee window.VIAJE_DATA inyectado por mapa-virtual.blade.php
 * Dibuja el mapa SVG, anima el vehículo y refleja el estado real del viaje.
 */

document.addEventListener('DOMContentLoaded', () => {
    const SVG_NS = 'http://www.w3.org/2000/svg';

    // ─── Configuración del mapa virtual ──────────────────────────────────────────

    const WAYPOINTS = [
        { x: 52, y: 240 },
        { x: 100, y: 200 },
        { x: 160, y: 175 },
        { x: 200, y: 140 },
        { x: 260, y: 120 },
        { x: 310, y: 88 },
        { x: 368, y: 52 },
    ];

    const VW = 420;
    const VH = 300;


    // ─── Estado interno ───────────────────────────────────────────────────────────

    //let animId = null;
    let startTs = null;
    let progress = 0;

    // ─── Helpers SVG ─────────────────────────────────────────────────────────────

    function mk(tag, attrs) {
        const el = document.createElementNS(SVG_NS, tag);
        Object.entries(attrs).forEach(([k, v]) => el.setAttribute(k, v));
        return el;
    }

    function pathLength(pts) {
        let d = 0;
        for (let i = 1; i < pts.length; i++) {
            const dx = pts[i].x - pts[i - 1].x;
            const dy = pts[i].y - pts[i - 1].y;
            d += Math.sqrt(dx * dx + dy * dy);
        }
        return d;
    }

    function ptAtProgress(pts, t) {
        const total = pathLength(pts);
        let target = t * total;
        let acc = 0;
        for (let i = 1; i < pts.length; i++) {
            const dx = pts[i].x - pts[i - 1].x;
            const dy = pts[i].y - pts[i - 1].y;
            const seg = Math.sqrt(dx * dx + dy * dy);
            if (acc + seg >= target) {
                const u = (target - acc) / seg;
                return {
                    x: pts[i - 1].x + dx * u,
                    y: pts[i - 1].y + dy * u,
                };
            }
            acc += seg;
        }
        return pts[pts.length - 1];
    }
    /*
    function ease(t) {
        return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
    }
    */
    // ─── Elementos SVG reutilizables ──────────────────────────────────────────────

    let vehicleEl = null;
    let vehicleDot = null;
    let vehicleRing = null;
    let trailEl = null;

    // ─── Dibujo base del mapa ─────────────────────────────────────────────────────

    function drawBase() {
        const svg = document.getElementById('map-svg');
        if (!svg) return;
        svg.innerHTML = '';

        // Fondo
        svg.appendChild(mk('rect', { x: 0, y: 0, width: VW, height: VH, fill: '#f8fafc' }));

        // Grilla
        for (let x = 0; x < VW; x += 30) {
            svg.appendChild(mk('line', { x1: x, y1: 0, x2: x, y2: VH, stroke: '#e2e8f0', 'stroke-width': '0.5' }));
        }
        for (let y = 0; y < VH; y += 30) {
            svg.appendChild(mk('line', { x1: 0, y1: y, x2: VW, y2: y, stroke: '#e2e8f0', 'stroke-width': '0.5' }));
        }

        // Calles de fondo
        const calles = [
            [0, 200, VW, 200],
            [0, 140, VW, 140],
            [0, 88, VW, 88],
            [100, 0, 100, VH],
            [200, 0, 200, VH],
            [310, 0, 310, VH],
        ];
        calles.forEach(([x1, y1, x2, y2]) => {
            svg.appendChild(mk('line', { x1, y1, x2, y2, stroke: '#e2e8f0', 'stroke-width': '7', 'stroke-linecap': 'round' }));
        });

        // Manzanas decorativas
        [
            [110, 148, 80, 48],
            [220, 148, 80, 48],
            [110, 96, 80, 44],
            [220, 96, 80, 44],
            [320, 100, 50, 40],
            [320, 208, 50, 40],
            [20, 148, 70, 48],
        ].forEach(([x, y, w, h]) => {
            svg.appendChild(mk('rect', { x, y, width: w, height: h, fill: '#f1f5f9', rx: '4' }));
        });

        // Ruta punteada (toda la ruta, de fondo)
        svg.appendChild(mk('polyline', {
            points: WAYPOINTS.map(p => `${p.x},${p.y}`).join(' '),
            fill: 'none',
            stroke: '#cbd5e1',
            'stroke-width': '2.5',
            'stroke-dasharray': '5,4',
            'stroke-linecap': 'round',
        }));

        // Trail (ruta recorrida — se actualiza en cada frame)
        trailEl = mk('polyline', {
            points: `${WAYPOINTS[0].x},${WAYPOINTS[0].y}`,
            fill: 'none',
            stroke: '#16a34a',
            'stroke-width': '3',
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
        });
        svg.appendChild(trailEl);

        // Punto origen
        svg.appendChild(mk('circle', { cx: WAYPOINTS[0].x, cy: WAYPOINTS[0].y, r: '7', fill: '#16a34a' }));
        const originLabel = mk('text', {
            x: WAYPOINTS[0].x,
            y: WAYPOINTS[0].y - 13,
            fill: '#166534',
            'font-size': '10',
            'text-anchor': 'middle',
            'font-family': 'sans-serif',
            'font-weight': '500',
        });
        originLabel.textContent = 'Origen';
        svg.appendChild(originLabel);

        // Punto destino
        const dest = WAYPOINTS[WAYPOINTS.length - 1];
        svg.appendChild(mk('circle', { cx: dest.x, cy: dest.y, r: '7', fill: '#dc2626' }));
        const destLabel = mk('text', {
            x: dest.x,
            y: dest.y - 13,
            fill: '#991b1b',
            'font-size': '10',
            'text-anchor': 'middle',
            'font-family': 'sans-serif',
            'font-weight': '500',
        });
        destLabel.textContent = 'Destino';
        svg.appendChild(destLabel);

        // Vehículo (anillo + círculo + punto)
        const ox = WAYPOINTS[0].x,
            oy = WAYPOINTS[0].y;
        vehicleRing = mk('circle', { cx: ox, cy: oy, r: '14', fill: 'none', stroke: '#16a34a', 'stroke-width': '1.5', opacity: '0' });
        vehicleEl = mk('circle', { cx: ox, cy: oy, r: '8', fill: '#fff', stroke: '#16a34a', 'stroke-width': '2.5' });
        vehicleDot = mk('circle', { cx: ox, cy: oy, r: '3.5', fill: '#16a34a' });
        svg.appendChild(vehicleRing);
        svg.appendChild(vehicleEl);
        svg.appendChild(vehicleDot);
    }

    // ─── Actualiza posición del vehículo ─────────────────────────────────────────

    function updateVehicle(pos, t) {
        if (!vehicleEl) return;

        vehicleEl.setAttribute('cx', pos.x);
        vehicleEl.setAttribute('cy', pos.y);
        vehicleDot.setAttribute('cx', pos.x);
        vehicleDot.setAttribute('cy', pos.y);
        vehicleRing.setAttribute('cx', pos.x);
        vehicleRing.setAttribute('cy', pos.y);

        // Pulso del anillo
        const pulse = (Math.sin(Date.now() / 400) + 1) / 2;
        vehicleRing.setAttribute('opacity', (0.15 + pulse * 0.25).toFixed(2));

        // Trail — línea verde que va dejando el vehículo
        const trailPts = [];
        for (let i = 0; i <= 24; i++) {
            const tp = (i / 24) * t;
            const p = ptAtProgress(WAYPOINTS, tp);
            trailPts.push(`${p.x.toFixed(1)},${p.y.toFixed(1)}`);
        }
        trailPts.push(`${pos.x.toFixed(1)},${pos.y.toFixed(1)}`);
        trailEl.setAttribute('points', trailPts.join(' '));

        // KM y barra de progreso
        //const km = (t * TOTAL_KM_SIMULADO).toFixed(1);
        const kmEl = document.getElementById('km-val');
        const bar = document.getElementById('progress-bar');
        if (kmEl) kmEl.textContent = km;
        if (bar) bar.style.width = (t * 100).toFixed(1) + '%';
    }

    // ─── Loop de animación ────────────────────────────────────────────────────────
    /*
    function frame(ts) {
        if (!startTs) startTs = ts;
        const raw = Math.min((ts - startTs) / DURATION, 1);
        progress = ease(raw);

        const pos = ptAtProgress(WAYPOINTS, progress);
        updateVehicle(pos, progress);

        if (raw < 1) {
            animId = requestAnimationFrame(frame);
        }
        // Al llegar al destino NO finalizamos automáticamente — el usuario lo hace con el form
    }
    */
    function setStatus(title, text, dotClass) {
        const titleEl = document.getElementById('status-title');
        const textEl = document.getElementById('status-text');
        const dot = document.getElementById('status-dot');
        if (titleEl) titleEl.textContent = title;
        if (textEl) textEl.textContent = text;
        if (dot) dot.className = dotClass;
    }

    // ─── Inicialización ───────────────────────────────────────────────────────────


    const data = window.VIAJE_DATA || {};
    const btnStart = document.getElementById('btn-start');
    const btnCancel = document.getElementById('btn-cancel');

    // Estado del viaje
    const noIniciado = !data.fechaInicio;
    const enCurso = data.fechaInicio && !data.fechaFin;
    const finalizado = data.fechaFin;
    drawBase();

    const kmEl = document.getElementById('km-val');


    if (kmEl) {

        if (data.kilometros_inicio && data.kilometros_fin) {
            const km = data.kilometros_fin - data.kilometros_inicio;


            kmEl.textContent = km.toFixed(1);
        } else {
            kmEl.textContent = '0.0';
        }
    }
    if (!btnStart || !btnCancel) return;

    if (noIniciado) {
        // VIAJE NO INICIADO
        btnStart.textContent = 'Comenzar viaje';

        btnStart.onclick = () => {
            window.location.href = `/operativo/viajes/${data.id}/comenzar`;
        };


        btnCancel.style.display = 'inline-block';

    } else if (enCurso) {
        //  VIAJE EN CURSO
        btnStart.textContent = 'Ir a finalizar viaje';
        btnStart.classList.add('bg-red-600');

        btnStart.onclick = () => {
            window.location.href = `/viajes/${data.id}`; // usa tu SHOW con formulario
        };

        btnCancel.style.display = 'none';

    } else if (finalizado) {
        // VIAJE FINALIZADO
        btnStart.textContent = 'Viaje finalizado';
        btnStart.disabled = true;

        btnCancel.style.display = 'none';
    } else {
        // ESTADO INICIAL (no iniciado o finalizado)
        btnStart.textContent = 'Comenzar viaje';

        btnStart.onclick = () => {
            window.location.href = `/operativo/viajes/${data.id}/comenzar`;
        };
    }


    if (finalizado) {
        // Mostrar vehículo en el destino directamente
        const dest = WAYPOINTS[WAYPOINTS.length - 1];
        updateVehicle(dest, 1);
        setStatus('Viaje finalizado', 'El recorrido fue completado', 'done');
    } else {
        // Iniciar animación automáticamente (el viaje ya está en curso del lado del server)
        setStatus('En camino', 'Viaje en progreso...', 'moving');
        //  animId = requestAnimationFrame(frame);
    }

});