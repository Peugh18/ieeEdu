<script setup lang="ts">
import type { PaginationLink } from '@/types/pagination';
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-20 flex justify-center">
        <div class="flex flex-wrap gap-4">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="rounded-xl px-5 py-2.5 text-xs font-bold text-outline-variant opacity-50"
                    v-html="link.label"
                ></div>
                <Link
                    v-else
                    :href="link.url"
                    class="rounded-xl px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-all"
                    :class="{
                        'bg-primary text-white shadow-xl shadow-primary/20': link.active,
                        'border border-outline-variant/20 bg-surface-container text-on-surface-variant hover:bg-surface-container-high': !link.active,
                    }"
                    v-html="link.label"
                    preserve-scroll
                    preserve-state
                />
            </template>
        </div>
    </div>
</template>
