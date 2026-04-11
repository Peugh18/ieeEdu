<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type SharedData, type User } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { 
    BookOpen, Calendar, ClipboardCheck, Award, Clock, ArrowRight, 
    Play, CheckCircle2, ChevronRight, BarChart3, TrendingUp, X, ExternalLink,
    Crown
} from 'lucide-vue-next';
import { ref } from 'vue';

interface SummaryStats {
    active_courses: number;
    completed_courses: number;
    upcoming_classes: number;
    available_exams: number;
    average_score: number;
    certificate_count: number;
}

interface ContinueLearning {
    course_title: string;
    course_slug: string;
    lesson_title: string;
    lesson_id: number;
    progress: number;
    image: string;
}

interface Recommendation {
    id: number;
    title: string;
    slug: string;
    image: string;
    category: {
        name: string;
    };
}

interface Certificate {
    id: number;
    course_title: string;
    issue_date: string;
    code: string;
    download_url: string;
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
    continueLearning: ContinueLearning | null;
    recommendations: Recommendation[];
    certificates: Certificate[];
    nextLiveClass: any | null;
}>();

const showLiveModal = ref(false);
</script>

<template>
    <Head title="Aula Virtual - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto space-y-12 pb-24">
            <!-- 1. BIENVENIDA -->
            <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-[#57572A] uppercase tracking-[0.2em] mb-1 italic">Instituto de Economía y Empresa</p>
                    <h1 class="text-4xl font-serif font-bold text-gray-900 leading-tight">
                        Bienvenido de nuevo,<br/> 
                        <span class="text-[#D4AF37]">{{ user.name }}</span>
                        <div v-if="user.has_subscription" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white text-[10px] font-bold uppercase tracking-[0.2em] px-4 py-1.5 rounded-full shadow-lg shadow-amber-200 ml-4 align-middle" title="Miembro Premium">
                            <Crown class="w-3.5 h-3.5" />
                            Premium
                        </div>
                    </h1>
                    <p class="text-gray-500 font-serif text-lg italic">Continúa tu aprendizaje y alcanza nuevas metas profesionales.</p>
                </div>
                
                <div v-if="nextLiveClass" class="flex items-center gap-4 bg-white/80 backdrop-blur-md p-4 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/40">
                    <div class="p-3 bg-[#57572A]/5 rounded-2xl">
                         <Calendar class="w-6 h-6 text-[#57572A]" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Próxima sesión en vivo</p>
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
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-[#57572A]/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-blue-50 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <BookOpen class="w-8 h-8 text-blue-600" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cursos Activos</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.active_courses }}</p>
                    <Link :href="route('student.courses.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-[#57572A] flex items-center gap-1.5 hover:underline">Ir a mis cursos <ChevronRight class="w-3 h-3" /></Link>
                </div>

                <!-- Completed Courses Stat -->
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-emerald-50 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <CheckCircle2 class="w-8 h-8 text-emerald-600" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cursos Completados</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.completed_courses }}</p>
                    <Link :href="route('student.certificates.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-emerald-600 flex items-center gap-1.5 hover:underline">Mis Certificados <Award class="w-3 h-3" /></Link>
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
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col items-center text-center group hover:shadow-xl hover:shadow-[#D4AF37]/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="p-4 bg-[#D4AF37]/5 rounded-2xl mb-4 group-hover:scale-110 transition-transform">
                        <ClipboardCheck class="w-8 h-8 text-[#D4AF37]" />
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Evaluaciones</h3>
                    <p class="text-4xl font-serif font-bold text-gray-900">{{ stats.available_exams }}</p>
                    <Link :href="route('student.exams.index')" class="mt-4 text-[10px] font-bold uppercase tracking-widest text-[#D4AF37] flex items-center gap-1.5 hover:underline text-center">Ir a exámenes <ChevronRight class="w-3 h-3" /></Link>
                </div>
            </section>

            <!-- Main dashboard content area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Left Column: Recent Activity and Continue learning -->
                <div class="lg:col-span-2 space-y-12">
                    
                    <!-- 2. CONTINUAR DONDE LO DEJASTE -->
                    <div v-if="continueLearning" class="space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <h2 class="text-2xl font-serif font-bold text-gray-900 italic">Continuar donde lo dejaste</h2>
                        </div>
                        
                        <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 group flex flex-col md:flex-row">
                            <div class="md:w-1/3 relative h-48 md:h-auto overflow-hidden">
                                <img :src="continueLearning.image" :alt="continueLearning.course_title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/40 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 p-2 bg-[#D4AF37] rounded-full text-white shadow-lg shadow-[#D4AF37]/30">
                                    <Play class="w-4 h-4 fill-current" />
                                </div>
                            </div>
                            <div class="p-8 md:w-2/3 flex flex-col justify-between bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#FAFAF5] via-white to-white">
                                <div>
                                    <p class="text-[10px] font-bold text-[#57572A] uppercase tracking-[0.2em] mb-2">Curso en curso</p>
                                    <h3 class="text-2xl font-serif font-bold text-gray-900 mb-2 leading-tight">{{ continueLearning.course_title }}</h3>
                                    <p class="text-gray-500 font-serif text-sm italic mb-6">Última lección: <span class="font-bold text-gray-700 underline decoration-[#D4AF37]/30">{{ continueLearning.lesson_title }}</span></p>
                                </div>
                                
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.1em]">
                                            <span class="text-gray-400 italic">Progreso actual</span>
                                            <span class="text-[#57572A] italic">{{ continueLearning.progress }}%</span>
                                        </div>
                                        <div class="h-2 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100 flex p-[1px]">
                                            <div class="h-full bg-gradient-to-r from-[#57572A] via-[#D4AF37] to-[#D4AF37] rounded-full transition-all duration-1000" :style="{ width: continueLearning.progress + '%' }"></div>
                                        </div>
                                    </div>
                                    
                                    <Link 
                                        :href="route('student.classroom', { course: continueLearning.course_slug })"
                                        class="inline-flex px-10 py-4 bg-[#57572A] text-white text-xs font-bold uppercase tracking-widest rounded-2xl items-center gap-2 shadow-xl hover:shadow-[#57572A]/30 active:scale-95 transition-all w-full md:w-auto justify-center"
                                    >
                                        Continuar curso
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State for new users -->
                    <div v-else-if="stats.active_courses === 0" class="bg-gradient-to-br from-[#57572A]/5 to-[#D4AF37]/5 border-2 border-dashed border-[#57572A]/10 rounded-[2.5rem] p-12 text-center space-y-6">
                        <div class="inline-flex p-6 bg-white rounded-[2rem] shadow-sm mb-2">
                            <BookOpen class="w-12 h-12 text-[#57572A]" />
                        </div>
                        <h2 class="text-3xl font-serif font-bold text-gray-900 italic">Empieza tu camino académico</h2>
                        <p class="text-gray-500 max-w-lg mx-auto font-serif leading-relaxed">Aún no tienes cursos en progreso. Explora nuestro catálogo premium y comienza a especializarte con los mejores expertos de la industria.</p>
                        <Link :href="route('student.explore.courses')" class="inline-flex px-10 py-4 bg-[#57572A] text-white text-xs font-bold uppercase tracking-widest rounded-2xl items-center gap-2 shadow-xl hover:shadow-[#57572A]/30 active:scale-95 transition-all">Ver catálogo de cursos</Link>
                    </div>

                    <!-- 4. CERTIFICADOS (RECIENTES) -->
                    <div v-if="certificates.length > 0" class="space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <h2 class="text-2xl font-serif font-bold text-gray-900 italic">Tus Certificados</h2>
                            <Link :href="route('student.certificates.index')" class="text-[10px] font-bold text-[#57572A] uppercase tracking-widest hover:underline flex items-center gap-1 italic">Ver todos <ArrowRight class="w-3 h-3" /></Link>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="cert in certificates" :key="cert.id" class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-[#D4AF37] transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 group-hover:scale-110 transition-transform">
                                        <Award class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 italic">{{ cert.course_title }}</h4>
                                        <p class="text-[10px] text-gray-400 font-serif italic">Logrado el {{ cert.issue_date }}</p>
                                    </div>
                                </div>
                                <a :href="cert.download_url" target="_blank" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-sm">
                                    <ExternalLink class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- RECOMENDACIONES -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <h2 class="text-2xl font-serif font-bold text-gray-900 italic">Recomendado para ti</h2>
                            <Link :href="route('student.explore.courses')" class="text-[10px] font-bold text-[#57572A] uppercase tracking-widest hover:underline flex items-center gap-1 italic">Explorar catálogo <ArrowRight class="w-3 h-3" /></Link>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <article v-for="rec in recommendations" :key="rec.id" 
                                class="bg-gray-50 rounded-[2rem] border border-gray-100 p-6 flex flex-col group hover:bg-white hover:shadow-xl transition-all duration-500"
                            >
                                <div class="relative h-32 rounded-2xl overflow-hidden mb-4">
                                    <img :src="rec.image" :alt="rec.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-90 group-hover:opacity-100" />
                                </div>
                                <p class="text-[9px] font-bold text-[#D4AF37] uppercase tracking-widest mb-1 italic">{{ rec.category?.name }}</p>
                                <h4 class="text-sm font-serif font-bold text-gray-900 mb-4 line-clamp-2 leading-snug group-hover:text-[#57572A] transition-colors italic">{{ rec.title }}</h4>
                                <Link :href="route('cursos.show', { slug: rec.slug, dashboard: true })" class="mt-auto text-[9px] font-bold uppercase tracking-widest text-[#57572A] opacity-60 group-hover:opacity-100 flex items-center gap-1 underline underline-offset-4 decoration-[#D4AF37] transition-all">Más información <ArrowRight class="w-2.5 h-2.5" /></Link>
                            </article>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Personal Stats / Highlights -->
                <aside class="space-y-8">
                    <!-- Academic Performance -->
                    <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                        <div class="relative z-10 flex flex-col h-full">
                            <header class="flex items-center justify-between mb-8">
                                <span class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/10"><BarChart3 class="w-6 h-6 text-[#D4AF37]" /></span>
                                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[9px] font-bold uppercase tracking-[0.2em] rounded-full border border-emerald-500/20 flex items-center gap-1.5"><TrendingUp class="w-3 h-3 text-emerald-400" /> Rendimiento Pro</span>
                            </header>
                            
                            <h3 class="text-2xl font-serif font-bold mb-2">Desempeño Académico</h3>
                            <p class="text-white/40 font-serif text-xs italic mb-8">Estado actual de tus evaluaciones y logros obtenidos.</p>
                            
                            <div class="flex items-center gap-6 mb-10">
                                <div class="text-center">
                                    <p class="text-4xl font-serif font-bold italic text-white/95">{{ stats.average_score > 0 ? stats.average_score : '--' }}</p>
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/30 italic">Puntaje Med.</p>
                                </div>
                                <div class="w-[1px] h-10 bg-white/10"></div>
                                <div class="text-center text-white/95">
                                    <p class="text-4xl font-serif font-bold italic">{{ stats.certificate_count < 10 ? '0' + stats.certificate_count : stats.certificate_count }}</p>
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/40 italic">Diplomas</p>
                                </div>
                            </div>
                            
                            <Link :href="route('student.certificates.index')" class="mt-auto w-full py-4 bg-white text-gray-900 text-xs text-center font-bold uppercase tracking-[0.2em] rounded-2xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-xl active:scale-95 group-hover:shadow-white/5">Ver certificados</Link>
                        </div>
                        <!-- Abstract background -->
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-[100px]"></div>
                        <div class="absolute -left-10 -top-10 w-48 h-48 bg-[#D4AF37]/5 rounded-full blur-[80px]"></div>
                    </div>

                    <!-- Announcements or next step -->
                    <div class="bg-[#FAFAF5] rounded-[2.5rem] border border-dashed border-[#C9C7B8] p-8 text-center group cursor-pointer hover:border-[#57572A] transition-colors bg-[url('https://www.transparenttextures.com/patterns/pinstriped-suit.png')]">
                        <div class="inline-flex p-4 bg-white rounded-[2rem] shadow-sm mb-4 border border-gray-100 group-hover:scale-110 transition-transform">
                             <Award class="w-8 h-8 text-[#D4AF37]" />
                        </div>
                        <h4 class="text-lg font-serif font-bold text-gray-900 italic">Especialízate más</h4>
                        <p class="text-xs text-gray-500 font-serif italic mb-6 leading-relaxed">¿Ya viste las nuevas Masterclasses de este mes? Tenemos temas de vanguardia.</p>
                        <Link :href="route('student.explore.masterclasses')" class="px-6 py-2.5 bg-transparent border border-gray-200 text-gray-900 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-white hover:border-gray-100 transition-all">Ver Masterclasses</Link>
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
