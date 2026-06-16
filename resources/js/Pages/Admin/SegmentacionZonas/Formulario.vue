<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import MapSegmentacion from "@/Components/MapSegmentacion.vue";
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
    return form.id == 0
        ? `<i class="fa fa-plus"></i> Nueva Segmentación de Zona`
        : `<i class="fa fa-edit"></i> Editar Segmentación de Zona`;
});

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (form.id == 0) {
        return `<i class="fa fa-save"></i> Guardar`;
    }
    return `<i class="fa fa-edit"></i> Actualizar`;
});

const enviarFormulario = () => {
    enviando.value = true;
    let url =
        form["_method"] == "POST"
            ? route("segmentacion_zonas.store")
            : route("segmentacion_zonas.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
            emits("envio-formulario");
        },
        onError: (err, code) => {
            console.log(code ?? "");
            console.log(form.errors);
            if (form.errors) {
                const error =
                    "Existen errores en el formulario, por favor verifique";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            } else {
                const error =
                    "Ocurrió un error inesperado contactese con el Administrador";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            }
            console.log("error: " + err.error);
        },
        onFinish: () => {
            enviando.value = false;
        },
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

const listDepartamentos = ref([]);
const listProvincias = ref([]);
const listCiudads = ref([]);

const cargarDepartamentos = () => {
    axios.get(route("departamentos.listado")).then((response) => {
        listDepartamentos.value = response.data.departamentos;
    });
};

const cargarProvincias = () => {
    axios.get(route("provincias.listado")).then((response) => {
        listProvincias.value = response.data.provincias;
    });
};

const cargarCiudads = () => {
    axios.get(route("ciudads.listado")).then((response) => {
        listCiudads.value = response.data.ciudads;
    });
};

const listSegmentacions = ref([]);
const cargaSegmentacion = ref(false);
const cargarSegmentacions = () => {
    cargaSegmentacion.value = false;
    axios
        .get(route("segmentacion_zonas.listadoSegmentacion"), {
            params: {
                id: form.id,
            },
        })
        .then((response) => {
            listSegmentacions.value = response.data.segmentacion_zonas;
        })
        .finally(() => {
            cargaSegmentacion.value = true;
        });
};

const cargarListas = () => {
    cargarDepartamentos();
    cargarProvincias();
    cargarCiudads();
    cargarSegmentacions();
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
                <p class="text-muted text-xs mb-0">
                    Todos los campos con
                    <span class="text-danger">(*)</span> son obligatorios.
                </p>
                <div class="row">
                    <div class="col-md-4 mt-2">
                        <label class="required">Seleccionar Departamento</label>
                        <el-select
                            :class="{
                                'parsley-error': form.errors?.departamento_id,
                            }"
                            v-model="form.departamento_id"
                            filterable
                            placeholder="- Seleccione -"
                            no-data-text="Sin datos"
                            no-match-text="Sin datos"
                        >
                            <el-option
                                v-for="item in listDepartamentos"
                                :key="item.id"
                                :value="item.id"
                                :label="item.nombre"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.departamento_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.departamento_id }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Seleccionar Provincia</label>
                        <el-select
                            :class="{
                                'parsley-error': form.errors?.provincia_id,
                            }"
                            v-model="form.provincia_id"
                            filterable
                            placeholder="- Seleccione -"
                            no-data-text="Sin datos"
                            no-match-text="Sin datos"
                        >
                            <el-option
                                v-for="item in listProvincias"
                                :key="item.id"
                                :value="item.id"
                                :label="item.nombre"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.provincia_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.provincia_id }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Seleccionar Ciudad</label>
                        <el-select
                            :class="{
                                'parsley-error': form.errors?.ciudad_id,
                            }"
                            v-model="form.ciudad_id"
                            filterable
                            placeholder="- Seleccione -"
                            no-data-text="Sin datos"
                            no-match-text="Sin datos"
                        >
                            <el-option
                                v-for="item in listCiudads"
                                :key="item.id"
                                :value="item.id"
                                :label="item.nombre"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.ciudad_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.ciudad_id }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Nombre de la Zona</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.zona,
                            }"
                            v-model="form.zona"
                        />
                        <ul
                            v-if="form.errors?.zona"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.zona }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Código de Color</label>
                        <input
                            type="color"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.color,
                            }"
                            v-model="form.color"
                        />
                        <ul
                            v-if="form.errors?.color"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.color }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="required"
                            >Indicar Ubicación
                            <i class="fa fa-map-marker-alt"></i
                        ></label>
                        <MapSegmentacion
                            v-if="cargaSegmentacion"
                            v-model:areas="form.segmentacion"
                            :areas-bloqueadas="listSegmentacions"
                            :color="form.color"
                        ></MapSegmentacion>

                        <ul
                            v-if="form.errors?.segmentacion"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.segmentacion }}
                            </li>
                        </ul>
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
            <button
                type="button"
                class="btn btn-primary"
                :disabled="enviando || !cargaSegmentacion"
                @click.prevent="enviarFormulario"
                v-html="textBtn"
            ></button>
        </template>
    </MiModal>
</template>
