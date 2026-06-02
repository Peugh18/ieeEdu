<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { BookOpen, CheckCircle, PlayCircle, Search, ArrowRight, LayoutGrid, List } from 'lucide-vue-next';

interface Course {
    id: number;
    title: string;
    slug: string;
    image: string;
    type: string;
    category: { name: string };
    pivot: {
        enrolled_at: string;
        completed_at: string | null;
        progress?: number;
    };
}

const props = defineProps<{
    courses: Course[];
}>();

const currentFilter = ref('all'); 
const searchTerm = ref('');
const viewMode = ref<'grid' | 'list'>('grid');

const filters = [
    { id: 'all', label: 'Todo mi catálogo', icon: BookOpen },
    { id: 'active', label: 'En curso', icon: PlayCircle },
    { id: 'completed', label: 'Finalizados', icon: CheckCircle },
];

const filteredCourses = computed(() => {
    let result = props.courses;
    if (currentFilter.value === 'active') {
        result = result.filter(c => !c.pivot?.completed_at && (c.pivot?.progress ?? 0) < 100);
    } else if (currentFilter.value === 'completed') {
        result = result.filter(c => !!c.pivot?.completed_at || (c.pivot?.progress ?? 0) >= 100);
    }
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        result = result.filter(c => c.title.toLowerCase().includes(term));
    }
    return result;
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos Académicos', href: '/student/courses' },
];

const currentPage = ref(1);

const pageSize = computed(() => {
    return viewMode.value === 'grid' ? 6 : 10;
});

watch([currentFilter, searchTerm, viewMode], () => {
    currentPage.value = 1;
});

const totalPages = computed(() => {
    return Math.ceil(filteredCourses.value.length / pageSize.value);
});

const paginatedCourses = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredCourses.value.slice(start, end);
});

