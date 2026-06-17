<script setup>
import { onMounted, ref, watch } from "vue";
import MapZonasClientes from "@/Components/MapZonasClientes.vue";
import { usePage } from "@inertiajs/vue3";

const { auth } = usePage().props;
const user = ref(auth.user);

const props = defineProps({
    zonas: {
        type: Array,
        default: () => [],
    },
});

const isFullscreen = ref(false);
const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
    // Forzar re-render del mapa para evitar que Leaflet se rompa al cambiar de tamaño
    setTimeout(() => {
        mapKey.value++;
    }, 100);
};

const fechaFiltro = ref(new Date().toISOString().split('T')[0]);
const filtros = ref({
    pendientes: true,
    despachados: true,
    entregados: true,
    clientes: false,
});

const mapData = ref({
    pendientes: [],
    despachados: [],
    entregados: [],
    clientes_todos: []
});

const cargaMapa = ref(false);
const marcadores = ref([]);
const mapKey = ref(0);

const cargarDatosMapa = () => {
    cargaMapa.value = false;
    axios.get(route('clientes.mapa_operativo'), {
        params: { fecha: fechaFiltro.value }
    }).then(response => {
        mapData.value = response.data;
        actualizarMarcadores();
    }).finally(() => {
        cargaMapa.value = true;
    });
};

const actualizarMarcadores = () => {
    let arr = [];
    
    if (user.value.tipo !== 'VENDEDOR') {
        if (filtros.value.pendientes) {
            arr = arr.concat(mapData.value.pendientes.map(c => ({...c, color_pin: 'red', popup_text: 'PEDIDO PENDIENTE'})));
        }
        if (filtros.value.despachados) {
            arr = arr.concat(mapData.value.despachados.map(c => ({...c, color_pin: 'orange', popup_text: 'PEDIDO DESPACHADO'})));
        }
        if (filtros.value.entregados) {
            arr = arr.concat(mapData.value.entregados.map(c => ({...c, color_pin: 'green', popup_text: 'PEDIDO ENTREGADO'})));
        }
    }
    
    if (filtros.value.clientes || user.value.tipo === 'VENDEDOR') {
        arr = arr.concat(mapData.value.clientes_todos.map(c => ({...c, color_pin: 'blue', popup_text: 'CLIENTE'})));
    }

    // Filtrar duplicados (priorizando pedidos sobre clientes)
    const unicos = [];
    const mapIds = new Set();
    arr.forEach(item => {
        if (!mapIds.has(item.id)) {
            mapIds.add(item.id);
            unicos.push(item);
        }
    });

    marcadores.value = unicos;
    mapKey.value++; // Forzar re-render del mapa
};

watch([filtros, fechaFiltro], () => {
    if (cargaMapa.value) {
        if (fechaFiltro.value) {
            cargarDatosMapa(); // Recargar si cambia la fecha
        } else {
            actualizarMarcadores(); // Solo actualizar visibilidad si cambia el filtro
        }
    }
}, { deep: true });

onMounted(() => {
    if(user.value.tipo === 'VENDEDOR'){
        filtros.value.clientes = true;
    }
    cargarDatosMapa();

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isFullscreen.value) {
            toggleFullscreen();
        }
    });
});

</script>

<template>
    <div :class="{'mapa-fullscreen-container': isFullscreen, 'mapa-card-container': !isFullscreen}">
        <div class="card w-100 h-100 mb-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="m-0"><i class="fa fa-map-marked-alt"></i> Mapa Operativo General</h5>
                <button v-if="!isFullscreen" class="btn btn-sm btn-light" @click="toggleFullscreen">
                    <i class="fa fa-expand"></i> Pantalla Completa
                </button>
            </div>
            
            <!-- Botón Flotante para Salir de Pantalla Completa -->
            <button v-if="isFullscreen" class="btn btn-danger btn-cerrar-fullscreen shadow-lg" @click.stop.prevent="toggleFullscreen" style="pointer-events: auto;">
                <i class="fa fa-times fa-lg"></i> <span class="d-none d-md-inline ms-1">Cerrar Pantalla Completa (Esc)</span>
            </button>
            
            <div class="card-body p-0 position-relative h-100 d-flex flex-column">
                
                <!-- Controles Flotantes -->
                <div class="map-controls bg-white p-2 border-bottom shadow-sm">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="form-group mb-0 me-3">
                            <label class="text-xs mb-0 fw-bold">Fecha de Operación</label>
                            <input type="date" class="form-control form-control-sm" v-model="fechaFiltro" @change="cargarDatosMapa">
                        </div>

                        <div v-if="user.tipo !== 'VENDEDOR'" class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm" :class="filtros.pendientes ? 'btn-danger' : 'btn-outline-danger'" @click="filtros.pendientes = !filtros.pendientes">
                                <i class="fa fa-map-marker-alt"></i> Pendientes ({{ mapData.pendientes.length }})
                            </button>
                            <button class="btn btn-sm" :class="filtros.despachados ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'" @click="filtros.despachados = !filtros.despachados">
                                <i class="fa fa-map-marker-alt"></i> Despachados ({{ mapData.despachados.length }})
                            </button>
                            <button class="btn btn-sm" :class="filtros.entregados ? 'btn-success' : 'btn-outline-success'" @click="filtros.entregados = !filtros.entregados">
                                <i class="fa fa-map-marker-alt"></i> Entregados ({{ mapData.entregados.length }})
                            </button>
                        </div>
                        
                        <button class="btn btn-sm" :class="filtros.clientes ? 'btn-primary' : 'btn-outline-primary'" @click="filtros.clientes = !filtros.clientes">
                            <i class="fa fa-users"></i> Clientes ({{ mapData.clientes_todos.length }})
                        </button>
                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div class="flex-grow-1 position-relative">
                    <MapZonasClientes
                        v-if="cargaMapa"
                        :key="mapKey"
                        :clientes="marcadores"
                        :zonas="zonas"
                        :class="{'map-expanded': isFullscreen}"
                    ></MapZonasClientes>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.mapa-fullscreen-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 1050; /* Bootstrap modal is 1050 usually */
    background: white;
}

.mapa-card-container {
    width: 100%;
}

.map-controls {
    z-index: 1000;
}

.map-expanded :deep(.mapa) {
    height: 100vh !important;
}

:deep(.mapa) {
    height: 60vh;
    width: 100%;
}

.btn-cerrar-fullscreen {
    position: fixed;
    top: 15px;
    right: 15px;
    z-index: 999999 !important;
    border-radius: 50px;
    padding: 10px 15px;
    font-weight: bold;
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .btn-cerrar-fullscreen {
        top: 10px;
        right: 10px;
        padding: 12px 18px;
    }
}
</style>
