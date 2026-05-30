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
const heroSubheading = computed(() => props.banner?.subheading || 'Acceda a nuestra biblioteca de investigación, libros y artículos técnicos para su desarrollo profesional.');
const heroBg = computed(() => props.banner?.image_path || '/images/landing/publications_hero.png');
const showText = computed(() => props.banner?.show_text !== false);
</script>

<template>
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div
            :class="['relative overflow-hidden shadow-2xl transition-all duration-500', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
            style="aspect-ratio: 16 / 5;"
        >
            <img :src="heroBg" alt="Publicaciones IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>

            <div v-if="showText" class="relative z-10 h-full px-8 md:px-14 flex flex-col justify-center">
                <div class="max-w-2xl space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></span>
                        Investigación de Alto Nivel
                    </span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                        {{ heroHeading }}
                    </h1>
                    <p class="text-xs sm:text-sm md:text-base text-white/75 max-w-lg font-light leading-relaxed">
                        {{ heroSubheading }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
