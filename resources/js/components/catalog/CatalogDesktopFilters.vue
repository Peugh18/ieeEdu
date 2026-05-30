<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

defineProps<{
    modalities: string[];
    categories: { id: number; name: string }[];
}>();

const filterForm = defineModel<{ search: string; modality: string; category: string }>('filterForm', { required: true });
</script>

<template>
    <aside class="hidden lg:block w-full lg:w-72 flex-shrink-0 space-y-10 sticky top-28 self-start">
        <div>
            <div class="relative flex items-center">
                <svg class="absolute left-0 top-3 text-on-surface-variant w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input v-model="filterForm.search" type="text" placeholder="Buscar programa..." class="w-full pl-8 pr-4 py-2 border-0 border-b border-outline-variant bg-transparent text-sm focus:outline-none focus:border-primary focus:border-b-2 focus:ring-0 transition-all text-on-background placeholder:text-on-surface-variant">
            </div>
        </div>
        <div class="p-6 bg-surface-container-highest rounded-3xl border border-outline-variant/10 shadow-sm">
            <h3 class="flex items-center gap-2 text-xs font-bold text-on-surface-variant mb-5 tracking-wider uppercase">Modalidad</h3>
            <div class="space-y-3">
                <button v-for="mod in modalities" :key="mod" @click="filterForm.modality = mod" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-[13px] font-bold tracking-[0.05em] uppercase transition-all" :class="filterForm.modality === mod ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-transparent text-primary hover:bg-surface-container'">{{ mod }}</button>
            </div>
        </div>
        <div class="p-6 bg-surface-container-highest rounded-3xl border border-outline-variant/10 shadow-sm">
            <h3 class="flex items-center gap-2 text-xs font-bold text-on-surface-variant mb-5 tracking-wider uppercase">Escuelas Especializadas</h3>
            <div class="space-y-4 block max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" v-model="filterForm.category" value="Todas las áreas" class="w-4 h-4 text-primary focus:ring-primary border-outline-variant rounded-sm" />
                    <span class="text-sm italic transition-colors" :class="filterForm.category === 'Todas las áreas' ? 'text-on-background font-bold underline' : 'text-on-surface-variant group-hover:text-on-background'">Todas las áreas</span>
                </label>
                <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" v-model="filterForm.category" :value="cat.name" class="w-4 h-4 text-primary focus:ring-primary border-outline-variant rounded-sm" />
                    <span class="text-xs italic transition-colors" :class="filterForm.category === cat.name ? 'text-on-background font-bold underline' : 'text-on-surface-variant group-hover:text-on-background'">{{ cat.name }}</span>
                </label>
            </div>
        </div>
    </aside>
</template>
