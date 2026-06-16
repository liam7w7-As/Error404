import L from "leaflet";
import "leaflet-rotatedmarker";

L.Control.CompassCustom = L.Control.extend({
    options: {
        position: "topright",
    },

    onAdd(map) {
        const container = L.DomUtil.create(
            "div",
            "leaflet-bar leaflet-control leaflet-control-compass-custom"
        );

        // Botón principal
        const btn = L.DomUtil.create("a", "compass-btn", container);
        btn.href = "#";
        btn.title = "Restablecer al Norte";
        btn.setAttribute("role", "button");
        btn.setAttribute("aria-label", "Restablecer al Norte");

        // SVG de brújula
        btn.innerHTML = `
            <svg class="compass-icon" viewBox="0 0 24 24" width="22" height="22">
                <circle cx="12" cy="12" r="10" fill="none" stroke="#666" stroke-width="1.5"/>
                <polygon points="12,3 14,12 12,10 10,12" fill="#e74c3c"/>
                <polygon points="12,21 10,12 12,14 14,12" fill="#bdc3c7"/>
                <circle cx="12" cy="12" r="2" fill="#333"/>
                <text x="12" y="2.5" text-anchor="middle" font-size="3" font-weight="bold" fill="#e74c3c">N</text>
            </svg>
        `;

        // Estilos inline del botón
        btn.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            background: white;
            cursor: pointer;
            transition: transform 0.3s ease;
            border-radius: 4px;
        `;

        // Estado interno
        this._currentBearing = 0;
        this._btn = btn;
        this._compassIcon = btn.querySelector(".compass-icon");
        this._orientationSupported = false;
        this._orientationHandler = null;

        // Prevenir propagación de clicks al mapa
        L.DomEvent.disableClickPropagation(container);

        // Click: restablecer al norte
        L.DomEvent.on(btn, "click", (e) => {
            L.DomEvent.preventDefault(e);
            this._resetNorth(map);
        });

        // Intentar escuchar orientación del dispositivo
        this._initDeviceOrientation();

        return container;
    },

    onRemove() {
        if (this._orientationHandler) {
            window.removeEventListener(
                "deviceorientationabsolute",
                this._orientationHandler
            );
            window.removeEventListener(
                "deviceorientation",
                this._orientationHandler
            );
        }
    },

    /**
     * Inicializar escucha de orientación del dispositivo
     */
    _initDeviceOrientation() {
        this._orientationHandler = (event) => {
            let heading = null;

            if (event.webkitCompassHeading !== undefined) {
                // iOS
                heading = event.webkitCompassHeading;
            } else if (event.alpha !== null) {
                // Android / otros
                heading = 360 - event.alpha;
            }

            if (heading !== null) {
                this._orientationSupported = true;
                this._currentBearing = heading;
                this._updateCompassRotation(heading);
            }
        };

        // Intentar con absolute primero (más preciso)
        if (window.DeviceOrientationEvent) {
            window.addEventListener(
                "deviceorientationabsolute",
                this._orientationHandler,
                true
            );

            // Fallback a orientación normal
            window.addEventListener(
                "deviceorientation",
                this._orientationHandler,
                true
            );
        }
    },

    /**
     * Actualizar la rotación visual de la brújula
     */
    _updateCompassRotation(degrees) {
        if (this._compassIcon) {
            this._compassIcon.style.transform = `rotate(${-degrees}deg)`;
            this._compassIcon.style.transition = "transform 0.2s ease-out";
        }
    },

    /**
     * Restablecer vista al norte
     */
    _resetNorth(map) {
        // Resetear rotación visual
        this._updateCompassRotation(0);

        // Animación de "pulse" en el botón
        this._btn.style.transform = "scale(1.2)";
        setTimeout(() => {
            this._btn.style.transform = "scale(1)";
        }, 200);

        // Restablecer vista del mapa (centrar sin cambiar zoom)
        map.setView(map.getCenter(), map.getZoom(), {
            animate: true,
        });
    },
});

/**
 * Función helper para agregar el control de brújula a un mapa
 * @param {L.Map} map - Instancia del mapa Leaflet
 * @returns {L.Control.CompassCustom} - El control creado
 */
export function addCompassControl(map) {
    const compass = new L.Control.CompassCustom();
    map.addControl(compass);
    return compass;
}

/**
 * Re-exportar leaflet-rotatedmarker para que esté disponible
 * cuando se importe este módulo
 */
export default L;
