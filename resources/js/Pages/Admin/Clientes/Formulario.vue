<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import MapMarkerSegmentacion from "@/Components/MapMarkerSegmentacion.vue";
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
        ? `<i class="fa fa-plus"></i> Nuevo Cliente`
        : `<i class="fa fa-edit"></i> Editar Cliente`;
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

const confirmacionIgnorada = ref(false);

const enviarFormulario = () => {
    if (enviando.value) return;

    if (sugerencias.value.length > 0 && !confirmacionIgnorada.value && form.id == 0) {
        Swal.fire({
            title: '¿Posible duplicado?',
            html: 'Existen clientes con nombres parecidos en el sistema.<br>¿Estás seguro de que deseas registrar este nuevo cliente?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Revisar'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmacionIgnorada.value = true;
                enviarFormulario();
            }
        });
        return;
    }

    enviando.value = true;
    confirmacionIgnorada.value = false;
    let url =
        form["_method"] == "POST"
            ? route("clientes.store")
            : route("clientes.update", form.id);

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
            cargarRecientes();
            emits("envio-formulario", response.props.flash?.cliente_id);
        },
        onError: (err, code) => {
            console.log(code ?? "");
            console.log(form.errors);
            if (form.errors) {
                let errorTitle = "Error de validación";
                let errorHtml = "Existen errores en el formulario, por favor verifique los campos en rojo.";

                if (form.errors.nombre) {
                    errorTitle = "¡Atención!";
                    errorHtml = `<span class="text-danger fs-6">${form.errors.nombre}</span><br><br><span class="text-muted text-sm">Por favor, utilice un nombre diferente para continuar.</span>`;
                }

                Swal.fire({
                    icon: "warning",
                    title: errorTitle,
                    html: `<strong>${errorHtml}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-warning text-dark px-4 fw-bold",
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

const listZonaAsignadas = ref([]);
const cargoZonas = ref(false);
const cargarZonaAsignadas = () => {
    cargoZonas.value = false;
    axios
        .get(route("usuarios.segmentacion_zonas_asignadas"))
        .then((response) => {
            listZonaAsignadas.value = response.data;
        })
        .finally(() => {
            cargoZonas.value = true;
        });
};

const listTIpoNegocios = ref([]);
const cargoTipoNegocios = () => {
    axios.get(route("tipo_negocios.listado")).then((response) => {
        listTIpoNegocios.value = response.data.tipo_negocios;
    });
};

const sugerencias = ref([]);
const recientes = ref([]);
let debounceTimer = null;

watch(() => form.nombre, (newVal) => {
    if (newVal && newVal.length >= 3) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            axios.get(route('clientes.buscarSimilares', { nombre: newVal })).then(res => {
                sugerencias.value = res.data.clientes;
            });
        }, 500);
    } else {
        sugerencias.value = [];
    }
});

const cargarRecientes = () => {
    axios.get(route('clientes.recientes')).then(res => {
        recientes.value = res.data.clientes;
    });
};

const cargarListas = () => {
    cargarZonaAsignadas();
    cargoTipoNegocios();
    cargarRecientes();
};

onMounted(() => {
    if (form.id != 0) {
        form.latitud = Number(form.latitud);
        form.longitud = Number(form.longitud);
    }
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
                    <div class="col-md-12 mb-3" v-if="recientes.length > 0 && form.id == 0">
                        <div class="card card-outline card-primary mb-0 shadow-sm">
                            <div class="card-header p-2">
                                <h6 class="card-title text-sm m-0"><i class="fa fa-history"></i> Clientes registrados recientemente</h6>
                            </div>
                            <div class="card-body p-2">
                                <span v-for="reciente in recientes" :key="reciente.id" class="badge bg-secondary me-1">
                                    {{ reciente.nombre }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Nombre del Cliente</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.nombre,
                            }"
                            v-model="form.nombre"
                        />
                        <ul
                            v-if="form.errors?.nombre"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.nombre }}
                            </li>
                        </ul>
                        <div v-if="sugerencias.length > 0" class="mt-1 p-2 bg-warning rounded text-dark" style="font-size: 0.85rem;">
                            <strong><i class="fa fa-info-circle"></i> Nombres similares encontrados:</strong>
                            <ul class="mb-0 pl-3">
                                <li v-for="sim in sugerencias" :key="sim.id">{{ sim.nombre }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Contacto</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.fono,
                            }"
                            v-model="form.fono"
                        />
                        <ul
                            v-if="form.errors?.fono"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.fono }}
                            </li>
                        </ul>
                    </div>
                    <!-- <div class="col-md-4 mt-2">
                        <label class="">Razón Social</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.razon_social,
                            }"
                            v-model="form.razon_social"
                        />
                        <ul
                            v-if="form.errors?.razon_social"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.razon_social }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="">Nit/C.I.</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.nit_ci,
                            }"
                            v-model="form.nit_ci"
                        />
                        <ul
                            v-if="form.errors?.nit_ci"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.nit_ci }}
                            </li>
                        </ul>
                    </div> -->
                    <div class="col-md-4 mt-2">
                        <label class="required">Referencia</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.dir,
                            }"
                            v-model="form.dir"
                        />
                        <ul
                            v-if="form.errors?.dir"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.dir }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <label class="required">Tipo de Negocio</label>
                        <el-select
                            v-model="form.tipo_negocio_id"
                            filterable
                            placeholder="Seleccione un tipo de negocio"
                            no-data-text="Sin datos"
                            no-match-text="Sin resultados"
                        >
                            <el-option
                                v-for="tipo in listTIpoNegocios"
                                :key="tipo.id"
                                :value="tipo.id"
                                :label="tipo.nombre"
                            >
                            </el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.tipo_negocio_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.tipo_negocio_id }}
                            </li>
                        </ul>
                    </div>

                    <div class="col-12 mt-2">
                        <label class="required"
                            >Indicar Ubicación
                            <i class="fa fa-map-marker-alt"></i
                        ></label>
                        <MapMarkerSegmentacion
                            v-if="cargoZonas"
                            :segmentaciones="listZonaAsignadas"
                            v-model:latitud="form.latitud"
                            v-model:longitud="form.longitud"
                            v-model:segmentacion_zona_id="
                                form.segmentacion_zona_id
                            "
                        ></MapMarkerSegmentacion>
                        <ul
                            v-if="form.errors?.latitud"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.latitud }}
                            </li>
                        </ul>
                        <!-- <ul
                            v-if="form.errors?.longitud"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.longitud }}
                            </li>
                        </ul> -->
                        <ul
                            v-if="form.errors?.longitud"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.segmentacion_zona_id }}
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
                :disabled="enviando"
                @click.prevent="enviarFormulario"
                v-html="textBtn"
            ></button>
        </template>
    </MiModal>
</template>
