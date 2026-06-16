<script setup>
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { usePedidos } from "@/composables/pedidos/usePedidos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import axios from "axios";
import MiPaginacion from "@/Components/MiPaginacion.vue";
import { useFiltros } from "@/composables/useFiltros";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setPedido, limpiarPedido, form } = usePedidos();

const miTable = ref(null);

const getFechaActual = () => {
    const fecha = new Date();
    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const anio = fecha.getFullYear();
    return `${anio}-${mes}-${dia}`;
};
const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarPedido();
    }
};

const listPedidos = ref([]);
const total = ref(0);
const page = ref(1);
const totalPendientes = ref(0);
const totalBsPendientes = ref(0);

const multiSearch = useFiltros('pedidos', {
    fecha_ini: getFechaActual(),
    fecha_fin: getFechaActual(),
    filtro: [],
    page: page.value,
    perPage: 12,
});

const cargarPedidos = () => {
    axios
        .get(route("pedidos.paginado"), {
            params: multiSearch.value,
        })
        .then((response) => {
            console.log(response.data);
            listPedidos.value = response.data.data;
            total.value = response.data.total;
            totalPendientes.value = response.data.total_pendientes ?? 0;
            totalBsPendientes.value = response.data.total_bs_pendientes ?? 0;
        });
};

// detectar cambio de pagina
const cambioDePagina = async (value) => {
    multiSearch.value.page = value;
    cargarPedidos();
};

const eliminarPedido = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>#${item.id}</strong>`,
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
                route("pedidos.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                cargarPedidos();
            }
        }
    });
};

const anularPedido = (item) => {
    Swal.fire({
        title: "¿Quierés anular este pedido?",
        html: `Al anularlo se liberará el stock reservado.<br/><strong>Pedido #${item.id}</strong>`,
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
                route("pedidos.anular", item.id),
            );
            if (respuesta && respuesta.sw) {
                cargarPedidos();
            }
        }
    });
};

