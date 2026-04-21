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

            <main :class="['flex-1 pb-20', isDashboard ? 'p-0' : '']">
                <!-- Hero Banner -->
                <div :class="[
                        'relative py-20 md:py-28 px-6 sm:px-12 text-center mb-12 overflow-hidden flex flex-col justify-center items-center', 
                        isDashboard ? 'rounded-[2rem] mx-6 mt-6' : '',
                        !banner?.image_path ? 'bg-surface-container-low' : 'bg-cover bg-center bg-no-repeat'
                    ]"
                    :style="banner?.image_path ? `background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url(${banner.image_path}); min-height: 420px;` : ''"
                >
                    <!-- Fallback Decoration -->
                    <div v-if="!banner?.image_path" class="absolute inset-0 pointer-events-none overflow-hidden">
                        <div class="absolute -top-20 left-1/4 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                        <div class="absolute -bottom-20 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                    </div>
                    
                    <!-- Text Content -->
                    <div v-if="!banner?.image_path || banner.show_text" class="relative z-10 max-w-4xl mx-auto w-full">
                        <p :class="['text-xs font-bold uppercase tracking-[0.2em] mb-4', banner?.image_path ? 'text-white/70' : 'text-primary']">
                            Estrategia y Excelencia
                        </p>
                        
                        <h1 v-if="banner?.heading" :class="['text-3xl md:text-5xl lg:text-[54px] font-serif font-bold mb-6 leading-tight tracking-[-0.02em]', banner?.image_path ? 'text-white' : 'text-on-surface']">
                            {{ banner.heading }}
                        </h1>
                        <h1 v-else :class="['text-3xl md:text-5xl lg:text-[54px] font-serif font-bold mb-6 leading-tight tracking-[-0.02em]', banner?.image_path ? 'text-white' : 'text-on-surface']">
                            Nuestra <span class="italic text-primary">Oferta Académica</span>
                        </h1>
                        
                        <p :class="['text-lg max-w-2xl mx-auto', banner?.image_path ? 'text-white/90' : 'text-on-surface-variant']">
                            {{ banner?.subheading || 'Invierta en su futuro profesional con diplomados diseñados por expertos en el sector.' }}
                        </p>

                        <!-- Optional Button from Banner -->
                        <div v-if="banner?.button_text" class="mt-8 flex justify-center">
                            <Link :href="banner?.button_link || '#'" class="px-8 py-3.5 rounded-2xl text-sm font-bold uppercase tracking-widest transition-all"
                                  :class="banner?.image_path ? 'bg-primary text-on-primary hover:bg-primary/90' : 'bg-primary text-on-primary hover:bg-primary/90 shadow-lg'">
                                {{ banner.button_text }}
                            </Link>
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
