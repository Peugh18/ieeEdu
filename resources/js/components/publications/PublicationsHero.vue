<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    banner?: {
        heading: string | null;
        subheading: string | null;
        image_path: string | null;
        show_text: boolean;
    } | null;
    isDashboard?: boolean;
}>();

const heroHeading = computed(() => props.banner?.heading || 'Nuestras Publicaciones Especializadas');
const heroSubheading = computed(
    () => props.banner?.subheading || 'Acceda a nuestra biblioteca de investigación, libros y artículos técnicos para su desarrollo profesional.',
);
const heroBg = computed(() => props.banner?.image_path || '/images/landing/publications_hero.png');
const showText = computed(() => props.banner?.show_text !== false);
</script>

<template>
    <div class="mx-auto mb-10 max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div
            :class="['relative overflow-hidden shadow-2xl transition-all duration-500', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
            style="aspect-ratio: 16 / 5"
        >
            <img :src="heroBg" alt="Publicaciones IEE" class="absolute inset-0 h-full w-full object-cover object-center" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>

            <div v-if="showText" class="relative z-10 flex h-full flex-col justify-center px-8 md:px-14">
                <div class="max-w-2xl space-y-4">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white/80 backdrop-blur-sm"
                    >
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#D4AF37]"></span>
                        Investigación de Alto Nivel
                    </span>
                    <h1 class="font-serif text-2xl font-bold leading-[1.15] tracking-tight text-white sm:text-3xl md:text-4xl lg:text-5xl">
                        {{ heroHeading }}
                    </h1>
                    <p class="max-w-lg text-xs font-light leading-relaxed text-white/75 sm:text-sm md:text-base">
                        {{ heroSubheading }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
