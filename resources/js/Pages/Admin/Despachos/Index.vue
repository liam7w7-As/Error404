<script setup>
import { useFiltros } from "@/composables/useFiltros";
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeMount, watch } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const miTable = ref(null);
// headers removidos ya que no se usa MiTable

const today = new Date();
const current_date = today.getFullYear() + "-" + String(today.getMonth() + 1).padStart(2, "0") + "-" + String(today.getDate()).padStart(2, "0");

const getSessionDate = (key, defaultDate) => {
    return sessionStorage.getItem(key) || defaultDate;
};

const multiSearch = useFiltros('despachos', {
    search: "",
    producto_id: "",
    cliente_id: "",
    fecha_ini: current_date,
    fecha_fin: current_date,
    filtro: [],
});

watch(
    () => multiSearch.value,
    (newVal) => {
        updateDatatable();
    },
    { deep: true }
);

const listDespachos = ref([]);
const totalResultados = ref(0);
const loading = ref(false);

const cargarDatos = () => {
    loading.value = true;
    axios.get(route('despachos.paginado'), {
        params: {
            ...multiSearch.value,
            perPage: -1,
            page: 1,
            orderBy: 'id',
            orderAsc: 'DESC'
        }
    }).then(res => {
        listDespachos.value = res.data.data;
        totalResultados.value = res.data.total;
        loading.value = false;
    }).catch(() => { loading.value = false; });
};

const updateDatatable = async () => {
    cargarDatos();
    selected_ids.value = [];
    select_all.value = false;
    limpiarDespacho();
    muestra_formulario.value = false;
};

const selected_ids = ref([]);
const select_all = ref(false);

const toggleSelectAll = () => {
    if (select_all.value) {
        selected_ids.value = listDespachos.value.map(item => item.id);
    } else {
        selected_ids.value = [];
    }
};

const imprimirNotas = () => {
    if (selected_ids.value.length === 0) {
        Swal.fire({ title: "Advertencia", text: "Debe seleccionar al menos un pedido", icon: "warning" });
        return;
    }
    const url = route('pedidos.pdf_notas_seleccionadas', { ids: selected_ids.value.join(',') });
    window.open(url, '_blank');
};

const imprimirReporte = () => {
    if (selected_ids.value.length === 0) {
        Swal.fire({ title: "Advertencia", text: "Debe seleccionar al menos un pedido", icon: "warning" });
        return;
    }
    const url = route('despachos.pdf_seleccionados', { ids: selected_ids.value.join(',') });
    window.open(url, '_blank');
};

const imprimirReporteCliente = () => {
    let url = route('despachos.pdf_seleccionados_cliente');
    if (selected_ids.value.length > 0) {
        url += "?ids=" + selected_ids.value.join(',');
    } else {
        url += "?fecha_ini=" + multiSearch.value.fecha_ini +
               "&fecha_fin=" + multiSearch.value.fecha_fin +
               "&cliente_id=" + (multiSearch.value.cliente_id || '') +
               "&producto_id=" + (multiSearch.value.producto_id || '') +
               "&search=" + (multiSearch.value.search || '');
    }
    window.open(url, '_blank');
};

