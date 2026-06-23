<script setup lang="ts">
const clientLogos = [
    { name: 'Antamina', src: '/images/landing/antamina.jpg' },
    { name: 'Barrick', src: '/images/landing/barrick.png' },
    { name: 'Boroo', src: '/images/landing/boroo.png' },
    { name: 'Casa Grande', src: '/images/landing/casa-grande.jpg' },
    { name: 'Colegio Médico del Perú', src: '/images/landing/colegio-medico-del peru.png' },
    { name: 'Danper', src: '/images/landing/danper.png' },
    { name: 'Gobierno Regional La Libertad', src: '/images/landing/gobierno-regional la-libertad.png' },
    { name: 'IICA', src: '/images/landing/IICA.png' },
    { name: 'Leon XIII', src: '/images/landing/Logo-Leonxiii.png' },
    { name: 'PCM', src: '/images/landing/pcm.png' },
    { name: 'Perupetro', src: '/images/landing/peru-petro.jpg' },
    { name: 'Produce', src: '/images/landing/peru-produce.jpg' },
    { name: 'Sociedad Nacional de Industrias', src: '/images/landing/sociedad-nacional-de-industrias.jpg' },
];
</script>

<template>
    <section class="overflow-hidden border-y border-outline-variant/10 bg-surface-container-low py-10 md:py-14">
        <div class="mx-auto mb-7 max-w-7xl px-6 md:mb-10 md:px-8">
            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-gradient-to-r from-transparent to-outline-variant/30"></div>
                <p class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant/40 md:text-xs">
                    Instituciones que confían en nosotros
                </p>
                <div class="h-px flex-1 bg-gradient-to-l from-transparent to-outline-variant/30"></div>
            </div>
        </div>
        <div class="relative">
            <div
                class="pointer-events-none absolute left-0 top-0 z-10 h-full w-20 bg-gradient-to-r from-surface-container-low to-transparent md:w-32"
            ></div>
            <div
                class="pointer-events-none absolute right-0 top-0 z-10 h-full w-20 bg-gradient-to-l from-surface-container-low to-transparent md:w-32"
            ></div>
            <div class="animate-scroll flex items-center gap-10 whitespace-nowrap md:gap-14">
                <div
                    v-for="logo in clientLogos"
                    :key="logo.name"
                    class="flex w-32 flex-shrink-0 items-center justify-center opacity-50 grayscale transition-all duration-300 hover:opacity-100 hover:grayscale-0 md:w-36 lg:w-40"
                >
                    <img :src="logo.src" :alt="logo.name" class="max-h-12 w-auto object-contain md:max-h-14" />
                </div>
                <div
                    v-for="logo in clientLogos"
                    :key="logo.name + '-dup'"
                    class="flex w-32 flex-shrink-0 items-center justify-center opacity-50 grayscale transition-all duration-300 hover:opacity-100 hover:grayscale-0 md:w-36 lg:w-40"
                >
                    <img :src="logo.src" :alt="logo.name" class="max-h-12 w-auto object-contain md:max-h-14" />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes scroll-smooth {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.animate-scroll {
    animation: scroll-smooth 18s linear infinite;
    will-change: transform;
}

.animate-scroll:hover {
    animation-play-state: paused;
}

@media (max-width: 768px) {
    .animate-scroll {
        animation-duration: 12s;
    }
}
</style>
