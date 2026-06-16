import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useClientes = () => {
    const initialState = {
        id: 0,
        nombre: "",
        fono: "",
        razon_social: "",
        nit_ci: "",
        dir: "",
        tipo_negocio_id: null,
        latitud: null,
        longitud: null,
        segmentacion_zona_id: null,
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setCliente = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarCliente = () => {
        form.clearErrors();
        form.defaults({ ...initialState });
        form.reset();
        Object.assign(form, initialState);
    };

    onMounted(() => {});

    return {
        form,
        setCliente,
        limpiarCliente,
    };
};
