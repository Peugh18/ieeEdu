<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import HeroSection from '../components/landing/HeroSection.vue';
import Navigation from '../components/landing/Navigation.vue';
import CourseCard from '@/components/CourseCard.vue';

const props = defineProps<{
    dynamicCourses: any[];
    teaserBooks: any[];
    teaserArticles: any[];
}>();

const courseTabs = [
    { id: 'en vivo', label: 'En Vivo' },
    { id: 'grabado', label: 'Grabados 24/7' },
];

const activeCourseTab = ref('en vivo');
const filteredCourses = computed(() => {
    if (!activeCourseTab.value) return props.dynamicCourses;
    return props.dynamicCourses.filter((course) => course.type === activeCourseTab.value);
});

const publicationTabs = [
    { id: 'free', label: 'Gratuitos' },
    { id: 'paid', label: 'Premium' },
];

const activePublicationTab = ref('free');
const filteredPublications = computed(() => {
    if (activePublicationTab.value === 'free') {
        return props.teaserBooks.filter((b) => !b.price || Number(b.price) === 0);
    }
    return props.teaserBooks.filter((b) => b.price && Number(b.price) > 0);
});

let revealObserver: IntersectionObserver | null = null;
onMounted(() => {
    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('revealed');
                revealObserver?.unobserve(e.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => revealObserver?.observe(el));
});
onUnmounted(() => { revealObserver?.disconnect(); });

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
    <Head title="IEE - Instituto de Economía y Empresa">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="flex min-h-screen flex-col bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container">
        <!-- Navigation -->
        <Navigation />

        <main class="pt-0">
            <!-- Hero Section -->
            <HeroSection />

            <!-- Clients Section -->
            <section class="py-10 md:py-14 bg-surface-container-low border-y border-outline-variant/10 overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 md:px-8 mb-7 md:mb-10">
                    <div class="flex items-center gap-4">
                        <div class="flex-1 h-px bg-gradient-to-r from-transparent to-outline-variant/30"></div>
                        <p class="text-[10px] md:text-xs font-bold tracking-[0.22em] text-on-surface-variant/40 uppercase whitespace-nowrap">Instituciones que confían en nosotros</p>
                        <div class="flex-1 h-px bg-gradient-to-l from-transparent to-outline-variant/30"></div>
                    </div>
                </div>
                <!-- Carousel wrapper with fade masks -->
                <div class="relative">
                    <div class="absolute left-0 top-0 h-full w-20 md:w-32 bg-gradient-to-r from-surface-container-low to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 h-full w-20 md:w-32 bg-gradient-to-l from-surface-container-low to-transparent z-10 pointer-events-none"></div>
                    <div class="flex gap-8 md:gap-14 items-center animate-scroll whitespace-nowrap">
                        <div v-for="logo in clientLogos" :key="logo.name" class="flex-shrink-0 w-24 md:w-32 lg:w-36 flex items-center justify-center grayscale opacity-40 hover:grayscale-0 hover:opacity-90 transition-all duration-500">
                            <img :src="logo.src" :alt="logo.name" class="max-h-10 md:max-h-12 w-auto object-contain" />
                        </div>
                        <div v-for="logo in clientLogos" :key="logo.name + '-dup'" class="flex-shrink-0 w-24 md:w-32 lg:w-36 flex items-center justify-center grayscale opacity-40 hover:grayscale-0 hover:opacity-90 transition-all duration-500">
                            <img :src="logo.src" :alt="logo.name" class="max-h-10 md:max-h-12 w-auto object-contain" />
                        </div>
                    </div>
                </div>
            </section>
            <section id="cursos" class="py-20 md:py-32 bg-surface relative reveal">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-1/3 right-0 w-[500px] h-[500px] bg-primary/[0.04] rounded-full blur-[120px]"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-tertiary-container/[0.06] rounded-full blur-[100px]"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">
                    <!-- Section label -->
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-[11px] font-bold text-primary/50 tracking-[0.25em] uppercase font-serif">01</span>
                        <div class="w-8 h-px bg-primary/30"></div>
                        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Oferta Académica</span>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-8">
                        <div>
                            <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight">
                                Programas de <span class="italic text-primary">Especialización</span>
                            </h2>
                            <p class="text-on-surface-variant max-w-xl text-base leading-relaxed">
                                Formación avanzada para líderes con metodología comprobada: en vivo o a tu ritmo.
                            </p>
                        </div>
                        <a href="/cursos" class="group flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface font-bold text-sm hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
                            Ver todos los cursos
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>

                    <!-- Pill-style tabs -->
                    <div class="inline-flex items-center p-1 rounded-2xl bg-surface-container border border-outline-variant/15 mb-10 gap-1">
                        <button
                            v-for="tab in courseTabs"
                            :key="tab.id"
                            @click.prevent="activeCourseTab = tab.id"
                            :class="[
                                'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200',
                                activeCourseTab === tab.id
                                    ? 'bg-primary text-on-primary shadow-md'
                                    : 'text-on-surface-variant hover:text-on-surface'
                            ]"
                        >
                            <svg v-if="tab.id === 'en vivo'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ tab.label }}
                        </button>
                    </div>

                    <!-- Course Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                        <CourseCard 
                            v-for="course in filteredCourses"
                            :key="course.id"
                            :course="course"
                        />
                        <template v-if="filteredCourses.length === 0">
                            <div class="col-span-full py-20 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-on-surface-variant/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-on-surface-variant font-medium mb-1">Sin cursos disponibles</p>
                                <p class="text-on-surface-variant/50 text-sm">Pronto tendremos nuevos programas en esta modalidad.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Consultoría Teaser -->
            <section id="consultoria" class="py-20 md:py-32 bg-surface-container-low overflow-hidden relative reveal">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                    <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                </div>
                <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">
                    <!-- Section label -->
                    <div class="flex items-center gap-3 mb-10">
                        <span class="text-[11px] font-bold text-primary/50 tracking-[0.25em] uppercase font-serif">02</span>
                        <div class="w-8 h-px bg-primary/30"></div>
                        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Soluciones Corporativas</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 items-center">

                        <!-- Left: Teaser Content -->
                        <div>
                            <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-5 leading-tight">
                                ¿Tu empresa necesita <span class="italic text-primary">asesoría estratégica</span>?
                            </h2>
                            <p class="text-on-surface-variant text-base md:text-lg mb-8 leading-relaxed">
                                Ofrecemos consultoría especializada para instituciones públicas y privadas. Diagnóstico, planificación y ejecución con expertos del mercado peruano.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                                <div class="bg-surface-container rounded-2xl p-5 border border-outline-variant/20 hover:border-primary/30 hover:shadow-md transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center mb-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </div>
                                    <h4 class="font-serif font-bold text-on-surface text-sm mb-1">Diagnóstico</h4>
                                    <p class="text-on-surface-variant text-xs leading-relaxed">Auditoría de capacidades técnicas</p>
                                </div>
                                <div class="bg-surface-container rounded-2xl p-5 border border-outline-variant/20 hover:border-primary/30 hover:shadow-md transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-tertiary-container/30 flex items-center justify-center mb-3">
                                        <svg class="w-5 h-5 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <h4 class="font-serif font-bold text-on-surface text-sm mb-1">Currícula</h4>
                                    <p class="text-on-surface-variant text-xs leading-relaxed">Diseño de programas a medida</p>
                                </div>
                                <div class="bg-surface-container rounded-2xl p-5 border border-outline-variant/20 hover:border-primary/30 hover:shadow-md transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-secondary-container/40 flex items-center justify-center mb-3">
                                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                    </div>
                                    <h4 class="font-serif font-bold text-on-surface text-sm mb-1">Proyección</h4>
                                    <p class="text-on-surface-variant text-xs leading-relaxed">Escenarios macro y estrategia</p>
                                </div>
                            </div>
                            <a href="/consultoria" class="group inline-flex items-center gap-3 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-base shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                                Ver todos los servicios
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>

                        <!-- Right: Stats + CTA Card -->
                        <div class="relative">
                            <!-- Glow behind card -->
                            <div class="absolute -inset-4 bg-primary/8 rounded-[2.5rem] blur-2xl pointer-events-none"></div>
                            <div class="relative bg-surface-container rounded-3xl p-8 md:p-10 border border-outline-variant/15 shadow-2xl overflow-hidden">
                                <!-- Subtle gradient top strip -->
                                <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-primary/40 to-transparent"></div>
                                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-outline-variant/10">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-6 h-6 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-serif font-bold text-on-surface text-lg leading-tight">+15 años asesorando</h3>
                                        <p class="text-on-surface-variant text-sm">empresas e instituciones del Perú</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-8">
                                    <div class="group bg-surface hover:bg-primary/5 rounded-2xl p-4 border border-outline-variant/10 hover:border-primary/20 text-center transition-all duration-300">
                                        <p class="text-3xl font-bold text-primary mb-1 font-serif">500+</p>
                                        <p class="text-xs text-on-surface-variant font-medium leading-tight">Profesionales formados</p>
                                    </div>
                                    <div class="group bg-surface hover:bg-primary/5 rounded-2xl p-4 border border-outline-variant/10 hover:border-primary/20 text-center transition-all duration-300">
                                        <p class="text-3xl font-bold text-primary mb-1 font-serif">98%</p>
                                        <p class="text-xs text-on-surface-variant font-medium leading-tight">Satisfacción</p>
                                    </div>
                                    <div class="group bg-surface hover:bg-primary/5 rounded-2xl p-4 border border-outline-variant/10 hover:border-primary/20 text-center transition-all duration-300">
                                        <p class="text-3xl font-bold text-primary mb-1 font-serif">25+</p>
                                        <p class="text-xs text-on-surface-variant font-medium leading-tight">Programas activos</p>
                                    </div>
                                    <div class="group bg-surface hover:bg-primary/5 rounded-2xl p-4 border border-outline-variant/10 hover:border-primary/20 text-center transition-all duration-300">
                                        <p class="text-3xl font-bold text-primary mb-1 font-serif">13+</p>
                                        <p class="text-xs text-on-surface-variant font-medium leading-tight">Instituciones aliadas</p>
                                    </div>
                                </div>
                                <a href="/consultoria" class="group w-full flex items-center justify-center gap-2.5 px-6 py-4 bg-primary text-on-primary rounded-xl font-bold text-sm hover:opacity-90 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Agendar Consultoría Gratuita
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Biblioteca de Libros -->
            <section id="publicaciones" class="py-20 md:py-32 bg-surface relative overflow-hidden reveal">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-1/3 left-0 w-[500px] h-[500px] bg-primary/[0.03] rounded-full blur-[120px]"></div>
                    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.06] rounded-full blur-[100px]"></div>
                </div>
                <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">

                    <!-- Section label -->
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-[11px] font-bold text-primary/50 tracking-[0.25em] uppercase font-serif">03</span>
                        <div class="w-8 h-px bg-primary/30"></div>
                        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Publicaciones</span>
                    </div>

                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
                        <div>
                            <h2 class="font-serif text-3xl md:text-5xl text-on-surface leading-tight mb-3">
                                Biblioteca de <span class="italic text-primary">Libros</span>
                            </h2>
                            <p class="text-on-surface-variant text-base max-w-lg leading-relaxed">
                                Recursos digitales gestionados por nuestro equipo editorial — gratuitos y premium.
                            </p>
                        </div>
                        <a href="/publicaciones" class="group flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface font-bold text-sm hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
                            Ver toda la biblioteca
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>

                    <!-- Tab switcher -->
                    <div class="inline-flex items-center p-1 rounded-2xl bg-surface-container border border-outline-variant/15 mb-10 gap-1">
                        <button
                            v-for="tab in publicationTabs"
                            :key="tab.id"
                            @click.prevent="activePublicationTab = tab.id"
                            :class="[
                                'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200',
                                activePublicationTab === tab.id
                                    ? 'bg-primary text-on-primary shadow-md'
                                    : 'text-on-surface-variant hover:text-on-surface'
                            ]"
                        >
                            <svg v-if="tab.id === 'free'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            {{ tab.label }}
                            <span :class="['text-[10px] font-bold px-1.5 py-0.5 rounded-full', activePublicationTab === tab.id ? 'bg-white/20 text-white' : 'bg-outline-variant/20 text-on-surface-variant']">
                                {{ tab.id === 'free' ? teaserBooks.filter(b => !b.price || Number(b.price) === 0).length : teaserBooks.filter(b => b.price && Number(b.price) > 0).length }}
                            </span>
                        </button>
                    </div>

                    <!-- Book Grid -->
                    <div v-if="filteredPublications.length > 0" class="grid gap-5 [grid-template-columns:repeat(auto-fill,minmax(240px,1fr))]">
                        <div
                            v-for="book in filteredPublications"
                            :key="book.id"
                            class="group relative bg-surface-container rounded-2xl border border-outline-variant/15 overflow-hidden hover:shadow-2xl hover:border-primary/20 hover:-translate-y-1 transition-all duration-300 flex flex-col"
                        >
                            <!-- Cover image or gradient placeholder -->
                            <div class="relative h-44 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20 flex-shrink-0">
                                <img
                                    v-if="book.cover_image"
                                    :src="book.cover_image"
                                    :alt="book.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div v-else class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <!-- Badge on image -->
                                <div class="absolute top-3 left-3">
                                    <span :class="['text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full backdrop-blur-sm', !book.price || Number(book.price) === 0 ? 'bg-green-500/90 text-white' : 'bg-primary/90 text-on-primary']">
                                        {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <p v-if="book.category" class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ book.category }}</p>
                                <h3 class="font-serif text-sm text-on-surface font-bold leading-snug mb-1 flex-1 line-clamp-2">{{ book.title }}</h3>
                                <p v-if="book.author" class="text-on-surface-variant/60 text-xs mb-4">{{ book.author }}</p>
                                <div class="flex items-center justify-between mt-auto pt-3 border-t border-outline-variant/10">
                                    <span class="text-lg font-bold text-primary font-serif">
                                        {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                                    </span>
                                    <a
                                        :href="(!book.price || Number(book.price) === 0) ? (book.download_url || book.file_path || '/publicaciones') : '/publicaciones'"
                                        :target="(!book.price || Number(book.price) === 0) && (book.download_url || book.file_path) ? '_blank' : '_self'"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold hover:opacity-90 hover:shadow-md active:scale-95 transition-all"
                                    >
                                        {{ !book.price || Number(book.price) === 0 ? 'Descargar' : 'Ver más' }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="py-24 text-center">
                        <div class="w-20 h-20 rounded-3xl bg-surface-container mx-auto flex items-center justify-center mb-5 border border-outline-variant/15">
                            <svg class="w-10 h-10 text-on-surface-variant/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-on-surface-variant font-serif text-lg font-bold mb-1">Sin publicaciones aún</p>
                        <p class="text-on-surface-variant/50 text-sm">El equipo editorial pronto añadirá libros en esta categoría.</p>
                    </div>
                </div>
            </section>

            <!-- CTA Final - Listo para impulsar -->
            <section class="relative overflow-hidden bg-surface-container-low reveal">
                <!-- Gradient blobs -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-primary/[0.07] rounded-full blur-[120px] -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px] translate-y-1/2"></div>
                </div>

                <div class="relative z-10 max-w-6xl mx-auto px-6 md:px-8 py-24 md:py-36">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                        <!-- Left: Main CTA -->
                        <div>
                            <!-- Badge -->
                            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-primary/[0.08] border border-primary/15 mb-8">
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                </span>
                                <span class="text-xs font-bold text-primary tracking-[0.18em] uppercase">Impulsa tu desarrollo profesional</span>
                            </div>

                            <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl text-on-surface mb-6 leading-[1.1] font-bold">
                                ¿Listo para<br/>
                                <span class="italic text-primary">impulsar tu<br/>aprendizaje?</span>
                            </h2>
                            <p class="text-on-surface-variant text-lg leading-relaxed mb-10 max-w-md">
                                Únete a miles de profesionales del Perú. Cursos en vivo, grabados y masterclasses para crecer a tu ritmo.
                            </p>

                            <!-- Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="/cursos" class="group inline-flex items-center justify-center gap-2.5 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Explorar Cursos
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                                <a href="/masterclass" class="group inline-flex items-center justify-center gap-2.5 px-8 py-4 bg-surface-container border border-outline-variant/30 text-on-surface rounded-xl font-bold text-sm hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Ver Masterclasses
                                    <span class="ml-1 px-2 py-0.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-[10px] font-bold uppercase tracking-wider">Gratis</span>
                                </a>
                            </div>
                        </div>

                        <!-- Right: Feature cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Masterclass card (destacada) -->
                            <div class="sm:col-span-2 relative rounded-2xl overflow-hidden border border-outline-variant/20 bg-surface-container p-6 hover:border-primary/20 hover:shadow-md transition-all group">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-serif font-bold text-on-surface text-base">Masterclasses</h4>
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-500/20 text-emerald-600 text-[10px] font-bold uppercase">Primera gratis</span>
                                        </div>
                                        <p class="text-on-surface-variant text-sm leading-relaxed">Sesiones en vivo con expertos del sector empresarial y público. Tu primera masterclass es completamente gratuita.</p>
                                    </div>
                                </div>
                                <a href="/masterclass" class="absolute inset-0" aria-label="Ver Masterclasses"></a>
                            </div>

                            <div class="relative rounded-2xl border border-outline-variant/20 bg-surface-container p-5 hover:border-primary/20 hover:shadow-md transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-primary/[0.08] flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <h4 class="font-serif font-bold text-on-surface text-sm mb-1">Cursos en Vivo</h4>
                                <p class="text-on-surface-variant text-xs leading-relaxed">Clases sincrónicas con docentes expertos e interacción en tiempo real.</p>
                                <a href="/cursos" class="absolute inset-0" aria-label="Ver Cursos en Vivo"></a>
                            </div>

                            <div class="relative rounded-2xl border border-outline-variant/20 bg-surface-container p-5 hover:border-primary/20 hover:shadow-md transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-primary/[0.08] flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h4 class="font-serif font-bold text-on-surface text-sm mb-1">Cursos Grabados</h4>
                                <p class="text-on-surface-variant text-xs leading-relaxed">Aprende a tu ritmo, 24/7, desde cualquier dispositivo.</p>
                                <a href="/cursos" class="absolute inset-0" aria-label="Ver Cursos Grabados"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-surface-container relative">
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
            <div class="max-w-7xl mx-auto px-6 md:px-8 pt-16 pb-10 md:pt-20 md:pb-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 md:gap-12">
                    <!-- Brand col (wider) -->
                    <div class="lg:col-span-2">
                        <img src="/images/empresa/IEE-Logo02.png" alt="IEE Instituto" class="h-14 w-auto object-contain mb-5" />
                        <p class="text-on-surface-variant text-sm leading-relaxed max-w-xs mb-6">
                            Formamos líderes con visión global desde Trujillo, Perú. Más de 15 años transformando el sector empresarial y público.
                        </p>
                        <!-- Social Icons -->
                        <div class="flex gap-3">
                            <a href="#" class="w-9 h-9 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-9 h-9 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="#" class="w-9 h-9 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Académico -->
                    <div>
                        <h4 class="font-serif text-sm font-bold text-on-surface mb-5 uppercase tracking-wider">Académico</h4>
                        <ul class="space-y-3">
                            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Cursos en Vivo</a></li>
                            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Cursos Grabados</a></li>
                            <li><a href="/masterclass" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Masterclasses</a></li>
                            <li><a href="/planes" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Planes Premium</a></li>
                        </ul>
                    </div>

                    <!-- Servicios -->
                    <div>
                        <h4 class="font-serif text-sm font-bold text-on-surface mb-5 uppercase tracking-wider">Servicios</h4>
                        <ul class="space-y-3">
                            <li><a href="/consultoria" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Consultoría</a></li>
                            <li><a href="/publicaciones" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Biblioteca</a></li>
                            <li><a href="/planes" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Suscripciones</a></li>
                            <li><a href="/login" class="text-on-surface-variant text-sm hover:text-primary transition-colors flex items-center gap-1.5 group"><svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>Aula Virtual</a></li>
                        </ul>
                    </div>

                    <!-- Contacto -->
                    <div>
                        <h4 class="font-serif text-sm font-bold text-on-surface mb-5 uppercase tracking-wider">Contacto</h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2 text-on-surface-variant text-sm">
                                <svg class="w-4 h-4 mt-0.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Trujillo, La Libertad, Perú
                            </li>
                            <li class="flex items-start gap-2 text-on-surface-variant text-sm">
                                <svg class="w-4 h-4 mt-0.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                info@iee.edu.pe
                            </li>
                            <li class="mt-4">
                                <a href="/login" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-lg text-xs font-bold hover:shadow-md hover:-translate-y-0.5 transition-all">
                                    Acceder al Aula
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Divider -->
                <div class="mt-12 pt-8 border-t border-outline-variant/10 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-on-surface-variant/50 text-xs">© {{ new Date().getFullYear() }} Instituto de Economía y Empresa. Todos los derechos reservados.</p>
                    <div class="flex items-center gap-5 text-xs text-on-surface-variant/50">
                        <a href="#" class="hover:text-primary transition-colors">Privacidad</a>
                        <span class="w-px h-3 bg-outline-variant/30"></span>
                        <a href="#" class="hover:text-primary transition-colors">Términos de Uso</a>
                        <span class="w-px h-3 bg-outline-variant/30"></span>
                        <a href="#" class="hover:text-primary transition-colors">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-scroll {
    animation: scroll 30s linear infinite;
}

/* ── Scroll reveal ── */
.reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1), transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}
</style>
