<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginationLink } from '@/types/pagination';

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-20 flex justify-center">
        <div class="flex flex-wrap gap-4">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" class="px-5 py-2.5 rounded-xl text-xs text-outline-variant opacity-50 font-bold" v-html="link.label"></div>
                <Link v-else :href="link.url" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all" :class="{ 'bg-primary text-white shadow-xl shadow-primary/20': link.active, 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high border border-outline-variant/20': !link.active }" v-html="link.label" preserve-scroll preserve-state />
            </template>
        </div>
    </div>
</template>
