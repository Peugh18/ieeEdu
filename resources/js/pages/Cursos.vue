<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import CourseCard from '@/components/CourseCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    courses: { data: any[]; links: any[]; total: number };
    categories: any[];
    filters: { search?: string; modality?: string; category?: string };
    isDashboard?: boolean;
    banner?: {
        image_path?: string | null;
        heading?: string;
        subheading?: string;
        button_text?: string;
        button_link?: string;
        show_text?: boolean;
    };
}>();

const searchTerm = ref(props.filters.search || '');
const selectedModality = ref(props.filters.modality || 'Todos');
const selectedCategory = ref(props.filters.category || 'Todas las áreas');

const modalities = ['Todos', 'En vivo', 'Grabado'];

function applyFilters() {
    const routeName = props.isDashboard ? 'student.explore.courses' : 'cursos.index';
    router.get(
        route(routeName),
        {
            search: searchTerm.value,
            modality: selectedModality.value,
            category: selectedCategory.value,
        },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}

watch([searchTerm, selectedModality, selectedCategory], () => {
    applyFilters();
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Explorar Catálogo', href: '#' },
];
</script>

<template>
    <Head title="Cursos Profesionales - IEE" />
    
    <!-- WRAPPER COMPONENT LOGIC -->
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex flex-col text-on-background font-sans', !isDashboard ? 'min-h-screen bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28 md:pt-28' : 'pt-0']">

                <!-- Hero Banner: Tarjeta Contenida Premium -->
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                    <!-- Con imagen -->
                    <div
                        v-if="banner?.image_path"
                        :class="['relative overflow-hidden shadow-2xl', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="banner.image_path" alt="Cursos IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <!-- Overlay izquierdo más denso para legibilidad del texto -->
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>

                        <div v-if="banner.show_text" class="relative z-10 h-full px-10 md:px-14 flex flex-col justify-center">
                            <div class="max-w-2xl space-y-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                    Estrategia y Excelencia
                                </span>

                                <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                                    {{ banner.heading || 'Nuestra Oferta Académica' }}
                                </h1>

                                <p class="text-base text-white/75 max-w-lg font-light leading-relaxed">
                                    {{ banner.subheading || 'Diplomados diseñados por expertos del sector para impulsar su carrera.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback: sin imagen -->
                    <div v-else
                        class="relative overflow-hidden rounded-[2rem] bg-surface-container-low py-20 px-10 md:px-14 flex flex-col justify-center"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <div class="absolute -top-20 left-1/4 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                        <div class="absolute -bottom-20 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                        <div class="relative z-10 max-w-2xl space-y-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Estrategia y Excelencia</p>
                            <h1 class="text-3xl md:text-5xl lg:text-[54px] font-serif font-bold leading-tight tracking-[-0.02em] text-on-surface">
                                Nuestra <span class="italic text-primary">Oferta Académica</span>
                            </h1>
                            <p class="text-lg text-on-surface-variant">
                                Invierta en su futuro profesional con diplomados diseñados por expertos.
                            </p>
                        </div>
                    </div>
                </div>


                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row gap-10">
                        
                        <!-- Left Sidebar Filters -->
                        <aside class="w-full lg:w-72 flex-shrink-0 space-y-10">
                            <!-- Search -->
                            <div>
                                <div class="relative flex items-center">
                                    <svg class="absolute left-0 top-3 text-on-surface-variant w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input 
                                        v-model="searchTerm"
                                        type="text" 
                                        placeholder="Buscar programa..." 
                                        class="w-full pl-8 pr-4 py-2 border-0 border-b border-outline-variant bg-transparent text-sm focus:outline-none focus:border-primary focus:border-b-2 focus:ring-0 transition-all text-on-background placeholder:text-on-surface-variant"
                                        @keyup.enter="applyFilters"
                                    >
                                </div>
                            </div>

                            <!-- Modality -->
                            <div class="p-6 bg-surface-container-highest rounded-3xl border border-outline-variant/10 shadow-sm">
                                <h3 class="flex items-center gap-2 text-xs font-bold text-on-surface-variant mb-5 tracking-wider uppercase">
                                    Modalidad
                                </h3>
                                <div class="space-y-3">
                                    <button 
                                        v-for="mod in modalities" 
                                        :key="mod"
                                        @click="selectedModality = mod"
                                        class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-[13px] font-bold tracking-[0.05em] uppercase transition-all"
                                        :class="selectedModality === mod ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-transparent text-primary hover:bg-surface-container'"
                                    >
                                        {{ mod }}
                                    </button>
                                </div>
                            </div>

                            <!-- Categories / Schools -->
                            <div class="p-6 bg-surface-container-highest rounded-3xl border border-outline-variant/10 shadow-sm">
                                <h3 class="flex items-center gap-2 text-xs font-bold text-on-surface-variant mb-5 tracking-wider uppercase">
                                    Escuelas Especializadas
                                </h3>
                                <div class="space-y-4 block max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" v-model="selectedCategory" value="Todas las áreas" class="w-4 h-4 text-primary focus:ring-primary border-outline-variant rounded-sm" />
                                        <span class="text-sm font-medium italic transition-colors" :class="selectedCategory === 'Todas las áreas' ? 'text-on-background font-bold underline' : 'text-on-surface-variant group-hover:text-on-background text-sm'">Todas las áreas</span>
                                    </label>
                                    <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" v-model="selectedCategory" :value="cat.name" class="w-4 h-4 text-primary focus:ring-primary border-outline-variant rounded-sm" />
                                        <span class="text-sm italic transition-colors text-xs" :class="selectedCategory === cat.name ? 'text-on-background font-bold underline' : 'text-on-surface-variant group-hover:text-on-background'">{{ cat.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </aside>

                        <!-- Right Content Grid -->
                        <main class="flex-1 min-w-0">
                            <div class="mb-10 pt-2 flex items-center justify-between">
                                <h2 class="text-on-surface-variant font-serif text-2xl"><strong class="text-on-background">{{ courses.total }}</strong> Programas de Excelencia</h2>
                            </div>

                            <!-- Grid -->
                            <div v-if="courses.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                                <CourseCard 
                                    v-for="course in courses.data" 
                                    :key="course.id"
                                    :course="course"
                                    :is-dashboard="isDashboard"
                                />
                            </div>

                            <!-- Empty state -->
                            <div v-else class="py-24 flex flex-col items-center justify-center text-center bg-surface-container-highest rounded-[3rem] border-2 border-dashed border-outline-variant/40 space-y-6">
                                <h3 class="text-3xl font-serif font-bold text-on-background italic">Sin coincidencias académicas</h3>
                                <p class="text-on-surface-variant max-w-md font-serif italic text-lg">No hemos encontrado programas que coincidan con su búsqueda en esta escuela.</p>
                                <button @click="() => { selectedModality='Todos'; selectedCategory='Todas las áreas'; searchTerm=''; applyFilters(); }" class="text-xs uppercase tracking-[0.2em] font-bold text-primary border-b border-primary/20 pb-2 hover:text-primary/70 transition-all">Restablecer Filtros</button>
                            </div>

                            <!-- Pagination -->
                            <div v-if="courses.links && courses.links.length > 3" class="mt-20 flex justify-center">
                                <div class="flex flex-wrap gap-4">
                                    <template v-for="(link, key) in courses.links" :key="key">
                                        <div v-if="link.url === null" class="px-5 py-2.5 rounded-xl text-xs text-outline-variant opacity-50 font-bold" v-html="link.label"></div>
                                        <Link v-else :href="link.url" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all" :class="{ 'bg-primary text-white shadow-xl shadow-primary/20': link.active, 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high border border-outline-variant/20': !link.active }" v-html="link.label" preserve-scroll />
                                    </template>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #C9C7B8;
    border-radius: 4px;
}
</style>
