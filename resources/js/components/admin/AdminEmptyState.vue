<script setup lang="ts">
import { Search } from 'lucide-vue-next';

withDefaults(defineProps<{
    title?: string;
    query?: string;
}>(), {
    title: 'Sin resultados',
    query: '',
});
</script>

<template>
    <div class="py-40 flex flex-col items-center justify-center text-center animate-in fade-in duration-700">
        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center text-on-background/10 mb-8 ring-1 ring-on-background/5 dark:bg-[#2a2a1a] dark:ring-primary-fixed/10 dark:text-primary-fixed/10">
            <Search class="h-10 w-10" />
        </div>
        <h3 class="font-serif text-2xl font-bold text-on-background mb-2 dark:text-primary-fixed">{{ title }}</h3>
        <p v-if="query" class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af] dark:text-[#6b7280]">
            No hemos encontrado resultados que coincidan con "<span class="text-on-background dark:text-primary-fixed">{{ query }}</span>"
        </p>
        <slot />
    </div>
</template>
