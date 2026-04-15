<script setup lang="ts">
import { ref, onMounted } from 'vue';

const isLoaded = ref(false);

onMounted(() => {
    isLoaded.value = true;
});
</script>

<template>
    <section class="hero-mobile-bg relative min-h-[80vh] sm:min-h-screen flex items-center justify-center overflow-hidden bg-background dark:bg-background pt-20 sm:pt-24 md:pt-20">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-32 right-10 w-96 h-96 bg-tertiary-container/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-radial from-primary-container/3 to-transparent"></div>
        </div>
        <!-- Mobile-only overlay for readable text -->
        <div class="sm:hidden absolute inset-0 pointer-events-none bg-background/90 dark:bg-background/90"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-8 w-full grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center relative z-10">
            <!-- Content -->
            <div :class="['transition-all duration-1000 ease-out', isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
                <!-- Badge -->
                <div class="hidden md:inline-flex items-center gap-2 mb-4 md:mb-8 px-4 py-2 rounded-full bg-primary-container/20 border border-primary-container/30">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs md:text-sm font-bold text-primary tracking-widest uppercase">+15 años liderando</span>
                </div>

                <!-- Main Heading -->
                <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-on-surface leading-snug md:leading-tight mb-4 md:mb-6 font-bold">
                    Excelencia Académica y 
                    <span class="italic text-primary">Liderazgo</span>
                    <br/>Empresarial
                </h1>

                <!-- Description -->
                <p class="text-on-surface-variant text-sm sm:text-base md:text-lg leading-relaxed max-w-xl mb-6 md:mb-10 font-light">
                    Formamos líderes con visión global, integrando teoría económica con la realidad del mercado peruano.
                </p>

                <!-- Stats Row - Hidden on mobile, shown on md and up -->
                <div class="hidden md:grid grid-cols-3 gap-6 mb-10 py-8 border-y border-outline-variant/10">
                    <div>
                        <p class="text-3xl font-bold text-primary">500+</p>
                        <p class="text-on-surface-variant text-sm font-medium">Profesionales formados</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary">98%</p>
                        <p class="text-on-surface-variant text-sm font-medium">Empleabilidad</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary">25+</p>
                        <p class="text-on-surface-variant text-sm font-medium">Programas activos</p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col gap-3 md:gap-4">
                    <button class="w-full md:w-auto bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 md:px-8 py-3 md:py-4 rounded-lg font-bold shadow-2xl hover:shadow-3xl hover:-translate-y-1 transition-all duration-300 transform text-sm md:text-base">
                        Ver Programas
                    </button>
                    <button class="hidden md:block w-full md:w-auto bg-surface-container-highest text-on-surface px-6 md:px-8 py-3 md:py-4 rounded-lg font-bold border border-outline-variant/20 hover:border-primary/30 hover:bg-primary/5 transition-all duration-300 text-sm md:text-base">
                        Más Información
                    </button>
                </div>

                <!-- Trust badge (mobile) -->
                <div class="sm:hidden mt-6">
                    <div class="bg-surface-container-lowest/95 backdrop-blur-xl px-4 py-3 rounded-2xl border border-outline-variant/20 shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-primary font-bold text-base leading-tight">4.9/5</p>
                                <p class="text-xs text-on-surface-variant">437 reseñas</p>
                            </div>
                            <p class="ml-auto text-xs text-on-surface-variant font-medium max-w-[14rem] text-right">
                                “Transformó completamente mi perspectiva profesional”
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Section (mobile + desktop) -->
            <div :class="['hidden sm:block relative transition-all duration-1000 ease-out delay-300', isLoaded ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10']">
                <!-- Main Image Container -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-primary-container/10 to-transparent rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-500"></div>
                    <div class="relative aspect-[4/3] sm:aspect-square rounded-3xl overflow-hidden shadow-2xl border-2 border-outline-variant/20 bg-[#4ca9ac] p-7 sm:p-10 md:p-12 flex items-center justify-center">
                        <img
                            alt="Liderazgo Empresarial"
                            class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBdrYOloe3ZI6nqwNdANvssQTmJWjZWFXnwXgy_3x47iNuSNSJmYUb4MG83PSCUku8n6QZqVJDaf8mUByAcb0voGDcrtOYvYsRqS1cGUcHI3CNzf5xik5vLUELXyzQ34_rEV-fiWCm6vQFqudM7qzYn8MWm8HPid-XhvKVxR2vDmDHCgazm0fhS0AwxLNd25cbY2nnSIYQ2mMEdCjeusVascU0tr8R0ZdvY0r0F0JBr1BSlUP6KSA-aUeaaNRsSAICgQvXw0Xid6sro"
                        />
                    </div>

                    <!-- Floating Card -->
                    <div class="mt-4 sm:mt-0 sm:absolute left-3 right-3 sm:left-4 sm:right-auto sm:-left-6 sm:-bottom-8 md:-left-8 sm:-mt-0 bg-surface-container-lowest/95 backdrop-blur-xl p-4 sm:p-5 md:p-6 rounded-2xl shadow-xl sm:shadow-2xl max-w-none sm:max-w-[240px] border border-outline-variant/20 transform sm:group-hover:-translate-y-2 sm:group-hover:shadow-3xl transition-all duration-500">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-primary font-bold text-lg">4.9/5</p>
                                <p class="text-xs text-on-surface-variant">437 reseñas</p>
                            </div>
                        </div>
                        <p class="text-on-surface-variant text-sm font-medium">
                            "Transformó completamente mi perspectiva profesional"
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="hidden sm:block absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-on-surface-variant/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>
</template>

<style scoped>
.hero-mobile-bg {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

@media (max-width: 639px) {
    .hero-mobile-bg {
        background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=60');
    }
}

@keyframes gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes pulse {
    0%, 100% { animation-play-state: running; }
}

.delay-1000 {
    animation-delay: 1s;
}
</style>
