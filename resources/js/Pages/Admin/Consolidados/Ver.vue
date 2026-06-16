<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeMount, watch, computed } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import axios from "axios";
// TOAST
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
const props = defineProps({
    consolidado: Object,
    categoria_productos: Array,
});
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const listDistribuidors = ref([]);
const distribuidor_id = ref(null);
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

const totalFinal = computed(() => {
    return props.categoria_productos.reduce((total_categoria, categoria) => {
        return (total_categoria += categoria.productos.reduce(
            (total_producto, producto) => {
                return (total_producto += producto.pedido_detalles.reduce(
                    (total_detalle, detalle) => {
                        return (total_detalle += parseFloat(detalle.subtotal));
                    },
                    0,
                ));
            },
            0,
        ));
    }, 0);
});

onMounted(async () => {
    cargarDistribuidors();

    appStore.stopLoading();
});
</script>
<template>
    <Head title="Consolidados"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-clipboard-list"></i> Consolidados
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('consolidados.index')"
                                >Consolidados</Link
                            >
                        </li>
                        <li class="breadcrumb-item active">Nuevo</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row mb-2">
            <div class="col-md-4">
                <Link
                    v-if="
                        props_page.auth?.user.permisos == '*' ||
                        props_page.auth?.user.permisos.includes(
                            'consolidados.index',
                        )
                    "
                    type="button"
                    class="btn btn-light bg-white border text-sm"
                    :href="route('consolidados.index')"
                >
                    <i class="fa fa-arrow-left"></i> Volver
                </Link>
            </div>
            <div class="col-md-8 my-1"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="mb-1">
                    <strong>Código Consolidado: </strong>
                    {{ consolidado.id }}
                </p>
                <p class="mb-1">
                    <strong>Código Despacho: </strong>
                    {{ consolidado.despacho_id }}
                </p>
                <p class="mb-1">
                    <strong>Fecha: </strong>
                    {{ consolidado.fecha_t }} {{ consolidado.hora }}
                </p>
                <p class="mb-1">
                    <strong>Distribuidor: </strong>
                    {{ consolidado.distribuidor.nombre }}
                </p>
            </div>
            <div class="col-12 mt-2 overflow-auto">
                <table
                    class="table table-bordered bg-white"
                    style="min-width: 900px"
                >
                    <thead>
                        <tr>
                            <th class="bg-principal">Producto</th>
                            <th class="bg-principal" style="min-width: 140px">
                                Cantidad Despacho
                            </th>
                            <th class="bg-principal" style="min-width: 140px">
                                Cantidad Entregado
                            </th>
                            <th class="bg-principal" style="min-width: 140px">
                                Cantidad Devolución
                            </th>
                            <th
                                class="bg-principal text-right"
                                style="min-width: 140px"
                            >
                                Total Bs.
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="categoria_productos.length > 0">
                            <template
                                v-for="(
                                    item, fila_categoria
                                ) in categoria_productos"
                                :key="item.id"
                            >
                                <tr>
                                    <td
                                        class="bg2t text-white fw-bold ps-2"
                                        colspan="5"
                                    >
                                        {{ item.nombre }}
                                    </td>
                                </tr>
                                <template
                                    v-for="(
                                        producto_categoria, fila_producto
                                    ) in item.productos"
                                    :key="producto_categoria.id"
                                >
                                    <tr>
                                        <td>
                                            <span class="fw-bold fs-6 me-1">
                                                {{
                                                    producto_categoria.nombre
                                                }}</span
                                            >
                                            <button
                                                class="btn btn-primary btn-sm text-xs px-1 pt-0 pb-0"
                                                @click="
                                                    producto_categoria.ver =
                                                        !producto_categoria.ver
                                                "
                                            >
                                                Ver
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            {{
                                                producto_categoria.cantidad_despacho
                                            }}
                                        </td>
                                        <td class="text-center">
                                            {{
                                                producto_categoria.cantidad_entregado
                                            }}
                                        </td>
                                        <td class="text-center">
                                            {{
                                                producto_categoria.cantidad_devolucion
                                            }}
                                        </td>
                                        <td class="text-right">
                                            {{ $formatMoney(producto_categoria.subtotal) }}
                                        </td>
                                    </tr>
                                    <tr
                                        v-if="producto_categoria.ver"
                                        v-for="(
                                            pedido_detalle, fila_detalle
                                        ) in producto_categoria.pedido_detalles"
                                        :key="pedido_detalle.id"
                                    >
                                        <td class="bgVerificado2">
                                            {{
                                                pedido_detalle.pedido?.cliente
                                                    ?.nombre
                                            }}
                                        </td>
                                        <td class="bgVerificado2 text-center">
                                            {{
                                                pedido_detalle.cantidad_despacho
                                            }}
                                        </td>
                                        <td class="bgVerificado2 text-center">
                                            {{
                                                pedido_detalle.cantidad_entregado
                                            }}
                                        </td>
                                        <td class="bgVerificado2 text-center">
                                            {{
                                                pedido_detalle.cantidad_devolucion
                                            }}
                                        </td>
                                        <td class="bgVerificado2 text-right">
                                            {{ $formatMoney(pedido_detalle.subtotal) }}
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            <tr>
                                <td class="bg-principal text-right" colspan="4">
                                    TOTAL
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal) }}
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="6" class="text-center text-muted">
                                <span v-if="!distribuidor_id || !despacho_id">
                                    Selecciona un Distribuidor y Despacho
                                </span>
                                <span v-else
                                    >NO SE ENCONTRARÓN REGISTROS PARA ESTE
                                    DISTRIBUIDOR</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Content>
</template>
<style scoped></style>
