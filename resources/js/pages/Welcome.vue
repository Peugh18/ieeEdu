<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import HeroSection from '../components/landing/HeroSection.vue';
import Navigation from '../components/landing/Navigation.vue';
import CourseCard from '@/components/CourseCard.vue';

const props = defineProps<{
    dynamicCourses: any[];
}>();

const testimonials = [
    {
        name: 'Juan Carlos Ruiz',
        role: 'Director Ejecutivo, Grupo Industrial',
        text: 'El diplomado transformó mi forma de pensar la gestión empresarial. Los profesores son expertos con experiencia real en el mercado.',
        rating: 5,
        image: 'person',
    },
    {
        name: 'María Alejandra López',
        role: 'Analista Senior, Firma de Inversiones',
        text: 'Excelente programa. Los módulos de análisis de datos me permitieron obtener mi ascenso en 6 meses.',
        rating: 5,
        image: 'person',
    },
    {
        name: 'David Cortés',
        role: 'Gerente de Operaciones, MINEM',
        text: 'La mejor inversión en educación que hice. Las herramientas y conocimientos siguen siendo relevantes.',
        rating: 5,
        image: 'person',
    },
];

const stats = [
    { value: '500+', label: 'Profesionales formados' },
    { value: '98%', label: 'Empleabilidad' },
    { value: '25+', label: 'Programas activos' },
    { value: '15', label: 'Años de trayectoria' },
];

const courseTabs = [
    { id: 'en vivo', label: 'Clases en vivo' },
    { id: 'grabado', label: 'Clases grabadas 24/7' },
    { id: 'hibrido', label: 'Híbrido' },
];

const activeCourseTab = ref('en vivo');
const filteredCourses = computed(() => {
    if (!activeCourseTab.value) return props.dynamicCourses;
    return props.dynamicCourses.filter((course) => course.type === activeCourseTab.value);
});

const publications = [
    { id: 1, title: 'E-book: Gestión Pública Ágil', type: 'free', category: 'PDF', size: '3.6 MB', price: 0, href: '#' },
    { id: 2, title: 'Manual de Analytics para Gerentes', type: 'paid', category: 'PDF', size: '5.4 MB', price: 59, href: '#' },
    { id: 3, title: 'Guía de Liderazgo Directivo', type: 'free', category: 'PDF', size: '2.1 MB', price: 0, href: '#' },
    { id: 4, title: 'Libro: Estrategias Corporativas 2026', type: 'paid', category: 'PDF', size: '7.3 MB', price: 120, href: '#' },
];

const publicationTabs = [
    { id: 'free', label: 'Libros gratuitos' },
    { id: 'paid', label: 'Libros de pago' },
];

const activePublicationTab = ref('free');
const filteredPublications = computed(() => {
    return publications.filter((item) => item.type === activePublicationTab.value);
});

const expandedAbout = ref<string | null>('mision');

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

const aboutSections = [
    {
        id: 'historia',
        title: 'Nuestra Historia',
        icon: 'history_edu',
        content: 'Fundado en 2009 en la ciudad de Trujillo, el IEE nació con la visión de descentralizar la educación de alta especialización en el Perú. A lo largo de más de 15 años, hemos consolidado nuestra posición como referentes en la formación de líderes empresariales y funcionarios públicos, adaptándonos constantemente a las exigencias del mercado global.',
    },
    {
        id: 'mision',
        title: 'Misión',
        icon: 'track_changes',
        content: 'Empoderar a los líderes y organizaciones a través de una educación rigurosa, práctica e innovadora que impulse el desarrollo sostenible y la competitividad empresarial en el Perú y la región.',
    },
    {
        id: 'vision',
        title: 'Visión',
        icon: 'visibility',
        content: 'Ser la institución líder en formación ejecutiva y consultoría estratégica en el Perú, reconocida internacionalmente por su excelencia académica, su impacto en la gestión pública y privada, y su contribución al crecimiento económico del país.',
    },
];

