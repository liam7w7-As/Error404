<script setup>
import { useFiltros } from "@/composables/useFiltros";
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { usePedidos } from "@/composables/pedidos/usePedidos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import MiPaginacion from "@/Components/MiPaginacion.vue";
import axios from "axios";

const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setPedido, limpiarPedido, form } = usePedidos();

const listDistribucions = ref([]);
const total = ref(0);
const loading = ref(false);

const getFechaActual = () => {
    const fecha = new Date();
    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const anio = fecha.getFullYear();
    return `${anio}-${mes}-${dia}`;
};

const multiSearch = useFiltros('distribucions', {
    search: "",
    filtro: [],
    estado: 'PENDIENTES',
    fecha_ini: getFechaActual(),
    fecha_fin: getFechaActual(),
    page: 1,
    perPage: 15,
});

const cambiarEstado = (nuevoEstado) => {
    multiSearch.value.estado = nuevoEstado;
    updateDatatable();
};

const updateDatatable = () => {
    multiSearch.value.page = 1;
    cargarDistribuciones();
};

const cambioDePagina = (page) => {
    multiSearch.value.page = page;
    cargarDistribuciones();
};

const cargarDistribuciones = () => {
    loading.value = true;
    axios.get(route("distribucions.paginado"), {
        params: multiSearch.value
    }).then(res => {
        listDistribucions.value = res.data.data;
        total.value = res.data.total;
    }).finally(() => {
        loading.value = false;
        limpiarPedido();
    });
};

const anularPedido = (item) => {
    Swal.fire({
        title: "¿Quierés anular este registro?",
        html: `<strong>${item.cliente?.nombre} | Código: ${item.id}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, anular",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "bg-danger",
            cancelButton: "bg-light text-dark border border-secondary",
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("distribucions.anular", item.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

const abrirAcciones = (item) => {
    Swal.fire({
        title: `Acciones Operativas`,
        html: `
            <div class="text-start mb-3">
                <strong>Pedido:</strong> #${item.id}<br/>
                <strong>Cliente:</strong> ${item.cliente?.nombre}<br/>
            </div>
            <div class="d-flex flex-column gap-2">
                <a href="${route('distribucions.create', { pedido_id: item.id })}" class="btn btn-primary btn-lg w-100 shadow-sm text-white text-decoration-none">
                    <i class="fa fa-truck me-2"></i> Entregar / Modificar
                </a>
                <button type="button" class="btn btn-outline-danger btn-lg w-100 shadow-sm" id="btn-rechazar-swal">
                    <i class="fa fa-ban me-2"></i> Rechazar / Anular
                </button>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "Cerrar",
        didOpen: () => {
            const btnRechazar = document.getElementById('btn-rechazar-swal');
            if (btnRechazar) {
                btnRechazar.addEventListener('click', () => {
                    Swal.close();
                    anularPedido(item);
                });
            }
        }
    });
};

