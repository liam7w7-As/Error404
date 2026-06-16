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
            <form @submit.prevent="enviarFormulario()" class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            Producto:
                            <span class="fw-normal">{{
                                form.producto.nombre
                            }}</span>
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center overflow-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-principal" width="200px">
                                        Nombre
                                    </th>
                                    <th class="bg-principal" width="200px">
                                        Equivale Unid.
                                    </th>
                                    <th class="bg-principal" width="200px">
                                        Precio Bs.
                                    </th>
                                    <th class="bg-principal" width="180px">
                                        Comisión Distribuidor (0-100%)
                                    </th>
                                    <th class="bg-principal" width="180px">
                                        Comisión Vendedor (0-100%)
                                    </th>
                                    <th class="bg-principal">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'productos.create',
                                        ) ||
                                        props_page.auth?.user.permisos.includes(
                                            'productos.edit',
                                        )
                                    "
                                >
                                    <td class="bg2 px-1 py-1">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="formNuevo.nombre"
                                        />
                                        <ul
                                            v-if="errors?.nombre"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{ errors?.nombre[0] }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="bg2 px-1 py-1">
                                        <input
                                            type="number"
                                            step="1"
                                            class="form-control text-center"
                                            v-model="formNuevo.equivale"
                                        />
                                        <ul
                                            v-if="errors?.equivale"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{ errors?.equivale[0] }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="bg2 px-1 py-1">
                                        <input
                                            type="number"
                                            step="0.01"
                                            class="form-control text-center"
                                            v-model="formNuevo.precio"
                                        />
                                        <ul
                                            v-if="errors?.precio"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{ errors?.precio[0] }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="bg2 px-1 py-1">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            class="form-control text-center"
                                            v-model="
                                                formNuevo.comi_distribuidor
                                            "
                                        />
                                        <ul
                                            v-if="errors?.comi_distribuidor"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{
                                                    errors?.comi_distribuidor[0]
                                                }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="bg2 px-1 py-1">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            class="form-control text-center"
                                            v-model="formNuevo.comi_vendedor"
                                        />
                                        <ul
                                            v-if="errors?.comi_vendedor"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{ errors?.comi_vendedor[0] }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="bg2 px-1 py-1">
                                        <button
                                            class="btn btn-sm btn-primary"
                                            @click.prevent="enviarFormulario"
                                            v-html="textBtn"
                                        ></button>
                                        <button
                                            @click.prevent="resetFormulario"
                                            class="btn btn-light btn-sm border ms-1"
                                        >
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <template
                                    v-if="listPresentacionProductos.length > 0"
                                >
                                    <tr
                                        v-for="item in listPresentacionProductos"
                                        :key="item.id"
                                    >
                                        <td>{{ item.nombre }}</td>
                                        <td>{{ item.equivale }}</td>
                                        <td>{{ item.precio }}</td>
                                        <td>{{ item.comi_distribuidor }}</td>
                                        <td>{{ item.comi_vendedor }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-warning"
                                                v-if="
                                                    props_page.auth?.user
                                                        .permisos == '*' ||
                                                    props_page.auth?.user.permisos.includes(
                                                        'presentacion_productos.edit',
                                                    )
                                                "
                                                @click.prevent="
                                                    setPresentacionProducto(
                                                        item,
                                                    )
                                                "
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-danger"
                                                @click.prevent="eliminar(item)"
                                                v-if="
                                                    props_page.auth?.user
                                                        .permisos == '*' ||
                                                    props_page.auth?.user.permisos.includes(
                                                        'presentacion_productos.destroy',
                                                    )
                                                "
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Sin registros
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
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