function scrollToTop() {
    const headerElement = document.querySelector('header');
    if (headerElement) {
        headerElement.scrollIntoView({ behavior: 'smooth' });
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>

<template>
    <Head title="Mis Cursos Académicos - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background">
        <div class="p-4 md:p-12 max-w-[1440px] mx-auto space-y-6 md:space-y-12 pb-24">
            
            <!-- Premium Header -->
            <header class="relative overflow-hidden bg-gradient-to-br from-on-background to-[#2D302B] rounded-2xl md:rounded-[3rem] p-6 md:p-16 mb-4 md:mb-12 shadow-2xl shadow-on-background/20">
                <!-- Decor -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-primary/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#D4AF37]/5 rounded-full blur-[80px] -ml-32 -mb-32"></div>

                <div class="relative z-10 space-y-6 max-w-4xl">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 shadow-inner">
                        <div class="w-2 h-2 rounded-full bg-[#D4AF37] animate-pulse"></div>
                        <span class="text-[10px] font-bold text-white/90 uppercase tracking-[0.25em]">Expediente Académico</span>
                    </div>
                    
                    <h1 class="text-2xl md:text-6xl font-serif font-bold text-white leading-tight tracking-tight">
                        Mi <span class="italic text-[#D4AF37]">Trayectoria</span> Profesional
                    </h1>
                    
                    <p class="hidden md:block text-background/70 font-serif text-lg md:text-xl italic max-w-2xl leading-relaxed">
                        Gestione su progreso y acceda a programas diseñados para alcanzar la excelencia en el sector público y privado.
                    </p>

                    <!-- Integrated Search -->
                    <div class="pt-6">
                        <div class="relative max-w-md group">
                            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-outline-variant transition-colors group-focus-within:text-[#D4AF37]" />
                            <input 
                                v-model="searchTerm"
                                type="text" 
                                placeholder="Buscar en mi catálogo..." 
                                class="w-full pl-12 pr-6 py-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-white text-sm focus:ring-4 focus:ring-[#D4AF37]/10 focus:border-[#D4AF37]/40 transition-all placeholder:text-white/30 font-serif italic"
                            />
                        </div>
                    </div>
                </div>
            </header>

            <!-- Refined Filter Tabs -->
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm p-3 md:p-4 rounded-2xl md:rounded-[2.5rem] border border-white dark:border-slate-800 shadow-sm w-full">
                <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
                    <button 
                        v-for="filter in filters" 
                        :key="filter.id"
                        @click="currentFilter = filter.id"
                        class="flex items-center gap-3 px-6 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.15em] transition-all duration-500 group relative overflow-hidden"
                        :class="currentFilter === filter.id 
                            ? 'bg-primary text-white shadow-xl shadow-primary/20 scale-105' 
                            : 'bg-transparent text-on-surface-variant hover:bg-surface-container-highest dark:hover:bg-slate-800 border border-outline-variant/20 dark:border-slate-800'"
                    >
                        <component :is="filter.icon" class="w-3.5 h-3.5" :class="currentFilter === filter.id ? 'text-[#D4AF37]' : 'text-primary/40'" />
                        {{ filter.label }}
                    </button>
                </div>
                
                <div class="flex items-center justify-between lg:justify-end gap-6 w-full lg:w-auto shrink-0">
                    <div class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-widest whitespace-nowrap flex items-center gap-2">
                        <span class="hidden md:inline-block w-8 h-[1px] bg-outline-variant"></span>
                        Exhibiendo {{ filteredCourses.length }} programas
                    </div>

                    <!-- Grid/List Toggle -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800/50 p-1 rounded-xl border border-slate-200/20">
                        <button 
                            @click="viewMode = 'grid'" 
                            class="p-2 rounded-lg transition-all"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>
                        <button 
                            @click="viewMode = 'list'" 
                            class="p-2 rounded-lg transition-all"
                            :class="viewMode === 'list' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                        >
                            <List class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Academic Catalog Content -->
            <div v-if="filteredCourses.length > 0">
                <!-- Grid View -->
                <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <article 
                        v-for="course in paginatedCourses" 
                        :key="course.id"
                        class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-outline-variant/20 dark:border-slate-800/80 shadow-sm overflow-hidden group hover:shadow-[0_30px_60px_rgba(87,87,42,0.12)] transition-all duration-700 flex flex-col hover:-translate-y-3"
                    >
                        <!-- Visual Cover -->
                        <div class="relative h-44 md:h-64 overflow-hidden bg-surface-container-highest">
                            <img 
                                v-if="course.image" 
                                :src="course.image" 
                                :alt="course.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-in-out"
                            />
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-on-background/60 via-transparent to-transparent opacity-60"></div>
                            
                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-2 rounded-sm bg-white/10 backdrop-blur-xl text-[10px] font-bold tracking-widest uppercase text-white border border-white/20">
                                    {{ course.type }}
                                </span>
                            </div>

                            <!-- Progress Circle Mini Badge -->
                            <div class="absolute bottom-6 right-6">
                                <div class="w-16 h-16 rounded-full bg-white/90 backdrop-blur-xl p-1 shadow-lg flex items-center justify-center border border-white/50 dark:border-slate-700">
                                    <svg class="w-full h-full transform -rotate-90">
                                        <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" fill="transparent" class="text-surface-container-highest dark:text-slate-800" />
                                        <circle 
                                            cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" fill="transparent" 
                                            class="text-[#D4AF37]"
                                            :stroke-dasharray="2 * Math.PI * 28"
                                            :stroke-dashoffset="(2 * Math.PI * 28) * (1 - (course.pivot.progress || (course.pivot.completed_at ? 100 : 0)) / 100)"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                    <span class="absolute text-[11px] font-bold text-on-background">{{ course.pivot.progress || (course.pivot.completed_at ? '100' : '0') }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Content -->
                        <div class="p-5 md:p-8 flex flex-col flex-1">
                            <div class="space-y-4 mb-8">
                                 <div class="flex items-center gap-3 text-[10px] font-bold text-primary/60 uppercase tracking-[0.2em]">
                                     {{ course.category?.name }}
                                 </div>
                                 <h3 class="font-serif font-bold text-2xl text-on-background leading-tight group-hover:text-primary transition-colors italic line-clamp-2">
                                    {{ course.title }}
                                 </h3>
                            </div>

                            <!-- Info Strip -->
                            <div class="flex items-center gap-4 mb-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2 text-[13px] font-medium italic text-on-surface-variant">
                                    <CheckCircle v-if="course.pivot.completed_at" class="w-4 h-4 text-[#D4AF37]" />
                                    <PlayCircle v-else class="w-4 h-4 text-primary" />
                                    {{ course.pivot.completed_at ? 'Módulo Completado' : 'Sesiones en curso' }}
                                </div>
                            </div>

                            <Link 
                                :href="route('student.classroom', { course: course.slug })"
                                class="w-full py-5 rounded-2xl bg-primary text-white text-[11px] font-black uppercase tracking-[0.2em] flex justify-center items-center gap-3 hover:bg-on-background transition-all duration-500 shadow-xl shadow-primary/10 relative group/btn overflow-hidden"
                            >
                                <span class="relative z-10">{{ course.pivot.completed_at ? 'Repasar Lecciones' : 'Aula Virtual' }}</span>
                                <ArrowRight class="w-4 h-4 relative z-10 group-hover/btn:translate-x-1 transition-transform" />
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                            </Link>
                        </div>
                    </article>
                </div>

                <!-- List View (Table Layout) -->
                <div v-else class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-outline-variant/20 dark:border-slate-800 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Programa Académico</th>
                                    <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-center">Tipo</th>
                                    <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Progreso</th>
                                    <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-center">Estado</th>
                                    <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-right">Acceso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                <tr v-for="course in paginatedCourses" :key="course.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300">
                                    <td class="px-8 py-5 relative">
                                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 rounded-lg overflow-hidden shadow-inner flex-shrink-0 relative">
                                                <img v-if="course.image" :src="course.image" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-[#D4AF37] mb-1">{{ course.category?.name }}</span>
                                                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight group-hover:text-primary transition-colors line-clamp-1" :title="course.title">{{ course.title }}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-50 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            {{ course.type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3 max-w-[200px]">
                                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-200/20">
                                                <div class="bg-[#D4AF37] h-full rounded-full transition-all duration-500" :style="{ width: `${course.pivot.progress || (course.pivot.completed_at ? 100 : 0)}%` }"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 tabular-nums">
                                                {{ course.pivot.progress || (course.pivot.completed_at ? '100' : '0') }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold"
                                            :class="course.pivot.completed_at 
                                                ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' 
                                                : 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400'"
                                        >
                                            <CheckCircle v-if="course.pivot.completed_at" class="w-3.5 h-3.5" />
                                            <PlayCircle v-else class="w-3.5 h-3.5" />
                                            {{ course.pivot.completed_at ? 'Completado' : 'En Curso' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-end">
                                            <Link 
                                                :href="route('student.classroom', { course: course.slug })"
                                                class="px-4 py-2 rounded-xl bg-primary text-white text-[10px] font-black uppercase tracking-[0.15em] hover:bg-on-background transition-all duration-300 flex items-center gap-1.5 active:scale-95 shadow-md shadow-primary/15"
                                            >
                                                <span>{{ course.pivot.completed_at ? 'Repasar' : 'Aula' }}</span>
                                                <ArrowRight class="w-3 h-3" />
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Client-side Pagination Controls -->
                <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 mt-8 border-t border-slate-100 dark:border-slate-800/80">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        Exhibiendo del {{ (currentPage - 1) * pageSize + 1 }} al {{ Math.min(currentPage * pageSize, filteredCourses.length) }} de {{ filteredCourses.length }} cursos
                    </span>

                    <div class="flex items-center gap-1 bg-slate-105 dark:bg-slate-800/50 p-1 rounded-xl border border-slate-200/20 shadow-inner">
                        <!-- Prev Button -->
                        <button 
                            @click="currentPage > 1 && (currentPage--, scrollToTop())"
                            :disabled="currentPage === 1"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-[0.1em] transition-all flex items-center gap-1 select-none"
                            :class="currentPage === 1 
                                ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed opacity-50' 
                                : 'text-slate-700 dark:text-slate-350 hover:bg-white dark:hover:bg-slate-900 hover:text-primary shadow-sm'"
                        >
                            Anterior
                        </button>

                        <!-- Page Numbers -->
                        <button 
                            v-for="page in totalPages" 
                            :key="page"
                            @click="currentPage = page; scrollToTop()"
                            class="w-8 h-8 rounded-lg text-xs font-bold transition-all"
                            :class="currentPage === page 
                                ? 'bg-primary text-white shadow-md shadow-primary/20 scale-105' 
                                : 'text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900 hover:text-primary'"
                        >
                            {{ page }}
                        </button>

                        <!-- Next Button -->
                        <button 
                            @click="currentPage < totalPages && (currentPage++, scrollToTop())"
                            :disabled="currentPage === totalPages"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-[0.1em] transition-all flex items-center gap-1 select-none"
                            :class="currentPage === totalPages 
                                ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed opacity-50' 
                                : 'text-slate-700 dark:text-slate-350 hover:bg-white dark:hover:bg-slate-900 hover:text-primary shadow-sm'"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Academic Empty State -->
            <div v-else class="py-32 text-center bg-white dark:bg-slate-900 rounded-[4rem] border border-outline-variant/20 dark:border-slate-800 shadow-sm space-y-10 max-w-4xl mx-auto overflow-hidden relative">
                <!-- Decorative background -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none flex items-center justify-center">
                    <BookOpen class="w-[500px] h-[500px]" />
                </div>

                <div class="relative z-10 space-y-8 px-8">
                    <div class="inline-flex p-10 bg-background dark:bg-slate-800 rounded-[3rem] border border-outline-variant/30 dark:border-slate-700/50 shadow-inner mb-4">
                        <BookOpen class="w-16 h-16 text-outline-variant dark:text-slate-500" />
                    </div>
                    <div class="space-y-4 max-w-lg mx-auto">
                        <h3 class="text-4xl font-serif font-bold text-on-background italic">Su registro académico está vacío</h3>
                        <p class="text-on-surface-variant font-serif italic text-lg leading-relaxed">Aún no se ha inscrito en programas académicos o sus filtros no arrojan resultados en su expediente actual.</p>
                    </div>
                    <Link :href="route('cursos.index')" class="inline-flex items-center gap-4 text-xs font-black text-primary hover:text-[#D4AF37] uppercase tracking-[0.25em] group transition-all">
                        Explorar Catálogo de Especialización 
                        <span class="w-12 h-[1px] bg-outline-variant group-hover:bg-[#D4AF37] group-hover:w-16 transition-all"></span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>
        </div>
        <BottomNav active="courses" />
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.08);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.15);
}
</style>
