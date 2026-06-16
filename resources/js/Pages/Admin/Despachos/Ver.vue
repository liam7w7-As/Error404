<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeMount, watch } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import axios from "axios";
// TOAST
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
const props = defineProps({
    despacho: Object,
    categoria_productos: Array,
});
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});
onMounted(async () => {
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
                        <i class="fa fa-clipboard-list"></i> Despachos
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('despachos.index')"
                                >Despachos</Link
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
                            'despachos.index',
                        )
                    "
                    type="button"
                    class="btn btn-light bg-white border text-sm"
                    :href="route('despachos.index')"
                >
                    <i class="fa fa-arrow-left"></i> Volver
                </Link>
            </div>
            <div class="col-md-8 my-1"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="mb-1">
                    <strong>Código Despacho: </strong>
                    {{ despacho.id }}
                </p>
                <p class="mb-1">
                    <strong>Fecha: </strong>
                    {{ despacho.fecha_t }} {{ despacho.hora }}
                </p>
                <p class="mb-1">
                    <strong>Distribuidor: </strong>
                    {{ despacho.distribuidor.nombre }}
                </p>
                <p class="mb-1">
                    <strong>Observacion: </strong>
                    {{ despacho.observacion }}
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
                                Cantidad Pedido
                            </th>
                            <!-- <th class="bg-principal" style="min-width: 140px">
                                Stock Actual
                            </th> -->
                            <th class="bg-principal" style="min-width: 140px">
                                Cantidad Despacho
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
                                        colspan="3"
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
                                        <td>
                                            {{
                                                producto_categoria.cantidad_total
                                            }}
                                        </td>
                                        <!-- <td>
                                            {{
                                                producto_categoria.stock_actual
                                            }}
                                        </td> -->
                                        <td>
                                            {{
                                                producto_categoria.cantidad_despacho
                                            }}
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
                                        <td class="bgVerificado2">
                                            {{ pedido_detalle.cantidad_total }}
                                        </td>
                                        <td
                                            colspan="3"
                                            class="bgVerificado2"
                                        ></td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </Content>
</template>
<style scoped></style>
