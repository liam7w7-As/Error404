<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
import axios from "axios";
// TOAST
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Swal from "sweetalert2";

const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

// MODO: false = COMPRA (INGRESO), true = BAJA (SALIDA)
const modoBaja = ref(false);

onBeforeMount(() => {
    appStore.startLoading();
});

const cargaDatos1 = ref(true);
const listCategoriaProductos = ref([]);
const listProductos = ref([]);

const cargarCategoriaProductos = () => {
    axios.get(route("categoria_productos.listado")).then((response) => {
        listCategoriaProductos.value = response.data.categoria_productos;
    });
};

const cargarProductosLista = () => {
    axios.get(route("productos.listado")).then((response) => {
        listProductos.value = response.data.productos;
    });
};

const filtro = ref({
    categoria_producto_id: "",
    producto_id: "",
});

const listProductosData = ref([]);

const cargarProductosData = () => {
    cargaDatos1.value = true;
    axios
        .get(route("productos.listadoStockBajo"), {
            params: filtro.value,
        })
        .then((response) => {
            // Reiniciar inputs de catálogo
            listProductosData.value = response.data.productos.map(p => ({
                ...p,
                ingreso_cantidad: 1,
                ingreso_precio: 0,
                motivo_baja: ""
            }));
        })
        .finally(() => {
            cargaDatos1.value = false;
        });
};

const alternarModo = () => {
    if (carrito.value.length > 0) {
        Swal.fire({
            title: "¿Cambiar de modo?",
            text: "Cambiar el modo limpiará todos los productos que ya añadiste a la lista.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, cambiar modo",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                carrito.value = [];
                modoBaja.value = !modoBaja.value;
            }
        });
    } else {
        modoBaja.value = !modoBaja.value;
    }
};

// CARRITO DE COMPRAS / LISTA DE BAJAS
const carrito = ref([]);

const agregarAlCarrito = (producto) => {
    const cant = parseFloat(producto.ingreso_cantidad || 0);

    if (cant <= 0) {
        toast.error("La cantidad debe ser mayor a 0");
        return;
    }

    if (modoBaja.value) {
        if (cant > parseFloat(producto.stock_actual)) {
            toast.error("La cantidad a dar de baja no puede superar el stock actual");
            return;
        }
        if (!producto.motivo_baja || producto.motivo_baja.trim() === "") {
            toast.error("Debe especificar un motivo para la baja de stock");
            return;
        }
    } else {
        const precio = parseFloat(producto.ingreso_precio || 0);
        if (precio <= 0) {
            toast.error("El precio de compra debe ser mayor a 0");
            return;
        }
    }

    // Verificar si ya existe en la lista
    const index = carrito.value.findIndex(item => item.producto_id === producto.id);
    if (index !== -1) {
        const nuevaCant = carrito.value[index].cantidad + cant;
        if (modoBaja.value && nuevaCant > parseFloat(producto.stock_actual)) {
            toast.error("La cantidad total acumulada supera el stock actual");
            return;
        }
        carrito.value[index].cantidad = nuevaCant;
        
        if (!modoBaja.value) {
            carrito.value[index].precio_compra = parseFloat(producto.ingreso_precio);
            carrito.value[index].total = carrito.value[index].cantidad * carrito.value[index].precio_compra;
        } else {
            carrito.value[index].motivo = producto.motivo_baja;
        }
    } else {
        if (modoBaja.value) {
            carrito.value.push({
                producto_id: producto.id,
                nombre: producto.nombre,
                stock_actual: producto.stock_actual,
                cantidad: cant,
                motivo: producto.motivo_baja
            });
        } else {
            const precio = parseFloat(producto.ingreso_precio);
            carrito.value.push({
                categoria_producto_id: producto.categoria_producto_id,
                producto_id: producto.id,
                nombre: producto.nombre,
                stock_actual: producto.stock_actual,
                cantidad: cant,
                precio_compra: precio,
                total: cant * precio
            });
        }
    }

    toast.success(`${producto.nombre} añadido a la lista`);
    
    // Limpiar inputs
    producto.ingreso_cantidad = 1;
    producto.ingreso_precio = 0;
    producto.motivo_baja = "";
};

const eliminarDelCarrito = (index) => {
    carrito.value.splice(index, 1);
};

const granTotal = computed(() => {
    if (modoBaja.value) return 0;
    return carrito.value.reduce((acc, item) => acc + parseFloat(item.total), 0);
});

const errors = ref(null);
const registrando = ref(false);

