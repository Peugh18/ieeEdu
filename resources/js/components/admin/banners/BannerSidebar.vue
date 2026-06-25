<script setup lang="ts">
import type { BannerSection } from '@/types/banner';

const activeBannerId = defineModel<string>('activeBannerId', { required: true });

defineProps<{
    banners: BannerSection[];
}>();
</script>

<template>
    <div class="lg:col-span-1">
        <div class="rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-lowest p-4 shadow-sm">
            <div class="mb-4 px-4 pt-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant">Apartados Web</div>
            <nav class="flex flex-col gap-1.5">
                <button
                    v-for="banner in banners"
                    :key="banner.id"
                    @click="activeBannerId = banner.id"
                    :class="[
                        'flex items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-300 hover:opacity-90',
                        activeBannerId === banner.id ? 'bg-primary text-white shadow-md' : 'text-on-surface-variant hover:bg-surface-container-high',
                    ]"
                >
                    <component :is="banner.icon" class="h-5 w-5" />
                    {{ banner.title }}
                </button>
            </nav>
        </div>
    </div>
</template>