onMounted(async () => {
    cargarPedidos();
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Pedidos"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-clipboard-check"></i> Pedidos
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>

                        <li class="breadcrumb-item active">Pedidos</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <Link
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'pedidos.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            :href="route('pedidos.create')"
                        >
                            <i class="fa fa-plus"></i> Nuevo Pedido
                        </Link>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="text-xs">Desde</label>
                                        <div
                                            class="input-group"
                                            style="align-items: end"
                                        >
                                            <input
                                                type="date"
                                                v-model="multiSearch.fecha_ini"
                                                class="form-control border-1"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-xs">Hasta</label>
                                        <div
                                            class="input-group"
                                            style="align-items: end"
                                        >
                                            <input
                                                type="date"
                                                v-model="multiSearch.fecha_fin"
                                                class="form-control border-1"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-12">
                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            @click="cargarPedidos"
                                        >
                                            Cargar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- INDICADORES DE RESUMEN -->
                <div class="row mb-3 mt-2">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                            <div class="card-body py-2 px-3 text-white">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-xs" style="opacity: 0.85;">Pedidos Pendientes</p>
                                        <h4 class="mb-0 fw-bold">{{ totalPendientes }}</h4>
                                    </div>
                                    <i class="fa fa-clock fa-2x" style="opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                            <div class="card-body py-2 px-3 text-white">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-xs" style="opacity: 0.85;">Total Bs Pendientes</p>
                                        <h4 class="mb-0 fw-bold">{{ Number(totalBsPendientes).toFixed(2) }} Bs</h4>
                                    </div>
                                    <i class="fa fa-money-bill-wave fa-2x" style="opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <template v-if="total > 0">
                        <div
                            class="col-md-4 mt-2"
                            v-for="item in listPedidos"
                            :key="item.id"
                        >
                            <div class="card">
                                <div class="card-header bg-principal">
                                    <h4 class="mb-0 text-white text-lg fw-bold">
                                        Pedido # {{ item.id }}
                                        <span class="badge bg-warning text-dark float-end text-sm" v-if="item.estado == 'DESPACHADO'">DESPACHADO</span>
                                    </h4>
                                </div>
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1">
                                                <strong>{{
                                                    item.cliente.nombre
                                                }}</strong>
                                            </p>
                                            <p class="mb-1">
                                                <span>{{
                                                    item.segmentacion_zona.zona
                                                }}</span>
                                            </p>
                                            <p class="mb-1">
                                                <span>
                                                    {{
                                                        item.cliente?.fono
                                                    }}</span
                                                >
                                            </p>
                                            <!-- <p class="mb-1">
                                                <span>
                                                    {{
                                                        item.cliente
                                                            ?.tipo_negocio
                                                            ?.nombre
                                                    }}</span
                                                >
                                            </p> -->
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-1 text-sm">
                                                <i
                                                    class="fa fa-calendar-alt me-2"
                                                ></i>
                                                <span>{{ item.fecha }}</span>
                                            </p>
                                            <div>
                                                <span
                                                    class="badge bg-primary w-100 text-md"
                                                    >{{ $formatMoney(item.total) }} Bs.</span
                                                >
                                            </div>
                                            <div class="mt-1">
                                                <template
                                                    v-if="
                                                        item.estado ==
                                                            'ENTREGADO' &&
                                                        (props_page.auth?.user
                                                            .permisos == '*' ||
                                                            props_page.auth?.user.permisos.includes(
                                                                'pedidos.pdf',
                                                            ))
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Imprimir"
                                                        placement="left-start"
                                                    >
                                                        <a
                                                            class="btn-sm btn btn-light"
                                                            :href="
                                                                route(
                                                                    'pedidos.pdf',
                                                                    item.id,
                                                                )
                                                            "
                                                            target="_blank"
                                                        >
                                                            <i
                                                                class="fa fa-print"
                                                            ></i></a
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        props_page.auth?.user
                                                            .permisos == '*' ||
                                                        props_page.auth?.user.permisos.includes(
                                                            'pedidos.ver',
                                                        )
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Ver"
                                                        placement="left-start"
                                                    >
                                                        <Link
                                                            class="btn-sm btn btn-primary"
                                                            :href="
                                                                route(
                                                                    'pedidos.ver',
                                                                    item.id,
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-eye"
                                                            ></i></Link
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        (item.estado == 'PENDIENTE' || item.estado == 'DESPACHADO') &&
                                                        (props_page.auth?.user.permisos == '*' ||
                                                            props_page.auth?.user.permisos.includes('pedidos.edit'))
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Editar"
                                                        placement="left-start"
                                                    >
                                                        <Link
                                                            class="btn-sm btn btn-warning"
                                                            :href="
                                                                route(
                                                                    'pedidos.edit',
                                                                    item.id,
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-pen"
                                                            ></i></Link
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        (item.estado == 'PENDIENTE' || item.estado == 'DESPACHADO') &&
                                                        (props_page.auth?.user.permisos == '*' ||
                                                            props_page.auth?.user.permisos.includes('pedidos.anular') || props_page.auth?.user.tipo == 'VENDEDOR')
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Anular"
                                                        placement="left-start"
                                                    >
                                                        <button
                                                            class="btn-sm btn btn-outline-danger"
                                                            @click="
                                                                anularPedido(
                                                                    item,
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-ban"
                                                            ></i></button
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        (item.estado == 'PENDIENTE' || item.estado == 'DESPACHADO') &&
                                                        (props_page.auth?.user.permisos == '*' ||
                                                            props_page.auth?.user.permisos.includes('pedidos.destroy'))
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Eliminar"
                                                        placement="left-start"
                                                    >
                                                        <button
                                                            class="btn-sm btn btn-danger"
                                                            @click="
                                                                eliminarPedido(
                                                                    item,
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-trash-alt"
                                                            ></i></button
                                                    ></el-tooltip>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <MiPaginacion
                                :current-page="multiSearch.page"
                                :total-data="total"
                                :per-page="multiSearch.perPage"
                                @updatePage="cambioDePagina"
                            />
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-12">
                            <h4 class="w-100 text-center text-md">
                                Sin registros
                            </h4>
                        </div>
                    </template>
                    <div class="col-12 overflow-auto">
                        <div class="contenedor">
                            <!-- <MiTable
                                :tableClass="'bg-white mitabla'"
                                ref="miTable"
                                :cols="headers"
                                :api="true"
                                :url="route('pedidos.paginado')"
                                :numPages="5"
                                :multiSearch="multiSearch"
                                :syncOrderBy="'id'"
                                :syncOrderAsc="'DESC'"
                                :header-class="'bg__primary'"
                                fixed-header
                            >
                                <template #estado="{ item }">
                                    <div
                                        :class="{
                                            'text-success fw-bold':
                                                item.estado == 'ENTREGADO',
                                        }"
                                    >
                                        {{ item.estado }}
                                    </div>
                                </template>
                                <template #fecha="{ item }">
                                    <div>
                                        {{ item.fecha_t }} {{ item.hora }}
                                    </div>
                                </template>

                                <template #acceso="{ item }">
                                    <div
                                        class="badge text-sm"
                                        :class="[
                                            item.acceso == 1
                                                ? 'bg-success'
                                                : 'bg-danger',
                                        ]"
                                    >
                                        {{
                                            item.acceso == 1
                                                ? "HABILITADO"
                                                : "DESHABILITADO"
                                        }}
                                    </div>
                                </template>
                                <template #accion="{ item }">
                                    <div>
                                        <template
                                            v-if="
                                                item.estado == 'ENTREGADO' &&
                                                (props_page.auth?.user
                                                    .permisos == '*' ||
                                                    props_page.auth?.user.permisos.includes(
                                                        'pedidos.pdf',
                                                    ))
                                            "
                                        >
                                            <el-tooltip
                                                class="box-item"
                                                effect="dark"
                                                content="Imprimir"
                                                placement="left-start"
                                            >
                                                <a
                                                    class="btn btn-light"
                                                    :href="
                                                        route(
                                                            'pedidos.pdf',
                                                            item.id,
                                                        )
                                                    "
                                                    target="_blank"
                                                >
                                                    <i
                                                        class="fa fa-print"
                                                    ></i></a
                                            ></el-tooltip>
                                        </template>
                                        <template
                                            v-if="
                                                props_page.auth?.user
                                                    .permisos == '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'pedidos.ver',
                                                )
                                            "
                                        >
                                            <el-tooltip
                                                class="box-item"
                                                effect="dark"
                                                content="Ver"
                                                placement="left-start"
                                            >
                                                <Link
                                                    class="btn btn-primary"
                                                    :href="
                                                        route(
                                                            'pedidos.ver',
                                                            item.id,
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-eye"
                                                    ></i></Link
                                            ></el-tooltip>
                                        </template>
                                        <template
                                            v-if="
                                                !item.despacho_id &&
                                                (props_page.auth?.user
                                                    .permisos == '*' ||
                                                    props_page.auth?.user.permisos.includes(
                                                        'pedidos.edit',
                                                    ))
                                            "
                                        >
                                            <el-tooltip
                                                class="box-item"
                                                effect="dark"
                                                content="Editar"
                                                placement="left-start"
                                            >
                                                <Link
                                                    class="btn btn-warning"
                                                    :href="
                                                        route(
                                                            'pedidos.edit',
                                                            item.id,
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-pen"
                                                    ></i></Link
                                            ></el-tooltip>
                                        </template>
                                        <template
                                            v-if="
                                                !item.despacho_id &&
                                                (props_page.auth?.user
                                                    .permisos == '*' ||
                                                    props_page.auth?.user.permisos.includes(
                                                        'pedidos.destroy',
                                                    ))
                                            "
                                        >
                                            <el-tooltip
                                                class="box-item"
                                                effect="dark"
                                                content="Eliminar"
                                                placement="left-start"
                                            >
                                                <button
                                                    class="btn btn-danger"
                                                    @click="
                                                        eliminarPedido(item)
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-trash-alt"
                                                    ></i></button
                                            ></el-tooltip>
                                        </template>
                                    </div>
                                </template>
                            </MiTable> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
