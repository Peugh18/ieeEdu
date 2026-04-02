<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ChevronLeft, ChevronRight, Menu, MessageSquare, 
    Download, ExternalLink, Play, Clock, 
    Send, Users, X, CheckCircle2, ChevronDown,
    ArrowRight, HandIcon, Flag, ListVideo
} from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';

interface Material {
    id: number;
    title: string;
    kind: string;
    external_url?: string;
    file_path?: string;
}

interface Lesson {
    id: number;
    title: string;
    description: string;
    content_type: 'video' | 'live' | 'text';
    video_url?: string;
    live_link?: string;
    start_time?: string;
    end_time?: string;
    materials?: Material[];
    module_id?: number;
}

interface Module {
    id: number;
    title: string;
    lessons: Lesson[];
}

interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    modules: Module[];
    lessons: Lesson[];
    quizzes: any[];
    whatsapp_link?: string;
}

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    prevLessonId?: number;
    nextLessonId?: number;
    allLessonsCount: number;
    currentLessonIndex: number;
}>();

const breadcrumbs = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/student/courses' },
    { title: props.course.title, href: route('student.classroom', { course: props.course.slug }) },
]);

const activeSidebarTab = ref<'curriculum' | 'chat'>('curriculum');
const activeTab = ref<'content' | 'resources' | 'exams'>('content');

const isLive = computed(() => props.currentLesson?.content_type === 'live');
const videoId = computed(() => {
    if (!props.currentLesson?.video_url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = props.currentLesson.video_url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
});

// Comments handling (Simplified for MVP but dynamic in feel)
const comments = ref([
    { id: 1, user: 'Asesor Académico', role: 'staff', content: 'Bienvenidos a esta sesión. No olviden descargar los materiales adjuntos en la pestaña de recursos.', time: 'hace 1 hora', likes: 12, is_verified: true },
]);

const newComment = ref('');
function postComment() {
    if (!newComment.value.trim()) return;
    comments.value.unshift({ id: Date.now(), user: 'Tu', role: 'Estudiante', content: newComment.value, time: 'ahora', likes: 0, is_verified: false });
    newComment.value = '';
}

// Countdown for Live Classes
const countdown = ref('--:--:--');
onMounted(() => {
    // Load Plyr
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
    document.head.appendChild(link);

    const script = document.createElement('script');
    script.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
    script.onload = () => {
        const players = document.querySelectorAll('.js-plyr');
        players.forEach(p => {
            new (window as any).Plyr(p, {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                tooltips: { controls: true, seek: true },
                youtube: { 
                    noCookie: true, 
                    rel: 0, 
                    showinfo: 0, 
                    iv_load_policy: 3, 
                    modestbranding: 1,
                    controls: 0,
                    disablekb: 1,
                    fs: 0
                }
            });
        });
    };
    document.head.appendChild(script);

    if (isLive.value && props.currentLesson?.start_time) {
        const target = new Date(props.currentLesson.start_time).getTime();
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const diff = target - now;
            if (diff <= 0) {
                countdown.value = "EN VIVO";
                clearInterval(timer);
                return;
            }
            const hours = Math.floor(diff / (1000 * 60 * 60)).toString().padStart(2, '0');
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
            const seconds = Math.floor((diff % (1000 * 60)) / 1000).toString().padStart(2, '0');
            countdown.value = `${hours}:${minutes}:${seconds}`;
        }, 1000);
    }
});

</script>

