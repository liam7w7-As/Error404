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
    comision: Object,
    comision_detalles: Array,
});
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});
const totalFinal = computed(() => {
    // total monto_vendido redondeado a 2 decimales
    return parseFloat(
        props.comision_detalles.reduce((total, comision_detalle) => {
            const totalDespacho = comision_detalle.categoria_productos.reduce(
                (totalCategoria, categoria) => {
                    const totalCategoriaProductos = categoria.productos.reduce(
                        (totalProducto, producto) => {
                            return (
                                totalProducto +
                                parseFloat(producto.monto_vendido)
                            );
                        },
                        0,
                    );
                    return totalCategoria + totalCategoriaProductos;
                },
                0,
            );
            return total + totalDespacho;
        }, 0),
    ).toFixed(2);
});

const totalFinal1 = computed(() => {
    // total comision_distribuidor redondeado a 2 decimales
    return parseFloat(
        props.comision_detalles.reduce((total, comision_detalle) => {
            const totalDespacho = comision_detalle.categoria_productos.reduce(
                (totalCategoria, categoria) => {
                    const totalCategoriaProductos = categoria.productos.reduce(
                        (totalProducto, producto) => {
                            return (
                                totalProducto +
                                parseFloat(producto.comision_distribuidor)
                            );
                        },
                        0,
                    );
                    return totalCategoria + totalCategoriaProductos;
                },
                0,
            );
            return total + totalDespacho;
        }, 0),
    ).toFixed(2);
});

const totalFinal2 = computed(() => {
    // total comision_vendedor
    return parseFloat(
        props.comision_detalles.reduce((total, comision_detalle) => {
            const totalDespacho = comision_detalle.categoria_productos.reduce(
                (totalCategoria, categoria) => {
                    const totalCategoriaProductos = categoria.productos.reduce(
                        (totalProducto, producto) => {
                            return (
                                totalProducto +
                                parseFloat(producto.comision_vendedor)
                            );
                        },
                        0,
                    );
                    return totalCategoria + totalCategoriaProductos;
                },
                0,
            );
            return total + totalDespacho;
        }, 0),
    ).toFixed(2);
});

const totalFinal3 = computed(() => {
    // total entrega_distribuidor
    return parseFloat(
        props.comision_detalles.reduce((total, comision_detalle) => {
            const totalDespacho = comision_detalle.categoria_productos.reduce(
                (totalCategoria, categoria) => {
                    const totalCategoriaProductos = categoria.productos.reduce(
                        (totalProducto, producto) => {
                            return (
                                totalProducto +
                                parseFloat(producto.entrega_distribuidor)
                            );
                        },
                        0,
                    );
                    return totalCategoria + totalCategoriaProductos;
                },
                0,
            );
            return total + totalDespacho;
        }, 0),
    ).toFixed(2);
});
const totalFinal4 = computed(() => {
    // total entrega_vendedor
    return parseFloat(
        props.comision_detalles.reduce((total, comision_detalle) => {
            const totalDespacho = comision_detalle.categoria_productos.reduce(
                (totalCategoria, categoria) => {
                    const totalCategoriaProductos = categoria.productos.reduce(
                        (totalProducto, producto) => {
                            return (
                                totalProducto +
                                parseFloat(producto.entrega_vendedor)
                            );
                        },
                        0,
                    );
                    return totalCategoria + totalCategoriaProductos;
                },
                0,
            );
            return total + totalDespacho;
        }, 0),
    ).toFixed(2);
});

