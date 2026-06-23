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
    <div class="mx-auto mb-10 max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div
            v-if="banner?.image_path"
            :class="['relative aspect-[16/9] overflow-hidden shadow-2xl sm:aspect-[16/5]', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
        >
            <img :src="banner.image_path" :alt="banner.heading || 'Banner'" class="absolute inset-0 h-full w-full object-cover object-center" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>
            <div v-if="banner.show_text" class="relative z-10 flex h-full flex-col justify-center px-6 sm:px-10 md:px-14">
                <div class="max-w-2xl space-y-2 sm:space-y-4">
                    <span
                        class="inline-flex items-center gap-2 self-start rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white/80 backdrop-blur-sm"
                    >
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary"></span>
                        {{ badgeText }}
                    </span>
                    <h1 class="font-serif text-2xl font-bold leading-[1.15] tracking-tight text-white sm:text-3xl md:text-4xl lg:text-5xl">
                        {{ banner.heading || defaultHeading }}
                    </h1>
                    <p class="line-clamp-3 max-w-lg text-xs font-light leading-relaxed text-white/75 sm:line-clamp-none sm:text-sm md:text-base">
                        {{ banner.subheading || defaultSubheading }}
                    </p>
                </div>
            </div>
        </div>
        <div
            v-else
            class="relative flex aspect-[16/9] flex-col justify-center overflow-hidden rounded-[2rem] bg-surface-container-low px-6 sm:aspect-[16/5] sm:px-10 md:px-14"
        >
            <div class="absolute -top-20 left-1/4 h-[500px] w-[500px] rounded-full bg-primary/[0.06] blur-[120px]"></div>
            <div class="absolute -bottom-20 right-1/4 h-[400px] w-[400px] rounded-full bg-tertiary-container/[0.08] blur-[100px]"></div>
            <div class="relative z-10 max-w-2xl space-y-2 sm:space-y-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary sm:text-xs">{{ badgeText }}</p>
                <h1 class="font-serif text-2xl font-bold leading-tight tracking-[-0.02em] text-on-surface sm:text-3xl md:text-5xl">
                    {{ defaultHeading }}
                </h1>
                <p class="line-clamp-3 text-xs text-on-surface-variant sm:line-clamp-none sm:text-sm md:text-lg">{{ defaultSubheading }}</p>
            </div>
        </div>
    </div>
</template>