<template>
    <Head :title="`${currentLesson?.title || 'Aula Virtual'} - ${course.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-[calc(100svh-4rem)] bg-surface-container-lowest text-on-surface font-sans selection:bg-primary/20 selection:text-primary flex flex-col">
            
            <!-- Navbar: Theme Colors (Olive/Gold) -->
            <header class="h-16 shrink-0 bg-surface-container-low border-b border-outline-variant/30 flex items-center px-4 md:px-8 justify-between relative z-40">
            <div class="flex items-center gap-4">
                <Link :href="route('student.courses.index')" class="p-2 hover:bg-surface-container rounded-xl transition text-on-surface-variant hover:text-primary">
                    <ChevronLeft class="w-5 h-5" />
                </Link>
                <div class="flex flex-col">
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest leading-none mb-1 italic">Clase {{ currentLessonIndex }} de {{ allLessonsCount }} - {{ course.title }}</p>
                    <h1 class="text-sm font-bold text-on-surface truncate max-w-[150px] md:max-w-md">{{ currentLesson?.title }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center bg-surface-container p-1 rounded-2xl border border-outline-variant/20 shadow-sm">
                    <Link 
                        v-if="prevLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: prevLessonId })"
                        class="px-4 py-2 hover:bg-surface-container-high rounded-xl transition flex items-center gap-2 text-xs font-bold text-on-surface-variant hover:text-primary"
                    >
                        <ChevronLeft class="w-4 h-4" /> <span class="hidden lg:inline">Clase anterior</span>
                    </Link>
                    <button 
                        @click="activeSidebarTab = activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum'"
                        class="px-4 py-2 bg-primary/5 hover:bg-primary/10 text-primary rounded-xl transition flex items-center gap-2 text-xs font-bold border border-primary/10"
                    >
                        <ListVideo v-if="activeSidebarTab === 'chat'" class="w-4 h-4" />
                        <MessageSquare v-else class="w-4 h-4" />
                        <span class="hidden lg:inline">{{ activeSidebarTab === 'chat' ? 'Ver clases' : 'Ver foro/chat' }}</span>
                    </button>
                    <Link 
                        v-if="nextLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: nextLessonId })"
                        class="px-5 py-2 bg-primary text-on-primary rounded-xl hover:bg-primary/90 transition flex items-center gap-2 text-xs font-bold shadow-lg shadow-primary/20"
                    >
                        <span class="hidden lg:inline">Siguiente clase</span> <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>
                
                <!-- Mobile Only -->
                 <button @click="activeSidebarTab = activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum'" class="sm:hidden p-2 bg-primary text-on-primary rounded-xl">
                     <ListVideo class="w-5 h-5" v-if="activeSidebarTab === 'chat'" />
                     <MessageSquare class="w-5 h-5" v-else />
                 </button>
            </div>
        </header>

        <main class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            
            <!-- Main Content Area: Video and Summary -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-surface-container-lowest relative">
                
                <!-- Video Section -->
                <div class="w-full bg-black relative group aspect-video">
                    <template v-if="!isLive">
                        <div v-if="videoId" class="w-full h-full relative group">
                            <!-- Premium Elegant Border Wrapper -->
                            <div class="absolute -inset-1 z-0 bg-gradient-to-tr from-primary/20 via-transparent to-primary/20 rounded-[2.5rem] blur-sm"></div>
                            
                            <div class="relative z-10 w-full h-full rounded-[2rem] overflow-hidden bg-black border-4 border-surface-container-high shadow-[0_20px_50px_rgba(87,87,42,0.3)]">
                                <!-- Plyr Container -->
                                <div 
                                    class="js-plyr w-full h-full" 
                                    :data-plyr-provider="'youtube'" 
                                    :data-plyr-embed-id="videoId"
                                ></div>
                            </div>

                            <!-- Branding Mask (Covers the tiny YT logo if it appears) -->
                            <div class="absolute bottom-4 right-4 z-20 w-16 h-8 bg-black/80 backdrop-blur-sm rounded-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-[8px] font-bold text-primary italic uppercase tracking-widest">IEE Player</span>
                            </div>
                        </div>
                        <div v-else class="w-full h-full flex flex-col items-center justify-center p-12 text-center bg-surface-container-high/20">
                            <Play class="w-16 h-16 text-primary/30 mb-6" />
                            <h2 class="text-2xl font-serif font-bold text-on-surface mb-2 italic">Preparando el contenido...</h2>
                            <p class="text-on-surface-variant italic font-serif max-w-sm">{{ currentLesson?.description }}</p>
                        </div>
                    </template>

                    <!-- Live Overlay -->
                    <template v-else>
                         <div class="absolute inset-0 bg-gradient-to-tr from-surface-container-highest via-surface-container-highest/90 to-primary/10 flex flex-col items-center justify-center p-8 text-center">
                            <div class="px-3 py-1 bg-primary text-on-primary rounded-full text-[10px] font-bold uppercase tracking-widest mb-10 animate-pulse">
                                EN VIVO
                            </div>
                            <h2 class="text-4xl md:text-5xl font-serif font-bold text-on-surface mb-6 italic leading-tight">{{ currentLesson?.title }}</h2>
                            <p class="text-on-surface-variant font-serif italic text-lg mb-12">La sesión interactiva está por comenzar</p>

                            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-[3rem] p-10 flex flex-col items-center shadow-2xl">
                                <div class="text-7xl font-serif font-bold text-primary tracking-tighter mb-4 italic tabular-nums">{{ countdown }}</div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.3em] mb-10 italic">Inicia el {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'hoy' }}</p>
                                
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a :href="currentLesson?.live_link" target="_blank" class="px-10 py-5 bg-primary text-on-primary font-bold text-xs uppercase tracking-widest rounded-2xl hover:scale-105 transition-all flex items-center gap-2 shadow-xl shadow-primary/20">
                                        <ExternalLink class="w-4 h-4" /> Entrar a Clase
                                    </a>
                                    <a v-if="course.whatsapp_link" :href="course.whatsapp_link" target="_blank" class="px-10 py-5 bg-[#25D366] text-white font-bold text-xs uppercase tracking-widest rounded-2xl hover:scale-105 transition-all flex items-center gap-2 shadow-xl shadow-emerald-500/20">
                                        <Users class="w-4 h-4" /> Grupo WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Info Details -->
                <div class="p-8 md:p-12 space-y-12 max-w-5xl mx-auto">
                    <!-- Internal Tabs -->
                    <div class="flex items-center gap-8 border-b border-outline-variant/30">
                        <button 
                            v-for="tab in ['content', 'resources']" :key="tab"
                            @click="activeTab = tab as any"
                            class="pb-4 text-xs font-bold uppercase tracking-[0.2em] transition-all relative"
                            :class="activeTab === tab ? 'text-primary' : 'text-on-surface-variant/50 hover:text-on-surface-variant'"
                        >
                            {{ tab === 'content' ? 'Resumen de la Clase' : 'Material de Apoyo' }}
                            <span v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-[3px] bg-primary rounded-full"></span>
                        </button>
                        <button 
                            v-if="currentLessonIndex === allLessonsCount && course.quizzes?.length"
                            @click="activeTab = 'exams'"
                            class="pb-4 text-xs font-bold uppercase tracking-[0.2em] transition-all relative text-emerald-600"
                        >
                             Certificar Conocimientos
                            <span v-if="activeTab === 'exams'" class="absolute bottom-0 left-0 w-full h-[3px] bg-emerald-600 rounded-full"></span>
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div v-show="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 space-y-8">
                         <div class="space-y-4">
                            <h2 class="text-3xl font-serif font-bold text-on-surface italic leading-tight">{{ currentLesson?.title }}</h2>
                            <div class="prose prose-p:text-on-surface-variant prose-p:font-serif prose-p:italic prose-p:leading-relaxed max-w-none">
                                <p v-if="currentLesson?.description">{{ currentLesson.description }}</p>
                                <p v-else class="text-on-surface-variant/40 italic">Para esta clase no se ha proporcionado una descripción detallada, por favor atienda a las instrucciones del docente durante la sesión.</p>
                            </div>
                         </div>

                         <!-- "Profundiza" Banner integrated in main view too for better UX -->
                         <div class="bg-primary/5 rounded-[2.5rem] p-8 border border-primary/10 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
                             <div class="space-y-2 relative z-10">
                                 <h3 class="text-primary font-bold text-lg italic">¿Tienes dudas adicionales?</h3>
                                 <p class="text-on-surface-variant text-sm font-serif italic max-w-md">Utiliza el foro de comentarios a la derecha para dejar tus inquietudes o aportes a la comunidad académica.</p>
                             </div>
                             <div class="flex-shrink-0 relative z-10">
                                 <button @click="activeTab = 'content'" class="px-8 py-4 bg-primary text-on-primary rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/10 hover:scale-105 transition-all">
                                     Preguntar al Docente
                                 </button>
                             </div>
                             <!-- Abstract decoration -->
                             <HandIcon class="absolute -right-8 top-0 w-32 h-32 text-primary/5 -rotate-12" />
                         </div>
                    </div>

                    <div v-show="activeTab === 'resources'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template v-if="currentLesson?.materials?.length">
                             <div v-for="mat in currentLesson.materials" :key="mat.id" class="p-6 bg-surface-container-low border border-outline-variant/30 rounded-3xl group hover:border-primary/40 transition-all flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="p-3 bg-primary/5 rounded-2xl group-hover:bg-primary/10 transition-colors">
                                        <Download v-if="mat.file_path" class="w-5 h-5 text-primary" />
                                        <ExternalLink v-else class="w-5 h-5 text-indigo-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold text-on-surface truncate">{{ mat.title }}</h4>
                                        <p class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest mt-1">{{ mat.kind }}</p>
                                    </div>
                                </div>
                                <a :href="mat.external_url || '/storage/' + mat.file_path" target="_blank" class="p-2 hover:bg-surface-container rounded-xl transition text-on-surface-variant">
                                    <ArrowRight class="w-4 h-4" />
                                </a>
                             </div>
                        </template>
                        <div v-else class="col-span-full py-20 text-center bg-surface-container-low/50 rounded-[3rem] border border-dashed border-outline-variant/30">
                            <Clock class="w-12 h-12 text-on-surface-variant/20 mx-auto mb-4" />
                            <p class="text-on-surface-variant font-serif italic text-lg">No hay archivos adjuntos en esta lección.</p>
                        </div>
                    </div>

                    <!-- Exam View -->
                    <div v-show="activeTab === 'exams'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 p-12 bg-gradient-to-br from-primary/5 to-surface-container-highest rounded-[3rem] border border-primary/10 text-center space-y-8">
                         <div class="inline-flex p-6 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                             <CheckCircle2 class="w-16 h-16 text-emerald-600" />
                         </div>
                         <div class="space-y-4">
                            <h2 class="text-4xl font-serif font-bold italic">Evaluación de Competencias</h2>
                            <p class="text-on-surface-variant italic font-serif text-lg max-w-xl mx-auto">Has completado el currículo modular. Para obtener tu certificación oficial, debes aprobar el examen final correspondiente.</p>
                         </div>
                         <Link :href="route('student.exams.index')" class="inline-flex px-12 py-6 bg-emerald-600 text-white font-bold text-xs uppercase tracking-widest rounded-2xl hover:bg-emerald-700 hover:scale-105 transition-all shadow-2xl shadow-emerald-900/20">
                             Iniciar Evaluación Ahora
                         </Link>
                    </div>
                </div>

                <!-- Footer spacing -->
                <div class="h-24"></div>
            </div>

            <!-- Interaction Sidebar (Comments / Q&A) -->
            <aside class="w-full lg:w-[420px] bg-surface-container-low border-l border-outline-variant/30 flex flex-col h-full z-40">
                <template v-if="activeSidebarTab === 'chat'">
                    <header class="p-6 border-b border-outline-variant/30 flex items-center justify-between">
                         <div class="flex items-center gap-2">
                             <MessageSquare class="w-5 h-5 text-primary" />
                             <span class="text-sm font-bold uppercase tracking-widest text-on-surface">Foro de la Clase</span>
                         </div>
                         <span class="text-[10px] font-bold text-on-surface-variant bg-surface-container-high px-2 py-1 rounded-full">{{ comments.length }} aportes</span>
                    </header>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-8">
                        <!-- New Comment Input -->
                        <div class="relative group">
                             <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 focus-within:border-primary/40 focus-within:ring-4 focus-within:ring-primary/5 transition-all p-5 space-y-4">
                                 <textarea 
                                    v-model="newComment"
                                    placeholder="Escribe tu consulta o aporte académico aquí..."
                                    class="w-full bg-transparent border-none p-0 text-sm placeholder:text-on-surface-variant/30 focus:ring-0 min-h-[100px] resize-none font-serif italic"
                                 ></textarea>
                                 <div class="flex items-center justify-between">
                                     <p class="text-[10px] text-on-surface-variant/40 italic">Sea respetuoso y claro con sus dudas.</p>
                                     <button 
                                        @click="postComment"
                                        class="p-2.5 bg-primary text-on-primary rounded-xl hover:scale-110 active:scale-95 transition-all shadow-lg shadow-primary/10"
                                     >
                                        <Send class="w-4 h-4" />
                                     </button>
                                 </div>
                             </div>
                        </div>

                        <!-- Comments Listing -->
                        <div class="space-y-6 pb-24">
                            <div v-for="c in comments" :key="c.id" class="p-6 bg-surface-container-lowest border border-outline-variant/20 rounded-[2.5rem] space-y-4 hover:shadow-xl hover:shadow-primary/5 transition-all border-l-4 border-l-primary/10">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-primary/10 border border-primary/10 flex items-center justify-center text-[11px] font-bold text-primary italic uppercase">
                                            {{ c.user.charAt(0) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-on-surface flex items-center gap-1.5">
                                                {{ c.user }}
                                                <CheckCircle2 v-if="c.is_verified" class="w-3 h-3 text-primary fill-primary/10" />
                                            </p>
                                            <p class="text-[9px] text-on-surface-variant uppercase tracking-widest">{{ c.role }} · {{ c.time }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center gap-0.5">
                                         <button class="p-1 hover:text-primary transition-colors">
                                             <HandIcon class="w-4 h-4 text-on-surface-variant/30 group-hover:text-primary transition-colors" />
                                         </button>
                                         <span class="text-[10px] font-bold text-on-surface-variant/40">{{ c.likes }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-on-surface-variant leading-relaxed font-serif italic">{{ c.content }}</p>
                                <div class="flex items-center gap-4 text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/40">
                                    <button class="hover:text-primary transition">Responder</button>
                                    <button class="hover:text-red-600 transition">Reportar aporte</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <header class="p-6 border-b border-outline-variant/30 flex flex-col justify-center bg-surface-container-low">
                         <div class="space-y-1">
                             <h2 class="text-xl font-serif font-bold italic text-on-surface">Progreso del curso</h2>
                             <div class="flex items-center gap-2 mt-2">
                                 <span class="text-xs font-bold text-on-surface-variant">{{ Math.round((currentLessonIndex / allLessonsCount) * 100) }}%</span>
                                 <div class="h-1 flex-1 bg-surface-container flex rounded-full overflow-hidden">
                                     <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" :style="{ width: `${(currentLessonIndex / allLessonsCount) * 100}%` }"></div>
                                 </div>
                             </div>
                         </div>
                    </header>
                    <nav class="flex-1 overflow-y-auto custom-scrollbar p-6 pb-24 space-y-8">
                        <div v-for="m in course.modules" :key="m.id" class="space-y-6">
                            <h3 class="text-sm font-bold text-on-surface">{{ m.title }}</h3>
                            <div class="space-y-4 relative before:absolute before:inset-y-0 before:left-4 before:w-0.5 before:bg-outline-variant/30">
                                <Link 
                                    v-for="(l, i) in m.lessons" :key="l.id"
                                    :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                    class="flex items-center gap-4 transition-all group relative z-10"
                                >
                                    <div class="flex-shrink-0 bg-surface-container-low py-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs"
                                        :class="currentLesson?.id === l.id ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-surface-container-highest text-on-surface-variant border border-outline-variant/30'">
                                            {{ i + 1 }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1 p-2 rounded-2xl transition-all flex items-start gap-3" :class="currentLesson?.id === l.id ? 'bg-primary/5 border border-primary/20' : 'hover:bg-surface-container border border-transparent'">
                                        <div class="w-16 h-10 bg-surface-container-high rounded-xl flex items-center justify-center flex-shrink-0 border border-outline-variant/20 mt-1">
                                            <Play class="w-3 h-3 text-on-surface-variant" />
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            <p class="text-sm font-bold leading-tight group-hover:text-primary transition-colors pr-2" :class="{ 'text-primary': currentLesson?.id === l.id }">{{ l.title }}</p>
                                            <p class="text-[9px] uppercase tracking-widest mt-1.5 flex items-center gap-1 font-bold" :class="currentLesson?.id === l.id ? 'text-primary' : 'text-on-surface-variant'">
                                                <CheckCircle2 v-if="currentLesson?.id === l.id" class="w-3 h-3" />
                                                {{ currentLesson?.id === l.id ? 'Viendo ahora' : 'Clase de Video' }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Lessons without modules -->
                        <div v-if="course.lessons.filter(l => !l.module_id).length" class="space-y-6">
                             <h3 class="text-sm font-bold text-on-surface">Módulos Extra</h3>
                             <div class="space-y-4 relative before:absolute before:inset-y-0 before:left-4 before:w-0.5 before:bg-outline-variant/30">
                                <Link 
                                    v-for="(l, i) in course.lessons.filter(l => !l.module_id)" :key="l.id"
                                    :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                    class="flex items-center gap-4 transition-all group relative z-10"
                                >
                                    <div class="flex-shrink-0 bg-surface-container-low py-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs"
                                        :class="currentLesson?.id === l.id ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-surface-container-highest text-on-surface-variant border border-outline-variant/30'">
                                            {{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1 p-2 rounded-2xl transition-all flex items-start gap-3" :class="currentLesson?.id === l.id ? 'bg-primary/5 border border-primary/20' : 'hover:bg-surface-container border border-transparent'">
                                        <div class="w-16 h-10 bg-surface-container-high rounded-xl flex items-center justify-center flex-shrink-0 border border-outline-variant/20 mt-1">
                                            <Play class="w-3 h-3 text-on-surface-variant" />
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            <p class="text-sm font-bold leading-tight group-hover:text-primary transition-colors text-on-surface pr-2" :class="{ 'text-primary': currentLesson?.id === l.id }">{{ l.title }}</p>
                                            <p class="text-[9px] uppercase tracking-widest mt-1.5 flex items-center gap-1 font-bold text-on-surface-variant">
                                                <CheckCircle2 v-if="currentLesson?.id === l.id" class="w-3 h-3" />
                                                {{ currentLesson?.id === l.id ? 'Viendo ahora' : 'Clase de Video' }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                             </div>
                        </div>
                    </nav>
                </template>
            </aside>
        </main>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.2);
}

.prose {
    max-width: none;
}

iframe {
    width: 100%;
    height: 100%;
}

:deep(.plyr) {
    --plyr-color-main: #57572A;
    --plyr-video-background: #000;
    --plyr-menu-background: #57572A;
    --plyr-menu-color: #fff;
    border-radius: 1.5rem;
    height: 100%;
    width: 100%;
}

:deep(.plyr__video-wrapper) {
    height: 100% !important;
    padding-bottom: 0 !important;
}

/* Intercept clicks and hover to YouTube iframe explicitly, and scale it up to push the title/logos out of the visible area */
:deep(.plyr iframe) {
    pointer-events: none !important;
    transform: scale(1.35) !important;
}

/* Hide typical YT elements inside Plyr if they peek through */
:deep(.plyr__poster) {
    background-size: cover;
}
</style>
