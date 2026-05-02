<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import HeroSection from '../components/landing/HeroSection.vue';
import Navigation from '../components/landing/Navigation.vue';
import CourseCard from '@/components/CourseCard.vue';
import BrandLogo from '@/components/BrandLogo.vue';

const props = defineProps<{
    dynamicCourses: any[];
    teaserBooks: any[];
    teaserArticles: any[];
    homeSlides: any[];
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

// Watch para resetear carrusel cuando cambia el filtro
watch([activeCourseTab, () => filteredCourses.value], () => {
    currentCourseIndex.value = 0;
    startCourseAutoplay();
});

// Carrusel mobile para cursos
const currentCourseIndex = ref(0);
let courseAutoplayInterval: number | null = null;

const isMobile = ref(false);
const checkMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

const startCourseAutoplay = () => {
    if (courseAutoplayInterval) clearInterval(courseAutoplayInterval);
    courseAutoplayInterval = window.setInterval(() => {
        if (filteredCourses.value.length > 1) {
            currentCourseIndex.value = (currentCourseIndex.value + 1) % filteredCourses.value.length;
        }
    }, 4000);
};

const stopCourseAutoplay = () => {
    if (courseAutoplayInterval) {
        clearInterval(courseAutoplayInterval);
        courseAutoplayInterval = null;
    }
};

const goToCourse = (index: number) => {
    currentCourseIndex.value = index;
    startCourseAutoplay();
};

const nextCourse = () => {
    if (filteredCourses.value.length > 0) {
        currentCourseIndex.value = (currentCourseIndex.value + 1) % filteredCourses.value.length;
        startCourseAutoplay();
    }
};

const prevCourse = () => {
    if (filteredCourses.value.length > 0) {
        currentCourseIndex.value = (currentCourseIndex.value - 1 + filteredCourses.value.length) % filteredCourses.value.length;
        startCourseAutoplay();
    }
};

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

// Carrusel mobile para libros
const currentBookIndex = ref(0);
let bookAutoplayInterval: number | null = null;

const startBookAutoplay = () => {
    if (bookAutoplayInterval) clearInterval(bookAutoplayInterval);
    bookAutoplayInterval = window.setInterval(() => {
        if (filteredPublications.value.length > 1) {
            currentBookIndex.value = (currentBookIndex.value + 1) % filteredPublications.value.length;
        }
    }, 5000);
};

const stopBookAutoplay = () => {
    if (bookAutoplayInterval) {
        clearInterval(bookAutoplayInterval);
        bookAutoplayInterval = null;
    }
};

const goToBook = (index: number) => {
    currentBookIndex.value = index;
    startBookAutoplay();
};

const nextBook = () => {
    if (filteredPublications.value.length > 0) {
        currentBookIndex.value = (currentBookIndex.value + 1) % filteredPublications.value.length;
        startBookAutoplay();
    }
};

const prevBook = () => {
    if (filteredPublications.value.length > 0) {
        currentBookIndex.value = (currentBookIndex.value - 1 + filteredPublications.value.length) % filteredPublications.value.length;
        startBookAutoplay();
    }
};

// Watch para resetear carrusel de libros cuando cambia el filtro
watch([activePublicationTab, () => filteredPublications.value], () => {
    currentBookIndex.value = 0;
    startBookAutoplay();
});

let revealObserver: IntersectionObserver | null = null;
onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
    startCourseAutoplay();
    startBookAutoplay();

    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('revealed');
                revealObserver?.unobserve(e.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => revealObserver?.observe(el));

    // Staggered children reveal
    const staggerObs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const children = e.target.querySelectorAll(':scope > *');
                children.forEach((child, i) => {
                    (child as HTMLElement).style.transitionDelay = `${i * 100}ms`;
                    child.classList.add('revealed');
                });
                staggerObs.unobserve(e.target);
            }
        });
    }, { threshold: 0.05 });
    document.querySelectorAll('.reveal-stagger').forEach(el => staggerObs.observe(el));
});
onUnmounted(() => {
    revealObserver?.disconnect();
    window.removeEventListener('resize', checkMobile);
    stopCourseAutoplay();
    stopBookAutoplay();
});

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
            <HeroSection :dbSlides="homeSlides" />

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

                    <!-- Mobile Carousel -->
                    <div v-if="isMobile && filteredCourses.length > 0" class="md:hidden">
                        <div class="relative overflow-hidden">
                            <!-- Carousel Track -->
                            <div class="flex transition-transform duration-500 ease-out"
                                 :style="{ transform: `translateX(-${currentCourseIndex * 100}%)` }">
                                <div v-for="course in filteredCourses" :key="course.id" class="w-full flex-shrink-0 px-1">
                                    <CourseCard :course="course" />
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            <button v-if="filteredCourses.length > 1" @click="prevCourse"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button v-if="filteredCourses.length > 1" @click="nextCourse"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        <!-- Dots Indicator -->
                        <div v-if="filteredCourses.length > 1" class="flex justify-center gap-2 mt-6">
                            <button v-for="(_, index) in filteredCourses" :key="index"
                                    @click="goToCourse(index)"
                                    :class="['w-2.5 h-2.5 rounded-full transition-all duration-300', currentCourseIndex === index ? 'bg-primary w-6' : 'bg-outline-variant/50 hover:bg-outline-variant']">
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Grid -->
                    <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                        <CourseCard
                            v-for="course in filteredCourses"
                            :key="course.id"
                            :course="course"
                        />
                    </div>

                    <!-- Empty State -->
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
            </section>

            <!-- Section divider -->
            <div class="section-divider"></div>

            <!-- Consultoría Teaser - Simplificada -->
            <section id="consultoria" class="py-16 md:py-24 bg-surface-container-low overflow-hidden relative reveal">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-20 -left-20 w-[600px] h-[600px] bg-primary/[0.05] rounded-full blur-[140px]"></div>
                </div>
                <div class="max-w-4xl mx-auto px-6 md:px-8 relative z-10 text-center">

                    <!-- Section label -->
                    <div class="flex items-center justify-center gap-3 mb-8">
                        <div class="w-8 h-px bg-primary/30"></div>
                        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Consultoría</span>
                        <div class="w-8 h-px bg-primary/30"></div>
                    </div>

                    <!-- Contenido simplificado -->
                    <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight">
                        ¿Tu organización necesita<br class="hidden md:block"/>
                        <span class="italic text-primary">soluciones estratégicas?</span>
                    </h2>

                    <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-8 max-w-2xl mx-auto">
                        Más de 15 años transformando entidades públicas y privadas del Perú con metodologías probadas.
                    </p>

                    <!-- CTA Principal -->
                    <a href="/consultoria" class="group inline-flex items-center gap-3 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                        Solicitar consultoría
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </section>


            <!-- Section divider -->
            <div class="section-divider alt"></div>


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

                    <!-- Mobile Carousel -->
                    <div v-if="isMobile && filteredPublications.length > 0" class="md:hidden">
                        <div class="relative overflow-hidden">
                            <!-- Carousel Track -->
                            <div class="flex transition-transform duration-500 ease-out"
                                 :style="{ transform: `translateX(-${currentBookIndex * 100}%)` }">
                                <div v-for="book in filteredPublications" :key="book.id" class="w-full flex-shrink-0 px-2">
                                    <div class="group relative bg-surface-container rounded-2xl border border-outline-variant/15 overflow-hidden flex flex-col">
                                        <!-- Cover image -->
                                        <div class="relative h-52 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20 flex-shrink-0">
                                            <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover"/>
                                            <div v-else class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <div class="absolute top-3 left-3">
                                                <span :class="['text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full backdrop-blur-sm', !book.price || Number(book.price) === 0 ? 'bg-green-500/90 text-white' : 'bg-primary/90 text-on-primary']">
                                                    {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-5 flex flex-col flex-1">
                                            <p v-if="book.category" class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ book.category }}</p>
                                            <h3 class="font-serif text-base text-on-surface font-bold leading-snug mb-1">{{ book.title }}</h3>
                                            <p v-if="book.author" class="text-on-surface-variant/60 text-sm mb-4">{{ book.author }}</p>
                                            <div class="flex items-center justify-between mt-auto pt-3 border-t border-outline-variant/10">
                                                <span class="text-lg font-bold text-primary font-serif">
                                                    {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                                                </span>
                                                <a :href="(!book.price || Number(book.price) === 0) ? (book.download_url || book.file_path || '/publicaciones') : '/publicaciones'"
                                                   :target="(!book.price || Number(book.price) === 0) && (book.download_url || book.file_path) ? '_blank' : '_self'"
                                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold">
                                                    {{ !book.price || Number(book.price) === 0 ? 'Descargar' : 'Ver más' }}
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            <button v-if="filteredPublications.length > 1" @click="prevBook"
                                    class="absolute left-2 top-1/3 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button v-if="filteredPublications.length > 1" @click="nextBook"
                                    class="absolute right-2 top-1/3 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        <!-- Dots Indicator -->
                        <div v-if="filteredPublications.length > 1" class="flex justify-center gap-2 mt-6">
                            <button v-for="(_, index) in filteredPublications" :key="index"
                                    @click="goToBook(index)"
                                    :class="['w-2.5 h-2.5 rounded-full transition-all duration-300', currentBookIndex === index ? 'bg-primary w-6' : 'bg-outline-variant/50 hover:bg-outline-variant']">
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Grid -->
                    <div v-if="filteredPublications.length > 0" class="hidden md:grid gap-5 [grid-template-columns:repeat(auto-fill,minmax(240px,1fr))]">
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

            <!-- Section divider -->
            <div class="section-divider"></div>

            <!-- CTA Final - Optimizado para conversión -->
            <section class="relative overflow-hidden bg-surface-container-low reveal">
                <!-- Gradient blobs -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-primary/[0.07] rounded-full blur-[120px] -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px] translate-y-1/2"></div>
                </div>

                <div class="relative z-10 max-w-4xl mx-auto px-6 md:px-8 py-20 md:py-28 text-center">
                    <!-- Badge GRATIS destacado -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 border border-green-500/30 mb-8 animate-pulse">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span class="text-sm font-bold text-green-600 tracking-wide">TU PRIMERA CLASE ES GRATIS</span>
                    </div>

                    <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight font-bold">
                        ¿Listo para seguir <br class="hidden md:block"/>
                        <span class="italic text-primary">aprendiendo?</span>
                    </h2>

                    <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-8 max-w-lg mx-auto">
                        Empieza hoy mismo — miles de profesionales del Perú ya confían en nosotros.
                    </p>

                    <!-- Botones de acción claros -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/cursos" class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                            Empezar ahora
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="/cursos" class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-surface-container border border-outline-variant/30 text-on-surface rounded-xl font-bold text-base hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
                            Ver cursos
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer - Optimizado Mobile -->
        <footer class="bg-surface-container relative">
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
            <div class="max-w-7xl mx-auto px-6 md:px-8 pt-12 pb-8 md:pt-16 md:pb-12">

                <!-- Mobile: Layout vertical simple -->
                <div class="md:hidden space-y-8">
                    <!-- Brand -->
                    <div class="text-center">
                        <BrandLogo imageClass="h-12 w-auto object-contain mx-auto mb-4" />
                        <p class="text-on-surface-variant text-sm leading-relaxed max-w-xs mx-auto">
                            Formamos líderes con visión global desde Trujillo, Perú.
                        </p>
                    </div>

                    <!-- Links simples -->
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-center">
                        <a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Cursos</a>
                        <a href="/masterclass" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Masterclasses</a>
                        <a href="/consultoria" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Consultoría</a>
                        <a href="/publicaciones" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Biblioteca</a>
                    </div>

                    <!-- Social & Contact -->
                    <div class="text-center space-y-4">
                        <div class="flex justify-center gap-4">
                            <a href="#" class="w-10 h-10 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                        <p class="text-on-surface-variant/70 text-sm">info@iee.edu.pe</p>
                    </div>

                    <!-- CTA Mobile -->
                    <div class="text-center">
                        <a href="/login" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold hover:shadow-md transition-all">
                            Acceder al Aula
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Desktop: Layout en columnas -->
                <div class="hidden md:grid grid-cols-4 gap-8 lg:gap-12">
                    <!-- Brand -->
                    <div class="col-span-1">
                        <BrandLogo imageClass="h-12 w-auto object-contain mb-4" />
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
                            Formamos líderes desde Trujillo, Perú. Más de 15 años de trayectoria.
                        </p>
                        <div class="flex gap-2">
                            <a href="#" class="w-8 h-8 rounded-lg bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-lg bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-lg bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Cursos -->
                    <div>
                        <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Cursos</h4>
                        <ul class="space-y-2">
                            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors">En Vivo</a></li>
                            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Grabados</a></li>
                            <li><a href="/masterclass" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Masterclasses</a></li>
                            <li><a href="/planes" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Planes</a></li>
                        </ul>
                    </div>

                    <!-- Servicios -->
                    <div>
                        <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Servicios</h4>
                        <ul class="space-y-2">
                            <li><a href="/consultoria" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Consultoría</a></li>
                            <li><a href="/publicaciones" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Biblioteca</a></li>
                            <li><a href="/login" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Aula Virtual</a></li>
                        </ul>
                    </div>

                    <!-- Contacto -->
                    <div>
                        <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Contacto</h4>
                        <ul class="space-y-2 text-on-surface-variant text-sm">
                            <li>Trujillo, Perú</li>
                            <li>info@iee.edu.pe</li>
                        </ul>
                        <a href="/login" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-bold hover:shadow-md transition-all">
                            Acceder
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Copyright - ambos -->
                <div class="mt-10 pt-6 border-t border-outline-variant/10 text-center">
                    <p class="text-on-surface-variant/50 text-xs">
                        © {{ new Date().getFullYear() }} Instituto de Economía y Empresa. Todos los derechos reservados.
                    </p>
                    <div class="flex justify-center gap-4 mt-3 text-xs text-on-surface-variant/50">
                        <a href="#" class="hover:text-primary transition-colors">Privacidad</a>
                        <a href="#" class="hover:text-primary transition-colors">Términos</a>
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

@keyframes scroll-smooth {
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
    animation: scroll-smooth 18s linear infinite;
    will-change: transform;
}

.animate-scroll:hover {
    animation-play-state: paused;
}

/* Mobile: logos más grandes y velocidad ajustada */
@media (max-width: 768px) {
    .animate-scroll {
        animation-duration: 12s;
    }
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

/* ── Stagger children ── */
.reveal-stagger > * {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal-stagger > .revealed {
    opacity: 1;
    transform: translateY(0);
}

/* ── Section dividers ── */
.section-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--md-sys-color-outline-variant, #c4c7c5) 20%, var(--md-sys-color-outline-variant, #c4c7c5) 80%, transparent);
    opacity: 0.15;
    max-width: 80rem;
    margin: 0 auto;
}
.section-divider.alt {
    opacity: 0.1;
}
</style>
