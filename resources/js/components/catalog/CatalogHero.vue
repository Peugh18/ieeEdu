<script setup lang="ts">
import { ref } from 'vue';

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

const imageError = ref(false);
</script>

<template>
    <div class="mx-auto mb-6 max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div
            v-if="banner?.image_path && !imageError"
            :class="[
                'relative flex min-h-[220px] w-full flex-col justify-center overflow-hidden shadow-xl sm:aspect-[16/5] sm:min-h-[auto]',
                isDashboard ? 'rounded-2xl md:rounded-[1.5rem]' : 'rounded-2xl md:rounded-[2rem]'
            ]"
        >
            <img :src="banner.image_path" :alt="banner.heading || 'Banner'" class="absolute inset-0 h-full w-full object-cover object-center" @error="imageError = true" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/65 to-black/30"></div>
            <div v-if="banner.show_text !== false" class="relative z-10 flex h-full flex-col justify-center p-6 sm:p-10 md:px-14">
                <div class="max-w-2xl space-y-2 sm:space-y-4">
                    <span
                        v-if="badgeText"
                        class="inline-flex items-center gap-2 self-start rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white/90 backdrop-blur-md"
                    >
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary"></span>
                        {{ badgeText }}
                    </span>
                    <h1 class="font-serif text-xl font-bold leading-tight tracking-tight text-white sm:text-3xl md:text-4xl lg:text-5xl">
                        {{ banner.heading || defaultHeading }}
                    </h1>
                    <p class="line-clamp-2 max-w-lg text-xs font-light leading-relaxed text-white/80 sm:line-clamp-none sm:text-sm md:text-base">
                        {{ banner.subheading || defaultSubheading }}
                    </p>
                </div>
            </div>
        </div>
        <div
            v-else
            :class="[
                'relative flex min-h-[200px] flex-col justify-center overflow-hidden border border-outline-variant/15 bg-surface-container-low px-6 py-8 sm:aspect-[16/5] sm:min-h-[auto] sm:px-10 sm:py-0 md:px-14',
                isDashboard ? 'rounded-2xl md:rounded-[1.5rem]' : 'rounded-2xl md:rounded-[2rem]'
            ]"
        >
            <div class="absolute -top-20 left-1/4 h-[300px] w-[300px] rounded-full bg-primary/[0.08] blur-[80px]"></div>
            <div class="absolute -bottom-20 right-1/4 h-[300px] w-[300px] rounded-full bg-tertiary-container/[0.1] blur-[80px]"></div>
            <div class="relative z-10 max-w-2xl space-y-2 sm:space-y-4">
                <p v-if="badgeText" class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary sm:text-xs">{{ badgeText }}</p>
                <h1 class="font-serif text-xl font-bold leading-tight tracking-[-0.02em] text-on-surface sm:text-3xl md:text-5xl">
                    {{ defaultHeading }}
                </h1>
                <p class="line-clamp-3 text-xs text-on-surface-variant sm:line-clamp-none sm:text-sm md:text-lg">{{ defaultSubheading }}</p>
            </div>
        </div>
    </div>
</template>
