<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { 
    ChevronLeft, ChevronRight, Menu, MessageSquare, 
    Download, ExternalLink, Play, Clock, 
    Send, Users, X, CheckCircle2, ChevronDown,
    ArrowRight, HandIcon, Flag, ListVideo,
    Heart, Reply, Trash2, Edit2, Trophy, AlertCircle,
    LayoutGrid, BookOpen, Calendar, ClipboardCheck, Award
} from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

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

import { router, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    prevLessonId?: number;
    nextLessonId?: number;
    allLessonsCount: number;
    currentLessonIndex: number;
    completedLessons: number[];
    allLessonsCompleted: boolean;
    comments: any[];
}>();

const { props: pageProps } = usePage() as any;
const currentUser = computed(() => pageProps.auth.user);

// ─── PROGRESS STATE (persistent via localStorage) ────────────────────────────
// Key: "iie_progress_{courseId}_{userId}"
// This survives component recreation, SPA navigation, and server timing issues.
const progressKey = computed(() => 
    `iie_progress_${props.course.id}_${pageProps.auth?.user?.id ?? 'guest'}`
);

function loadFromStorage(): number[] {
    try {
        const raw = localStorage.getItem(progressKey.value);
        if (!raw) return [];
        return JSON.parse(raw) as number[];
    } catch { return []; }
}

function saveToStorage(ids: number[]): void {
    try { localStorage.setItem(progressKey.value, JSON.stringify(ids)); } catch {}
}

// Initialize: server data + localStorage (merge both, localStorage can be ahead)
function buildInitialCompleted(): number[] {
    const fromServer = props.completedLessons.map((id: any) => Number(id));
    const fromStorage = loadFromStorage();
    const merged = [...fromServer];
    fromStorage.forEach(id => { if (!merged.includes(id)) merged.push(id); });
    return merged;
}

const localCompleted = ref<number[]>(buildInitialCompleted());
const localAllCompleted = ref(
    props.allLessonsCompleted || localCompleted.value.length >= props.allLessonsCount
);

// When server sends new completedLessons (after SPA nav), MERGE with our localStorage state
// We NEVER replace — server data can be stale if POST hasn't finished yet
watch(() => props.completedLessons, (serverCompleted) => {
    const current = loadFromStorage();
    const serverIds = serverCompleted.map((id: any) => Number(id));
    serverIds.forEach(id => { if (!current.includes(id)) current.push(id); });
    // Also keep anything already in localCompleted (optimistic additions this session)
    localCompleted.value.forEach(id => { if (!current.includes(id)) current.push(id); });
    localCompleted.value = current;
    saveToStorage(current);
    if (current.length >= props.allLessonsCount && props.allLessonsCount > 0) {
        localAllCompleted.value = true;
    }
}, { deep: true });

watch(() => props.allLessonsCompleted, (newVal) => {
    if (newVal === true) localAllCompleted.value = true;
});

async function completeLesson() {
    if (!props.currentLesson) return;
    const lessonId = props.currentLesson.id;

    if (!localCompleted.value.includes(lessonId)) {
        // 1. Update UI immediately (optimistic)
        localCompleted.value.push(lessonId);
        // 2. Persist to localStorage immediately (survives navigation)
        saveToStorage(localCompleted.value);
        // 3. Check if all done
        if (localCompleted.value.length >= props.allLessonsCount) {
            localAllCompleted.value = true;
        }
        // 4. Send to server via Inertia (Ensures state synchronization across views)
        router.post(route('student.classroom.progress'), { 
            lesson_id: lessonId 
        }, {
            preserveScroll: true,
            onSuccess: () => {
                 console.log('[iieEdu] Progress synced with server');
            }
        });
    }
}
// ─────────────────────────────────────────────────────────────────────────────
const handleMaterialClick = (mat: any) => {
    try {
        if (mat.external_url) {
            window.open(mat.external_url, '_blank', 'noopener,noreferrer');
            return;
        }

        if (mat.file_path) {
            let path = mat.file_path.replace(/^public\//, '');
            // Evitar doble /storage/
            if (!path.startsWith('/storage/') && !path.startsWith('storage/')) {
                path = '/storage/' + path;
            } else if (path.startsWith('storage/')) {
                path = '/' + path;
            }
            
            window.open(path, '_blank', 'noopener,noreferrer');
        }
    } catch (e) {
        console.error('[iieEdu] Error downloading material:', e);
    }
};
// ─────────────────────────────────────────────────────────────────────────────



import ExamModal from '@/components/student/ExamModal.vue';

const breadcrumbs = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/student/courses' },
    { title: props.course.title, href: route('student.classroom', { course: props.course.slug }) },
]);

const activeSidebarTab = ref<'curriculum' | 'chat' | 'comments'>('curriculum');
const mobileSidebarOpen = ref(false);
const now = ref(new Date());
setInterval(() => {
    now.value = new Date();
}, 1000);

// Helper to parse Laravel dates safely regardless of timezone suffixes
const parseSafeDate = (dateStr: string) => {
    if (!dateStr) return null;
    // We strip Z and T to force the browser to treat it as LOCAL wall-clock time
    // This fixed the "5 hour shift" issue in Peru/Colombia
    let normalized = dateStr.replace('Z', '').replace('z', '').replace('T', ' ').substring(0, 19);
    const d = new Date(normalized);
    return isNaN(d.getTime()) ? null : d;
};

const lessonState = computed(() => {
    if (props.currentLesson?.video_url) return 'recorded';
    
    if (props.currentLesson?.content_type === 'live' || props.currentLesson?.start_time) {
        const startDate = parseSafeDate(props.currentLesson?.start_time || '');
        if (!startDate) return 'recorded';

        const startTime = startDate.getTime();
        const endDate = parseSafeDate(props.currentLesson?.end_time || '');
        
        // If no end time, assume 3 hours duration for the live session window
        const endTime = endDate ? endDate.getTime() : startTime + (3 * 60 * 60 * 1000); 
        
        const currentTime = now.value.getTime();
        
        if (currentTime < startTime) return 'scheduled';
        if (currentTime >= startTime && currentTime <= endTime) return 'live';
        return 'processing';
    }
    
    return 'recorded';
});