onMounted(async () => {
    cargarDistribuciones();
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Pedidos por Entregar"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-truck"></i> Pedidos por Entregar
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>

                        <li class="breadcrumb-item active">
                            Pedidos por Entregar
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-12">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <Link
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'distribucions.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            :href="route('distribucions.create')"
                        >
                            <i class="fa fa-plus"></i> Nueva Entrega de Pedido
                        </Link>
                        <a
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'pedidos.pdf_pendientes',
                                )
                            "
                            type="button"
                            class="btn btn-outline-primary text-sm ms-1"
                            :href="route('pedidos.pdf_pendientes')"
                            target="_blank"
                        >
                            <i class="fa fa-print"></i> Tickets Pendientes
                        </a>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <label class="mb-0 fw-bold text-muted small">Desde:</label>
                            <input type="date" class="form-control form-control-sm" style="width: 140px" v-model="multiSearch.fecha_ini" @change="updateDatatable" title="Fecha Inicio">
                            <label class="mb-0 fw-bold text-muted small">Hasta:</label>
                            <input type="date" class="form-control form-control-sm" style="width: 140px" v-model="multiSearch.fecha_fin" @change="updateDatatable" title="Fecha Fin">
                        </div>
                    </div>
                </div>

                <div class="row mb-3 mt-3">
                    <div class="col-12">
                        <div class="btn-group w-100 shadow-sm" role="group">
                            <button 
                                type="button" 
                                class="btn py-2 fw-bold" 
                                :class="multiSearch.estado == 'PENDIENTES' ? 'btn-primary' : 'btn-outline-primary bg-white'"
                                @click="cambiarEstado('PENDIENTES')"
                            >
                                <i class="fa fa-clock me-1"></i> Pedidos Pendientes
                            </button>
                            <button 
                                type="button" 
                                class="btn py-2 fw-bold" 
                                :class="multiSearch.estado == 'ENTREGADOS' ? 'btn-success' : 'btn-outline-success bg-white'"
                                @click="cambiarEstado('ENTREGADOS')"
                            >
                                <i class="fa fa-check-circle me-1"></i> Pedidos Entregados
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row" v-if="loading">
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 fw-bold text-muted">Cargando pedidos...</p>
                    </div>
                </div>
                <div class="row" v-else-if="listDistribucions.length === 0">
                    <div class="col-12 text-center py-5 bg-light rounded border border-light shadow-sm">
                        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted fw-bold mb-0">No se encontraron resultados.</p>
                    </div>
                </div>
                <div class="row custom-cards-container mt-2" v-else>
                    <div class="col-12 col-md-6 col-lg-4 mb-3" v-for="item in listDistribucions" :key="item.id">
                        <div class="card h-100 shadow-sm custom-card-pedido border-0">
                            <!-- Header -->
                            <div class="card-header text-white p-2 d-flex justify-content-between align-items-center rounded-top" :class="item.estado == 'ENTREGADO' ? 'bg-success' : 'bg-primary'">
                                <h6 class="mb-0 fw-bold m-0" style="font-size: 0.95rem;">Pedido # {{ item.id }}</h6>
                                <div class="badge bg-white px-2 py-1 shadow-sm" :class="item.estado == 'ENTREGADO' ? 'text-success' : 'text-primary'">
                                    <i :class="item.estado == 'ENTREGADO' ? 'fa fa-check-circle' : 'fa fa-clock'"></i> {{ item.estado }}
                                </div>
                            </div>
                            <!-- Body -->
                            <div class="card-body p-2 position-relative bg-white rounded-bottom" style="border: 1px solid #e9ecef; border-top: none;">
                                <div class="row m-0">
                                    <div class="col-7 p-0">
                                        <h6 class="fw-bold mb-1 text-uppercase text-dark" style="font-size: 0.85rem; line-height: 1.2;">
                                            <i class="fa fa-user text-muted me-1"></i> {{ item.cliente?.nombre }}
                                        </h6>
                                        <small class="text-muted d-block lh-1 text-uppercase mb-1" style="font-size: 0.75rem;">
                                            <i class="fa fa-map-marker-alt"></i> {{ item.segmentacion_zona?.zona || 'Sin Zona' }}
                                        </small>
                                        <small class="text-muted d-block mt-1 fw-bold text-uppercase" style="font-size: 0.75rem;" v-if="item.user?.full_name">
                                            Vend: {{ item.user?.full_name }}
                                        </small>
                                    </div>
                                    <div class="col-5 p-0 text-end d-flex flex-column justify-content-between align-items-end">
                                        <small class="d-flex flex-column align-items-end text-muted mb-2 lh-1" style="font-size: 0.75rem;">
                                            <span><i class="fa fa-calendar-alt me-1"></i> {{ item.fecha_t }}</span>
                                            <span class="mt-1">{{ item.hora }}</span>
                                        </small>
                                        <span class="badge text-dark px-2 py-1 fw-bold w-100 rounded-pill mt-auto shadow-sm" style="background-color: #f39c12; font-size: 0.85rem;">
                                            {{ $formatMoney(item.total) }} Bs
                                        </span>
                                    </div>
                                </div>
                                <hr class="my-2 text-muted">
                                <!-- Footer / Actions -->
                                <div class="mt-2 w-100">
                                    <template v-if="item.estado == 'PENDIENTE' || item.estado == 'DESPACHADO'">
                                        <button class="btn btn-primary btn-sm fw-bold shadow-sm w-100 py-2" @click="abrirAcciones(item)">
                                            <i class="fa fa-bolt me-1"></i> Accionar
                                        </button>
                                    </template>
                                    <template v-else-if="item.estado == 'ENTREGADO' && (props_page.auth?.user.permisos == '*' || props_page.auth?.user.permisos.includes('pedidos.pdf'))">
                                        <a class="btn btn-outline-success btn-sm fw-bold w-100 py-2" :href="route('pedidos.pdf', item.id)" target="_blank">
                                            <i class="fa fa-print me-1"></i> Imprimir Ticket
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3" v-if="!loading && total > 0">
                    <div class="col-12">
                        <MiPaginacion
                            :current-page="multiSearch.page"
                            :total-data="total"
                            :per-page="multiSearch.perPage"
                            @updatePage="cambioDePagina"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>

<style scoped>
.custom-card-pedido {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.custom-card-pedido:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
