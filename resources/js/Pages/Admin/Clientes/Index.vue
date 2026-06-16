<script setup>
import { useFiltros } from "@/composables/useFiltros";
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useClientes } from "@/composables/clientes/useClientes";
import { ref, onMounted, onBeforeMount } from "vue";
import Formulario from "./Formulario.vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import UbicacionCliente from "@/Pages/Admin/Distribucions/UbicacionCliente.vue";
import MiPaginacion from "@/Components/MiPaginacion.vue";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setCliente, limpiarCliente, form } = useClientes();

const miTable = ref(null);
const headers = [
    {
        label: "",
        key: "id",
        sortable: true,
        width: "3%",
    },
    {
        label: "NOMBRE",
        key: "nombre",
        sortable: true,
    },
    {
        label: "CONTACTO",
        key: "fono",
        sortable: true,
    },
    // {
    //     label: "RAZÓN SOCIAL",
    //     key: "razon_social",
    //     sortable: true,
    // },
    // {
    //     label: "NIT/C.I.",
    //     key: "nit_ci",
    //     sortable: true,
    // },
    {
        label: "REFERENCIA",
        key: "dir",
        sortable: true,
    },
    {
        label: "TIPO DE NEGOCIO",
        key: "tipo_negocio.nombre",
        sortable: true,
    },
    {
        label: "ZONA",
        key: "segmentacion_zona.zona",
        sortable: true,
    },
    {
        label: "FECHA REGISTRO",
        key: "fecha_registro_t",
        sortable: true,
    },
    {
        label: "ACCIÓN",
        key: "accion",
        fixed: "right",
        width: "4%",
    },
];

const listClientes = ref([]);
const total = ref(0);
const page = ref(1);

const multiSearch = useFiltros('clientes', {
    search: "",
    filtro: [],
    page: page.value,
    perPage: 12,
});

const muestra_formulario = ref(false);
const muestra_ubicacion = ref(false);

const agregarRegistro = () => {
    limpiarCliente();
    muestra_formulario.value = true;
};

const valueINterval = ref(null);
const intervalClientes = () => {
    clearInterval(valueINterval.value);
    valueINterval.value = setTimeout(() => {
        cargarClientes();
    }, 400);
};

const cargarClientes = () => {
    axios
        .get(route("clientes.paginado"), {
            params: multiSearch.value,
        })
        .then((response) => {
            console.log(response.data);
            listClientes.value = response.data.data;
            total.value = response.data.total;
        });
};

const nuevo_cliente_id = ref(null);
const actualizaClientes = (id) => {
    nuevo_cliente_id.value = id;
    cargarClientes();
    setTimeout(() => { nuevo_cliente_id.value = null; }, 5000); // 5 segundos de resaltado
};

const eliminarCliente = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>${item.nombre}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "bg-danger",
            cancelButton: "bg-light text-dark border border-secondary",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("clientes.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                cargarClientes();
            }
        }
    });
};

// detectar cambio de pagina
const cambioDePagina = async (value) => {
    multiSearch.value.page = value;
    cargarClientes();
};

onMounted(async () => {
    cargarClientes();
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Clientes"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-user-friends"></i> Clientes
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Clientes</li>
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
                        <button
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'clientes.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div
                                    class="input-group"
                                    style="align-items: end"
                                >
                                    <input
                                        v-model="multiSearch.search"
                                        placeholder="Buscar"
                                        class="form-control border-1 border-right-0"
                                        @keyup="intervalClientes"
                                    />
                                    <button
                                        class="btn btn-default bg-white border"
                                        @click="updateDatos"
                                    >
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <template v-if="total > 0">
                        <div
                            class="col-md-4 mt-2"
                            v-for="item in listClientes"
                            :key="item.id"
                        >
                            <div class="card" :class="{'border-success shadow-lg border-2': item.id == nuevo_cliente_id}" style="transition: all 0.5s ease;">
                                <div class="card-header bg-principal">
                                    <h4 class="mb-0 text-white text-lg fw-bold">
                                        <p class="mb-0">
                                            <strong>{{ item.nombre }}</strong>
                                        </p>
                                    </h4>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="mb-1 text-sm">
                                                <strong>Zona:</strong> {{ item.segmentacion_zona?.zona }}<br/>
                                                <strong>Tipo de Negocio:</strong> {{ item.tipo_negocio?.nombre }}
                                            </p>
                                            <div class="mt-2 d-flex gap-1 align-items-center">
                                                <!-- llevar a whatsapp -->
                                                <a
                                                    class="btn btn-sm btn-success"
                                                    :href="`https://wa.me/+591${item.fono}?text=Hola%20${item.nombre}`"
                                                    target="_blank"
                                                >
                                                    <i
                                                        class="fab fa-whatsapp"
                                                    ></i>
                                                </a>

                                                <template
                                                    v-if="
                                                        props_page.auth?.user
                                                            .permisos == '*' ||
                                                        props_page.auth?.user.permisos.includes(
                                                            'clientes.index',
                                                        )
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Ver"
                                                        placement="left-start"
                                                    >
                                                        <button
                                                            class="btn btn-sm btn-primary"
                                                            @click="
                                                                setCliente(
                                                                    item,
                                                                );
                                                                muestra_ubicacion = true;
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-eye"
                                                            ></i></button
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        props_page.auth?.user
                                                            .permisos == '*' ||
                                                        props_page.auth?.user.permisos.includes(
                                                            'clientes.edit',
                                                        )
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Editar"
                                                        placement="left-start"
                                                    >
                                                        <button
                                                            class="btn btn-sm btn-warning"
                                                            @click="
                                                                setCliente(
                                                                    item,
                                                                );
                                                                muestra_formulario = true;
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-pen"
                                                            ></i></button
                                                    ></el-tooltip>
                                                </template>
                                                <template
                                                    v-if="
                                                        props_page.auth?.user
                                                            .permisos == '*' ||
                                                        props_page.auth?.user.permisos.includes(
                                                            'clientes.destroy',
                                                        )
                                                    "
                                                >
                                                    <el-tooltip
                                                        class="box-item"
                                                        effect="dark"
                                                        content="Eliminar"
                                                        placement="left-start"
                                                    >
                                                        <button
                                                            class="btn btn-sm btn-danger"
                                                            @click="
                                                                eliminarCliente(
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

                    <div class="col-12">
                        <!-- <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('clientes.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
                            <template #accion="{ item }">
                              
                            </template>
                        </MiTable> -->
                    </div>
                </div>
            </div>
        </div>
    </Content>

    <Formulario
        v-if="muestra_formulario"
        :muestra_formulario="muestra_formulario"
        :form="form"
        @envio-formulario="actualizaClientes"
        @cerrar-formulario="muestra_formulario = false"
    ></Formulario>
    <UbicacionCliente
        v-if="muestra_ubicacion"
        :muestra_formulario="muestra_ubicacion"
        :cliente="form"
        @cerrar-formulario="muestra_ubicacion = false"
    ></UbicacionCliente>
</template>
