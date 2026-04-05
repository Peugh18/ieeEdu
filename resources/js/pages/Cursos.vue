<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    courses: { data: any[]; links: any[]; total: number };
    categories: any[];
    filters: { search?: string; modality?: string; category?: string };
    isDashboard?: boolean;
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
        <div :class="['flex flex-col text-[#1A1C19] font-sans', !isDashboard ? 'min-h-screen bg-[#FAFAF5]' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', isDashboard ? 'p-0' : '']">
                <!-- Hero Banner -->
                <div :class="['bg-gradient-to-br from-[#57572A] to-[#707040] py-20 px-6 sm:px-12 text-center relative mb-12', isDashboard ? 'rounded-[2rem] mx-6 mt-6 overflow-hidden' : '']">
                    <div class="relative z-10 max-w-4xl mx-auto">
                        <p class="text-[#FAFAF5]/80 text-sm font-bold uppercase tracking-[0.1em] mb-4">Estrategia y Excelencia</p>
                        <h1 class="text-3xl md:text-5xl lg:text-[54px] font-serif font-bold text-white mb-6 leading-tight tracking-[-0.02em]">
                            Nuestra <span class="italic">Oferta Académica</span>
                        </h1>
                        <p class="text-[#FAFAF5]/90 text-lg max-w-2xl mx-auto font-serif italic">Invierta en su futuro profesional con diplomados diseñados por expertos en el sector.</p>
                    </div>
                </div>

                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row gap-10">
                        
                        <!-- Left Sidebar Filters -->
                        <aside class="w-full lg:w-72 flex-shrink-0 space-y-10">
                            <!-- Search -->
                            <div>
                                <div class="relative flex items-center">
                                    <svg class="absolute left-0 top-3 text-[#5F5E5E] w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input 
                                        v-model="searchTerm"
                                        type="text" 
                                        placeholder="Buscar programa..." 
                                        class="w-full pl-8 pr-4 py-2 border-0 border-b border-[#C9C7B8] bg-transparent text-sm focus:outline-none focus:border-[#57572A] focus:border-b-2 focus:ring-0 transition-all text-[#1A1C19] placeholder:text-[#5F5E5E]"
                                        @keyup.enter="applyFilters"
                                    >
                                </div>
                            </div>

                            <!-- Modality -->
                            <div class="p-6 bg-[#F4F4EF] rounded-3xl border border-outline-variant/10 shadow-sm">
                                <h3 class="flex items-center gap-2 text-xs font-bold text-[#5F5E5E] mb-5 tracking-wider uppercase">
                                    Modalidad
                                </h3>
                                <div class="space-y-3">
                                    <button 
                                        v-for="mod in modalities" 
                                        :key="mod"
                                        @click="selectedModality = mod"
                                        class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-[13px] font-bold tracking-[0.05em] uppercase transition-all"
                                        :class="selectedModality === mod ? 'bg-[#57572A] text-white shadow-lg shadow-[#57572A]/20' : 'bg-transparent text-[#57572A] hover:bg-[#EBEBE3]'"
                                    >
                                        {{ mod }}
                                    </button>
                                </div>
                            </div>

                            <!-- Categories / Schools -->
                            <div class="p-6 bg-[#F4F4EF] rounded-3xl border border-outline-variant/10 shadow-sm">
                                <h3 class="flex items-center gap-2 text-xs font-bold text-[#5F5E5E] mb-5 tracking-wider uppercase">
                                    Escuelas Especializadas
                                </h3>
                                <div class="space-y-4 block max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" v-model="selectedCategory" value="Todas las áreas" class="w-4 h-4 text-[#57572A] focus:ring-[#57572A] border-[#C9C7B8] rounded-sm" />
                                        <span class="text-sm font-medium italic transition-colors" :class="selectedCategory === 'Todas las áreas' ? 'text-[#1A1C19] font-bold underline' : 'text-[#5F5E5E] group-hover:text-[#1A1C19] text-sm'">Todas las áreas</span>
                                    </label>
                                    <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" v-model="selectedCategory" :value="cat.name" class="w-4 h-4 text-[#57572A] focus:ring-[#57572A] border-[#C9C7B8] rounded-sm" />
                                        <span class="text-sm italic transition-colors text-xs" :class="selectedCategory === cat.name ? 'text-[#1A1C19] font-bold underline' : 'text-[#5F5E5E] group-hover:text-[#1A1C19]'">{{ cat.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </aside>

                        <!-- Right Content Grid -->
                        <main class="flex-1 min-w-0">
                            <div class="mb-10 pt-2 flex items-center justify-between">
                                <h2 class="text-[#5F5E5E] font-serif text-2xl"><strong class="text-[#1A1C19]">{{ courses.total }}</strong> Programas de Excelencia</h2>
                            </div>

                            <!-- Grid -->
                            <div v-if="courses.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                                <article 
                                    v-for="course in courses.data" 
                                    :key="course.id"
                                    class="bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm overflow-hidden group hover:shadow-2xl hover:shadow-[#57572A]/10 transition-all duration-500 flex flex-col hover:-translate-y-2 border-opacity-30"
                                >
                                    <!-- Visual Cover -->
                                    <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="relative h-[220px] w-full block bg-[#F4F4EF] overflow-hidden">
                                        <img v-if="course.image" :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#57572A]/20 to-transparent"></div>
                                        
                                        <!-- Elegant Tags -->
                                        <div class="absolute top-4 left-4 z-10">
                                            <span class="px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-[20px] text-[#1A1C19] text-[9px] font-bold tracking-widest uppercase flex items-center gap-2 border border-white/50">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="course.type === 'en vivo' ? 'bg-[#D32F2F]' : (course.type === 'evento' ? 'bg-[#7B1FA2]' : 'bg-[#57572A]')"></span>
                                                {{ course.type === 'en vivo' ? 'EN VIVO' : (course.type === 'evento' ? 'EVENTO' : 'GRABADO') }}
                                            </span>
                                        </div>
                                    </Link>

                                    <!-- Academic Content -->
                                    <div class="p-8 flex flex-col flex-1 space-y-4">
                                        <span class="text-[9px] font-bold text-[#57572A] uppercase tracking-widest mb-1">{{ course.category?.name || 'Escuela de Negocios' }}</span>
                                        
                                        <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })">
                                            <h3 class="font-serif font-bold text-xl text-[#1A1C19] leading-tight group-hover:text-[#57572A] transition-colors italic mb-4" :title="course.title">
                                                {{ course.title }}
                                            </h3>
                                        </Link>

                                        <!-- Features -->
                                        <div class="space-y-2 flex-1">
                                            <div class="flex items-center gap-2 text-xs text-[#5F5E5E] font-medium italic">
                                                <svg class="w-4 h-4 text-[#57572A]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ course.type === 'grabado' ? 'Acceso 24/7 de por vida' : `Próximo inicio: ${course.start_date || 'Pronto'}` }}
                                            </div>
                                        </div>

                                        <!-- Footer Pricing & CTA -->
                                        <div class="flex flex-col pt-6 border-t border-outline-variant/10 gap-6 mt-4">
                                            <div class="flex justify-between items-end">
                                                <div class="flex flex-col">
                                                    <p class="text-[9px] uppercase tracking-widest text-on-surface-variant/40 font-bold mb-1">Matrícula</p>
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-3xl font-serif font-bold text-[#1A1C19] italic">S/ {{ course.sale_price || course.price }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-3">
                                                <button class="w-full py-4 rounded-2xl bg-[#D4AF37] text-white text-[10px] font-bold uppercase tracking-widest hover:opacity-90 shadow-lg shadow-[#D4AF37]/20 transition-all flex justify-center items-center gap-2">
                                                    Matrícula
                                                </button>
                                                <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="w-full py-4 rounded-2xl bg-[#57572A] text-white text-[10px] font-bold uppercase tracking-widest hover:opacity-90 shadow-lg shadow-[#57572A]/20 transition-all flex justify-center items-center">
                                                    Detalles
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <!-- Empty state -->
                            <div v-else class="py-24 flex flex-col items-center justify-center text-center bg-[#F4F4EF] rounded-[3rem] border-2 border-dashed border-[#C9C7B8]/40 space-y-6">
                                <h3 class="text-3xl font-serif font-bold text-[#1A1C19] italic">Sin coincidencias académicas</h3>
                                <p class="text-[#5F5E5E] max-w-md font-serif italic text-lg">No hemos encontrado programas que coincidan con su búsqueda en esta escuela.</p>
                                <button @click="() => { selectedModality='Todos'; selectedCategory='Todas las áreas'; searchTerm=''; applyFilters(); }" class="text-xs uppercase tracking-[0.2em] font-bold text-primary border-b border-primary/20 pb-2 hover:text-primary/70 transition-all">Restablecer Filtros</button>
                            </div>

                            <!-- Pagination -->
                            <div v-if="courses.links && courses.links.length > 3" class="mt-20 flex justify-center">
                                <div class="flex flex-wrap gap-4">
                                    <template v-for="(link, key) in courses.links" :key="key">
                                        <div v-if="link.url === null" class="px-5 py-2.5 rounded-xl text-xs text-[#C9C7B8] opacity-50 font-bold" v-html="link.label"></div>
                                        <Link v-else :href="link.url" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all" :class="{ 'bg-[#57572A] text-white shadow-xl shadow-[#57572A]/20': link.active, 'bg-white text-[#5F5E5E] hover:bg-[#F4F4EF] border border-[#C9C7B8]/20': !link.active }" v-html="link.label" preserve-scroll />
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
