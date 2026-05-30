<script setup lang="ts">
defineProps<{
    banner?: {
        image_path?: string | null;
        heading?: string;
        subheading?: string;
        show_text?: boolean;
    } | null;
    isDashboard?: boolean;
    defaultHeading: string;
    defaultSubheading: string;
    badgeText?: string;
}>();
</script>

<template>
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div v-if="banner?.image_path"
            :class="['relative overflow-hidden shadow-2xl', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
            style="aspect-ratio: 16 / 5;">
            <img :src="banner.image_path" :alt="banner.heading || 'Banner'" class="absolute inset-0 w-full h-full object-cover object-center" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>
            <div v-if="banner.show_text" class="relative z-10 h-full px-10 md:px-14 flex flex-col justify-center">
                <div class="max-w-2xl space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                        {{ badgeText }}
                    </span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">{{ banner.heading || defaultHeading }}</h1>
                    <p class="text-base text-white/75 max-w-lg font-light leading-relaxed">{{ banner.subheading || defaultSubheading }}</p>
                </div>
            </div>
        </div>
        <div v-else class="relative overflow-hidden rounded-[2rem] bg-surface-container-low py-20 px-10 md:px-14 flex flex-col justify-center" style="aspect-ratio: 16 / 5;">
            <div class="absolute -top-20 left-1/4 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-20 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
            <div class="relative z-10 max-w-2xl space-y-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">{{ badgeText }}</p>
                <h1 class="text-3xl md:text-5xl font-serif font-bold leading-tight tracking-[-0.02em] text-on-surface">{{ defaultHeading }}</h1>
                <p class="text-lg text-on-surface-variant">{{ defaultSubheading }}</p>
            </div>
        </div>
    </div>
</template>
