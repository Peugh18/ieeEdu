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
  <section class="py-10 md:py-14 bg-surface-container-low border-y border-outline-variant/10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 md:px-8 mb-7 md:mb-10">
      <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-gradient-to-r from-transparent to-outline-variant/30"></div>
        <p class="text-[10px] md:text-xs font-bold tracking-[0.22em] text-on-surface-variant/40 uppercase whitespace-nowrap">Instituciones que confían en nosotros</p>
        <div class="flex-1 h-px bg-gradient-to-l from-transparent to-outline-variant/30"></div>
      </div>
    </div>
    <div class="relative">
      <div class="absolute left-0 top-0 h-full w-20 md:w-32 bg-gradient-to-r from-surface-container-low to-transparent z-10 pointer-events-none"></div>
      <div class="absolute right-0 top-0 h-full w-20 md:w-32 bg-gradient-to-l from-surface-container-low to-transparent z-10 pointer-events-none"></div>
      <div class="flex gap-10 md:gap-14 items-center animate-scroll whitespace-nowrap">
        <div v-for="logo in clientLogos" :key="logo.name" class="flex-shrink-0 w-32 md:w-36 lg:w-40 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
          <img :src="logo.src" :alt="logo.name" class="max-h-12 md:max-h-14 w-auto object-contain" />
        </div>
        <div v-for="logo in clientLogos" :key="logo.name + '-dup'" class="flex-shrink-0 w-32 md:w-36 lg:w-40 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
          <img :src="logo.src" :alt="logo.name" class="max-h-12 md:max-h-14 w-auto object-contain" />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
@keyframes scroll-smooth {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
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