const registrarOperacion = () => {
    if (carrito.value.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Lista vacía",
            text: "Debes agregar al menos un producto para registrar la operación."
        });
        return;
    }

    const title = modoBaja.value ? "¿Registrar estas Bajas de Stock?" : "¿Registrar estas Compras?";
    let text = modoBaja.value 
        ? `Estás a punto de dar de BAJA y restar ${carrito.value.length} productos del inventario.`
        : `Estás a punto de INGRESAR ${carrito.value.length} productos al inventario por un total de Bs. ${granTotal.value.toFixed(2)}`;
    const confirmText = modoBaja.value ? "Sí, registrar Bajas" : "Sí, registrar Compras";
    const confirmColor = modoBaja.value ? "#d33" : "#28a745";
    const routeName = modoBaja.value ? "compras.baja_store" : "compras.store";

    Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: "Cancelar",
        confirmButtonColor: confirmColor,
    }).then((result) => {
        if (result.isConfirmed) {
            registrando.value = true;
            axios.post(route(routeName), {
                productos: carrito.value
            }).then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: response.data.message || "Operación registrada correctamente y el stock fue actualizado."
                });
                carrito.value = []; // Limpiar carrito
                cargarProductosData(); // Recargar stock real
            }).catch((error) => {
                if (error.response?.status === 422) {
                    const errores = error.response.data.errors;
                    let listaErrores = "<ul class='text-start'>";
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
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error.response?.data?.message || "Ocurrió un problema al registrar la operación."
                    });
                }
            }).finally(() => {
                registrando.value = false;
            });
        }
    });
};

onMounted(async () => {
    cargarProductosData();
    cargarCategoriaProductos();
    cargarProductosLista();
    appStore.stopLoading();
});
</script>

