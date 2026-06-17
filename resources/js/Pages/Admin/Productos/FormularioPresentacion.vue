<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import { useAxios } from "@/composables/axios/useAxios";
const { axiosDelete } = useAxios();
const { props: props_page } = usePage();
const props = defineProps({
    muestra_formulario: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
    },
});

const muestra_form = ref(props.muestra_formulario);
const enviando = ref(false);
const form = props.form;

const tituloDialog = computed(() => {
    return `<i class="fa fa-list"></i> Presentación de Producto`;
});

const errors = ref(null);
const enviarFormulario = () => {
    enviando.value = true;
    let url =
        formNuevo.id == 0
            ? route("presentacion_productos.store")
            : route("presentacion_productos.update", formNuevo.id);

    axios
        .post(url, formNuevo.data())
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

            cargarPresentacionProductosByProdycto();
            resetFormulario();
            emits("envio-formulario");
        })
        .catch((error) => {
            if (error.response.status === 422) {
                form.errors = error.response.data.errors;
                errors.value = error.response.data.errors;
                console.log(error.response.data.errors);
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                html: `<strong>Ocurrió un error</strong>`,
            });
        })
        .finally(() => {
            enviando.value = false;
        });
};

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(muestra_form, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const cerrarFormulario = () => {
    muestra_form.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const listPresentacionProductos = ref([]);
const cargarPresentacionProductosByProdycto = () => {
    axios
        .get(route("presentacion_productos.listado"), {
            params: {
                producto_id: form.producto.id,
            },
        })
        .then((response) => {
            listPresentacionProductos.value =
                response.data.presentacion_productos;
        });
};

const initialState = {
    id: 0,
    producto_id: form.producto.id,
    nombre: "",
    equivale: "",
    precio: "",
    comi_distribuidor: "",
    comi_vendedor: "",
    _method: "POST",
};
const formNuevo = useForm(initialState);

const resetFormulario = () => {
    formNuevo.clearErrors();
    formNuevo.reset();
    formNuevo.defaults({ ...initialState });
    errors.value = null;
};

const setPresentacionProducto = (item = null) => {
    formNuevo.clearErrors();
    formNuevo.reset();
    Object.assign(formNuevo, item);
    formNuevo._method = "PUT";
};

const cargarListas = () => {
    cargarPresentacionProductosByProdycto();
};

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i>`;
    }
    if (formNuevo.id == 0) {
        return `<i class="fa fa-save"></i>`;
    }
    return `<i class="fa fa-edit"></i>`;
});

const eliminar = (item) => {
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
                route("presentacion_productos.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                cargarPresentacionProductosByProdycto();
                emits("envio-formulario");
            }
        }
    });
};

onMounted(() => {
    cargarListas();
});
</script>

<template>
    <MiModal
        :open_modal="muestra_form"
        @close="cerrarFormulario"
        :size="'modal-xl'"
        :header-class="'bg-principal'"
        :footer-class="'justify-content-end'"
    >
        <template #header>
            <h4 class="modal-title text-white" v-html="tituloDialog"></h4>
            <button
                type="button"
                class="btn-close btn-close-white"
                @click.prevent="cerrarFormulario()"
            ></button>
        </template>

        <template #body>
            <div class="container-fluid px-3">
                <!-- Header del Producto -->
                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-12 d-flex align-items-center">
                        <div class="bg-primary text-white rounded p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 65px; height: 65px;">
                            <i class="fa fa-box-open fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">Producto: <span class="text-primary">{{ form.producto.nombre }}</span></h4>
                            <p class="text-muted mb-0">Gestione las presentaciones y precios de venta para este producto.</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario Nueva Presentación (Tarjeta Separada) -->
                <div class="card shadow-sm mb-4 border-0" v-if="props_page.auth?.user.permisos == '*' || props_page.auth?.user.permisos.includes('productos.create') || props_page.auth?.user.permisos.includes('productos.edit')">
                    <div class="card-header bg-light border-bottom border-primary border-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa" :class="formNuevo.id == 0 ? 'fa-plus-circle text-success' : 'fa-edit text-warning'"></i> 
                            {{ formNuevo.id == 0 ? 'Registrar Nueva Presentación' : 'Editar Presentación' }}
                        </h5>
                    </div>
                    <div class="card-body bg-white">
                        <form @submit.prevent="enviarFormulario()">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold text-sm">Nombre de Presentación</label>
                                    <input type="text" class="form-control" v-model="formNuevo.nombre" placeholder="Ej. Docena, Paquete x6, Unidad" />
                                    <span v-if="errors?.nombre" class="text-danger text-xs fw-bold">{{ errors?.nombre[0] }}</span>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="fw-bold text-sm">Unidades que contiene</label>
                                    <input type="number" step="1" class="form-control" v-model="formNuevo.equivale" placeholder="Ej. 12" />
                                    <span v-if="errors?.equivale" class="text-danger text-xs fw-bold">{{ errors?.equivale[0] }}</span>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="fw-bold text-sm">Precio (Bs.)</label>
                                    <input type="number" step="0.01" class="form-control text-success fw-bold" v-model="formNuevo.precio" placeholder="0.00" />
                                    <span v-if="errors?.precio" class="text-danger text-xs fw-bold">{{ errors?.precio[0] }}</span>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="fw-bold text-sm" title="Comisión para el Distribuidor">Comisión Dist. (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control" v-model="formNuevo.comi_distribuidor" placeholder="0-100" />
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <span v-if="errors?.comi_distribuidor" class="text-danger text-xs fw-bold">{{ errors?.comi_distribuidor[0] }}</span>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold text-sm" title="Comisión para el Vendedor">Comisión Vendedor (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control" v-model="formNuevo.comi_vendedor" placeholder="0-100" />
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <span v-if="errors?.comi_vendedor" class="text-danger text-xs fw-bold">{{ errors?.comi_vendedor[0] }}</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-end">
                                    <button class="btn btn-light border me-2" @click.prevent="resetFormulario" v-if="formNuevo.id != 0">
                                        <i class="fa fa-times"></i> Cancelar Edición
                                    </button>
                                    <button class="btn btn-primary px-4 fw-bold shadow-sm" @click.prevent="enviarFormulario">
                                        <span v-html="textBtn"></span> {{ formNuevo.id == 0 ? 'Guardar Presentación' : 'Actualizar Presentación' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Presentaciones Registradas -->
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-list text-primary me-2"></i> Presentaciones Registradas</h5>
                    <span class="badge bg-secondary rounded-pill px-3 py-2">{{ listPresentacionProductos.length }} registros</span>
                </div>
                
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-hover table-striped mb-0 bg-white">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="py-3">Nombre</th>
                                <th class="text-center py-3">Unidades</th>
                                <th class="text-center py-3">Precio de Venta</th>
                                <th class="text-center py-3">Comisión Distribuidor</th>
                                <th class="text-center py-3">Comisión Vendedor</th>
                                <th class="text-center py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="listPresentacionProductos.length > 0">
                                <tr v-for="item in listPresentacionProductos" :key="item.id">
                                    <td class="align-middle fw-bold text-dark">{{ item.nombre }}</td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill">{{ item.equivale }} unds.</span>
                                    </td>
                                    <td class="align-middle text-center fw-bold text-success fs-5">{{ item.precio }} Bs.</td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2">{{ item.comi_distribuidor }}%</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2">{{ item.comi_vendedor }}%</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button
                                            class="btn btn-sm btn-warning me-1 shadow-sm"
                                            v-if="props_page.auth?.user.permisos == '*' || props_page.auth?.user.permisos.includes('presentacion_productos.edit')"
                                            @click.prevent="setPresentacionProducto(item)"
                                            title="Editar Presentación"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-danger shadow-sm"
                                            v-if="props_page.auth?.user.permisos == '*' || props_page.auth?.user.permisos.includes('presentacion_productos.destroy')"
                                            @click.prevent="eliminar(item)"
                                            title="Eliminar Presentación"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted bg-light">
                                        <i class="fa fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold">No hay presentaciones registradas</h5>
                                        <p class="mb-0">Utilice el formulario superior para agregar la primera presentación para este producto.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-light"
                @click.prevent="cerrarFormulario()"
            >
                Cerrar
            </button>
        </template>
    </MiModal>
</template>
