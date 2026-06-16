import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const usePresentacionProductos = () => {
    const initialState = {
        id: 0,
        producto: null,
        producto_id: "",
        nombre: "",
        equivale: "",
        precio: "",
        comi_distribuidor: "",
        comi_vendedor: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setPresentacionProducto = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
        form.producto = item;
        form._method = "POST";
    };

    const limpiarPresentacionProducto = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setPresentacionProducto,
        limpiarPresentacionProducto,
    };
};
