<script setup lang="ts">
import { Search } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        title?: string;
        query?: string;
    }>(),
    {
        title: 'Sin resultados',
        query: '',
    },
);
</script>

<template>
    <div class="flex flex-col items-center justify-center py-40 text-center duration-700 animate-in fade-in">
        <div
            class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-surface-container text-on-background/10 ring-1 ring-on-background/5 dark:bg-[#2a2a1a] dark:text-primary-fixed/10 dark:ring-primary-fixed/10"
        >
            <Search class="h-10 w-10" />
        </div>
        <h3 class="mb-2 font-serif text-2xl font-bold text-on-background dark:text-primary-fixed">{{ title }}</h3>
        <p v-if="query" class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af] dark:text-[#6b7280]">
            No hemos encontrado resultados que coincidan con "<span class="text-on-background dark:text-primary-fixed">{{ query }}</span
            >"
        </p>
        <slot />
    </div>
</template>
