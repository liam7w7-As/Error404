<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import MapMarkerCliente from "@/Components/MapMarkerCliente.vue";
const props = defineProps({
    muestra_formulario: {
        type: Boolean,
        default: false,
    },
    cliente: {
        type: Object,
    },
});

const muestra_form = ref(props.muestra_formulario);
const oCliente = ref(props.cliente);
const tituloDialog = computed(() => {
    return `<i class="fa fa-map-marker-alt"></i> Ubicación CLiente`;
});

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(
    () => props.cliente,
    (newValue) => {
        oCliente.value = newValue;
    },
);

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

const cargarListas = () => {
    cargarZonaAsignadas();
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
            <div class="row">
                <div class="col-12" v-if="oCliente">
                    <MapMarkerCliente
                        :latitud="Number(oCliente.latitud)"
                        :longitud="Number(oCliente.longitud)"
                        :nombre-cliente="oCliente.nombre"
                        :readonly="true"
                        :areas="oCliente.segmentacion_zona.segmentacion"
                    ></MapMarkerCliente>
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