const isLiveLegacy = computed(() => lessonState.value === 'live' || lessonState.value === 'scheduled');

const videoId = computed(() => {
    if (!props.currentLesson?.video_url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = props.currentLesson.video_url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
});

// UI State
const viewingExam = ref(false);
const activeTab = ref<'content' | 'resources'>('content');

const handleOpenExam = () => {
    if (!localAllCompleted.value) {
        alert('Debes completar todas las lecciones del curso para poder rendir la evaluación final.');
        return;
    }
    viewingExam.value = true;
    mobileSidebarOpen.value = false;
};

// Comments handling
const newComment = ref('');
const replyingTo = ref<any>(null);
const editingComment = ref<any>(null);

function postComment() {
    if (!newComment.value.trim() || !props.currentLesson) return;
    
    router.post(route('student.comments.store', { lesson: props.currentLesson.id }), {
        content: newComment.value,
        parent_id: replyingTo.value?.id
    }, {
        onSuccess: () => {
            newComment.value = '';
            replyingTo.value = null;
        }
    });
}

function toggleLike(commentId: number) {
    router.post(route('student.comments.like', { comment: commentId }), {}, {
        preserveScroll: true
    });
}

function deleteComment(commentId: number) {
    if (confirm('¿Estás seguro de eliminar este comentario?')) {
        router.delete(route('student.comments.destroy', { comment: commentId }), {
            preserveScroll: true
        });
    }
}

function updateComment() {
    if (!editingComment.value?.content.trim()) return;
    router.put(route('student.comments.update', { comment: editingComment.value.id }), {
        content: editingComment.value.content
    }, {
        onSuccess: () => {
            editingComment.value = null;
        }
    });
}

const timeSince = (dateString: string) => {
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);
    
    let interval = seconds / 31536000;
    if (interval > 1) return 'hace ' + Math.floor(interval) + ' años';
    interval = seconds / 2592000;
    if (interval > 1) return 'hace ' + Math.floor(interval) + ' meses';
    interval = seconds / 86400;
    if (interval > 1) return 'hace ' + Math.floor(interval) + ' días';
    interval = seconds / 3600;
    if (interval > 1) return 'hace ' + Math.floor(interval) + ' horas';
    interval = seconds / 60;
    if (interval > 1) return 'hace ' + Math.floor(interval) + ' min';
    return 'hace un momento';
};


// Countdown for Live Classes
const countdown = ref('--:--:--');
let timerInterval: any = null;

function startCountdown() {
    if (timerInterval) clearInterval(timerInterval);
    
    if (lessonState.value === 'scheduled' && props.currentLesson?.start_time) {
        const startDate = parseSafeDate(props.currentLesson.start_time);
        if (!startDate) return;
        
        const target = startDate.getTime();
        
        const update = () => {
            const currentTime = new Date().getTime();
            const diff = target - currentTime;
            
            if (diff <= 0) {
                countdown.value = "ENTRANDO...";
                if (timerInterval) clearInterval(timerInterval);
                // Trigger a small delay and maybe a refresh or just let the computed property switch the view
                return;
            }
            
            const hours = Math.floor(diff / (1000 * 60 * 60)).toString().padStart(2, '0');
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
            const seconds = Math.floor((diff % (1000 * 60)) / 1000).toString().padStart(2, '0');
            countdown.value = `${hours}:${minutes}:${seconds}`;
        };
        
        update();
        timerInterval = setInterval(update, 1000);
    }
}

watch(() => props.currentLesson?.id, () => {
    startCountdown();
}, { immediate: true });

// CRITICAL FIX: When navigating between lessons in Inertia SPA,
// the server sends a fresh list of completed lessons for the new lesson's page.
// We must MERGE server data with our local state, NOT replace it.
// Otherwise, lessons marked as complete in this session would disappear.
watch(() => props.completedLessons, (serverCompleted) => {
    // Find any IDs the server knows about that we don't have locally yet
    const merged = [...localCompleted.value];
    serverCompleted.forEach((id: number) => {
        if (!merged.includes(id)) {
            merged.push(id);
        }
    });
    localCompleted.value = merged;
    // Recalculate allCompleted with merged data
    if (merged.length >= props.allLessonsCount && props.allLessonsCount > 0) {
        localAllCompleted.value = true;
    }
}, { deep: true });

