import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useProductos = () => {
    const initialState = {
        id: 0,
        nombre: "",
        descripcion: "",
        categoria_producto_id: "",
        stock_min: "",
        stock_actual: "",
        estado: 1,
        precio_compra: "",
        imagen: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setProducto = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarProducto = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setProducto,
        limpiarProducto,
    };
};