onMounted(async () => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Comisiones"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-clipboard-list"></i> Comisiones
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('comisions.index')"
                                >Comisiones</Link
                            >
                        </li>
                        <li class="breadcrumb-item active">Ver</li>
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
                            'comisions.index',
                        )
                    "
                    type="button"
                    class="btn btn-light bg-white border text-sm"
                    :href="route('comisions.index')"
                >
                    <i class="fa fa-arrow-left"></i> Volver
                </Link>
            </div>
            <div class="col-md-8 my-1"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="mb-1">
                    <strong>Código Comisión: </strong>
                    {{ comision.id }}
                </p>
                <p class="mb-1">
                    <strong>Distribuidor: </strong>
                    {{ comision.distribuidor.nombre }}
                </p>
            </div>
            <div class="col-12">
                {{ comision_detalles.length }} Despachos encontrados para este
                distribuidor
            </div>
            <div class="col-12">
                <a
                    class="btn btn-light"
                    :href="route('comisions.pdf', comision.id)"
                    target="_blank"
                >
                    Imprimir <i class="fa fa-print"></i>
                </a>
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
                                Cantidad Vendidos
                            </th>
                            <th class="bg-principal" style="min-width: 140px">
                                Monto Vendido
                            </th>
                            <th class="bg-principal" style="min-width: 140px">
                                Comisión Generada Distribuidor
                            </th>
                            <th
                                class="bg-principal text-right"
                                style="min-width: 140px"
                            >
                                Comisión Generada Vendedor
                            </th>
                            <th
                                class="bg-principal text-right"
                                style="min-width: 140px"
                            >
                                Total Pagar Distribuidor
                            </th>
                            <th
                                class="bg-principal text-right"
                                style="min-width: 140px"
                            >
                                Total Pagar Vendedor
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="comision_detalles.length > 0">
                            <template
                                v-for="(
                                    item, fila_categoria
                                ) in comision_detalles"
                                :key="item.id"
                            >
                                <tr>
                                    <td
                                        class="bg2t text-white fw-bold ps-2"
                                        colspan="7"
                                    >
                                        Código Despacho
                                        {{ item.despacho_id }}
                                    </td>
                                </tr>
                                <template
                                    v-for="(
                                        categoria_producto, index_categoria
                                    ) in item.categoria_productos"
                                >
                                    <template
                                        v-for="(
                                            producto_categoria, fila_producto
                                        ) in categoria_producto.productos"
                                        :key="producto_categoria.id"
                                    >
                                        <tr>
                                            <td align="top">
                                                <span class="fw-bold fs-6 me-1">
                                                    {{
                                                        producto_categoria.nombre
                                                    }}</span
                                                >
                                            </td>
                                            <td class="text-center">
                                                <ul>
                                                    <li
                                                        class="text-left border-bottom"
                                                        v-for="presentacion in producto_categoria.presentacions"
                                                    >
                                                        {{
                                                            presentacion.nombre
                                                        }}
                                                        ({{
                                                            presentacion.total_cantidad
                                                        }})<br />
                                                        <span class="text-xxs"
                                                            >C.D. =
                                                            {{
                                                                presentacion.total
                                                            }}
                                                            Bs. *
                                                            {{
                                                                presentacion.p_distribuidor
                                                            }}% =
                                                            {{
                                                                presentacion.comision_distribuidor
                                                            }}
                                                            Bs.</span
                                                        ><br />
                                                        <span class="text-xxs"
                                                            >C.V. =
                                                            {{
                                                                presentacion.total
                                                            }}
                                                            Bs. *
                                                            {{
                                                                presentacion.p_vendedor
                                                            }}% =
                                                            {{
                                                                presentacion.comision_vendedor
                                                            }}
                                                            Bs.</span
                                                        >
                                                    </li>
                                                </ul>
                                            </td>
                                            <td class="text-right">
                                                {{
                                                    producto_categoria.monto_vendido
                                                }}
                                                Bs.
                                            </td>
                                            <td class="text-right">
                                                {{
                                                    producto_categoria.comision_distribuidor
                                                }}
                                                Bs.
                                            </td>
                                            <td class="text-right">
                                                {{
                                                    producto_categoria.comision_vendedor
                                                }}
                                                Bs.
                                            </td>
                                            <td class="text-right">
                                                {{
                                                    producto_categoria.entrega_distribuidor
                                                }}
                                                Bs.
                                            </td>
                                            <td class="text-right">
                                                {{
                                                    producto_categoria.entrega_vendedor
                                                }}
                                                Bs.
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                            <tr>
                                <td class="bg-principal text-right" colspan="2">
                                    TOTAL
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal) }} Bs.
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal1) }} Bs.
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal2) }} Bs.
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal3) }} Bs.
                                </td>
                                <td class="bg-principal text-right">
                                    {{ $formatMoney(totalFinal4) }} Bs.
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="6" class="text-center text-muted">
                                <span
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
