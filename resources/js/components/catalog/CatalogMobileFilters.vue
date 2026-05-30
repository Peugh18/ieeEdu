<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
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

watch(() => props.show, (val) => {
    if (val) {
        tempSearch.value = props.filters.search || '';
        tempModality.value = props.filters.modality || 'Todos';
        tempCategory.value = props.filters.category || 'Todas las áreas';
    }
});

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
watch([tempModality, tempSearch, tempCategory], ([m, s, c]) => {
    activeCount.value = [m !== 'Todos' ? 1 : 0, s ? 1 : 0, c !== 'Todas las áreas' ? 1 : 0].reduce((a, b) => a + b, 0);
}, { immediate: true });
</script>

<template>
    <div v-if="show" class="lg:hidden fixed inset-0 z-50" @click.self="emit('close')">
        <div class="absolute inset-x-0 bottom-0 bg-surface-container-low rounded-t-3xl shadow-2xl max-h-[85vh] overflow-y-auto">
            <div class="sticky top-0 bg-surface-container-low px-6 py-4 border-b border-outline-variant/10 flex items-center justify-between">
                <h3 class="font-bold text-lg">Filtrar cursos</h3>
                <button @click="emit('close')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-3 uppercase tracking-wider">Buscar</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input v-model="tempSearch" type="text" placeholder="Buscar cursos..." class="w-full pl-12 pr-4 py-3.5 bg-surface-container rounded-2xl border border-outline-variant/20 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-3 uppercase tracking-wider">Modalidad</label>
                    <div class="space-y-2">
                        <button v-for="mod in modalities" :key="mod" @click="tempModality = mod" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-medium transition-all" :class="tempModality === mod ? 'bg-primary text-on-primary shadow-lg' : 'bg-surface-container text-on-surface hover:bg-surface-container-high'">
                            <span>{{ mod }}</span>
                            <svg v-if="tempModality === mod" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-3 uppercase tracking-wider">Categorías</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        <button @click="tempCategory = 'Todas las áreas'" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all" :class="tempCategory === 'Todas las áreas' ? 'bg-primary/10 text-primary font-medium' : 'bg-surface-container text-on-surface hover:bg-surface-container-high'">
                            <span>Todas las áreas</span>
                            <svg v-if="tempCategory === 'Todas las áreas'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        <button v-for="cat in categories" :key="cat.id" @click="tempCategory = cat.name" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all" :class="tempCategory === cat.name ? 'bg-primary/10 text-primary font-medium' : 'bg-surface-container text-on-surface hover:bg-surface-container-high'">
                            <span>{{ cat.name }}</span>
                            <svg v-if="tempCategory === cat.name" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>
                <button v-if="activeCount > 0" @click="clear" class="w-full py-3 text-sm text-on-surface-variant hover:text-primary transition-colors">Limpiar filtros</button>
            </div>
            <div class="sticky bottom-0 bg-surface-container-low p-6 border-t border-outline-variant/10">
                <button @click="apply" class="w-full py-4 bg-primary text-on-primary rounded-2xl font-bold text-base shadow-lg hover:shadow-xl transition-all">Aplicar filtros</button>
            </div>
        </div>
        <div class="absolute inset-0 bg-black/40 -z-10"></div>
    </div>
</template>
