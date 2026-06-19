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
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

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

const distribuidor_id = ref(null);
const observacion = ref("");

const cargandoPedidos = ref(false);
const listPedidos = ref([]);
const cargarPedidosDistribuidor = () => {
    cargandoPedidos.value = true;
    axios
        .get(route("pedidos.listado_pedidos_distribuidor"), {
            params: {
                distribuidor_id: distribuidor_id.value,
            },
        })
        .then((response) => {
            listPedidos.value = response.data.pedidos.map(p => {
                p.seleccionado = false;
                return p;
            });
            seleccionarTodo.value = false;
        })
        .finally(() => {
            cargandoPedidos.value = false;
        });
};

const seleccionarTodo = ref(false);
const toggleSeleccionarTodo = () => {
    listPedidos.value.forEach(p => {
        p.seleccionado = seleccionarTodo.value;
    });
};

const verificarSeleccionTodo = () => {
    seleccionarTodo.value = listPedidos.value.every(p => p.seleccionado);
};

const enviando = ref(false);
const errors = ref(null);
const enviarFormulario = () => {
    enviando.value = true;
    let url = route("despachos.store");

    const pedido_ids = listPedidos.value.filter(p => p.seleccionado).map(p => p.id);

    if (pedido_ids.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Debes seleccionar al menos un pedido para asignar",
        });
        enviando.value = false;
        return;
    }

    axios
        .post(url, {
            distribuidor_id: distribuidor_id.value,
            observacion: observacion.value,
            pedido_ids: pedido_ids,
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
                window.location.href = route("despachos.index");
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

onMounted(async () => {
    cargarDistribuidors();
    if (props_page.auth?.user.tipo == "DISTRIBUIDOR") {
        distribuidor_id.value = props_page.auth?.user.id;
        cargarPedidosDistribuidor();
    }

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
            <div
                class="col-md-12"
                v-if="props_page.auth?.user.tipo == 'ADMINISTRADOR'"
            >
                <label>Distribuidor</label>
                <el-select
                    v-model="distribuidor_id"
                    placeholder="- Seleccione Distribuidor -"
                    filterable
                    no-data-text="Sin datos"
                    no-match-text="Sin resultados"
                    @change="cargarPedidosDistribuidor"
                >
                    <el-option
                        v-for="item in listDistribuidors"
                        :key="item.id"
                        :value="item.id"
                        :label="item.nombre"
                    ></el-option>
                </el-select>
            </div>
            <div class="col-md-12">
                <label>Observación</label>
                <el-input type="textarea" v-model="observacion" autosize>
                </el-input>
            </div>
            <div class="col-12"></div>
            <div class="col-12 mt-2 overflow-auto">
                <table
                    class="table table-bordered bg-white"
                    style="min-width: 900px"
                >
                    <thead>
                        <tr>
                            <th class="bg-principal text-center" style="width: 50px;">
                                <input type="checkbox" v-model="seleccionarTodo" @change="toggleSeleccionarTodo" class="form-check-input" style="width: 20px; height: 20px; cursor: pointer;">
                            </th>
                            <th class="bg-principal">Nro. Pedido</th>
                            <th class="bg-principal">Cliente</th>
                            <th class="bg-principal">Zona</th>
                            <th class="bg-principal">Vendedor</th>
                            <th class="bg-principal">Total Bs.</th>
                            <th class="bg-principal">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template
                            v-if="
                                listPedidos.length > 0 &&
                                !cargandoPedidos
                            "
                        >
                            <tr 
                                v-for="item in listPedidos" 
                                :key="item.id"
                                @click="item.seleccionado = !item.seleccionado; verificarSeleccionTodo()"
                                style="cursor: pointer;"
                                :class="{ 'bg-light': item.seleccionado }"
                            >
                                <td class="text-center align-middle" @click.stop>
                                    <input type="checkbox" v-model="item.seleccionado" @change="verificarSeleccionTodo" class="form-check-input" style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                                <td class="align-middle fw-bold">#{{ item.id }}</td>
                                <td class="align-middle">{{ item.cliente?.nombre }}</td>
                                <td class="align-middle">{{ item.segmentacion_zona?.zona }}</td>
                                <td class="align-middle">{{ item.user?.full_name }}</td>
                                <td class="align-middle fw-bold text-success">{{ $formatMoney(item.total) }}</td>
                                <td class="align-middle">{{ item.fecha }} {{ item.hora }}</td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td
                                colspan="6"
                                v-if="cargandoPedidos"
                                class="text-center"
                            >
                                <div class="my-3">
                                    Cargando
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </td>
                            <td
                                colspan="6"
                                class="text-center text-muted"
                                v-else
                            >
                                <span v-if="!distribuidor_id">
                                    Selecciona un Distribuidor para ver los pedidos de su zona asignada.
                                </span>
                                <span v-else
                                    >NO SE ENCONTRARON PEDIDOS PENDIENTES PARA ESTA ZONA</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6 offset-md-3">
                <button
                    class="btn btn-primary w-100"
                    :disabled="enviando"
                    @click="enviarFormulario"
                >
                    <i class="fa fa-save"></i> Registrar Despacho
                </button>
            </div>
        </div>
    </Content>
</template>
<style scoped></style>
