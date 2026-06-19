<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeMount, watch, computed } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import axios from "axios";
import { usePedidos } from "@/composables/pedidos/usePedidos";
const { setPedido, limpiarPedido, form } = usePedidos();

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

const listPedidos = ref([]);
const pedido_id = ref(null);
const oDistribuidor = ref(null);
const muestra_formulario = ref(false);
const cargarPedidosDistribuidor = () => {
    listPedidos.value = [];
    pedido_id.value = null;
    axios
        .get(route("pedidos.listadoByDistribuidor"), {
            params: {
                distribuidor_id: distribuidor_id.value,
            },
        })
        .then((response) => {
            listPedidos.value = response.data.pedidos;
        })
        .finally(() => {});
};

const registrarAsignaciones = () => {
    const ids = listPedidos.value.filter((p) => p.salida == 1).map((p) => p.id);

    if (parseFloat(ids.length) <= 0) {
        toast.info("Debes marcar al menos 1 pedido");
        return;
    }

    Swal.fire({
        title: "¿Asignar pedidos?",
        html: `<strong>Nro. de Pedidos asignados: </strong> ${ids.length}`,
        showCancelButton: true,
        confirmButtonText: "Si, registrar",
        cancelButtonText: "No, cancelar",
        customClass: {
            confirmButton: "btn-success",
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.post(route("salidas.store"), {
                    ids: ids,
                    distribuidor_id: distribuidor_id.value,
                });
                toast.success("Pedidos asignados correctamente");
                listPedidos.value = [];
                distribuidor_id.value = "";
                // router.reload({ only: ["salidas"] });
            } catch (error) {
                console.error(error);
                toast.error(
                    "Error al registrar salidas. Verifica que seleccionaste al menos un pedido",
                );
            }
        }
    });
};

const checkAll = ref(false);
const toggleAll = () => {
    checkAll.value = !checkAll.value;
    listPedidos.value.forEach((p) => {
        p.salida = checkAll.value ? 1 : 0;
    });
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
                        <i class="fa fa-truck"></i> Salidas de Chofer
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('distribucions.index')"
                                >Pedidos por Entregar</Link
                            >
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

        <div class="row mb-2">
            <div class="col-md-6">
                <Link
                    v-if="
                        props_page.auth?.user.permisos == '*' ||
                        props_page.auth?.user.permisos.includes('salidas.index')
                    "
                    type="button"
                    class="btn btn-light bg-white border text-sm"
                    :href="route('salidas.index')"
                >
                    <i class="fa fa-table"></i> Historial de Salidas
                </Link>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="">Seleccionar Distribuidor</label>
                <el-select
                    v-model="distribuidor_id"
                    no-data-text="Sin datos"
                    no-match-text="Sin resultados"
                    placeholder="- Seleccione -"
                    filterable
                    @change="cargarPedidosDistribuidor()"
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
        </div>
        <div class="row">
            <div class="col-12 overflow-auto">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Zona</th>
                            <th>Total</th>
                            <th>
                                <input
                                    class="checkboxTable"
                                    type="checkbox"
                                    :checked="checkAll"
                                    @click="toggleAll"
                                />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="listPedidos.length > 0">
                            <tr 
                                v-for="item in listPedidos" 
                                :key="item.id" 
                                @click="item.salida = item.salida ? 0 : 1" 
                                style="cursor: pointer;"
                                :class="{ 'bg-light': item.salida }"
                            >
                                <td>{{ item.id }}</td>
                                <td>{{ item.cliente.nombre }}</td>
                                <td>{{ item.segmentacion_zona.zona }}</td>
                                <td>{{ $formatMoney(item.total) }} Bs.</td>
                                <td @click.stop>
                                    <input
                                        class="checkboxTable form-check-input"
                                        style="cursor: pointer;"
                                        type="checkbox"
                                        :true-value="1"
                                        :false-value="0"
                                        v-model="item.salida"
                                    />
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <span v-if="distribuidor_id"
                                        >NO SE ENCONTRÓ NINGÚN PEDIDO PARA ESTE
                                        DISTRIBUIDOR</span
                                    >
                                    <span v-else
                                        >SELECCIONA UN DISTRIBUIDOR</span
                                    >
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="row" v-if="listPedidos.length > 0">
                    <div
                        class="col-md-4 offset-md-8 d-flex justify-content-end"
                    >
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="registrarAsignaciones"
                        >
                            Asignar Pedidos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
<style scoped>
.fila_carrito {
    background-color: rgb(245, 245, 245);
}
.fila_total {
    background-color: rgb(249, 252, 236);
}
</style>
