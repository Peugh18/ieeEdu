<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';

const props = defineProps<{
    courses: { data: any[]; links: any[]; total: number };
    categories: any[];
    filters: { search?: string; modality?: string; category?: string };
}>();

const searchTerm = ref(props.filters.search || '');
const selectedModality = ref(props.filters.modality || 'Todos');
const selectedCategory = ref(props.filters.category || 'Todas las áreas');

const modalities = ['Todos', 'En vivo', 'Grabado'];

function applyFilters() {
    router.get(
        route('cursos.index'),
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

const getDuration = (course: any) => {
    if (course.type === 'evento') return '1 Sesión';
    if (course.duration_weeks) return `${course.duration_weeks} semanas`;
    return 'A tu propio ritmo';
};
</script>

<template>
    <Head title="Cursos Profesionales - IEE" />
    
    <div class="flex min-h-screen flex-col bg-[#FAFAF5] text-[#1A1C19] font-sans">
        <Navigation />

        <main class="flex-1 pb-20">
            <!-- Hero Banner -->
            <div class="bg-gradient-to-br from-[#57572A] to-[#707040] py-20 px-6 sm:px-12 text-center relative mb-12">
                <div class="relative z-10 max-w-4xl mx-auto">
                    <p class="text-[#FAFAF5]/80 text-sm font-bold uppercase tracking-[0.1em] mb-4">Instituto de Economía y Empresa</p>
                    <h1 class="text-4xl md:text-5xl lg:text-[64px] font-serif font-bold text-white mb-6 leading-tight tracking-[-0.02em]">
                        Catálogo de Cursos
                    </h1>
                    <p class="text-[#FAFAF5]/90 text-lg md:text-xl max-w-2xl mx-auto font-serif">Conoce nuestros programas en vivo y grabados impartidos por líderes en el sector y mejora tu trayectoria profesional.</p>
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
                        <div class="p-6 bg-[#F4F4EF] rounded">
                            <h3 class="flex items-center gap-2 text-xs font-bold text-[#5F5E5E] mb-5 tracking-wider uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                Modalidad
                            </h3>
                            <div class="space-y-3">
                                <button 
                                    v-for="mod in modalities" 
                                    :key="mod"
                                    @click="selectedModality = mod"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded text-[13px] font-bold tracking-[0.05em] uppercase transition-colors"
                                    :class="selectedModality === mod ? 'bg-[#57572A] text-white' : 'bg-transparent text-[#57572A] hover:bg-[#EBEBE3]'"
                                >
                                    <!-- Todos Icon -->
                                    <svg v-if="mod === 'Todos'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                    <!-- En Vivo Icon -->
                                    <svg v-else-if="mod === 'En vivo'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <!-- Grabado Icon -->
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ mod }}
                                </button>
                            </div>
                        </div>

                        <!-- Categories / Schools -->
                        <div class="p-6 bg-[#F4F4EF] rounded">
                            <h3 class="flex items-center gap-2 text-xs font-bold text-[#5F5E5E] mb-5 tracking-wider uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Escuelas
                            </h3>
                            <div class="space-y-4 block max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" v-model="selectedCategory" value="Todas las áreas" class="w-4 h-4 text-[#57572A] focus:ring-[#57572A] border-[#C9C7B8] rounded-sm" />
                                    <span class="text-sm font-medium transition-colors" :class="selectedCategory === 'Todas las áreas' ? 'text-[#1A1C19] font-bold' : 'text-[#5F5E5E] group-hover:text-[#1A1C19]'">Todas las áreas</span>
                                </label>
                                <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" v-model="selectedCategory" :value="cat.name" class="w-4 h-4 text-[#57572A] focus:ring-[#57572A] border-[#C9C7B8] rounded-sm" />
                                    <span class="text-sm transition-colors" :class="selectedCategory === cat.name ? 'text-[#1A1C19] font-bold' : 'text-[#5F5E5E] group-hover:text-[#1A1C19]'">{{ cat.name }}</span>
                                </label>
                            </div>
                        </div>
                    </aside>

                    <!-- Right Content Grid -->
                    <main class="flex-1 min-w-0">
                        <div class="mb-10 pt-2">
                            <h2 class="text-[#5F5E5E] font-serif text-2xl"><strong class="text-[#1A1C19]">{{ courses.total }}</strong> Programas encontrados</h2>
                        </div>

                        <!-- Grid -->
                        <div v-if="courses.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                            <article 
                                v-for="course in courses.data" 
                                :key="course.id"
                                class="bg-[#FFFFFF] rounded border border-[#C9C7B8] border-opacity-30 flex flex-col group overflow-hidden"
                                style="box-shadow: 0px 20px 40px rgba(26, 28, 25, 0.04);"
                            >
                                <!-- Image container (No round borders inside card) -->
                                <Link :href="route('cursos.show', course.slug)" class="relative h-[220px] w-full block bg-[#F4F4EF] overflow-hidden">
                                    <img v-if="course.image" :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                                    
                                    <!-- Elegant Tags -->
                                    <div class="absolute top-4 left-4 z-10">
                                        <span class="px-3 py-1.5 rounded bg-white/90 backdrop-blur-[20px] text-[#1A1C19] text-[10px] font-bold tracking-widest uppercase flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="course.type === 'en vivo' ? 'bg-[#D32F2F]' : (course.type === 'evento' ? 'bg-[#7B1FA2]' : 'bg-[#57572A]')"></span>
                                            {{ course.type === 'en vivo' ? 'EN VIVO' : (course.type === 'evento' ? 'EVENTO' : 'GRABADO') }}
                                        </span>
                                    </div>
                                </Link>

                                <!-- Content -->
                                <div class="p-6 flex flex-col flex-1">
                                    <span class="text-[10px] font-bold text-[#57572A] uppercase tracking-widest mb-3">{{ course.category?.name || 'General' }}</span>
                                    
                                    <Link :href="route('cursos.show', course.slug)">
                                        <h3 class="font-serif font-bold text-xl text-[#1A1C19] leading-snug mb-4 group-hover:text-[#57572A] transition-colors" :title="course.title">
                                            {{ course.title }}
                                        </h3>
                                    </Link>

                                    <!-- Features/Duration -->
                                    <div class="space-y-3 mb-8 mt-auto">
                                        <div class="flex items-center gap-2 text-sm text-[#5F5E5E]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ getDuration(course) }}
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-[#5F5E5E]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ course.type === 'evento' ? 'Certificable' : 'Material Incluido' }}
                                        </div>
                                    </div>

                                    <!-- Footer Pricing & CTA -->
                                    <div class="flex items-end justify-between pt-6 border-t border-[#C9C7B8] border-opacity-30">
                                        <div class="flex flex-col">
                                            <span v-if="course.sale_price && course.sale_price > 0" class="text-xs text-[#5F5E5E] line-through mb-1">S/ {{ course.price }}</span>
                                            <span class="text-2xl font-serif font-bold text-[#1A1C19]">S/ {{ course.sale_price && course.sale_price > 0 ? course.sale_price : course.price }}</span>
                                        </div>
                                        <Link :href="route('cursos.show', course.slug)" class="rounded text-[11px] uppercase tracking-[0.05em] font-bold px-4 py-2.5 bg-gradient-to-br from-[#57572A] to-[#707040] text-white hover:opacity-90 transition-opacity">
                                            Detalles
                                        </Link>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Empty state -->
                        <div v-else class="py-24 flex flex-col items-center justify-center text-center bg-[#F4F4EF] rounded border border-[#C9C7B8] border-opacity-30">
                            <svg class="w-16 h-16 text-[#C9C7B8] mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <h3 class="text-2xl font-serif font-bold text-[#1A1C19] mb-3">No hay coincidencias</h3>
                            <p class="text-[#5F5E5E] max-w-md font-sans mb-8">No hemos encontrado cursos o programas que coincidan con tu búsqueda actual.</p>
                            <button @click="() => { selectedModality='Todos'; selectedCategory='Todas las áreas'; searchTerm=''; applyFilters(); }" class="text-xs uppercase tracking-[0.05em] font-bold text-[#57572A] border-b border-[#57572A] pb-1 hover:text-[#1A1C19] hover:border-[#1A1C19] transition-colors">Limpiar Filtros</button>
                        </div>

                        <!-- Pagination -->
                        <div v-if="courses.links && courses.links.length > 3" class="mt-16 flex justify-center">
                            <div class="flex flex-wrap gap-2">
                                <template v-for="(link, key) in courses.links" :key="key">
                                    <div v-if="link.url === null" class="px-3 py-1.5 rounded text-sm text-[#C9C7B8] cursor-not-allowed" v-html="link.label"></div>
                                    <Link v-else :href="link.url" class="px-3 py-1.5 rounded text-sm font-bold transition-colors" :class="{ 'bg-[#57572A] text-white': link.active, 'bg-transparent text-[#5F5E5E] hover:bg-[#F4F4EF]': !link.active }" v-html="link.label" preserve-scroll />
                                </template>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </main>
    </div>
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
