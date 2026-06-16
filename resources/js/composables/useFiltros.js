import { ref, watch } from "vue";

export function useFiltros(key, defaultValues) {
    const filtros = ref(defaultValues);

    const storageKey = "filtros_" + key;
    const saved = sessionStorage.getItem(storageKey);

    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            // Fusionamos los valores por defecto con los guardados,
            // por si se agregan nuevas propiedades en el futuro a defaultValues.
            filtros.value = { ...defaultValues, ...parsed };
        } catch (e) {
            console.error("Error parsing saved filters", e);
        }
    }

    watch(
        filtros,
        (newVal) => {
            sessionStorage.setItem(storageKey, JSON.stringify(newVal));
        },
        { deep: true }
    );

    return filtros;
}
