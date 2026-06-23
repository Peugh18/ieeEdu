import { onMounted, ref, watch } from 'vue';

export function useDebouncedInertiaFilters(form: { data(): Record<string, unknown> }, applyFiltersCallback: () => void, debounceMs: number = 400) {
    const skipFilterWatch = ref(true);
    let searchTimeout: ReturnType<typeof setTimeout>;

    watch(
        () => Object.values(form.data()),
        () => {
            if (skipFilterWatch.value) return;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFiltersCallback();
            }, debounceMs);
        },
        { deep: true },
    );

    onMounted(() => {
        skipFilterWatch.value = false;
    });

    return {
        skipFilterWatch,
    };
}
