import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useSegmentacionZonas = () => {
    const initialState = {
        id: 0,
        departamento_id: "",
        provincia_id: "",
        ciudad_id: "",
        zona: "",
        color: "#000000",
        segmentacion: [],
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setSegmentacionZona = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarSegmentacionZona = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setSegmentacionZona,
        limpiarSegmentacionZona,
    };
};
