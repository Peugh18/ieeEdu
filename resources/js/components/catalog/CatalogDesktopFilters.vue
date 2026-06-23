<script setup lang="ts">
defineProps<{
    modalities: string[];
    categories: { id: number; name: string }[];
}>();

const filterForm = defineModel<{ search: string; modality: string; category: string }>('filterForm', { required: true });
</script>

<template>
    <aside class="sticky top-28 hidden w-full flex-shrink-0 space-y-10 self-start lg:block lg:w-72">
        <div>
            <div class="relative flex items-center">
                <svg class="absolute left-0 top-3 h-5 w-5 text-on-surface-variant" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="filterForm.search"
                    type="text"
                    placeholder="Buscar programa..."
                    class="w-full border-0 border-b border-outline-variant bg-transparent py-2 pl-8 pr-4 text-sm text-on-background transition-all placeholder:text-on-surface-variant focus:border-b-2 focus:border-primary focus:outline-none focus:ring-0"
                />
            </div>
        </div>
        <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-highest p-6 shadow-sm">
            <h3 class="mb-5 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Modalidad</h3>
            <div class="space-y-3">
                <button
                    v-for="mod in modalities"
                    :key="mod"
                    @click="filterForm.modality = mod"
                    class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-[13px] font-bold uppercase tracking-[0.05em] transition-all"
                    :class="
                        filterForm.modality === mod
                            ? 'bg-primary text-on-primary shadow-lg shadow-primary/20'
                            : 'bg-transparent text-primary hover:bg-surface-container'
                    "
                >
                    {{ mod }}
                </button>
            </div>
        </div>
        <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-highest p-6 shadow-sm">
            <h3 class="mb-5 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Escuelas Especializadas</h3>
            <div class="custom-scrollbar block max-h-96 space-y-4 overflow-y-auto pr-2">
                <label class="group flex cursor-pointer items-center gap-3">
                    <input
                        type="radio"
                        v-model="filterForm.category"
                        value="Todas las áreas"
                        class="h-4 w-4 rounded-sm border-outline-variant text-primary focus:ring-primary"
                    />
                    <span
                        class="text-sm italic transition-colors"
                        :class="
                            filterForm.category === 'Todas las áreas'
                                ? 'font-bold text-on-background underline'
                                : 'text-on-surface-variant group-hover:text-on-background'
                        "
                        >Todas las áreas</span
                    >
                </label>
                <label v-for="cat in categories" :key="cat.id" class="group flex cursor-pointer items-center gap-3">
                    <input
                        type="radio"
                        v-model="filterForm.category"
                        :value="cat.name"
                        class="h-4 w-4 rounded-sm border-outline-variant text-primary focus:ring-primary"
                    />
                    <span
                        class="text-xs italic transition-colors"
                        :class="
                            filterForm.category === cat.name
                                ? 'font-bold text-on-background underline'
                                : 'text-on-surface-variant group-hover:text-on-background'
                        "
                        >{{ cat.name }}</span
                    >
                </label>
            </div>
        </div>
    </aside>
</template>
