<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});
import Content from "@/Components/Content.vue";
import { usePage, Head, Link } from "@inertiajs/vue3";
import { onMounted, onBeforeMount, ref, computed, nextTick } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import Highcharts from "highcharts";
import "highcharts/modules/exporting";
import "highcharts/modules/accessibility";
import MapaOperativo from "@/Components/MapaOperativo.vue";
Highcharts.setOptions({
    lang: {
        downloadPNG: "Descargar PNG",
        downloadJPEG: "Descargar JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        printChart: "Imprimir gráfico",
        contextButtonTitle: "Menú de exportación",
        viewFullscreen: "Pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
    },
});

const { auth } = usePage().props;
const user = ref(auth.user);

const props_page = defineProps({
    array_infos: {
        type: Array,
    },
});

const appStore = useAppStore();
onBeforeMount(() => {
    appStore.startLoading();
});

const { props } = usePage();

const listSegmentacions = ref([]);
const cargaSegmentacion = ref(false);
const cargarSegmentacions = () => {
    cargaSegmentacion.value = false;
    axios
        .get(route("segmentacion_zonas.listado"))
        .then((response) => {
            listSegmentacions.value = response.data.segmentacion_zonas;
        })
        .finally(() => {
            cargaSegmentacion.value = true;
        });
};

const timer = ref(null);
const currentDateTime = ref('');
const greeting = ref('');

const updateDateTime = () => {
    const now = new Date();
    const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString('es-ES', optionsDate);
    const timeString = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    
    currentDateTime.value = `${dateString.charAt(0).toUpperCase() + dateString.slice(1)} - ${timeString}`;

    const hour = now.getHours();
    if (hour < 12) {
        greeting.value = 'Buenos días';
    } else if (hour < 19) {
        greeting.value = 'Buenas tardes';
    } else {
        greeting.value = 'Buenas noches';
    }
};

onMounted(() => {
    cargarSegmentacions();
    updateDateTime();
    timer.value = setInterval(updateDateTime, 1000);
    appStore.stopLoading();
});

import { onUnmounted } from "vue";
onUnmounted(() => {
    if (timer.value) {
        clearInterval(timer.value);
    }
});
</script>
<template>
    <Head title="Inicio"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0"><i class="fa fa-home"></i> Inicio</h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item active">Inicio</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row mb-3">
            <div class="col-12">
                <div class="alert border-0 shadow-sm d-flex align-items-center mb-0" style="background-color: #f8f9fa; color: #333; border-left: 5px solid #0dcaf0 !important;">
                    <div class="me-3 text-info">
                        <i class="fa fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ greeting }}, {{ user.full_name || user.name }}</h5>
                        <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                            Hoy es <strong>{{ currentDateTime }}</strong>. <br>
                            Los siguientes indicadores muestran exclusivamente el resumen del <strong>día de hoy</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6" v-for="item in array_infos">
                <!-- small box -->
                <div class="small-box" :class="[item.color]">
                    <div class="inner">
                        <h3 class="">{{ item.cantidad }}</h3>

                        <p class="font-weight-600">{{ item.label }}</p>
                    </div>
                    <div class="small-box-icon">
                        <i class="text-dark fa" :class="[item.icon]"></i>
                    </div>
                    <Link
                        :href="route(item.url)"
                        class="small-box-footer bg-item link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                        >Ver más <i class="fa fa-arrow-alt-circle-right"></i
                    ></Link>
                </div>
            </div>
        </div>
        <div
            class="row"
            v-if="
                !auth?.user.asignacion_zonas.length &&
                auth?.user.tipo != 'ADMINISTRADOR'
            "
        >
            <div class="alert alert-danger col-12 text-white">
                <h5 class="text-white fw-bold">
                    <i class="icon fas fa-ban"></i> Sin asignación de zona
                </h5>
                No se ha asignado una zona a su usuario, por lo tanto no podrá
                realizar ningún registro. Por favor, contacte con el
                administrador del sistema para que le asigne una zona.
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <MapaOperativo v-if="cargaSegmentacion" :zonas="listSegmentacions"></MapaOperativo>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <select
                                    v-model="form1.tipo"
                                    class="form-control text-sm"
                                    @change="generarReporte1"
                                >
                                    <option
                                        v-for="item in filtroGrafico1"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div id="container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div id="container2"></div>
                    </div>
                </div>
            </div>
        </div> -->
    </Content>
</template>
<style scoped>
.item_btn {
    margin: 10px;
}

.contenido_item i {
    color: black;
}

.contenido_item {
    transition: all 0.8s;
    color: black;
    padding: 10px;
    cursor: pointer;
    background-color: rgb(248, 229, 229);
    border: solid 2px rgb(243, 211, 211);
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    font-size: 1.3em;
    flex-direction: column;
}
</style>