watch(() => props.allLessonsCompleted, (newVal) => {
    // Only update to TRUE from the server, never back to false
    // (local state can be ahead of server)
    if (newVal === true) localAllCompleted.value = true;
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

onMounted(() => {
    // 1. Sync missing progress from localStorage to server
    const missingOnServer = localCompleted.value.filter(
        id => !props.completedLessons.map(Number).includes(Number(id))
    );

    if (missingOnServer.length > 0) {
        console.log('Synchronizing missing progress to server...', missingOnServer);
        axios.post(route('student.classroom.progress'), {
            lesson_ids: missingOnServer
        }).then(response => {
           console.log('Progress synchronized successfully');
        }).catch(err => {
           console.error('Failed to sync progress:', err);
        });
    }

    // 2. Load Plyr
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
    document.head.appendChild(link);

    const script = document.createElement('script');
    script.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
    script.onload = () => {
        const players = document.querySelectorAll('.js-plyr');
        players.forEach(p => {
            const player = new (window as any).Plyr(p, {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                tooltips: { controls: true, seek: true },
                youtube: { 
                    noCookie: false, 
                    rel: 0, 
                    showinfo: 0, 
                    iv_load_policy: 3, 
                    modestbranding: 1,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
                    enablejsapi: 1,
                    origin: window.location.origin
                }
            });

            if (props.currentLesson) {
                player.on('timeupdate', (event: any) => {
                    const instance = event.detail.plyr;
                    if (!instance.duration) return;
                    
                    const percentage = instance.currentTime / instance.duration;
                    if (percentage >= 0.8) {
                        completeLesson();
                    }
                });
            }
        });
    };
    document.head.appendChild(script);

    startCountdown();
});

</script>

<template>
    <Head :title="`${currentLesson?.title || 'Aula Virtual'} - ${course.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-[calc(100svh-4rem)] bg-background text-on-background selection:bg-primary/20 selection:text-primary flex flex-col overflow-hidden">
            
            <!-- Navbar: Institutional Header -->
            <header class="h-14 md:h-20 shrink-0 bg-white border-b border-outline-variant/20 flex items-center px-4 md:px-10 justify-between relative z-40 shadow-sm">
            <div class="flex items-center gap-6">
                <Link :href="route('student.courses.index')" class="p-3 bg-background hover:bg-surface-container-highest rounded-[1.25rem] border border-outline-variant/20 transition-all text-primary group shadow-inner">
                    <ChevronLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
                </Link>
                <div class="flex flex-col">
                    <p class="text-[10px] font-black text-primary/60 uppercase tracking-[0.25em] leading-none mb-2 italic">
                        Módulo {{ currentLessonIndex }} de {{ allLessonsCount }} • {{ course.title }}
                    </p>
                    <h1 class="text-sm md:text-base font-serif font-bold text-on-background truncate max-w-[180px] sm:max-w-xs md:max-w-md italic">{{ currentLesson?.title }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center bg-background p-1.5 rounded-[1.25rem] border border-outline-variant/20 shadow-inner">
                    <Link 
                        v-if="prevLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: prevLessonId })"
                        class="px-5 py-2.5 hover:bg-white hover:shadow-sm rounded-xl transition-all flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-on-surface-variant hover:text-primary"
                    >
                        <ChevronLeft class="w-4 h-4" /> <span class="hidden lg:inline">Anterior</span>
                    </Link>
                    
                    <button 
                        @click="activeSidebarTab = activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum'"
                        class="px-6 py-2.5 bg-primary/5 hover:bg-primary/10 text-primary rounded-xl transition-all flex items-center gap-3 text-[11px] font-black uppercase tracking-widest border border-primary/10 mx-1"
                    >
                        <ListVideo v-if="activeSidebarTab === 'chat' || activeSidebarTab === 'comments'" class="w-4 h-4" />
                        <MessageSquare v-else class="w-4 h-4" />
                        <span class="hidden lg:inline">{{ activeSidebarTab === 'curriculum' ? 'Foro / Chat' : 'Contenido' }}</span>
                    </button>

                    <Link 
                        v-if="nextLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: nextLessonId })"
                        class="px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-on-background transition-all flex items-center gap-3 text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary/20"
                    >
                        <span class="hidden lg:inline">Siguiente</span> <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>
                
                <!-- Mobile: open sidebar drawer -->
                 <button @click="mobileSidebarOpen = true" class="sm:hidden p-2.5 bg-primary text-white rounded-xl shadow-lg">
                     <ListVideo class="w-4 h-4" v-if="activeSidebarTab === 'chat'" />
                     <MessageSquare class="w-4 h-4" v-else />
                 </button>
            </div>
        </header>

        <main class="flex-1 flex flex-col lg:flex-row overflow-hidden bg-background relative">
            
            <!-- Main Content Area: Video and Summary -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-white relative rounded-br-[4rem] shadow-2xl">
                
                <!-- Section: Exam Modal Trigger Case -->
                <div v-if="viewingExam" class="min-h-full flex flex-col items-center justify-center p-4 md:p-8 lg:p-24 bg-background animate-in fade-in duration-700">
                     <div class="max-w-4xl w-full p-6 md:p-16 bg-white rounded-2xl md:rounded-[4rem] border border-outline-variant/20 text-center shadow-[0_40px_100px_rgba(87,87,42,0.1)] relative overflow-hidden">
                          <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent pointer-events-none opacity-40"></div>
                          
                          <div class="relative z-10 space-y-6 md:space-y-10">
                               <div class="inline-flex p-5 md:p-8 rounded-2xl md:rounded-[2.5rem] border shadow-inner" :class="localAllCompleted ? 'bg-primary/5 border-primary/20' : 'bg-orange-500/5 border-orange-500/10'">
                                  <Trophy v-if="localAllCompleted" class="w-12 h-12 md:w-20 md:h-20 text-[#D4AF37]" />
                                  <AlertCircle v-else class="w-12 h-12 md:w-20 md:h-20 text-orange-400" />
                              </div>

                              <div class="space-y-3 md:space-y-6">
                                 <h2 class="text-2xl md:text-4xl lg:text-6xl font-serif font-bold italic text-on-background">Certificación Final</h2>
                                 <p v-if="localAllCompleted" class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                                    Excelencia académica alcanzada. Has completado exitosamente todos los módulos curriculares. Ahora puedes proceder con la evaluación final para acreditar tus conocimientos.
                                 </p>
                                 <p v-else class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                                    Aún no cumples con el 100% de la visualización del currículo académico. Por favor, asegúrate de ver todas las lecciones antes de rendir el examen.
                                 </p>
                              </div>
                              
                              <div v-if="!localAllCompleted" class="pt-4 md:pt-10">
                                  <div class="px-5 py-4 md:px-10 md:py-6 bg-background rounded-xl md:rounded-[2rem] border border-outline-variant/20 shadow-inner inline-block">
                                      <p class="text-[9px] md:text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] md:tracking-[0.3em] mb-2 md:mb-3">Progreso del programa</p>
                                      <div class="flex items-center gap-4 md:gap-6">
                                          <div class="text-xl md:text-3xl font-serif font-bold italic text-primary tabular-nums">{{ localCompleted.length }} / {{ allLessonsCount }}</div>
                                          <div class="w-24 md:w-48 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                                              <div class="h-full bg-[#D4AF37] rounded-full" :style="{ width: `${(localCompleted.length / allLessonsCount) * 100}%` }"></div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              
                              <div class="pt-4 md:pt-8">
                                <Link v-if="localAllCompleted" :href="route('student.exams.index')" class="inline-flex px-8 py-4 md:px-16 md:py-6 bg-primary text-white font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl hover:bg-on-background hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-primary/20 group">
                                    Iniciar Evaluación
                                    <ArrowRight class="w-4 h-4 md:w-5 md:h-5 ml-3 md:ml-4 group-hover:translate-x-2 transition-transform" />
                                </Link>
                                <button v-else disabled class="px-8 py-4 md:px-16 md:py-6 bg-outline-variant/20 text-outline-variant font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl cursor-not-allowed">
                                    Evaluación Bloqueada
                                </button>
                              </div>
                          </div>
                     </div>
                </div>

                <template v-else>
                    <!-- Video Section: The Cinematic Stage -->
                <div class="w-full bg-on-background relative aspect-video shadow-2xl overflow-hidden md:rounded-br-[4rem]">
                    <!-- RECORDED: Show Video Player -->
                    <template v-if="lessonState === 'recorded'">
                        <div v-if="videoId" class="w-full h-full relative">
                            <div class="js-plyr w-full h-full" :data-plyr-provider="'youtube'" :data-plyr-embed-id="videoId"></div>
                        </div>
                        <div v-else class="w-full h-full flex flex-col items-center justify-center p-12 text-center bg-gradient-to-br from-on-background to-[#2D302B]">
                            <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-8 border border-white/10">
                                <Play class="w-10 h-10 text-white/20" />
                            </div>
                            <h2 class="text-3xl font-serif font-bold text-white/50 mb-3 italic">Material en Aula</h2>
                            <p class="text-outline-variant/40 italic font-serif max-w-sm">{{ currentLesson?.description || 'Esta sesión está siendo preparada por el equipo docente.' }}</p>
                        </div>
                    </template>

                    <!-- SCHEDULED & LIVE -->
                    <template v-else-if="lessonState === 'scheduled' || lessonState === 'live'">
                         <div class="absolute inset-0 bg-gradient-to-br from-[#1A1D1A] to-[#2D302B] flex flex-col items-center justify-center p-10 text-center">
                            
                            <div class="relative z-10 w-full max-w-4xl space-y-12">
                                <div class="inline-flex items-center gap-3 px-6 py-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-full shadow-2xl">
                                    <div class="w-2 h-2 rounded-full" :class="lessonState === 'live' ? 'bg-red-500 animate-pulse' : 'bg-[#D4AF37]'"></div>
                                    <span class="text-[10px] font-black text-white/80 uppercase tracking-[0.3em]">{{ lessonState === 'live' ? 'Sesión en Vivo Interactiva' : 'Programada para hoy' }}</span>
                                </div>
                                
                                <h2 class="text-4xl md:text-7xl font-serif font-bold text-white italic leading-tight tracking-tight">{{ currentLesson?.title }}</h2>

                                <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[4rem] p-12 inline-block shadow-2xl mx-auto">
                                    <div v-if="lessonState === 'scheduled'" class="text-8xl font-serif font-bold text-[#D4AF37] tracking-tighter mb-6 italic tabular-nums drop-shadow-[0_10px_30px_rgba(212,175,55,0.2)]">{{ countdown }}</div>
                                    <div v-else class="text-8xl font-serif font-bold text-emerald-400 tracking-tighter mb-6 italic tabular-nums animate-pulse">STREAMING</div>
                                    
                                    <p class="text-[11px] font-black text-outline-variant/60 uppercase tracking-[0.4em] mb-12 italic border-t border-white/5 pt-6">Fecha Institucional: {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'Pendiente' }}</p>
                                    
                                    <div v-if="lessonState === 'live'">
                                        <a 
                                            :href="currentLesson?.live_link" 
                                            target="_blank" 
                                            @click="completeLesson"
                                            class="px-16 py-6 bg-white text-on-background font-black text-[12px] uppercase tracking-[0.3em] rounded-2xl hover:bg-[#D4AF37] hover:text-white transition-all transform hover:-translate-y-2 flex items-center gap-4 shadow-2xl shadow-black/40 group"
                                        >
                                            <ExternalLink class="w-5 h-5" /> Entrar al Aula Privada
                                        </a>
                                    </div>
                                    <div v-else class="px-16 py-6 border border-white/10 text-white/40 font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl bg-white/5">
                                        Acceso en Espera
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- PROCESSING -->
                    <template v-else-if="lessonState === 'processing'">
                        <div class="absolute inset-0 bg-[#0F110F] flex flex-col items-center justify-center p-10 text-center">
                            <div class="relative mb-12">
                                <div class="w-28 h-28 bg-primary/10 rounded-full flex items-center justify-center animate-pulse"></div>
                                <Clock class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 text-[#D4AF37] animate-spin-slow" />
                            </div>
                            <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6 italic tracking-tight">Procesado de Grabación</h2>
                            <p class="text-outline-variant/60 font-serif italic text-xl max-w-2xl mx-auto leading-relaxed">
                                La cátedra en vivo ha concluido con éxito. Nuestro equipo de soporte académico está editando la grabación para su alojamiento definitivo. Estará disponible en minutos.
                            </p>
                        </div>
                    </template>
                </div>

                <!-- Mobile Prev/Next Navigation Bar -->
                <div class="sm:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-outline-variant/10 sticky top-0 z-10 shadow-sm">
                    <Link
                        v-if="prevLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: prevLessonId })"
                        class="flex items-center gap-2 px-4 py-2.5 bg-background border border-outline-variant/20 rounded-xl text-[11px] font-black uppercase tracking-widest text-primary active:scale-95 transition-all"
                    >
                        <ChevronLeft class="w-4 h-4" /> Anterior
                    </Link>
                    <span v-else class="px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-outline-variant/30">Anterior</span>

                    <span class="text-[10px] font-black text-on-surface-variant/40 uppercase tracking-widest">{{ currentLessonIndex }}/{{ allLessonsCount }}</span>

                    <Link
                        v-if="nextLessonId"
                        :href="route('student.classroom', { course: course.slug, lesson: nextLessonId })"
                        class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 active:scale-95 transition-all"
                    >
                        Siguiente <ChevronRight class="w-4 h-4" />
                    </Link>
                    <span v-else class="px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-outline-variant/30">Siguiente</span>
                </div>

                <!-- Info Details: The Reading Space -->
                <div class="p-5 md:p-20 space-y-10 md:space-y-20 max-w-6xl mx-auto">
                    <!-- Premium Tabs Navigation -->
                    <div class="flex items-center gap-12 border-b border-outline-variant/20">
                        <button 
                            v-for="tab in ['content', 'resources']" :key="tab"
                            @click="activeTab = tab as any"
                            class="pb-6 text-[11px] font-black uppercase tracking-[0.3em] transition-all relative group"
                            :class="activeTab === tab ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
                        >
                            {{ tab === 'content' ? 'Nota Académica' : 'Documentación' }}
                            <span class="absolute bottom-0 left-0 w-full h-[4px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10"></span>
                            <span v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-[4px] bg-primary rounded-full animate-in zoom-in duration-500"></span>
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div v-show="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-6 duration-700 space-y-12">
                         <div class="space-y-6">
                            <h2 class="text-4xl md:text-5xl font-serif font-bold text-on-background italic leading-tight">{{ currentLesson?.title }}</h2>
                            <div class="relative pl-10 border-l-[3px] border-primary/10">
                                <div class="prose prose-p:text-on-surface-variant prose-p:font-serif prose-p:italic prose-p:text-xl prose-p:leading-loose max-w-none">
                                    <p v-if="currentLesson?.description">{{ currentLesson.description }}</p>
                                    <p v-else class="text-on-surface-variant/40 italic">Descripción en revisión académica.</p>
                                </div>
                            </div>
                         </div>

                         <!-- Interactive Invitation -->
                         <div class="bg-background rounded-[2rem] md:rounded-[3.5rem] p-6 md:p-12 border border-outline-variant/30 flex flex-col md:flex-row items-center justify-between gap-5 md:gap-8 relative overflow-hidden group">
                             <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                             
                             <div class="space-y-3 relative z-10 max-w-2xl">
                                 <h3 class="text-primary font-serif font-bold text-2xl italic tracking-tight">¿Desea profundizar en algún punto?</h3>
                                 <p class="text-on-surface-variant/80 text-lg font-serif italic leading-relaxed">Le invitamos a participar en el foro académico de esta sesión. Sus aportes enriquecen la comunidad estudiantil del Instituto IEE.</p>
                             </div>
                             <div class="relative z-10">
                                 <button @click="activeSidebarTab = 'chat'" class="px-10 py-5 bg-primary text-white rounded-2xl font-black text-[12px] uppercase tracking-[0.25em] shadow-2xl shadow-primary/20 hover:bg-on-background transform hover:-translate-y-2 transition-all active:scale-95 whitespace-nowrap">
                                     Dialogar con el Mentor
                                 </button>
                             </div>
                         </div>
                    </div>

                    <div v-show="activeTab === 'resources'" class="animate-in fade-in slide-in-from-bottom-6 duration-700 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 pb-24 md:pb-32">
                         <template v-if="currentLesson?.materials?.length">
                              <div 
                                v-for="mat in currentLesson.materials" :key="mat.id" 
                                @click="handleMaterialClick(mat)"
                                class="p-8 bg-white border border-outline-variant/20 rounded-[2.5rem] group hover:border-primary/40 hover:shadow-[0_20px_40px_rgba(87,87,42,0.08)] transition-all flex items-center justify-between gap-6 cursor-pointer transform hover:-translate-y-2"
                              >
                                <div class="flex items-center gap-6 min-w-0">
                                    <div class="w-14 h-14 bg-background rounded-[1.25rem] group-hover:bg-primary transition-colors flex items-center justify-center border border-outline-variant/20">
                                        <Download v-if="mat.file_path" class="w-6 h-6 text-primary group-hover:text-white transition-colors" />
                                        <ExternalLink v-else class="w-6 h-6 text-indigo-400 group-hover:text-white transition-colors" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-base font-bold text-on-background truncate mb-1">{{ mat.title }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] text-primary font-black uppercase tracking-[0.2em] px-2 py-0.5 bg-primary/5 rounded">{{ mat.kind }}</span>
                                            <span class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-widest whitespace-nowrap">Recurso Institucional</span>
                                        </div>
                                    </div>
                                </div>
                                <ArrowRight class="w-5 h-5 text-outline-variant group-hover:text-primary group-hover:translate-x-1 transition-all" />
                              </div>
                         </template>
                        <div v-else class="col-span-full py-40 text-center bg-background rounded-[4rem] border border-dashed border-outline-variant/40">
                            <Clock class="w-20 h-20 text-outline-variant/40 mx-auto mb-8 animate-pulse" />
                            <p class="text-on-surface-variant/60 font-serif italic text-2xl max-w-sm mx-auto leading-relaxed">No se han adjuntado recursos documentales para esta sesión técnica.</p>
                        </div>
                    </div>

                </div>
                </template>

                <!-- Custom Gutter -->
                <div class="h-32 lg:h-32"></div>
                <!-- Bottom nav spacer (mobile) -->
                <div class="h-16 lg:hidden"></div>
            </div>

            <!-- Mobile sidebar backdrop -->
            <div
                v-if="mobileSidebarOpen"
                @click="mobileSidebarOpen = false"
                class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 transition-opacity"
            ></div>

            <!-- Interaction Sidebar: fixed drawer on mobile, inline on desktop -->
            <aside
                class="fixed top-0 right-0 h-full z-50 w-[88vw] max-w-sm
                       lg:relative lg:top-auto lg:right-auto lg:h-full lg:w-[460px] lg:z-40 lg:max-w-none
                       bg-white lg:bg-background border-l border-outline-variant/20
                       flex flex-col shadow-2xl lg:shadow-none
                       transition-transform duration-300 ease-out"
                :class="mobileSidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
            >
                
                <template v-if="activeSidebarTab === 'chat'">
                    <header class="h-24 px-8 border-b border-outline-variant/20 flex items-center justify-between bg-white shadow-sm">
                         <div class="flex items-center gap-4">
                             <button @click="mobileSidebarOpen = false" class="lg:hidden p-2 bg-background rounded-xl border border-outline-variant/20 mr-1">
                                 <X class="w-4 h-4 text-on-background" />
                             </button>
                             <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center border border-primary/10">
                                <MessageSquare class="w-6 h-6 text-primary" />
                             </div>
                             <div>
                                <h3 class="text-sm font-black uppercase tracking-[0.15em] text-on-background">Foro Académico</h3>
                                <p class="text-[10px] text-on-surface-variant/40 font-bold">{{ comments.length }} aportes científicos</p>
                             </div>
                         </div>
                         <button @click="activeSidebarTab = 'curriculum'" class="p-3 hover:bg-surface-container-highest rounded-2xl transition-all">
                            <AlertCircle class="w-5 h-5 text-outline-variant" />
                         </button>
                    </header>

                    <div class="flex-1 overflow-y-auto custom-scrollbar bg-background">
                        <div class="p-8 space-y-12">
                            <!-- New Comment / Reply Input -->
                            <div class="bg-white rounded-[2.5rem] border border-outline-variant/20 p-8 shadow-sm transition-all focus-within:shadow-2xl focus-within:shadow-primary/5 relative">
                                 <div v-if="replyingTo" class="mb-4 flex items-center justify-between p-3 bg-primary/5 rounded-xl">
                                     <p class="text-[10px] font-black text-primary italic truncate">En respuesta a: {{ replyingTo.user.name }}</p>
                                     <button @click="replyingTo = null" class="text-primary/40 hover:text-red-500"><X class="w-4 h-4" /></button>
                                 </div>
                                 <textarea 
                                    v-model="newComment"
                                    :placeholder="replyingTo ? 'Escriba su respuesta técnica...' : 'Inicie una consulta académica con sus pares y mentores...'"
                                    class="w-full bg-transparent border-none p-0 text-base placeholder:text-on-surface-variant/30 focus:ring-0 min-h-[120px] resize-none font-serif italic leading-relaxed"
                                 ></textarea>
                                 <div class="flex items-center justify-between pt-6 border-t border-background">
                                     <div class="flex items-center gap-2">
                                         <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></div>
                                         <span class="text-[9px] font-black text-on-surface-variant/30 uppercase tracking-[0.15em]">Espacio de debate</span>
                                     </div>
                                     <button 
                                        @click="postComment"
                                        class="px-8 py-3.5 bg-primary text-white rounded-xl hover:bg-on-background hover:-translate-y-1 transition-all shadow-xl shadow-primary/10 active:scale-95 font-black text-[10px] uppercase tracking-widest flex items-center gap-3"
                                     >
                                        Enviar <Send class="w-3.5 h-3.5" />
                                     </button>
                                 </div>
                            </div>

                            <!-- Comments Listing with Masterclass Aesthetics -->
                            <div class="space-y-8 pb-32">
                                <div v-for="c in comments" :key="c.id" class="space-y-4">
                                    <div class="p-8 bg-white border border-outline-variant/20 rounded-[3rem] space-y-6 hover:border-primary/30 transition-all group shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-14 h-14 rounded-2xl bg-background border border-outline-variant/30 flex items-center justify-center text-lg font-serif font-bold text-primary italic">
                                                    {{ c.user.name.charAt(0) }}
                                                </div>
                                                <div>
                                                    <p class="text-[13px] font-bold text-on-background flex items-center gap-2">
                                                        {{ c.user.name }}
                                                        <CheckCircle2 v-if="c.user.role === 'admin'" class="w-3.5 h-3.5 text-primary" />
                                                    </p>
                                                    <p class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-[0.15em]">{{ c.user.role === 'admin' ? 'Coordinador Académico' : 'Alumno Verificado' }} • {{ timeSince(c.created_at) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                 <button @click="toggleLike(c.id)" class="p-2 transition-transform hover:scale-125" :class="c.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
                                                     <Heart class="w-5 h-5" :fill="c.is_liked ? 'currentColor' : 'none'" />
                                                 </button>
                                                 <span class="text-[9px] font-black text-on-surface-variant/30 -mt-1">{{ c.likes_count }}</span>
                                            </div>
                                        </div>
                                        
                                        <div v-if="editingComment?.id === c.id" class="space-y-4">
                                            <textarea v-model="editingComment.content" class="w-full bg-background rounded-2xl p-4 text-sm font-serif italic border border-primary/20 focus:ring-0 min-h-[100px]"></textarea>
                                            <div class="flex justify-end gap-3 text-[10px] font-black uppercase tracking-widest">
                                                <button @click="editingComment = null" class="text-on-surface-variant/40 hover:text-red-500 transition-colors">Abortar</button>
                                                <button @click="updateComment" class="px-6 py-2.5 bg-primary text-white rounded-xl">Actualizar</button>
                                            </div>
                                        </div>
                                        <p v-else class="text-base text-on-surface-variant leading-[1.8] font-serif italic">{{ c.content }}</p>
                                        
                                        <div v-if="editingComment?.id !== c.id" class="flex items-center justify-between pt-6 border-t border-background">
                                            <div class="flex items-center gap-6">
                                                <button @click="replyingTo = c" class="text-[10px] font-black uppercase tracking-[0.2em] text-primary hover:text-on-background transition-all flex items-center gap-2"><Reply class="w-3.5 h-3.5" /> Opinar</button>
                                                <button v-if="c.user_id === currentUser.id" @click="editingComment = { ...c }" class="text-[10px] font-black uppercase tracking-[0.2em] text-on-surface-variant/40 hover:text-amber-600 transition-all flex items-center gap-2"><Edit2 class="w-3.5 h-3.5" /> Editar</button>
                                            </div>
                                            <button v-if="c.user_id === currentUser.id" @click="deleteComment(c.id)" class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500/30 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 flex items-center gap-2"><Trash2 class="w-3.5 h-3.5" /> Eliminar</button>
                                        </div>
                                    </div>

                                    <!-- Indented Replies -->
                                    <div v-if="c.replies?.length" class="pl-12 space-y-4">
                                        <div v-for="r in c.replies" :key="r.id" class="p-6 bg-white border border-outline-variant/10 rounded-[2.5rem] shadow-sm group/reply relative">
                                            <div class="absolute -left-6 top-1/2 w-6 h-[1px] bg-outline-variant/30"></div>
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-background border border-outline-variant/20 flex items-center justify-center text-sm font-serif font-bold text-primary italic">
                                                        {{ r.user.name.charAt(0) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-[11px] font-bold text-on-background">{{ r.user.name }}</p>
                                                        <p class="text-[8px] text-on-surface-variant/40 font-bold uppercase tracking-widest">{{ timeSince(r.created_at) }}</p>
                                                    </div>
                                                </div>
                                                <button @click="toggleLike(r.id)" class="p-1 transition-all" :class="r.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
                                                     <Heart class="w-4 h-4" :fill="r.is_liked ? 'currentColor' : 'none'" />
                                                </button>
                                            </div>
                                            <p class="text-sm text-on-surface-variant/80 leading-relaxed font-serif italic">{{ r.content }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <!-- Sidebar Tab: Curriculum Meta-Informática -->
                    <header class="h-28 md:h-48 px-6 md:px-10 flex flex-col justify-center bg-white border-b border-outline-variant/20 relative overflow-hidden">
                         <!-- Mobile close button -->
                         <button @click="mobileSidebarOpen = false" class="lg:hidden absolute top-4 left-4 z-20 p-2 bg-background rounded-xl border border-outline-variant/20 shadow-sm">
                             <X class="w-4 h-4 text-on-background" />
                         </button>
                         <div class="absolute inset-x-0 bottom-0 h-1 bg-background">
                             <div class="h-full bg-[#D4AF37] transition-all duration-1000" :style="{ width: `${(localCompleted.length / allLessonsCount) * 100}%` }"></div>
                         </div>
                         
                         <div class="relative z-10 space-y-2">
                             <div class="flex justify-between items-end">
                                 <h2 class="text-2xl font-serif font-bold italic text-on-background">Módulos Académicos</h2>
                                 <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">{{ Math.round((localCompleted.length / allLessonsCount) * 100) }}% Finalizado</span>
                             </div>
                             <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-[0.25em]">Estás en la clase {{ currentLessonIndex }} de {{ allLessonsCount }}</p>
                         </div>
                    </header>

                    <nav class="flex-1 overflow-y-auto custom-scrollbar bg-background p-8 pb-32 space-y-12">
                        <!-- Module Section -->
                        <div v-for="m in course.modules" :key="m.id" class="space-y-6">
                            <h3 class="flex items-center gap-4 text-[11px] font-black text-on-background/60 uppercase tracking-[0.3em] pl-4">
                                <span class="w-2 h-2 rounded-full bg-primary/20"></span>
                                {{ m.title }}
                            </h3>
                            
                            <div class="space-y-4 relative pl-4 border-l border-outline-variant/20 ml-5">
                                <Link 
                                    v-for="(l, i) in m.lessons" :key="l.id"
                                    :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                    class="flex items-start gap-5 transition-all group relative py-2"
                                >
                                    <!-- Status Icon Container -->
                                    <div class="flex-shrink-0 mt-1 relative">
                                        <div class="absolute -left-[1.65rem] top-1/2 -translate-y-1/2 w-4 h-[1px] bg-outline-variant/40"></div>
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-700 overflow-hidden"
                                        :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                            ? 'bg-primary text-white shadow-2xl shadow-primary/30 scale-110' 
                                            : (localCompleted.includes(l.id) 
                                                ? 'bg-primary/5 text-primary border border-primary/20' 
                                                : 'bg-white text-outline-variant border border-outline-variant/30 group-hover:border-primary')">
                                            <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-5 h-5 text-[#D4AF37]" />
                                            <Play v-else-if="currentLesson?.id === l.id" class="w-4 h-4 text-white" />
                                            <span v-else class="text-[10px] font-black italic">{{ i + 1 }}</span>
                                        </div>
                                    </div>

                                    <!-- Lesson Metadata -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[13px] font-bold leading-tight group-hover:text-primary transition-colors" 
                                            :class="{ 'text-primary': currentLesson?.id === l.id, 'text-on-surface-variant/60': localCompleted.includes(l.id) && currentLesson?.id !== l.id, 'text-on-background': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                           {{ l.title }}
                                        </h4>
                                        <div class="flex items-center gap-3 mt-2 text-[9px] font-black uppercase tracking-[0.15em]"
                                             :class="currentLesson?.id === l.id ? 'text-[#D4AF37]' : (localCompleted.includes(l.id) ? 'text-primary/40' : 'text-on-surface-variant/30')">
                                            <template v-if="localCompleted.includes(l.id)">
                                                Finalizado • Reingresar
                                            </template>
                                            <template v-else-if="currentLesson?.id === l.id">
                                                <div class="w-1 h-1 rounded-full bg-[#D4AF37] animate-pulse"></div>
                                                En sesión ahora
                                            </template>
                                            <template v-else>
                                                {{ l.content_type === 'live' ? 'Sesión Interactiva' : 'Cátedra Multimedia' }}
                                            </template>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- One-off Lessons (Módulos Flexibles) -->
                        <div v-if="course.lessons.filter(l => !l.module_id).length" class="space-y-6">
                             <h3 class="flex items-center gap-4 text-[11px] font-black text-on-background/60 uppercase tracking-[0.3em] pl-4">
                                <span class="w-2 h-2 rounded-full bg-primary/20"></span>
                                Sesiones Complementarias
                             </h3>
                             <div class="space-y-4 relative pl-4 border-l border-outline-variant/20 ml-5">
                                <Link 
                                    v-for="(l, i) in course.lessons.filter(l => !l.module_id)" :key="l.id"
                                    :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                    class="flex items-start gap-5 transition-all group relative py-2"
                                >
                                    <div class="flex-shrink-0 mt-1 relative">
                                        <div class="absolute -left-[1.65rem] top-1/2 -translate-y-1/2 w-4 h-[1px] bg-outline-variant/40"></div>
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-700 overflow-hidden"
                                        :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                            ? 'bg-primary text-white shadow-2xl shadow-primary/30 scale-110' 
                                            : (localCompleted.includes(l.id) 
                                                ? 'bg-primary/5 text-primary border border-primary/20' 
                                                : 'bg-white text-outline-variant border border-outline-variant/30 group-hover:border-primary')">
                                            <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-5 h-5 text-[#D4AF37]" />
                                            <Play v-else-if="currentLesson?.id === l.id" class="w-4 h-4 text-white" />
                                            <span v-else class="text-[10px] font-black italic">{{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[13px] font-bold leading-tight group-hover:text-primary transition-colors" 
                                            :class="{ 'text-primary': currentLesson?.id === l.id, 'text-on-surface-variant/60': localCompleted.includes(l.id) && currentLesson?.id !== l.id, 'text-on-background': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                           {{ l.title }}
                                        </h4>
                                        <div class="flex items-center gap-3 mt-2 text-[9px] font-black uppercase tracking-[0.15em]">
                                            {{ l.content_type === 'live' ? 'Transmisión Guardada' : 'Grabado Editorial' }}
                                        </div>
                                    </div>
                                </Link>
                             </div>
                        </div>

                        <!-- Evaluación Block: Sidebar Design -->
                        <div v-if="course.quizzes?.length" class="pt-8">
                             <div class="bg-white rounded-[2.5rem] border border-outline-variant/20 p-8 shadow-sm group hover:border-[#D4AF37]/50 transition-all cursor-pointer relative overflow-hidden" @click="handleOpenExam">
                                 <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#D4AF37]/5 rounded-full blur-2xl group-hover:bg-[#D4AF37]/10 transition-colors"></div>
                                 <div class="relative z-10 space-y-4 text-center">
                                     <div class="w-16 h-16 rounded-[1.25rem] bg-background border border-outline-variant/20 mx-auto flex items-center justify-center transition-all group-hover:scale-110" :class="localAllCompleted ? 'bg-emerald-50 border-emerald-100' : ''">
                                         <Trophy class="w-8 h-8" :class="localAllCompleted ? 'text-[#D4AF37]' : 'text-outline-variant'" />
                                     </div>
                                     <div>
                                         <h4 class="text-[12px] font-black uppercase tracking-[0.2em] text-on-background">Examen de Titulación</h4>
                                         <p class="text-[9px] font-bold uppercase tracking-[0.1em] mt-2 opacity-50" :class="localAllCompleted ? 'text-emerald-600 opacity-100' : 'text-orange-600'">
                                             {{ localAllCompleted ? 'Candidato Apto' : 'Requisito: Ver Clases' }}
                                         </p>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </nav>
                </template>
            </aside>
        </main>
        
        <!-- MODAL: Technical Hijack (Logic maintained) -->
        <ExamModal 
            v-if="course.quizzes?.length"
            :show="viewingExam"
            :quiz="course.quizzes[0]"
            :course-id="course.id"
            @close="viewingExam = false"
        />
        </div>
        <BottomNav active="courses" />

    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
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
    --plyr-menu-background: #1A1C19;
    --plyr-menu-color: #fff;
    --plyr-video-control-background-hover: rgba(87,87,42,0.5);
    border-radius: 0; /* Full cover in mobile, we use md:rounded above if needed */
    height: 100%;
    width: 100%;
    border-bottom: 4px solid #57572A/10;
}

@media (min-width: 768px) {
    :deep(.plyr) {
        border-radius: 0 0 4rem 0;
    }
}

:deep(.plyr__video-wrapper) {
    height: 100% !important;
    padding-bottom: 0 !important;
}

/* Institutional Video Polish: Push away context UI */
:deep(.plyr iframe) {
    pointer-events: none !important;
    transform: scale(1.4) !important;
}

:deep(.plyr__poster) {
    background-size: cover;
}

/* Animations */
.animate-spin-slow {
    animation: spin 8s linear infinite;
}

@keyframes spin {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Transition smoothness */
main, aside, section {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