const eliminarDespacho = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>${item.despacho_id}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "bg-danger",
            cancelButton: "bg-light text-dark border border-secondary",
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("despachos.destroy", item.despacho_id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

const listClientes = ref([]);
const listProductos = ref([]);

const cargarClientes = () => {
    axios.get(route("clientes.listado")).then((respuesta) => {
        listClientes.value = respuesta.data.clientes;
    });
};
const cargarProductos = () => {
    axios.get(route("productos.listado")).then((respuesta) => {
        listProductos.value = respuesta.data.productos;
    });
};

const muestra_formulario = ref(false);
const limpiarDespacho = () => {
    // vaciar estado si es necesario
};

onBeforeMount(() => {
    cargarClientes();
    cargarProductos();
});

onMounted(async () => {
    cargarDatos();
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Despachos"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-clipboard-check"></i> Despachos
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>

                        <li class="breadcrumb-item active">Despachos</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <Link
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'despachos.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm me-2 mb-2"
                            :href="route('despachos.create')"
                        >
                            <i class="fa fa-plus"></i> Nuevo Despacho
                        </Link>
                        <a
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'despachos.create',
                                )
                            "
                            :href="route('pedidos.pdf_preparacion_general')"
                            target="_blank"
                            class="btn btn-info text-sm mb-2 text-white"
                        >
                            <i class="fa fa-file-pdf"></i> Reporte General de Preparación
                        </a>
                    </div>
                    <div class="col-md-9 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <el-select
                                    v-model="multiSearch.producto_id"
                                    placeholder="Producto"
                                    clearable
                                    filterable
                                    no-data-text="Sin datos"
                                    no-match-text="Sin resultados"
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                            </div>
                            <div class="col-md-3">
                                <el-select
                                    v-model="multiSearch.cliente_id"
                                    placeholder="Cliente"
                                    clearable
                                    filterable
                                    no-data-text="Sin datos"
                                    no-match-text="Sin resultados"
                                >
                                    <el-option
                                        v-for="item in listClientes"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" v-model="multiSearch.fecha_ini" title="Fecha Inicio">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" v-model="multiSearch.fecha_fin" title="Fecha Fin">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-12 d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-secondary btn-sm" @click="imprimirNotas"><i class="fa fa-print"></i> Imprimir Notas de Venta</button>
                        <button type="button" class="btn btn-secondary btn-sm" @click="imprimirReporte"><i class="fa fa-file-pdf"></i> Imprimir Reporte Despacho por Producto</button>
                        <button type="button" class="btn btn-secondary btn-sm" @click="imprimirReporteCliente"><i class="fa fa-file-pdf"></i> Imprimir Reporte Despacho por Cliente</button>
                        <div class="form-check ms-3">
                            <input class="form-check-input" type="checkbox" id="selectAll" v-model="select_all" @change="toggleSelectAll">
                            <label class="form-check-label font-weight-bold" for="selectAll">Seleccionar Todos</label>
                        </div>
                    </div>
                </div>

                <div class="row" v-if="loading">
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 fw-bold text-muted">Cargando despachos...</p>
                    </div>
                </div>
                <div class="row" v-else-if="listDespachos.length === 0">
                    <div class="col-12 text-center py-5 bg-light rounded border border-light shadow-sm">
                        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted fw-bold mb-0">No se encontraron resultados para los filtros aplicados.</p>
                    </div>
                </div>
                <div class="row custom-cards-container mt-2" v-else>
                    <div class="col-12 col-md-6 col-lg-4 mb-3" v-for="item in listDespachos" :key="item.id">
                        <div class="card h-100 shadow-sm custom-card-pedido border-0">
                            <!-- Header -->
                            <div class="card-header text-white p-2 d-flex justify-content-between align-items-center rounded-top" style="background-color: #2c3e50;">
                                <h6 class="mb-0 fw-bold m-0" style="font-size: 0.95rem;">Pedido # {{ item.id }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input m-0 custom-checkbox" :value="item.id" v-model="selected_ids" title="Seleccionar">
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-white p-0 m-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1;">
                                            <i class="fa fa-ellipsis-v px-2" style="font-size: 1.1rem;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <Link class="dropdown-item" :href="route('despachos.ver', item.despacho_id)">
                                                    <i class="fa fa-eye text-primary"></i> Ver Detalle
                                                </Link>
                                            </li>
                                            <li v-if="props_page.auth?.user.permisos == '*' || props_page.auth?.user.permisos.includes('despachos.destroy')">
                                                <button class="dropdown-item" @click="eliminarDespacho(item)">
                                                    <i class="fa fa-trash text-danger"></i> Eliminar
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
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
                                            {{ item.cliente?.direccion || item.segmentacion_zona?.zona }}
                                        </small>
                                        <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
                                            Telf: {{ item.cliente?.fono || 0 }}
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>

<style scoped>
:deep(.compact-table td), :deep(.compact-table th) {
    padding: 4px !important;
    vertical-align: middle;
}
:deep(.compact-table th) {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}
</style>
