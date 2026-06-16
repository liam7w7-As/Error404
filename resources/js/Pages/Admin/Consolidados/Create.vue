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

const listDespachos = ref([]);
const despacho_id = ref(null);
const cargarDespachos = () => {
    despacho_id.value = null;
    listDespachos.value = [];
    listCategoriaProductoPedidos.value = [];
    if (!distribuidor_id.value) return;
    axios
        .get(route("despachos.listado"), {
            params: {
                distribuidor_id: distribuidor_id.value,
                estado: "SIN CONSOLIDAR",
            },
        })
        .then((response) => {
            listDespachos.value = response.data.despachos;
        })
        .finally(() => {});
};

const listCategoriaProductoPedidos = ref([]);
const cargarPedidosDistribuidorDespacho = () => {
    if (!distribuidor_id.value || !despacho_id.value) return;
    axios
        .get(route("pedidos.pedidos_despacho"), {
            params: {
                distribuidor_id: distribuidor_id.value,
                despacho_id: despacho_id.value,
                estado: "SIN CONSOLIDAR",
                detalles: detalles.value,
            },
        })
        .then((response) => {
            listCategoriaProductoPedidos.value =
                response.data.categoria_productos;
        });
};

const modificaCantidadCliente = (
    e,
    fila_categoria,
    fila_producto,
    fila_detalle,
) => {
    const value = parseFloat(e.target.value ? e.target.value : 0);

    listCategoriaProductoPedidos.value[fila_categoria]["productos"][
        fila_producto
    ]["pedido_detalles"][fila_detalle]["cantidad_total"] = value;
    recalculaCantidadConsolidado(fila_categoria, fila_producto);
};

const recalculaCantidadConsolidado = (fila_categoria, fila_producto) => {
    listCategoriaProductoPedidos.value[fila_categoria]["productos"][
        fila_producto
    ].cantidad_consolidado = listCategoriaProductoPedidos.value[fila_categoria][
        "productos"
    ][fila_producto]["pedido_detalles"].reduce((total, detalle) => {
        return (total += parseFloat(detalle.cantidad_total));
    }, 0);

    listCategoriaProductoPedidos.value[fila_categoria]["productos"][
        fila_producto
    ].stock_previsto =
        parseFloat(
            listCategoriaProductoPedidos.value[fila_categoria]["productos"][
                fila_producto
            ].stock_actual,
        ) -
        parseFloat(
            listCategoriaProductoPedidos.value[fila_categoria]["productos"][
                fila_producto
            ].cantidad_consolidado,
        );
};

const enviando = ref(false);
const errors = ref(null);
const enviarFormulario = () => {
    enviando.value = true;
    let url = route("consolidados.store");

    axios
        .post(url, {
            distribuidor_id: distribuidor_id.value,
            despacho_id: despacho_id.value,
            // observacion: observacion.value,
            listCategoriaProductoPedidos: listCategoriaProductoPedidos.value,
            _method: "post",
        })
        .then((response) => {
            console.log("correcto");
            const success = "Registro correcto";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
            setTimeout(() => {
                window.location.href = route("consolidados.index");
            }, 300);
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors;
                const errores = error.response.data.errors;
                let listaErrores = "<ul style='text-align:left;'>";
                Object.entries(errores).forEach(([campo, mensajes]) => {
                    mensajes.forEach((msg) => {
                        listaErrores += `<li>${msg}</li>`;
                    });
                });
                listaErrores += "</ul>";

                Swal.fire({
                    icon: "error",
                    title: "Errores de validación",
                    html: listaErrores,
                    confirmButtonText: "Aceptar",
                    customClass: {
                        confirmButton: "btn-alert-success",
                    },
                });
                return;
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                html: `<strong>Ocurrió un error</strong>`,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
        })
        .finally(() => {
            enviando.value = false;
        });
};

