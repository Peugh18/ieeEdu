<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type SharedData, type User } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { 
    BookOpen, Calendar, ClipboardCheck, Award, Clock, ArrowRight, 
    Play, CheckCircle2, ChevronRight, BarChart3, TrendingUp, X, ExternalLink 
} from 'lucide-vue-next';
import { ref } from 'vue';

interface SummaryStats {
    active_courses: number;
    completed_courses: number;
    upcoming_classes: number;
    available_exams: number;
}

interface CourseItem {
    id: number;
    title: string;
    slug: string;
    image: string;
    progress: number;
}

interface LiveClass {
    id: number;
    title: string;
    course_title: string;
    start_time: string;
    start_time_human: string;
    course_slug: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mi Aula Virtual',
        href: '/dashboard',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const props = defineProps<{
    stats: SummaryStats;
    recentCourses: CourseItem[];
    nextLiveClass: LiveClass | null;
}>();

const showLiveModal = ref(false);
</script>

<template>
    <Head title="Aula Virtual - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto space-y-12 pb-24">
            <!-- Header section with Welcome message -->
            <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-[#57572A] uppercase tracking-[0.2em] mb-1 italic">Instituto de Economía y Empresa</p>
                    <h1 class="text-4xl font-serif font-bold text-gray-900 leading-tight">Bienvenido de nuevo,<br/> <span class="text-[#D4AF37]">{{ user.name }}</span></h1>
                    <p class="text-gray-500 font-serif text-lg">Es un buen día para avanzar en tus metas profesionales.</p>
                </div>
                
                <div v-if="nextLiveClass" class="flex items-center gap-4 bg-white/80 backdrop-blur-md p-4 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/40">
                    <div class="p-3 bg-[#57572A]/5 rounded-2xl">
                         <Calendar class="w-6 h-6 text-[#57572A]" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Próxima sesión en vivo</p>
                        <p class="text-sm font-bold text-gray-900 italic">Te toca: {{ nextLiveClass.course_title }}</p>
                        <p class="text-[11px] text-[#57572A] font-medium">{{ nextLiveClass.start_time_human }}</p>
                    </div>
                    <button 
                        @click="showLiveModal = true"
                        class="p-2.5 bg-[#57572A] text-white rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-lg active:shadow-none"
                    >
                        <Play class="w-4 h-4 fill-current" />
                    </button>
                </div>
                <div v-else class="flex items-center gap-4 bg-white/40 backdrop-blur-md p-4 rounded-3xl border border-dashed border-gray-200">
                    <div class="p-3 bg-gray-100 rounded-2xl text-gray-400">
                         <Clock class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Sesiones en vivo</p>
                        <p class="text-xs text-gray-500 font-serif">No tienes clases próximas hoy.</p>
                    </div>
                </div>
            </header>


            <!-- Stats grid -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Active Courses Stat -->
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-blue-50 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <BookOpen class="w-8 h-8 text-blue-600" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cursos Activos</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.active_courses }}</p>
                    <Link :href="route('student.courses.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-blue-600 flex items-center gap-1.5 hover:underline">Ir a mis cursos <ChevronRight class="w-3 h-3" /></Link>
                </div>

                <!-- Completed Courses Stat -->
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-emerald-50 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <CheckCircle2 class="w-8 h-8 text-emerald-600" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Graduado en</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.completed_courses }} <span class="text-sm text-gray-400">Prog.</span></p>
                    <Link :href="route('student.certificates.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-emerald-600 flex items-center gap-1.5 hover:underline">Ver certificados <ChevronRight class="w-3 h-3" /></Link>
                </div>

                <!-- Live classes Stat -->
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-amber-50 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <Calendar class="w-8 h-8 text-amber-600" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Próximas Clases</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.upcoming_classes }}</p>
                    <Link :href="route('student.live-classes.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-amber-600 flex items-center gap-1.5 hover:underline">Ver calendario <ChevronRight class="w-3 h-3" /></Link>
                </div>

                <!-- Exams Stat -->
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-[#57572A]/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-[#57572A]/5 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <ClipboardCheck class="w-8 h-8 text-[#57572A]" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Evaluaciones</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.available_exams }}</p>
                    <Link :href="route('student.exams.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-[#57572A] flex items-center gap-1.5 hover:underline">Ir a exámenes <ChevronRight class="w-3 h-3" /></Link>
                </div>
            </section>

            <!-- Main dashboard content area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Left Column: Recent Activity and Continue learning -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-serif font-bold text-gray-900">Continuar Aprendizaje</h2>
                        <Link :href="route('student.courses.index')" class="text-xs font-bold text-[#57572A] uppercase tracking-widest hover:underline flex items-center gap-1">Ver todos los cursos <ArrowRight class="w-3 h-3" /></Link>
                    </div>

                    <div v-if="recentCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <article v-for="course in recentCourses" :key="course.id" 
                            class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 group"
                        >
                            <div class="relative h-48 overflow-hidden">
                                <img :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                <div class="absolute inset-0 bg-black/10 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <Link 
                                        :href="route('student.classroom', { course: course.slug })"
                                        class="px-6 py-3 bg-white text-gray-900 text-xs font-bold uppercase tracking-widest rounded-2xl flex items-center gap-2 shadow-xl hover:bg-gray-50 active:scale-95 transition-all"
                                    >
                                        <Play class="w-3 h-3 fill-current" /> Continuar
                                    </Link>
                                </div>
                            </div>
                            <div class="p-8">
                                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 group-hover:text-[#57572A] transition-colors leading-tight">{{ course.title }}</h3>
                                
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.1em]">
                                        <span class="text-gray-400 italic">Progreso actual</span>
                                        <span class="text-[#57572A] italic">{{ course.progress }}%</span>
                                    </div>
                                    <div class="h-2 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100 flex p-[2px]">
                                        <div class="h-full bg-gradient-to-r from-[#57572A] to-[#D4AF37] rounded-full transition-all duration-1000" :style="{ width: course.progress + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Right Column: Personal Stats / Highlights -->
                <aside class="space-y-8">
                    <!-- Academic Performance -->
                    <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                        <div class="relative z-10 flex flex-col h-full">
                            <header class="flex items-center justify-between mb-8">
                                <span class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/10"><BarChart3 class="w-6 h-6 text-[#D4AF37]" /></span>
                                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[9px] font-bold uppercase tracking-[0.2em] rounded-full border border-emerald-500/20 flex items-center gap-1.5"><TrendingUp class="w-3 h-3 text-emerald-400" /> Rendimiento Alto</span>
                            </header>
                            
                            <h3 class="text-2xl font-serif font-bold mb-2">Desempeño Global</h3>
                            <p class="text-white/40 font-serif text-sm italic mb-8">Basado en tus últimos 5 exámenes y tareas completadas.</p>
                            
                            <div class="flex items-center gap-6 mb-10">
                                <div class="text-center">
                                    <p class="text-4xl font-serif font-bold italic text-white/95">18.5</p>
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/30 italic">Promedio</p>
                                </div>
                                <div class="w-[1px] h-10 bg-white/10"></div>
                                <div class="text-center">
                                    <p class="text-4xl font-serif font-bold italic text-[#D4AF37]">92%</p>
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/30 italic">Asistencia</p>
                                </div>
                                <div class="w-[1px] h-10 bg-white/10"></div>
                                <div class="text-center text-white/95">
                                    <p class="text-4xl font-serif font-bold italic">04</p>
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/40 italic">Cert.</p>
                                </div>
                            </div>
                            
                            <button class="mt-auto w-full py-4 bg-white text-gray-900 text-xs font-bold uppercase tracking-[0.2em] rounded-2xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-xl active:scale-95 group-hover:shadow-white/5">Descargar Historial Académico</button>
                        </div>
                        <!-- Abstract background -->
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-[100px]"></div>
                        <div class="absolute -left-10 -top-10 w-48 h-48 bg-[#D4AF37]/5 rounded-full blur-[80px]"></div>
                    </div>

                    <!-- Announcements or next step -->
                    <div class="bg-[#FAFAF5] rounded-[2.5rem] border-2 border-dashed border-[#C9C7B8] p-8 text-center group cursor-pointer hover:border-[#57572A] transition-colors">
                        <div class="inline-flex p-4 bg-white rounded-[2rem] shadow-sm mb-4 border border-gray-100 group-hover:scale-110 transition-transform">
                             <Award class="w-8 h-8 text-[#D4AF37]" />
                        </div>
                        <h4 class="text-lg font-serif font-bold text-gray-900">¿Listo para el siguiente paso?</h4>
                        <p class="text-xs text-gray-500 font-serif italic mb-6 leading-relaxed">Explora nuestras nuevas Masterclasses impartidas por expertos internacionales.</p>
                        <Link :href="route('cursos.index')" class="px-6 py-2.5 bg-transparent border border-gray-200 text-gray-900 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-white hover:border-gray-900 transition-all">Ver Masterclasses</Link>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Live Class Modal -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showLiveModal && nextLiveClass" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" @click.self="showLiveModal = false">
                    <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="scale-95 opacity-0" enter-to-class="scale-100 opacity-100">
                        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden border border-gray-100">
                            <!-- Modal Header -->
                            <div class="relative h-48 bg-gray-900 flex items-center justify-center overflow-hidden p-8">
                                <div class="absolute inset-0 opacity-20">
                                    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-[#D4AF37] via-[#57572A] to-transparent"></div>
                                </div>
                                <div class="relative z-10 text-center text-white">
                                    <div class="inline-flex p-3 bg-white/10 rounded-2xl backdrop-blur-md mb-3 border border-white/10">
                                        <Calendar class="w-8 h-8 text-[#D4AF37]" />
                                    </div>
                                    <h4 class="text-xl font-serif font-bold italic">¡Es hora de aprender!</h4>
                                </div>
                                <button @click="showLiveModal = false" class="absolute top-6 right-6 p-2 bg-white/10 hover:bg-white/20 rounded-xl text-white transition-colors">
                                    <X class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-10 space-y-6">
                                <div class="space-y-4">
                                    <p class="text-gray-500 font-serif text-sm italic">Para ingresar a la sesión en vivo de:</p>
                                    <h5 class="text-2xl font-serif font-bold text-gray-900 leading-tight">{{ nextLiveClass.title }}</h5>
                                    
                                    <div class="p-5 bg-[#FAFAF5] rounded-3xl border border-[#C9C7B8]/30">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 italic">Curso correspondiente</p>
                                        <p class="text-sm font-bold text-[#57572A]">{{ nextLiveClass.course_title }}</p>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500 leading-relaxed font-serif">Por favor, dirígete a la página del curso donde encontrarás el enlace directo, materiales de apoyo y el chat interactivo de la sesión.</p>

                                <div class="flex flex-col gap-3 pt-4">
                                    <Link 
                                        :href="route('student.classroom', { course: nextLiveClass.course_slug })"
                                        class="w-full py-4 bg-[#57572A] text-white text-xs font-bold uppercase tracking-[0.2em] rounded-2xl flex items-center justify-center gap-2 shadow-xl hover:shadow-[#57572A]/20 hover:-translate-y-0.5 transition-all active:scale-95"
                                    >
                                        Ir al Curso Ahora <ExternalLink class="w-4 h-4" />
                                    </Link>
                                    <button 
                                        @click="showLiveModal = false"
                                        class="w-full py-4 bg-transparent text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] hover:text-gray-600 transition-colors"
                                    >
                                        Cerrar ventana
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
/* No additional styles needed, using utility classes */
</style>
