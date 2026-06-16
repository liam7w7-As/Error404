<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { usePedidos } from "@/composables/pedidos/usePedidos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import MapMarkerCliente from "@/Components/MapMarkerCliente.vue";
const props = defineProps({
    pedido: null,
});
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setPedido, limpiarPedido, form } = usePedidos();

onMounted(async () => {
    limpiarPedido();
    setPedido(props.pedido);
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
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Pedidos</Link>
                        </li>
                        <li class="breadcrumb-item active">Ver</li>
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
                                    'pedidos.index',
                                )
                            "
                            type="button"
                            class="btn btn-light bg-white border text-sm"
                            :href="route('pedidos.index')"
                        >
                            <i class="fa fa-arrow-left"></i> Volver
                        </Link>
                    </div>
                    <div class="col-md-8 my-1"></div>
                </div>
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Ver Pedido</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <b>Fecha:</b>
                                        {{ pedido.fecha_t }} {{ pedido.hora }}
                                    </div>
                                    <div class="col-12">
                                        <b>Cliente:</b>
                                        {{ pedido.cliente.nombre }}
                                    </div>
                                </div>
                                <div
                                    class="row mt-2 border-top pt-2 fila_carrito"
                                >
                                    <div class="col-12">
                                        <h4 class="h5">Carrito</h4>
                                        <div
                                            class="row border-top mb-2"
                                            v-for="(
                                                item, index
                                            ) in form.pedido_detalles"
                                        >
                                            <div class="col-12 fw-bold fs-5">
                                                <i
                                                    class="fa fa-caret-right"
                                                ></i>
                                                {{ item.producto.nombre }}
                                            </div>
                                            <div class="col-3">
                                                <div
                                                    class="fw-bold text-center"
                                                >
                                                    {{
                                                        item
                                                            .presentacion_producto
                                                            .nombre
                                                    }}
                                                </div>
                                                <div
                                                    class="text-center text-md"
                                                >
                                                    {{ item.cantidad }}
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div
                                                    class="fw-bold text-center"
                                                >
                                                    Unids.
                                                </div>
                                                <div
                                                    class="text-center text-md"
                                                >
                                                    {{ item.cantidad_total }}
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div
                                                    class="fw-bold text-center"
                                                >
                                                    {{
                                                        item
                                                            .presentacion_producto
                                                            .nombre
                                                    }}
                                                    Bs.
                                                </div>
                                                <div
                                                    class="text-center text-md"
                                                >
                                                    {{ $formatMoney(item.precio) }}
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div
                                                    class="fw-bold text-center"
                                                >
                                                    Subtotal Bs.
                                                </div>
                                                <div
                                                    class="text-center text-md"
                                                >
                                                    {{ $formatMoney(item.subtotal) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row border-top pt-2 fila_total">
                                    <div
                                        class="col-6 border rounded pb-0 mb-0 d-flex align-items-center"
                                    >
                                        <b>Subtotal Bs.</b>
                                        {{ $formatMoney(pedido.subtotal) }}
                                    </div>
                                    <div class="col-6 border rounded pb-0 mb-0">
                                        <span class="label fw-bold"
                                            >Descuento Bs.</span
                                        >
                                        {{ $formatMoney(pedido.descuento) }}
                                    </div>
                                    <div
                                        class="col-12 border rounded mt-2 pt-2 pb-2"
                                    >
                                        <b>Total Bs.</b> {{ $formatMoney(pedido.total) }}
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <MapMarkerCliente
                                            :latitud="
                                                Number(pedido?.cliente.latitud)
                                            "
                                            :longitud="
                                                Number(pedido?.cliente.longitud)
                                            "
                                            :nombre-cliente="
                                                pedido?.cliente.nombre
                                            "
                                            :readonly="true"
                                            :areas="
                                                pedido?.cliente
                                                    .segmentacion_zona
                                                    .segmentacion
                                            "
                                            :muestra-nombre="false"
                                        ></MapMarkerCliente>
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
