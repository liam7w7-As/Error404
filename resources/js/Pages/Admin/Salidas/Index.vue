<script setup>
import { useFiltros } from "@/composables/useFiltros";
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { usePedidos } from "@/composables/pedidos/usePedidos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setPedido, limpiarPedido, form } = usePedidos();

const miTable = ref(null);
const headers = [
    {
        label: "Código",
        key: "id",
        sortable: true,
        width: "3%",
    },
    {
        label: "CLIENTE",
        key: "cliente.nombre",
        sortable: true,
    },
    {
        label: "ZONA",
        key: "segmentacion_zona.zona",
        sortable: true,
    },
    {
        label: "DISTRIBUIDOR",
        key: "user_distribucion.full_name",
        sortable: true,
    },

    {
        label: "TOTAL Bs.",
        key: "total",
        sortable: true,
    },

    {
        label: "ESTADO",
        key: "estado",
        sortable: true,
    },
    {
        label: "FECHA SALIDA",
        key: "fecha_salida",
        sortable: true,
    },
];

const getFechaActual = () => {
    const fecha = new Date();
    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const anio = fecha.getFullYear();
    return `${anio}-${mes}-${dia}`;
};

const multiSearch = useFiltros('salidas', {
    fecha_ini: getFechaActual(),
    fecha_fin: getFechaActual(),
    distribuidor_id: "",
    filtro: [],
});

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarPedido();
        // muestra_formulario.value = false;
    }
};

const anularPedido = (item) => {
    Swal.fire({
        title: "¿Quierés anular este registro?",
        html: `<strong>${item.cliente.nombre} | Código: ${item.id}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, anular",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "bg-danger",
            cancelButton: "bg-light text-dark border border-secondary",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(route("salidas.anular", item.id));
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

const listDistribuidors = ref([]);

const cargarDistribuidors = () => {
    axios
        .get(route("usuarios.listado"), {
            params: {
                tipo: "DISTRIBUIDOR",
            },
        })
        .then((response) => {
            listDistribuidors.value = response.data.usuarios;
        })
        .finally(() => {});
};

onMounted(async () => {
    cargarDistribuidors();
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Salidas de Chofer"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-clipboard-list"></i> Salidas de Chofer
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>

                        <li class="breadcrumb-item active">
                            Salidas de Chofer
                        </li>
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
                                    'salidas.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            :href="route('salidas.create')"
                        >
                            <i class="fa fa-plus"></i> Nueva salida
                        </Link>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <label class="text-xs">Distribuidor</label>
                                <el-select
                                    v-model="multiSearch.distribuidor_id"
                                    no-data-text="Sin datos"
                                    no-match-text="Sin resultados"
                                    placeholder="- Seleccione -"
                                    filterable
                                    clearable
                                >
                                    <el-option
                                        v-for="item in listDistribuidors"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                            </div>
                            <div class="col-md-8">
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('salidas.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
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
                                    <br />
                                    <small
                                        class="text-muted text-xxs"
                                        v-if="!item.despacho_id"
                                        >(Sin despacho)</small
                                    >
                                </div>
                            </template>
                            <template #fecha="{ item }">
                                <div>{{ item.fecha_t }} {{ item.hora }}</div>
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
                                            (props_page.auth?.user.permisos ==
                                                '*' ||
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
                                                <i class="fa fa-print"></i></a
                                        ></el-tooltip>
                                    </template>
                                    <template
                                        v-if="
                                            item.estado == 'PENDIENTE' &&
                                            item.despacho_id &&
                                            (props_page.auth?.user.permisos ==
                                                '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'salidas.anular',
                                                ))
                                        "
                                    >
                                        <el-tooltip
                                            class="box-item"
                                            effect="dark"
                                            content="Anular"
                                            placement="left-start"
                                        >
                                            <button
                                                class="btn btn-danger"
                                                @click="anularPedido(item)"
                                            >
                                                <i
                                                    class="fa fa-ban"
                                                ></i></button
                                        ></el-tooltip>
                                    </template>
                                    <!-- <template
                                        v-if="
                                            props_page.auth?.user.permisos ==
                                                '*' ||
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
                                                <i class="fa fa-eye"></i></Link
                                        ></el-tooltip>
                                    </template> -->
                                </div>
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
