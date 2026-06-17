<script setup>
import { useFiltros } from "@/composables/useFiltros";
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useProductos } from "@/composables/productos/useProductos";
import { usePresentacionProductos } from "@/composables/presentacion_productos/usePresentacionProductos";
import { ref, onMounted, onBeforeMount } from "vue";
import Formulario from "./Formulario.vue";
import FormularioPresentacion from "./FormularioPresentacion.vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setProducto, limpiarProducto, form } = useProductos();
const {
    setPresentacionProducto,
    limpiarPresentacionProducto,
    form: formPresentacion,
} = usePresentacionProductos();

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
        label: "DESCRIPCIÓN",
        key: "nombre",
        sortable: true,
    },
    {
        label: "CATEGORÍA",
        key: "categoria_producto.nombre",
        sortable: true,
    },
    {
        label: "PRECIO COMPRA",
        key: "precio_compra",
        sortable: true,
    },
    {
        label: "ALERTA STOCK MIN.",
        key: "stock_min",
        sortable: true,
    },
    {
        label: "STOCK ACTUAL",
        key: "stock_actual",
        sortable: true,
    },
    {
        label: "PRESENTACIÓN",
        key: "presentacion_productos_count",
    },
    {
        label: "ESTADO",
        key: "estado",
        sortable: true,
    },
    {
        label: "IMAGEN",
        key: "imagen",
        sortable: true,
    },
    {
        label: "ACCIÓN",
        key: "accion",
        fixed: "right",
        width: "4%",
    },
];

const multiSearch = useFiltros('productos', {
    search: "",
    filtro: [],
});
const muestra_formulario_presentacion = ref(false);

const muestra_formulario = ref(false);

const agregarRegistro = () => {
    limpiarProducto();
    muestra_formulario.value = true;
};

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarProducto();
        muestra_formulario.value = false;
    }
};

const updateDatatable2 = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarProducto();
    }
};

const eliminarProducto = (item) => {
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
                route("productos.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

onMounted(async () => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Productos"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0"><i class="fa fa-boxes"></i> Productos</h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Productos</li>
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
                                    'productos.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo Producto
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
                                    />
                                    <div class="input-append">
                                        <button
                                            class="btn btn-default rounded-0 border-left-0"
                                            @click="updateDatos"
                                        >
                                            <i class="fa fa-search"></i>
                                        </button>
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
                            :url="route('productos.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
                            <template #presentacion_productos_count="{ item }">
                                <span
                                    v-if="
                                        item.presentacion_productos_count == 0
                                    "
                                    class="text-muted"
                                >
                                    Sin presentaciones
                                </span>
                                <span
                                    v-else
                                    class="badge bg-primary text-md text-wrap"
                                    >{{
                                        item.presentacion_productos_count
                                    }}
                                    registro(s)</span
                                >
                            </template>
                            <template #precio_compra="{ item }">
                                {{ $formatMoney(item.precio_compra) }}
                            </template>
                            <template #imagen="{ item }">
                                <img
                                    class=""
                                    height="50px"
                                    :src="item.url_imagen"
                                    alt="Imagen"
                                />
                            </template>

                            <template #estado="{ item }">
                                <div
                                    class="badge text-sm text-nowrap"
                                    :class="[
                                        item.estado == 1
                                            ? 'bg-success'
                                            : 'bg-danger',
                                    ]"
                                >
                                    {{
                                        item.estado == 1
                                            ? "HABILITADO"
                                            : "DESHABILITADO"
                                    }}
                                </div>
                            </template>
                            <template #accion="{ item }">
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'presentacion_productos.index',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Presentación"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-info text-white"
                                            @click="
                                                setPresentacionProducto(item);
                                                muestra_formulario_presentacion = true;
                                            "
                                        >
                                            <i class="fa fa-boxes"></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'productos.edit',
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
                                            class="btn btn-warning"
                                            @click="
                                                setProducto(item);
                                                muestra_formulario = true;
                                            "
                                        >
                                            <i class="fa fa-pen"></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'productos.destroy',
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
                                            class="btn btn-danger"
                                            @click="eliminarProducto(item)"
                                        >
                                            <i
                                                class="fa fa-trash-alt"
                                            ></i></button
                                    ></el-tooltip>
                                </template>
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
    </Content>
    <Formulario
        v-if="muestra_formulario"
        :muestra_formulario="muestra_formulario"
        :form="form"
        @envio-formulario="updateDatatable"
        @cerrar-formulario="muestra_formulario = false"
    ></Formulario>
    <FormularioPresentacion
        v-if="muestra_formulario_presentacion"
        :muestra_formulario="muestra_formulario_presentacion"
        :form="formPresentacion"
        @envio-formulario="updateDatatable2"
        @cerrar-formulario="muestra_formulario_presentacion = false"
    ></FormularioPresentacion>
</template>