const toggleAbout = (id: string) => {
    expandedAbout.value = expandedAbout.value === id ? null : id;
};
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

            <!-- Clients Section (Always visible) -->
            <section class="py-12 md:py-16 lg:py-20 bg-surface border-b border-outline-variant/10 overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 md:px-8">
                    <p class="text-center text-xs font-bold tracking-[0.2em] text-primary/60 uppercase mb-8 md:mb-12">Instituciones que confían en nosotros</p>
                    
                    <div class="relative flex items-center">
                        <!-- Infinite Scroll Carousel -->
                        <div class="flex gap-6 md:gap-10 items-center animate-scroll whitespace-nowrap">
                            <!-- First set of logos -->
                            <div v-for="logo in clientLogos" :key="logo.name" class="flex-shrink-0 w-24 md:w-32 lg:w-40 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                                <img :src="logo.src" :alt="logo.name" class="max-h-10 md:max-h-12 lg:max-h-16 w-auto object-contain" />
                            </div>
                            <!-- Duplicated set for seamless loop -->
                            <div v-for="logo in clientLogos" :key="logo.name + '-dup'" class="flex-shrink-0 w-24 md:w-32 lg:w-40 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                                <img :src="logo.src" :alt="logo.name" class="max-h-10 md:max-h-12 lg:max-h-16 w-auto object-contain" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="cursos" class="py-12 md:py-24 bg-surface-container-low relative">
                <!-- Background decoration -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-1/2 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-8">
                        <div>
                            <p class="text-primary text-sm font-bold uppercase tracking-widest mb-2">Nuestra Oferta Académica</p>
                            <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight">
                                Programas de <span class="italic text-primary">Especialización</span>
                            </h2>
                            <p class="text-on-surface-variant max-w-xl text-base md:text-lg leading-relaxed">
                                Formación avanzada para líderes empresariales y profesionales del sector público con metodología comprobada.
                            </p>
                        </div>
                        <Link href="#consultoria" class="w-full md:w-auto px-8 py-3 bg-primary text-on-primary rounded-lg font-bold hover:shadow-xl transition-all text-center md:text-left">
                            Consultorías
                        </Link>
                    </div>

                    <!-- Tabs for course type -->
                    <div class="flex flex-wrap gap-3 mb-8">
                        <button
                            v-for="tab in courseTabs"
                            :key="tab.id"
                            @click.prevent="activeCourseTab = tab.id"
                            :class="[
                                'px-4 py-2 rounded-full font-semibold transition-all',
                                activeCourseTab === tab.id ? 'bg-primary text-on-primary shadow-lg' : 'bg-surface-container-low border border-outline-variant/40 text-on-surface-variant hover:bg-surface'
                            ]"
                        >
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
                            <div class="col-span-full p-10 text-center bg-surface-container-high rounded-2xl border border-outline-variant/20">
                                <p class="text-on-surface-variant">No hay cursos disponibles para esta categoría todavía.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section class="py-12 md:py-24 bg-surface">
                <div class="max-w-7xl mx-auto px-6 md:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <p class="text-primary text-xs sm:text-sm font-bold uppercase tracking-widest mb-2">Nuestra Diferencia</p>
                            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-on-surface mb-5 md:mb-6 leading-tight">¿Por qué elegir IEE?</h2>
                            <ul class="space-y-4 md:space-y-6">
                                <li class="flex gap-4">
                                    <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-on-primary-container" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-serif text-lg text-on-surface mb-1">Docentes Expertos</h3>
                                        <p class="text-on-surface-variant text-sm md:text-base">Profesores con experiencia real en el sector empresarial y académico</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <div class="w-12 h-12 rounded-full bg-tertiary-container flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-on-tertiary-container" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-serif text-lg text-on-surface mb-1">Metodología Práctica</h3>
                                        <p class="text-on-surface-variant text-sm md:text-base">50% teoría, 50% casos de estudio y proyectos reales</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-on-secondary-container" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-serif text-lg text-on-surface mb-1">Garantía de Valor</h3>
                                        <p class="text-on-surface-variant text-sm md:text-base">ROI comprobado: 98% de nuestros egresados avanzan profesionalmente</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <div class="w-12 h-12 rounded-full bg-outline-variant/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-serif text-lg text-on-surface mb-1">Redes Profesionales</h3>
                                        <p class="text-on-surface-variant text-sm md:text-base">Conecta con líderes empresariales y amplía tus oportunidades</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-primary-container/10 to-transparent rounded-3xl blur-2xl"></div>
                            <div class="relative grid grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                                <div v-for="stat in stats" :key="stat.label" class="bg-surface-container-high p-4 sm:p-6 md:p-8 rounded-2xl border border-outline-variant/10 text-center hover:shadow-xl transition-all">
                                    <p class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-1 md:mb-2">{{ stat.value }}</p>
                                    <p class="text-on-surface-variant font-medium text-xs sm:text-sm">{{ stat.label }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About Us Section -->
            <section id="nosotros" class="py-12 md:py-24 bg-surface-container-low">
                <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                    <!-- Left: Image -->
                    <div class="order-2 lg:order-1">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-square bg-[#0c2b2c] flex items-center justify-center">
                            <img alt="Entorno Académico IEE" class="w-1/2 md:w-2/3 h-1/2 md:h-2/3 object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYRtr6gwElBp8TdhYs4DLnsPmTxsIuU8pnQAlml2fB1Db1mp-Hp-wJtNhJuv11Pf1tL1adDvEid3NpKamgJU1rx_NaBGo_mf9dV9PZToZs70THZQpfAsNpci3fBt0Y18wO7MU_AGebEmveWp-rln30vcEFS-kJsUbzDxd3luC8QaZuFSlaULfeAf2aANLFpuIruhr8NtaTfe5idwRD-EvvVDlIB8kyDMoi9-9d1SW8yU-tCHLGEf7AiFCAUjRR4QDUrleSurnZn_Fj" />
                        </div>
                    </div>

                    <!-- Right: Content -->
                    <div class="order-1 lg:order-2">
                        <p class="text-primary font-bold uppercase tracking-widest text-xs mb-3 md:mb-4 block">Nuestra Institución</p>
                        <h2 class="font-serif text-2xl md:text-4xl lg:text-5xl text-on-surface mb-4 md:mb-6 leading-tight">Tradición de Excelencia desde Trujillo, 2009.</h2>
                        <p class="text-on-surface-variant mb-6 md:mb-10 leading-relaxed text-sm sm:text-base md:text-lg">
                            Fundado con el firme propósito de descentralizar la formación de alto nivel, el IEE nace en el norte del Perú para dotar a profesionales y empresas de las herramientas críticas necesarias para navegar la economía global.
                        </p>

                        <!-- Accordion -->
                        <div class="space-y-2 md:space-y-3">
                            <button
                                v-for="section in aboutSections"
                                :key="section.id"
                                @click="toggleAbout(section.id)"
                                class="w-full text-left"
                            >
                                <div :class="[
                                    'bg-surface-container-lowest rounded-xl p-4 md:p-5 lg:p-6 transition-all duration-300 border',
                                    expandedAbout === section.id 
                                        ? 'border-primary/30 shadow-md' 
                                        : 'border-outline-variant/10 hover:border-primary/10'
                                ]">
                                    <div class="flex justify-between items-center gap-2">
                                        <div class="flex items-center gap-2 md:gap-3 min-w-0">
                                            <span class="material-symbols-outlined text-primary text-xl md:text-2xl flex-shrink-0" translate="no">{{ section.icon }}</span>
                                            <h3 class="font-serif text-base md:text-lg lg:text-xl text-on-surface truncate" :class="{'font-bold text-primary': expandedAbout === section.id, 'italic': expandedAbout !== section.id}">
                                                {{ section.title }}
                                            </h3>
                                        </div>
                                        <span :class="[
                                            'material-symbols-outlined text-primary transition-transform duration-300 flex-shrink-0',
                                            expandedAbout === section.id ? 'rotate-180' : ''
                                        ]">expand_more</span>
                                    </div>
                                    <Transition
                                        enter-active-class="transition duration-300"
                                        enter-from-class="max-h-0 opacity-0"
                                        enter-to-class="max-h-96 opacity-100"
                                        leave-active-class="transition duration-300"
                                        leave-from-class="max-h-96 opacity-100"
                                        leave-to-class="max-h-0 opacity-0"
                                    >
                                        <p v-if="expandedAbout === section.id && section.content" class="text-on-surface-variant mt-3 md:mt-4 text-xs md:text-sm leading-relaxed">
                                            {{ section.content }}
                                        </p>
                                    </Transition>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Corporate Solutions Section -->
            <section id="consultoria" class="py-12 md:py-24 bg-surface overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                    <div>
                        <h2 class="font-serif text-2xl md:text-4xl lg:text-5xl text-primary mb-3 md:mb-6 leading-tight">Soluciones Corporativas</h2>
                        <p class="text-on-surface-variant text-sm md:text-base lg:text-lg mb-4 md:mb-10 leading-relaxed">
                            Ayudamos a firmas e instituciones a optimizar sus flujos de análisis y formación de equipos.
                        </p>
                        <ul class="space-y-3 md:space-y-6 mb-6 md:mb-12">
                            <li class="flex items-center gap-3 md:gap-4">
                                <div class="w-7 md:w-8 h-7 md:h-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm md:text-base" translate="no">check</span>
                                </div>
                                <span class="text-on-surface-variant font-medium text-sm md:text-base">Auditoría de capacidades técnicas</span>
                            </li>
                            <li class="flex items-center gap-3 md:gap-4">
                                <div class="w-7 md:w-8 h-7 md:h-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm md:text-base" translate="no">check</span>
                                </div>
                                <span class="text-on-surface-variant font-medium text-sm md:text-base">Diseño de currícula propietaria</span>
                            </li>
                            <li class="flex items-center gap-3 md:gap-4">
                                <div class="w-7 md:w-8 h-7 md:h-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm md:text-base" translate="no">check</span>
                                </div>
                                <span class="text-on-surface-variant font-medium text-sm md:text-base">Proyección de escenarios macro</span>
                            </li>
                        </ul>
                    </div>

                    <div class="relative">
                        <!-- Form Card -->
                        <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-8 lg:p-10 shadow-lg md:shadow-[0_20px_50px_rgba(87,87,42,0.15)] border border-outline-variant/10 relative z-10">
                            <div class="flex justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
                                <h3 class="font-serif text-xl md:text-2xl text-primary font-bold">Consulta Estratégica</h3>
                                <button class="text-on-surface-variant hover:text-on-surface flex-shrink-0 md:ml-auto">
                                    <span class="material-symbols-outlined" translate="no">close</span>
                                </button>
                            </div>
                            <form class="space-y-4 md:space-y-6">
                                <div>
                                    <label class="block text-[10px] md:text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 ml-1">Empresa</label>
                                    <input class="w-full bg-surface-container-low border-none rounded-lg px-4 md:px-6 py-3 md:py-4 text-sm md:text-base text-on-surface placeholder:text-outline-variant/60 focus:ring-2 focus:ring-primary transition-all" placeholder="Nombre de la organización" type="text"/>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                                    <div>
                                        <label class="block text-[10px] md:text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 ml-1">Sector</label>
                                        <div class="relative">
                                            <select class="w-full bg-surface-container-low border-none rounded-lg px-4 md:px-6 py-3 md:py-4 text-sm md:text-base text-on-surface appearance-none focus:ring-2 focus:ring-primary transition-all">
                                                <option>Banca & Finanzas</option>
                                                <option>Minería</option>
                                                <option>Educación</option>
                                                <option>Retail</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-3 md:right-4 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-lg md:text-xl" translate="no">expand_more</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] md:text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 ml-1">País</label>
                                        <input class="w-full bg-surface-container-low border-none rounded-lg px-4 md:px-6 py-3 md:py-4 text-sm md:text-base text-on-surface placeholder:text-outline-variant/60 focus:ring-2 focus:ring-primary transition-all" placeholder="Ubicación" type="text"/>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] md:text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 ml-1">Problema o Desafío</label>
                                    <textarea class="w-full bg-surface-container-low border-none rounded-lg px-4 md:px-6 py-3 md:py-4 text-sm md:text-base text-on-surface placeholder:text-outline-variant/60 focus:ring-2 focus:ring-primary transition-all resize-none" placeholder="Describe brevemente tu necesidad..." rows="4"></textarea>
                                </div>
                                <button class="w-full bg-primary text-white py-3 md:py-4 rounded-full font-bold text-base md:text-lg hover:opacity-90 transition-opacity active:scale-95 shadow-lg" type="submit">
                                    Enviar Solicitud
                                </button>
                            </form>
                        </div>
                        <!-- Background Blob Decor -->
                        <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary/10 rounded-full blur-3xl -z-0"></div>
                        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl -z-0"></div>
                    </div>
                </div>
            </section>



            <!-- Publicaciones y Biblioteca -->
            <section id="publicaciones" class="py-12 sm:py-16 md:py-24 px-4 md:px-8 bg-gradient-to-b from-surface to-surface-container-low">
                <div class="max-w-7xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-8 sm:mb-12 md:mb-16">
                        <p class="text-primary text-xs sm:text-sm font-bold uppercase tracking-widest mb-2">Publicaciones</p>
                        <h2 class="font-serif text-2xl sm:text-3xl md:text-5xl text-on-surface mb-3 md:mb-4 leading-tight">
                            Biblioteca de <span class="italic text-primary">Libros Digitales</span>
                        </h2>
                        <p class="text-on-surface-variant text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                            Descarga libros gratuitos o adquiere publicaciones premium para profundizar tus conocimientos.
                        </p>
                        <div class="mt-6">
                            <a href="/ebooks" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:shadow-xl transition-all">
                                Ir a Ebooks
                                <span class="material-symbols-outlined" translate="no">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 mb-8">
                        <button
                            v-for="tab in publicationTabs"
                            :key="tab.id"
                            @click.prevent="activePublicationTab = tab.id"
                            :class="[
                                'px-4 py-2 rounded-full font-semibold transition-all',
                                activePublicationTab === tab.id ? 'bg-primary text-on-primary shadow-lg' : 'bg-surface-container-low border border-outline-variant/40 text-on-surface-variant hover:bg-surface'
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <template v-if="filteredPublications.length > 0">
                            <div v-for="item in filteredPublications" :key="item.id" class="bg-white rounded-2xl border border-outline-variant/10 p-6 shadow-sm hover:shadow-lg transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold uppercase tracking-widest text-primary">{{ item.category }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ item.size }}</span>
                                </div>
                                <h3 class="font-serif text-lg text-on-surface font-bold mb-3">{{ item.title }}</h3>
                                <p class="text-on-surface-variant text-sm mb-5">{{ item.type === 'free' ? 'Descarga gratis ahora' : 'Compra y accede al instante' }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-primary">{{ item.type === 'free' ? 'Gratis' : `S/ ${item.price}` }}</span>
                                    <a :href="item.href" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-semibold hover:opacity-90 transition-all">
                                        {{ item.type === 'free' ? 'Descargar' : 'Comprar' }}
                                        <span class="material-symbols-outlined text-sm" translate="no">east</span>
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-span-full p-8 text-center bg-surface-container-high rounded-2xl border border-outline-variant/20">
                                <p class="text-on-surface-variant">No hay libros disponibles para esta categoría aún.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section (Near the end, for narrative coherence) -->
            <section class="py-12 md:py-24 bg-surface-container-low">
                <div class="max-w-7xl mx-auto px-6 md:px-8">
                    <div class="text-center mb-8 md:mb-16">
                        <p class="text-primary text-xs md:text-sm font-bold uppercase tracking-widest mb-2 md:mb-3">Historias de Éxito</p>
                        <h2 class="font-serif text-2xl md:text-4xl lg:text-5xl text-on-surface mb-2 md:mb-4 leading-tight">
                            Lo que dicen<br/><span class="italic">nuestros egresados</span>
                        </h2>
                        <p class="text-on-surface-variant text-sm md:text-lg max-w-2xl mx-auto leading-relaxed hidden md:block">
                            Profesionales que transformaron sus carreras a través de nuestros programas
                        </p>
                    </div>

                    <!-- Show 1 testimonial on mobile, all on larger screens -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                        <div v-for="(testimonial, index) in testimonials" :key="index" :class="['bg-surface rounded-2xl p-4 md:p-6 lg:p-8 border border-outline-variant/10 hover:shadow-2xl hover:border-primary/20 transition-all duration-300', index > 0 && 'hidden md:block']">
                            <div class="flex gap-0.5 md:gap-1 mb-3 md:mb-6">
                                <svg v-for="star in 5" :key="star" class="w-3 md:w-4 lg:w-5 h-3 md:h-4 lg:h-5 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-on-surface-variant text-sm md:text-base lg:text-lg mb-3 md:mb-4 lg:mb-6 italic leading-relaxed">
                                "{{ testimonial.text }}"
                            </p>
                            <div class="flex items-center gap-2 md:gap-3 lg:gap-4">
                                <div class="w-8 md:w-10 lg:w-12 h-8 md:h-10 lg:h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary text-sm md:text-lg lg:text-2xl" translate="no">{{ testimonial.image }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-serif font-bold text-on-surface text-xs md:text-sm lg:text-base truncate">{{ testimonial.name }}</p>
                                    <p class="text-on-surface-variant text-xs md:text-xs lg:text-sm truncate">{{ testimonial.role }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-surface-container-low border-t border-outline-variant/10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 px-6 md:px-8 py-12 md:py-16 max-w-7xl mx-auto">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <img src="/images/empresa/IEE-Logo02.png" alt="IEE Instituto" class="h-16 w-auto object-contain" />
                    </div>
                    <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed max-w-xs">
                        Transformando el futuro corporativo del Perú con educación de excelencia.
                    </p>
                </div>

                <!-- Academic Programs -->
                <div>
                    <h4 class="font-serif text-base md:text-lg text-on-surface font-bold mb-4 md:mb-6">Académico</h4>
                    <ul class="space-y-2 md:space-y-3">
                        <li><a href="#cursos" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Cursos</a></li>
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Diplomados</a></li>
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Certificaciones</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="font-serif text-base md:text-lg text-on-surface font-bold mb-4 md:mb-6">Empresa</h4>
                    <ul class="space-y-2 md:space-y-3">
                        <li><a href="#consultoria" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Consultoría</a></li>
                        <li><a href="#nosotros" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Sobre Nosotros</a></li>
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Blog</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-serif text-base md:text-lg text-on-surface font-bold mb-4 md:mb-6">Legal</h4>
                    <ul class="space-y-2 md:space-y-3">
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Privacidad</a></li>
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Términos</a></li>
                        <li><a href="#" class="text-on-surface-variant text-xs md:text-sm hover:text-primary transition-colors">Contacto</a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-outline-variant/10 py-6 md:py-8 px-6 md:px-8">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-on-surface-variant/60">
                    <p>© 2024 Instituto de Economía y Empresa. Todos los derechos reservados.</p>
                    <div class="flex gap-4 md:gap-6">
                        <a href="#" class="hover:text-primary transition-colors">Twitter</a>
                        <a href="#" class="hover:text-primary transition-colors">LinkedIn</a>
                        <a href="#" class="hover:text-primary transition-colors">Facebook</a>
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
</style>
