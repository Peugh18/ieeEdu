<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    modalities: string[];
    categories: { id: number; name: string }[];
    filters: { search?: string; modality?: string; category?: string };
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'apply', filters: { search: string; modality: string; category: string }): void;
}>();

const tempSearch = ref(props.filters.search || '');
const tempModality = ref(props.filters.modality || 'Todos');
const tempCategory = ref(props.filters.category || 'Todas las áreas');

watch(
    () => props.show,
    (val) => {
        if (val) {
            tempSearch.value = props.filters.search || '';
            tempModality.value = props.filters.modality || 'Todos';
            tempCategory.value = props.filters.category || 'Todas las áreas';
        }
    },
);

function apply() {
    emit('apply', { search: tempSearch.value, modality: tempModality.value, category: tempCategory.value });
    emit('close');
}

function clear() {
    tempModality.value = 'Todos';
    tempSearch.value = '';
    tempCategory.value = 'Todas las áreas';
}

const activeCount = ref(0);
watch(
    [tempModality, tempSearch, tempCategory],
    ([m, s, c]) => {
        activeCount.value = [m !== 'Todos' ? 1 : 0, s ? 1 : 0, c !== 'Todas las áreas' ? 1 : 0].reduce((a, b) => a + b, 0);
    },
    { immediate: true },
);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 lg:hidden" @click.self="emit('close')">
        <div class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-surface-container-low shadow-2xl">
            <div class="sticky top-0 flex items-center justify-between border-b border-outline-variant/10 bg-surface-container-low px-6 py-4">
                <h3 class="text-lg font-bold">Filtrar cursos</h3>
                <button
                    @click="emit('close')"
                    class="flex h-8 w-8 items-center justify-center rounded-full transition-colors hover:bg-surface-container"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-6 p-6">
                <div>
                    <label class="mb-3 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Buscar</label>
                    <div class="relative">
                        <svg
                            class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-on-surface-variant"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="tempSearch"
                            type="text"
                            placeholder="Buscar cursos..."
                            class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container py-3.5 pl-12 pr-4 text-sm transition-all focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>
                </div>
                <div>
                    <label class="mb-3 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Modalidad</label>
                    <div class="space-y-2">
                        <button
                            v-for="mod in modalities"
                            :key="mod"
                            @click="tempModality = mod"
                            class="flex w-full items-center justify-between rounded-2xl px-4 py-3.5 text-sm font-medium transition-all"
                            :class="
                                tempModality === mod
                                    ? 'bg-primary text-on-primary shadow-lg'
                                    : 'bg-surface-container text-on-surface hover:bg-surface-container-high'
                            "
                        >
                            <span>{{ mod }}</span>
                            <svg v-if="tempModality === mod" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="mb-3 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Categorías</label>
                    <div class="custom-scrollbar max-h-48 space-y-2 overflow-y-auto pr-2">
                        <button
                            @click="tempCategory = 'Todas las áreas'"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm transition-all"
                            :class="
                                tempCategory === 'Todas las áreas'
                                    ? 'bg-primary/10 font-medium text-primary'
                                    : 'bg-surface-container text-on-surface hover:bg-surface-container-high'
                            "
                        >
                            <span>Todas las áreas</span>
                            <svg v-if="tempCategory === 'Todas las áreas'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="tempCategory = cat.name"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm transition-all"
                            :class="
                                tempCategory === cat.name
                                    ? 'bg-primary/10 font-medium text-primary'
                                    : 'bg-surface-container text-on-surface hover:bg-surface-container-high'
                            "
                        >
                            <span>{{ cat.name }}</span>
                            <svg v-if="tempCategory === cat.name" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button
                    v-if="activeCount > 0"
                    @click="clear"
                    class="w-full py-3 text-sm text-on-surface-variant transition-colors hover:text-primary"
                >
                    Limpiar filtros
                </button>
            </div>
            <div class="sticky bottom-0 border-t border-outline-variant/10 bg-surface-container-low p-6">
                <button
                    @click="apply"
                    class="w-full rounded-2xl bg-primary py-4 text-base font-bold text-on-primary shadow-lg transition-all hover:shadow-xl"
                >
                    Aplicar filtros
                </button>
            </div>
        </div>
        <div class="absolute inset-0 -z-10 bg-black/40"></div>
    </div>
</template>
