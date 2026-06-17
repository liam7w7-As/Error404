<script setup>
import { onMounted, onUnmounted, ref, nextTick, reactive } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import ItemMenu from "@/Components/ItemMenu.vue";
import { useSideBar } from "@/composables/useSidebar.js";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const { closeSidebar, toggleSubMenuELem } = useSideBar();
const { auth } = usePage().props;
const configuracionStore = useConfiguracionStore();
const appStore = useAppStore();
const usuario = ref(null);
const permisos = ref([]);
const route_current = ref("");

const toggleSubMenu = (menu) => {
    openMenus[menu] = !openMenus[menu];
    console.log(`Toggled ${menu} to ${openMenus[menu]}`);
};

const sincronizarMenus = () => {
    Object.keys(openMenus).forEach((key) => {
        openMenus[key] = false;
    });

    if (
        route_current.value == "usuarios.index" ||
        route_current.value == "roles.index" ||
        route_current.value == "clientes.index" ||
        route_current.value == "productos.index" ||
        route_current.value == "categoria_productos.index"
    ) {
        openMenus.configuracion = true;
    }

    if (
        route_current.value == "segmentacion_zonas.index" ||
        route_current.value == "salidas.create" ||
        route_current.value == "salidas.index" ||
        route_current.value == "asignacion_zonas.index"
    ) {
        openMenus.logistica = true;
    }

    if (
        route_current.value == "pedidos.index" ||
        route_current.value == "distribucions.create" ||
        route_current.value == "distribucions.index" ||
        route_current.value == "despachos.index"
    ) {
        openMenus.pedidos = true;
    }

    if (
        route_current.value == "enfermedads.index" ||
        route_current.value == "categoria_enfermedads.index" ||
        route_current.value == "tipo_transmisions.index"
    ) {
        openMenus.enfermedads = true;
    }
};

const openMenus = reactive({
    configuracion: false,
    logistica: false,
    pedidos: false,
    enfermedads: false,
});

router.on("navigate", (event) => {
    route_current.value = route().current();
    sincronizarMenus();
    closeSidebar();
});

onMounted(() => {
    usuario.value = appStore.getUsuario;
    permisos.value = auth.user.permisos;
    route_current.value = route().current();
    sincronizarMenus();
});

const salir = () => {
    Swal.fire({
        icon: "question",
        title: "Cerrar sesión",
        html: `¿Esta seguro(a) de cerrar sesión?`,
        showCancelButton: true,
        confirmButtonText: "Si, salir",
        cancelButtonText: "Cancelar",
        denyButtonText: `Cancelar`,
        customClass: {
            confirmButton: "btn-success",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            axios
                .post(route("logout"))
                .then((response) => {})
                .finally(() => {
                    window.location.href = "/";
                });
        }
    });
};

