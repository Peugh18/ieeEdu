<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BookOpen, Clock, CheckCircle, PlayCircle, Search, Filter, ArrowRight, Star } from 'lucide-vue-next';

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
</script>

<template>
    <Head title="Mis Cursos Académicos - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-12 max-w-[1400px] mx-auto space-y-12 pb-32">
            
            <!-- Academic Header -->
            <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 border-b border-outline-variant/30 pb-12">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary/5 rounded-full border border-primary/10">
                        <Star class="w-3 h-3 text-primary" />
                        <span class="text-[9px] font-bold text-primary uppercase tracking-[0.2em] italic">Panel del Estudiante</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-serif font-bold text-on-surface leading-tight italic">Mi Expediente<br/><span class="text-primary">Académico</span></h1>
                    <p class="text-on-surface-variant font-serif text-lg italic max-w-2xl">Gestione su progreso, acceda a sus sesiones en vivo y continúe su formación con excelencia.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative group">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary opacity-40 group-focus-within:opacity-100 transition-opacity" />
                        <input 
                            v-model="searchTerm"
                            type="text" 
                            placeholder="Buscar en sus cursos..." 
                            class="pl-12 pr-6 py-4 bg-surface-container-low border border-outline-variant/30 rounded-2xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary w-full sm:w-80 transition-all font-serif italic"
                        />
                    </div>
                </div>
            </header>

            <!-- Refined Filter Tabs -->
            <div class="flex flex-wrap gap-4 items-center">
                <button 
                    v-for="filter in filters" 
                    :key="filter.id"
                    @click="currentFilter = filter.id"
                    class="flex items-center gap-3 px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest transition-all border shadow-sm group"
                    :class="currentFilter === filter.id 
                        ? 'bg-primary text-on-primary border-primary shadow-primary/20 scale-105' 
                        : 'bg-white text-on-surface-variant border-outline-variant/30 hover:border-primary/40 hover:bg-primary/5'"
                >
                    <component :is="filter.icon" class="w-4 h-4 transition-transform group-hover:scale-110" />
                    {{ filter.label }}
                </button>
            </div>

            <!-- Enhanced Course Grid -->
            <div v-if="filteredCourses.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-10">
                <article 
                    v-for="course in filteredCourses" 
                    :key="course.id"
                    class="bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm overflow-hidden group hover:shadow-2xl hover:shadow-primary/5 transition-all duration-500 flex flex-col hover:-translate-y-2"
                >
                    <!-- Visual Cover -->
                    <div class="relative h-56 overflow-hidden bg-surface-container">
                        <img 
                            v-if="course.image" 
                            :src="course.image" 
                            :alt="course.title"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent"></div>
                        <div class="absolute top-6 left-6">
                            <span class="px-4 py-1.5 rounded-full bg-white/90 backdrop-blur-md text-[9px] font-bold tracking-[0.2em] uppercase text-primary border border-white/50">
                                {{ course.type }}
                            </span>
                        </div>
                    </div>

                    <!-- Academic Content -->
                    <div class="p-8 flex flex-col flex-1 space-y-6">
                        <div class="space-y-2">
                             <div class="flex items-center gap-2 text-[10px] font-bold text-primary uppercase tracking-widest">
                                 <span class="w-8 h-[1px] bg-primary/30"></span>
                                 {{ course.category?.name }}
                             </div>
                             <h3 class="font-serif font-bold text-2xl text-on-surface leading-snug group-hover:text-primary transition-colors italic">
                                {{ course.title }}
                             </h3>
                        </div>

                        <!-- Progress Section -->
                        <div class="bg-surface-container-low p-6 rounded-3xl space-y-4 border border-outline-variant/10">
                            <div class="flex justify-between items-end">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-bold text-on-surface-variant/40 uppercase tracking-widest">Estado académico</p>
                                    <p class="text-xs font-bold text-on-surface italic">{{ course.pivot.completed_at ? 'Módulo Completado' : 'Continuar Formación' }}</p>
                                </div>
                                <span class="text-lg font-serif font-bold text-primary italic">{{ course.pivot.progress || (course.pivot.completed_at ? '100' : '0') }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-surface-container rounded-full overflow-hidden p-[2px] border border-outline-variant/10">
                                <div 
                                    class="h-full bg-primary rounded-full transition-all duration-1000" 
                                    :style="{ width: (course.pivot.progress || (course.pivot.completed_at ? 100 : 0)) + '%' }"
                                ></div>
                            </div>
                        </div>

                        <Link 
                            :href="route('student.classroom', { course: course.slug })"
                            class="w-full py-5 rounded-2xl bg-primary text-on-primary text-xs font-bold uppercase tracking-widest flex justify-center items-center gap-3 hover:bg-primary/90 transition-all shadow-xl shadow-primary/10 active:scale-95 group"
                        >
                            {{ course.pivot.completed_at ? 'Repasar Lecciones' : 'Entrar al Aula Virtual' }}
                            <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Academic Empty State -->
            <div v-else class="py-32 text-center bg-surface-container-low rounded-[4rem] border-2 border-dashed border-outline-variant/40 space-y-8 max-w-4xl mx-auto">
                <div class="inline-flex p-8 bg-white rounded-[2.5rem] shadow-xl shadow-primary/5 mb-4 group ring-8 ring-primary/5 transition-all">
                    <BookOpen class="w-12 h-12 text-primary/30 group-hover:text-primary transition-colors" />
                </div>
                <div class="space-y-4 px-8">
                    <h3 class="text-3xl font-serif font-bold text-on-surface italic">Su registro académico está vacío</h3>
                    <p class="text-on-surface-variant font-serif italic text-lg max-w-md mx-auto">Aún no se ha inscrito en programas académicos o no hay resultados que coincidan con su búsqueda.</p>
                </div>
                <Link :href="route('cursos.index')" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-primary/70 uppercase tracking-widest border-b border-primary/20 pb-2 transition-all">
                    Explorar Catálogo de Especialización <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.1);
    border-radius: 10px;
}
</style>