const totalFinal = computed(() => {
    return listCategoriaProductoPedidos.value.reduce(
        (total_categoria, categoria) => {
            return (total_categoria += categoria.productos.reduce(
                (total_producto, producto) => {
                    return (total_producto += producto.pedido_detalles.reduce(
                        (total_detalle, detalle) => {
                            return (total_detalle += parseFloat(
                                detalle.subtotal,
                            ));
                        },
                        0,
                    ));
                },
                0,
            ));
        },
        0,
    );
});

const tiempoReal = ref(false);
const detalles = ref(0);

const intervalTiempoReal = ref(null);
const toggleTiempoReal = () => {
    console.log(tiempoReal.value);
    if (!tiempoReal.value) {
        clearInterval(intervalTiempoReal.value);
        return;
    }
    intervalTiempoReal.value = setInterval(() => {
        cargarPedidosDistribuidorDespacho();
    }, 1900);
};

onMounted(async () => {
    cargarDistribuidors();
    if (props_page.auth?.user.tipo == "DISTRIBUIDOR") {
        distribuidor_id.value = props_page.auth?.user.id;
        cargarDespachos();
    }
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
            <div
                class="col-md-6"
                v-if="props_page.auth?.user.tipo == 'ADMINISTRADOR'"
            >
                <label>Distribuidor</label>
                <el-select
                    v-model="distribuidor_id"
                    placeholder="- Seleccione Distribuidor -"
                    filterable
                    no-data-text="Sin datos"
                    no-match-text="Sin resultados"
                    @change="cargarDespachos"
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
            <div class="col-md-6">
                <label
                    >Código Despacho
                    <span v-if="distribuidor_id" class="text-muted text-sm"
                        >({{ listDespachos.length }} Pendientes)</span
                    ></label
                >
                <el-select
                    v-model="despacho_id"
                    placeholder="- Seleccione Despacho -"
                    filterable
                    no-data-text="Sin datos"
                    no-match-text="Sin resultados"
                    @change="cargarPedidosDistribuidorDespacho"
                >
                    <el-option
                        v-for="item in listDespachos"
                        :key="item.id"
                        :value="item.id"
                        :label="item.id"
                    ></el-option>
                </el-select>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4">
                        <label>Modo Tiempo Real</label><br />
                        <el-switch
                            size="large"
                            active-text="ACTIVADO"
                            inactive-text="DESACTIVO"
                            v-model="tiempoReal"
                            style="
                                --el-switch-on-color: #13ce66;
                                --el-switch-off-color: #ff4949;
                            "
                            @change="toggleTiempoReal"
                        />
                    </div>
                    <div class="col-md-4">
                        <label>Detalles</label><br />
                        <el-switch
                            size="large"
                            active-text="ACTIVADO"
                            inactive-text="DESACTIVO"
                            v-model="detalles"
                            :active-value="1"
                            :inactive-value="0"
                            style="
                                --el-switch-on-color: #13ce66;
                                --el-switch-off-color: #ff4949;
                            "
                            @change="cargarPedidosDistribuidorDespacho"
                        />
                    </div>
                </div>
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
                        <template
                            v-if="listCategoriaProductoPedidos.length > 0"
                        >
                            <template
                                v-for="(
                                    item, fila_categoria
                                ) in listCategoriaProductoPedidos"
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
                                            <br />
                                            <span class="text-xs">
                                                ({{
                                                    pedido_detalle.pedido
                                                        .estado
                                                }})
                                            </span>
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

            <div class="col-md-6 offset-md-3">
                <button
                    v-if="
                        props_page.auth?.user.permisos == '*' ||
                        props_page.auth?.user.permisos.includes(
                            'consolidados.store',
                        )
                    "
                    class="btn btn-primary w-100"
                    :disabled="enviando"
                    @click="enviarFormulario"
                >
                    <i class="fa fa-save"></i> Registrar Consolidado
                </button>
            </div>
        </div>
    </Content>
</template>
<style scoped></style>