onUnmounted(() => {});
</script>
<template>
    <!-- Main Sidebar Container -->
    <aside class="app-sidebar shadow bg-white">
        <!-- Brand Logo -->
        <div class="sidebar-brand bg1">
            <a
                :href="route('inicio')"
                class="brand-link d-flex justify-content-center align-items-center py-0"
            >
                <img
                    :src="configuracionStore.oConfiguracion.url_logo"
                    alt="Logo"
                    class="rounded-circle"
                    style="max-height: 51px"
                />
                <span class="brand-text font-weight-600 ml-1 text-white">{{
                    configuracionStore.oConfiguracion.nombre_sistema
                }}</span>
            </a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar-wrapper">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-2 d-flex border-bottom">
                <div class="image">
                    <img
                        :src="usuario?.url_foto"
                        class="rounded-circle elevation-2 user-image"
                        alt="User Image"
                    />
                </div>
                <div class="info">
                    <Link
                        :href="route('profile.edit')"
                        class="d-block text-decoration-none"
                    >
                        <div class="nombre">
                            {{ usuario?.nombre }} {{ usuario?.paterno }}
                            {{ usuario?.materno }}
                        </div>
                        <div class="tipo">{{ usuario?.tipo }}</div>
                    </Link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav sidebar-menu flex-column"
                    role="navigation"
                    aria-label="Main navigation"
                    id="navigation"
                >
                    <ItemMenu
                        :label="'Inicio'"
                        :ruta="'inicio'"
                        :icon="'fa fa-home'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold"
                        v-if="
                            usuario?.tipo == 'ADMINISTRADOR' &&
                            (permisos == '*' ||
                                permisos.includes('usuarios.index') ||
                                permisos.includes('segmentacion_zonas.index') ||
                                permisos.includes('asignacion_zonas.index') ||
                                permisos.includes(
                                    'categoria_productos.index',
                                ) ||
                                permisos.includes('productos.index') ||
                                permisos.includes('compras.index') ||
                                permisos.includes('clientes.index') ||
                                permisos.includes('pedidos.index') ||
                                permisos.includes('despachos.index') ||
                                permisos.includes('distribucions.index') ||
                                permisos.includes('consolidados.index') ||
                                permisos.includes('comisions.index'))
                        "
                    >
                        ADMINISTRACIÓN
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('comisions.index')
                        "
                        :label="'Comisiones'"
                        :ruta="'comisions.index'"
                        :icon="'fa fa-hand-holding-usd'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('consolidados.index')
                        "
                        :label="'Consolidado'"
                        :ruta="'consolidados.index'"
                        :icon="'fa fa-list'"
                    ></ItemMenu>
                    
                    <!-- PEDIDOS SUBMENU -->
                    <li class="nav-item" :class="{'menu-open': openMenus.pedidos}" v-if="permisos == '*' || permisos.includes('pedidos.index') || permisos.includes('distribucions.index') || permisos.includes('despachos.index')">
                        <a class="nav-link" style="cursor: pointer;" :class="{'active': openMenus.pedidos}" @click="toggleSubMenu('pedidos')">
                            <i class="nav-icon fa fa-clipboard-check"></i>
                            <p>
                                PEDIDOS
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav flex-column ms-3" style="display: block;" v-if="openMenus.pedidos">
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('pedidos.index')
                                "
                                :label="'Pedidos'"
                                :ruta="'pedidos.index'"
                                :icon="'fa fa-clipboard-check'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('distribucions.index')
                                "
                                :label="'Pedidos por Entregar'"
                                :ruta="'distribucions.index'"
                                :icon="'fa fa-truck'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('despachos.index')
                                "
                                :label="'Despachos'"
                                :ruta="'despachos.index'"
                                :icon="'fa fa-clipboard-list'"
                            ></ItemMenu>
                        </ul>
                    </li>

                    <!-- LOGÍSTICA SUBMENU -->
                    <li class="nav-item" :class="{'menu-open': openMenus.logistica}" v-if="permisos == '*' || permisos.includes('segmentacion_zonas.index') || permisos.includes('salidas.create') || permisos.includes('asignacion_zonas.index')">
                        <a class="nav-link" style="cursor: pointer;" :class="{'active': openMenus.logistica}" @click="toggleSubMenu('logistica')">
                            <i class="nav-icon fa fa-truck-loading"></i>
                            <p>
                                LOGÍSTICA
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav flex-column ms-3" style="display: block;" v-if="openMenus.logistica">
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('segmentacion_zonas.index')
                                "
                                :label="'Zonas'"
                                :ruta="'segmentacion_zonas.index'"
                                :icon="'fa fa-map-marked-alt'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('salidas.create')
                                "
                                :label="'Movimiento de Salida Chofer'"
                                :ruta="'salidas.create'"
                                :icon="'fa fa-clipboard-list'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('asignacion_zonas.index')
                                "
                                :label="'Asignación de Zonas'"
                                :ruta="'asignacion_zonas.index'"
                                :icon="'fa fa-table'"
                            ></ItemMenu>
                        </ul>
                    </li>

                    <!-- CONFIGURACION SUBMENU -->
                    <li class="nav-item" :class="{'menu-open': openMenus.configuracion}" v-if="permisos == '*' || permisos.includes('clientes.index') || permisos.includes('productos.index') || permisos.includes('categoria_productos.index') || permisos.includes('usuarios.index')">
                        <a class="nav-link" style="cursor: pointer;" :class="{'active': openMenus.configuracion}" @click="toggleSubMenu('configuracion')">
                            <i class="nav-icon fa fa-cogs"></i>
                            <p>
                                CONFIGURACIÓN
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav flex-column ms-3" style="display: block;" v-if="openMenus.configuracion">
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('clientes.index')
                                "
                                :label="'Clientes'"
                                :ruta="'clientes.index'"
                                :icon="'fa fa-user-friends'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('productos.index')
                                "
                                :label="'Productos'"
                                :ruta="'productos.index'"
                                :icon="'fa fa-boxes'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('categoria_productos.index')
                                "
                                :label="'Categoría de Productos'"
                                :ruta="'categoria_productos.index'"
                                :icon="'fa fa-list'"
                            ></ItemMenu>
                            <ItemMenu
                                v-if="
                                    permisos == '*' ||
                                    permisos.includes('usuarios.index')
                                "
                                :label="'Usuarios'"
                                :ruta="'usuarios.index'"
                                :icon="'fa fa-users'"
                            ></ItemMenu>
                        </ul>
                    </li>

                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('compras.index')
                        "
                        :label="'Compras'"
                        :ruta="'compras.index'"
                        :icon="'fa fa-clipboard-list'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('presentacions.index')
                        "
                        :label="'Presentación de Productos'"
                        :ruta="'presentacions.index'"
                        :icon="'fa fa-list'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold"
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.usuarios') ||
                            permisos.includes('reportes.productos') ||
                            permisos.includes(
                                'reportes.movimiento_inventarios',
                            ) ||
                            permisos.includes('reportes.liquidacion')
                        "
                    >
                        REPORTES
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.usuarios')
                        "
                        :label="'Lista de Usuarios'"
                        :ruta="'reportes.usuarios'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.productos')
                        "
                        :label="'Lista de Productos'"
                        :ruta="'reportes.productos'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.movimiento_inventarios')
                        "
                        :label="'Movimiento de Inventario'"
                        :ruta="'reportes.movimiento_inventarios'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.liquidacion')
                        "
                        :label="'Liquidación de ventas por vendedor o distribuidor'"
                        :ruta="'reportes.liquidacion'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.utilidad_bruta')
                        "
                        :label="'Utilidad bruta por producto'"
                        :ruta="'reportes.utilidad_bruta'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold"
                        v-if="usuario?.tipo == 'ADMINISTRADOR'"
                    >
                        OTROS
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('configuracions.index')
                        "
                        :label="'Configuración Sistema'"
                        :ruta="'configuracions.index'"
                        :icon="'fa fa-cog'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="usuario?.tipo == 'ADMINISTRADOR'"
                        :label="'Perfil'"
                        :ruta="'profile.edit'"
                        :icon="'fa fa-id-card'"
                    ></ItemMenu>
                    <li
                        class="nav-item"
                        v-if="usuario?.tipo == 'ADMINISTRADOR'"
                    >
                        <a
                            href="#"
                            class="nav-link"
                            @click.prevent="salir()"
                            ref="link"
                        >
                            <i class="nav-icon fa fa-power-off"></i>
                            <p>Salir</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>
<style scoped></style>