<template>
    <Head title="Ingresos y Bajas"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i :class="modoBaja ? 'fa fa-arrow-down text-danger' : 'fa fa-shopping-cart text-success'"></i> 
                        {{ modoBaja ? 'Bajas / Ajustes de Stock' : 'Registro de Compras (Ingreso)' }}
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Inventario</li>
                    </ol>
                </div>
            </div>
        </template>

        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center bg-white p-2 shadow-sm rounded border">
                    <span class="fw-bold me-3 text-muted">Módulo de Operación:</span>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" :checked="!modoBaja" @change="alternarModo">
                        <label class="btn btn-outline-success fw-bold" for="btnradio1"><i class="fa fa-plus-circle"></i> COMPRA (Ingreso)</label>

                        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" :checked="modoBaja" @change="alternarModo">
                        <label class="btn btn-outline-danger fw-bold" for="btnradio2"><i class="fa fa-minus-circle"></i> BAJA (Salida / Ajuste)</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS -->
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card shadow-sm h-100 border-0" :class="modoBaja ? 'border-danger' : 'border-primary'" style="border-top: 4px solid !important;">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="mb-3 fw-bold" :class="modoBaja ? 'text-danger' : 'text-primary'">
                            <i class="fa fa-box-open me-2"></i> Catálogo de Productos
                        </h5>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <el-select
                                    v-model="filtro.categoria_producto_id"
                                    filterable
                                    placeholder="Filtrar por Categoría"
                                    clearable
                                    class="w-100"
                                    @change="cargarProductosData"
                                >
                                    <el-option
                                        v-for="item in listCategoriaProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                            </div>
                            <div class="col-md-6">
                                <el-select
                                    v-model="filtro.producto_id"
                                    filterable
                                    placeholder="Buscar un Producto Específico"
                                    clearable
                                    class="w-100"
                                    @change="cargarProductosData"
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body bg-light p-3 overflow-auto" style="max-height: 65vh;">
                        <div v-if="cargaDatos1" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Cargando catálogo...</p>
                        </div>
                        
                        <div v-else-if="listProductosData.length === 0" class="text-center py-5 text-muted">
                            <i class="fa fa-search fa-3x mb-3 text-light"></i>
                            <h5>No se encontraron productos</h5>
                        </div>

                        <div v-else class="row g-3">
                            <div class="col-md-6" v-for="item in listProductosData" :key="item.id">
                                <div class="card h-100 border-0 shadow-sm producto-card">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ item.nombre }}</h6>
                                            <span class="badge rounded-pill" :class="item.stock_actual <= 0 ? 'bg-danger' : 'bg-secondary'">Stock: {{ item.stock_actual }}</span>
                                        </div>
                                        <div class="mt-auto pt-3 border-top">
                                            <div class="row g-2 align-items-end">
                                                <div :class="modoBaja ? 'col-4' : 'col-5'">
                                                    <label class="form-label text-xs mb-1 text-muted">Cant.</label>
                                                    <input type="number" class="form-control form-control-sm text-center" v-model="item.ingreso_cantidad" min="1">
                                                </div>
                                                
                                                <div v-if="!modoBaja" class="col-7">
                                                    <label class="form-label text-xs mb-1 text-muted">Precio Unit. (Bs)</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light text-muted border-end-0">Bs.</span>
                                                        <input type="number" class="form-control form-control-sm text-end border-start-0 ps-0" v-model="item.ingreso_precio" min="0" step="0.01">
                                                    </div>
                                                </div>

                                                <div v-if="modoBaja" class="col-8">
                                                    <label class="form-label text-xs mb-1 text-muted">Motivo de la Baja</label>
                                                    <input type="text" class="form-control form-control-sm" v-model="item.motivo_baja" placeholder="Ej: Vencido, Roto...">
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <button class="btn btn-sm w-100 fw-bold" :class="modoBaja ? 'btn-outline-danger' : 'btn-outline-success'" @click="agregarAlCarrito(item)">
                                                        <i :class="modoBaja ? 'fa fa-arrow-right' : 'fa fa-cart-plus'"></i> Añadir a Lista
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: LISTA DE OPERACIONES -->
            <div class="col-lg-5 col-md-12">
                <div class="card shadow-sm h-100 border-0" style="position: sticky; top: 20px;">
                    <div class="card-header text-white" :class="modoBaja ? 'bg-danger' : 'bg-success'">
                        <h5 class="mb-0 fw-bold d-flex justify-content-between align-items-center">
                            <span><i :class="modoBaja ? 'fa fa-clipboard-list me-2' : 'fa fa-shopping-cart me-2'"></i> {{ modoBaja ? 'Lista de Bajas' : 'Carrito de Compras' }}</span>
                            <span class="badge bg-light rounded-pill fs-6" :class="modoBaja ? 'text-danger' : 'text-success'">{{ carrito.length }}</span>
                        </h5>
                    </div>
                    
                    <div class="card-body p-0 overflow-auto" style="max-height: 50vh;">
                        <ul class="list-group list-group-flush" v-if="carrito.length > 0">
                            <li class="list-group-item p-3" v-for="(cartItem, index) in carrito" :key="index">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="w-75">
                                        <h6 class="fw-bold text-dark mb-1">{{ cartItem.nombre }}</h6>
                                        
                                        <p v-if="!modoBaja" class="text-muted text-sm mb-0">
                                            {{ cartItem.cantidad }} unidades x Bs. {{ $formatMoney(cartItem.precio_compra) }}
                                        </p>
                                        <p v-if="modoBaja" class="text-danger text-sm mb-0">
                                            <strong>Restar:</strong> {{ cartItem.cantidad }} unidades <br>
                                            <strong>Motivo:</strong> {{ cartItem.motivo }}
                                        </p>

                                    </div>
                                    <div class="text-end w-25">
                                        <h6 v-if="!modoBaja" class="fw-bold text-success mb-1">Bs. {{ $formatMoney(cartItem.total) }}</h6>
                                        <button class="btn btn-link text-danger p-0 text-sm mt-1" style="text-decoration: none;" @click="eliminarDelCarrito(index)">
                                            <i class="fa fa-trash-alt"></i> Quitar
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                        <div v-else class="text-center py-5 px-3">
                            <i :class="modoBaja ? 'fa fa-clipboard-check fa-4x text-light mb-3' : 'fa fa-cart-arrow-down fa-4x text-light mb-3'"></i>
                            <h5 class="text-muted">Lista vacía</h5>
                            <p class="text-muted text-sm">Selecciona productos del catálogo en la izquierda para añadirlos aquí.</p>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light border-top-0 p-4">
                        <div v-if="!modoBaja" class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-muted mb-0 fw-bold">Total a Pagar:</h5>
                            <h3 class="text-success mb-0 fw-bold">Bs. {{ $formatMoney(granTotal) }}</h3>
                        </div>
                        <div v-if="modoBaja" class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-muted mb-0 fw-bold">Unidades a dar de Baja:</h5>
                            <h3 class="text-danger mb-0 fw-bold">{{ carrito.reduce((acc, i) => acc + i.cantidad, 0) }}</h3>
                        </div>

                        <button 
                            class="btn btn-lg w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center" 
                            :class="modoBaja ? 'btn-danger' : 'btn-success'"
                            :disabled="carrito.length === 0 || registrando"
                            @click="registrarOperacion"
                        >
                            <span v-if="registrando" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <i v-else class="fa fa-check-circle me-2"></i>
                            {{ registrando ? 'Procesando...' : (modoBaja ? 'Confirmar Bajas de Stock' : 'Registrar Compras') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>

<style scoped>
.producto-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.producto-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.text-xs {
    font-size: 0.75rem;
}
.bg-light {
    background-color: #f8f9fa !important;
}
</style>
