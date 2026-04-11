<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { 
    ChevronLeft, ChevronRight, Menu, MessageSquare, 
    Download, ExternalLink, Play, Clock, 
    Send, Users, X, CheckCircle2, ChevronDown,
    ArrowRight, HandIcon, Flag, ListVideo,
    Heart, Reply, Trash2, Edit2, Trophy, AlertCircle
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
                    noCookie: true, 
                    rel: 0, 
                    showinfo: 0, 
                    iv_load_policy: 3, 
                    modestbranding: 1,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
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
                
                <div v-if="viewingExam" class="min-h-full flex flex-col items-center justify-center p-8 lg:p-16 animate-in fade-in duration-500 bg-surface-container-lowest">
                     <div class="max-w-3xl w-full p-12 bg-white rounded-[3rem] border border-outline-variant/20 text-center shadow-[0_20px_60px_rgba(87,87,42,0.05)] relative overflow-hidden">
                          <!-- Decorative gradient bg -->
                          <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-surface-container-highest pointer-events-none opacity-50"></div>
                          
                          <div class="relative z-10">
                              <div class="inline-flex p-6 rounded-full border mb-8" :class="localAllCompleted ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-orange-500/10 border-orange-500/20'">
                                 <CheckCircle2 v-if="localAllCompleted" class="w-16 h-16 text-emerald-600" />
                                 <Clock v-else class="w-16 h-16 text-orange-600" />
                             </div>
                             <div class="space-y-6 mb-10">
                                <h2 class="text-4xl lg:text-5xl font-serif font-bold italic text-on-surface">Evaluación Final</h2>
                                <p v-if="localAllCompleted" class="text-on-surface-variant italic font-serif text-[1.1rem] max-w-xl mx-auto leading-relaxed">Has completado el currículo modular satisfactoriamente. Para obtener tu certificación, debes aprobar el examen final correspondiente.</p>
                                <p v-else class="text-on-surface-variant italic font-serif text-[1.1rem] max-w-xl mx-auto leading-relaxed">Debes completar todas las clases curriculares (al menos visualizar el 80% de cada video) para poder acceder a la evaluación final.</p>
                             </div>
                             
                             <div v-if="!localAllCompleted" class="inline-flex flex-col items-center">
                                 <div class="px-8 py-5 bg-orange-50 rounded-2xl border border-orange-200 shadow-sm animate-in zoom-in-95">
                                     <p class="text-sm font-bold text-orange-800 uppercase tracking-widest">Requisito Pendiente</p>
                                     <p class="text-sm font-serif italic text-orange-700 mt-2">Has completado <span class="font-bold text-lg tabular-nums">{{ localCompleted.length }}</span> de <span class="font-bold text-lg tabular-nums">{{ allLessonsCount }}</span> clases necesarias.</p>
                                 </div>
                                 <button disabled class="mt-8 inline-flex px-12 py-6 bg-surface-container-highest text-on-surface-variant font-bold text-sm uppercase tracking-widest rounded-2xl cursor-not-allowed">
                                     Evaluación Bloqueada
                                 </button>
                             </div>
                             <Link v-else :href="route('student.exams.index')" class="inline-flex px-14 py-6 bg-emerald-600 text-white font-bold text-sm uppercase tracking-widest rounded-2xl hover:bg-emerald-700 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-emerald-900/20 relative group">
                                 Iniciar Evaluación Ahora
                                 <span class="absolute inset-0 rounded-2xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                             </Link>
                          </div>
                     </div>
                </div>

                <template v-else>
                    <!-- Video Section -->
                <div class="w-full bg-black relative group aspect-video">
                    <!-- RECORDED: Show Video Player -->
                    <template v-if="lessonState === 'recorded'">
                        <div v-if="videoId" class="w-full h-full relative group">
                            <div class="absolute -inset-1 z-0 bg-gradient-to-tr from-primary/20 via-transparent to-primary/20 rounded-[2.5rem] blur-sm"></div>
                            <div class="relative z-10 w-full h-full rounded-[2rem] overflow-hidden bg-black border-4 border-surface-container-high shadow-[0_20px_50px_rgba(87,87,42,0.3)]">
                                <div class="js-plyr w-full h-full" :data-plyr-provider="'youtube'" :data-plyr-embed-id="videoId"></div>
                            </div>
                        </div>
                        <div v-else class="w-full h-full flex flex-col items-center justify-center p-12 text-center bg-surface-container-high/20">
                            <Play class="w-16 h-16 text-primary/30 mb-6" />
                            <h2 class="text-2xl font-serif font-bold text-on-surface mb-2 italic text-on-surface/40">Contenido en preparación</h2>
                            <p class="text-on-surface-variant italic font-serif max-w-sm">{{ currentLesson?.description || 'Esta lección será activada próximamente.' }}</p>
                        </div>
                    </template>

                    <!-- SCHEDULED & LIVE -->
                    <template v-else-if="lessonState === 'scheduled' || lessonState === 'live'">
                         <div class="absolute inset-0 bg-gradient-to-tr from-surface-container-highest via-surface-container-highest/90 to-primary/10 flex flex-col items-center justify-center p-8 text-center animate-in fade-in duration-500">
                            <div class="px-3 py-1 bg-primary text-on-primary rounded-full text-[10px] font-bold uppercase tracking-widest mb-10" :class="lessonState === 'live' ? 'animate-pulse' : ''">
                                {{ lessonState === 'live' ? 'EN VIVO' : 'PROGRAMADA' }}
                            </div>
                            <h2 class="text-4xl md:text-5xl font-serif font-bold text-on-surface mb-6 italic leading-tight">{{ currentLesson?.title }}</h2>
                            <p class="text-on-surface-variant font-serif italic text-lg mb-12">{{ lessonState === 'live' ? 'La sesión interactiva está activa' : 'La sesión interactiva está por comenzar' }}</p>

                            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-[3rem] p-10 flex flex-col items-center shadow-2xl">
                                <div v-if="lessonState === 'scheduled'" class="text-7xl font-serif font-bold text-primary tracking-tighter mb-4 italic tabular-nums">{{ countdown }}</div>
                                <div v-else class="text-7xl font-serif font-bold text-emerald-600 tracking-tighter mb-4 italic tabular-nums animate-in zoom-in">STREAMING</div>
                                
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.3em] mb-10 italic">Inicia el {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'hoy' }}</p>
                                
                                <div class="flex flex-col sm:flex-row gap-4" v-if="lessonState === 'live'">
                                    <a 
                                        :href="currentLesson?.live_link" 
                                        target="_blank" 
                                        @click="completeLesson"
                                        class="px-10 py-5 bg-primary text-on-primary font-bold text-xs uppercase tracking-widest rounded-2xl hover:scale-105 transition-all flex items-center gap-2 shadow-xl shadow-primary/20"
                                    >
                                        <ExternalLink class="w-4 h-4" /> Entrar a Clase
                                    </a>
                                </div>
                                <p v-else class="text-sm font-serif italic text-on-surface-variant/60">El acceso se habilitará automáticamente al iniciar</p>
                            </div>
                        </div>
                    </template>

                    <!-- PROCESSING: The Gap -->
                    <template v-else-if="lessonState === 'processing'">
                        <div class="absolute inset-0 bg-gradient-to-tr from-surface-container-high to-surface-container-lowest flex flex-col items-center justify-center p-8 text-center animate-in fade-in duration-500">
                            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-10">
                                <Clock class="w-12 h-12 text-primary animate-spin-slow" />
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-serif font-bold text-on-surface mb-6 italic leading-tight">Procesamiento Académico</h2>
                            <p class="text-on-surface-variant font-serif italic text-xl max-w-xl mx-auto leading-relaxed h-20">
                                La sesión en vivo ha finalizado satisfactoriamente. Nuestro equipo está procesando la grabación académica para tu consulta asíncrona. Estará disponible en breve.
                            </p>
                            
                            <div class="mt-12 flex flex-col sm:flex-row gap-6">
                                <div class="px-6 py-4 bg-white/50 backdrop-blur rounded-2xl border border-primary/20 flex flex-col items-center">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em] mb-2">Finalizó a las</span>
                                    <span class="text-lg font-serif italic text-on-surface">{{ currentLesson?.end_time ? new Date(currentLesson.end_time).toLocaleTimeString() : 'Hace poco' }}</span>
                                </div>
                                <div class="px-6 py-4 bg-white/50 backdrop-blur rounded-2xl border border-primary/20 flex flex-col items-center">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em] mb-2">Material PDF</span>
                                    <span class="text-lg font-serif italic text-on-surface">{{ currentLesson?.materials?.length || 0 }} Archivos listos</span>
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
                                 <button @click="activeSidebarTab = 'chat'" class="px-8 py-4 bg-primary text-on-primary rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/10 hover:scale-105 transition-all active:scale-95">
                                     Preguntar al Docente
                                 </button>
                             </div>
                             <!-- Abstract decoration -->
                             <HandIcon class="absolute -right-8 top-0 w-32 h-32 text-primary/5 -rotate-12" />
                         </div>
                    </div>

                    <div v-show="activeTab === 'resources'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 grid grid-cols-1 md:grid-cols-2 gap-4">
                         <template v-if="currentLesson?.materials?.length">
                              <div 
                                v-for="mat in currentLesson.materials" :key="mat.id" 
                                @click="handleMaterialClick(mat)"
                                class="p-6 bg-surface-container-low border border-outline-variant/30 rounded-3xl group hover:border-primary/40 hover:bg-primary/5 transition-all flex items-center justify-between gap-4 cursor-pointer active:scale-[0.98]"
                              >
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="p-3 bg-primary/5 rounded-2xl group-hover:bg-primary/10 transition-colors">
                                        <Download v-if="mat.file_path" class="w-5 h-5 text-primary" />
                                        <ExternalLink v-else class="w-5 h-5 text-indigo-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold text-on-surface truncate pr-4">{{ mat.title }}</h4>
                                        <p class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest mt-1 opacity-60">{{ mat.kind }}</p>
                                    </div>
                                </div>
                                <div class="p-2 bg-white rounded-xl shadow-sm border border-outline-variant/20 group-hover:bg-primary group-hover:text-on-primary transition-all">
                                    <Download class="w-4 h-4" />
                                </div>
                              </div>
                         </template>
                        <div v-else class="col-span-full py-20 text-center bg-surface-container-low/50 rounded-[3rem] border border-dashed border-outline-variant/30">
                            <Clock class="w-12 h-12 text-on-surface-variant/20 mx-auto mb-4" />
                            <p class="text-on-surface-variant font-serif italic text-lg">No hay archivos adjuntos en esta lección.</p>
                        </div>
                    </div>

                </div>
                </template>

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
                        <!-- New Comment / Reply Input -->
                        <div class="relative group">
                             <div v-if="replyingTo" class="mb-2 flex items-center justify-between px-3 py-2 bg-primary/5 rounded-t-2xl border-x border-t border-primary/20">
                                 <p class="text-[10px] font-bold text-primary italic truncate">Respondiendo a @{{ replyingTo.user.name }}</p>
                                 <button @click="replyingTo = null" class="p-1 hover:bg-primary/10 rounded-full transition-colors"><X class="w-3 h-3 text-primary" /></button>
                             </div>
                             <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 focus-within:border-primary/40 focus-within:ring-4 focus-within:ring-primary/5 transition-all p-5 space-y-4" :class="replyingTo ? 'rounded-t-none' : ''">
                                 <textarea 
                                    v-model="newComment"
                                    :placeholder="replyingTo ? 'Escribe tu respuesta...' : 'Escribe tu consulta o aporte académico aquí...'"
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
                            <div v-for="c in comments" :key="c.id" class="space-y-3">
                                <div class="p-6 bg-surface-container-lowest border border-outline-variant/20 rounded-[2.5rem] space-y-4 hover:shadow-xl hover:shadow-primary/5 transition-all border-l-4 border-l-primary/10 group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-primary/10 border border-primary/10 flex items-center justify-center text-[11px] font-bold text-primary italic uppercase">
                                                {{ c.user.name.charAt(0) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[11px] font-bold text-on-surface flex items-center gap-1.5">
                                                    {{ c.user.name }}
                                                    <CheckCircle2 v-if="c.user.role === 'admin' || c.user.role === 'editor'" class="w-3 h-3 text-primary fill-primary/10" />
                                                </p>
                                                <p class="text-[9px] text-on-surface-variant uppercase tracking-widest">{{ c.user.role === 'admin' ? 'Staff IEE' : 'Estudiante Scholar' }} · {{ timeSince(c.created_at) }} <span v-if="c.is_edited" class="italic">(editado)</span></p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-center gap-0.5">
                                             <button @click="toggleLike(c.id)" class="p-1 transition-colors" :class="c.is_liked ? 'text-red-500' : 'text-on-surface-variant/30 hover:text-red-400'">
                                                 <Heart class="w-4 h-4" :fill="c.is_liked ? 'currentColor' : 'none'" />
                                             </button>
                                             <span class="text-[10px] font-bold text-on-surface-variant/40">{{ c.likes_count }}</span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="editingComment?.id === c.id" class="space-y-3">
                                        <textarea v-model="editingComment.content" class="w-full bg-surface-container-low rounded-xl p-3 text-xs font-serif italic border border-primary/20 focus:ring-0 focus:border-primary/40 min-h-[80px] resize-none"></textarea>
                                        <div class="flex justify-end gap-2 text-[10px] font-bold uppercase tracking-widest">
                                            <button @click="editingComment = null" class="px-4 py-2 text-on-surface-variant">Cancelar</button>
                                            <button @click="updateComment" class="px-4 py-2 bg-primary text-on-primary rounded-lg">Guardar</button>
                                        </div>
                                    </div>
                                    <p v-else class="text-xs text-on-surface-variant leading-relaxed font-serif italic">{{ c.content }}</p>
                                    
                                    <div v-if="editingComment?.id !== c.id" class="flex items-center justify-between text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/40">
                                        <div class="flex items-center gap-4">
                                            <button @click="replyingTo = c" class="hover:text-primary transition flex items-center gap-1"><Reply class="w-3 h-3" /> Responder</button>
                                            <button v-if="c.user_id === currentUser.id" @click="editingComment = { ...c }" class="hover:text-amber-600 transition flex items-center gap-1"><Edit2 class="w-3 h-3" /> Editar</button>
                                        </div>
                                        <button v-if="c.user_id === currentUser.id" @click="deleteComment(c.id)" class="hover:text-red-600 transition opacity-0 group-hover:opacity-100 flex items-center gap-1"><Trash2 class="w-3 h-3" /> Borrar</button>
                                    </div>
                                </div>

                                <!-- Recursive Replies (Simple Level 1 Indentation) -->
                                <div v-if="c.replies?.length" class="pl-8 space-y-3 mt-4">
                                    <div v-for="r in c.replies" :key="r.id" class="p-4 bg-surface-container-low border border-outline-variant/10 rounded-[1.5rem] space-y-3 group/reply relative before:absolute before:-left-4 before:top-1/2 before:w-4 before:h-[2px] before:bg-outline-variant/20">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-xl bg-primary/5 border border-primary/10 flex items-center justify-center text-[9px] font-bold text-primary italic uppercase">
                                                    {{ r.user.name.charAt(0) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-bold text-on-surface flex items-center gap-1">{{ r.user.name }}</p>
                                                    <p class="text-[8px] text-on-surface-variant uppercase tracking-tight">{{ timeSince(r.created_at) }} <span v-if="r.is_edited">(editado)</span></p>
                                                </div>
                                            </div>
                                            <button @click="toggleLike(r.id)" class="p-1 transition-colors" :class="r.is_liked ? 'text-red-500' : 'text-on-surface-variant/20 hover:text-red-400'">
                                                 <Heart class="w-3.5 h-3.5" :fill="r.is_liked ? 'currentColor' : 'none'" />
                                            </button>
                                        </div>

                                        <div v-if="editingComment?.id === r.id" class="space-y-3">
                                            <textarea v-model="editingComment.content" class="w-full bg-surface-container-lowest rounded-xl p-3 text-xs font-serif italic border border-primary/20 focus:ring-0 min-h-[60px] resize-none"></textarea>
                                            <div class="flex justify-end gap-2 text-[10px] font-bold uppercase tracking-widest">
                                                <button @click="editingComment = null" class="px-3 py-1.5 text-on-surface-variant">Cancelar</button>
                                                <button @click="updateComment" class="px-3 py-1.5 bg-primary text-on-primary rounded-lg">Guardar</button>
                                            </div>
                                        </div>
                                        <p v-else class="text-xs text-on-surface-variant/80 leading-relaxed font-serif italic">{{ r.content }}</p>

                                        <div v-if="editingComment?.id !== r.id && r.user_id === currentUser.id" class="flex justify-end gap-3 text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/30 opacity-0 group-hover/reply:opacity-100 transition-opacity">
                                            <button @click="editingComment = { ...r }" class="hover:text-amber-600 transition flex items-center gap-1"><Edit2 class="w-3 h-3" /> Editar</button>
                                            <button @click="deleteComment(r.id)" class="hover:text-red-600 transition flex items-center gap-1"><Trash2 class="w-3 h-3" /> Borrar</button>
                                        </div>
                                    </div>
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
                                 <span class="text-xs font-bold text-on-surface-variant text-right tabular-nums min-w-[2.5rem]">{{ Math.round((localCompleted.length / allLessonsCount) * 100) }}%</span>
                                 <div class="h-1 flex-1 bg-surface-container flex rounded-full overflow-hidden">
                                     <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" :style="{ width: `${(localCompleted.length / allLessonsCount) * 100}%` }"></div>
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
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] transition-all duration-500"
                                        :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                            ? 'bg-primary text-on-primary shadow-lg shadow-primary/20 scale-110 z-10' 
                                            : (localCompleted.includes(l.id) 
                                                ? 'bg-emerald-500 text-white border border-emerald-400/20' 
                                                : 'bg-surface-container-highest text-on-surface-variant border border-outline-variant/30')">
                                            <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-4 h-4" />
                                            <span v-else>{{ i + 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1 p-2.5 rounded-2xl transition-all flex items-start gap-3" 
                                        :class="currentLesson?.id === l.id 
                                            ? 'bg-white border border-primary/30 shadow-sm' 
                                            : (localCompleted.includes(l.id) ? 'bg-emerald-50/30' : 'hover:bg-surface-container border border-transparent')">
                                        <div class="w-14 h-9 rounded-xl flex items-center justify-center flex-shrink-0 border transition-colors mt-0.5"
                                            :class="currentLesson?.id === l.id ? 'bg-primary/10 border-primary/20' : (localCompleted.includes(l.id) ? 'bg-emerald-100/50 border-emerald-200' : 'bg-surface-container-high border-outline-variant/20')">
                                            <Play class="w-3 h-3" :class="currentLesson?.id === l.id ? 'text-primary' : (localCompleted.includes(l.id) ? 'text-emerald-600' : 'text-on-surface-variant')" />
                                        </div>
                                        <div class="min-w-0 flex-1 pt-0.5">
                                            <p class="text-[13px] font-bold leading-tight group-hover:text-primary transition-colors pr-2" 
                                               :class="{ 'text-primary': currentLesson?.id === l.id && !localCompleted.includes(l.id), 'text-emerald-700': localCompleted.includes(l.id), 'text-on-surface': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                               {{ l.title }}
                                            </p>
                                            <p class="text-[9px] uppercase tracking-[0.1em] mt-1.5 flex items-center gap-1.5 font-bold" 
                                               :class="currentLesson?.id === l.id ? 'text-primary' : (localCompleted.includes(l.id) ? 'text-emerald-600' : 'text-on-surface-variant/50')">
                                                <template v-if="localCompleted.includes(l.id)">
                                                    <CheckCircle2 class="w-3 h-3 fill-emerald-600/10" />
                                                    Completado
                                                </template>
                                                <template v-else-if="currentLesson?.id === l.id">
                                                    <div class="w-1 h-1 rounded-full bg-primary animate-pulse"></div>
                                                    Viendo ahora
                                                </template>
                                                <template v-else-if="l.video_url">
                                                    <Play class="w-2.5 h-2.5" />
                                                    Clase Grabada
                                                </template>
                                                <template v-else-if="l.content_type === 'live' && l.start_time && new Date(l.start_time) > new Date()">
                                                    <Clock class="w-2.5 h-2.5" />
                                                    En Vivo (Programada)
                                                </template>
                                                <template v-else>
                                                    <AlertCircle class="w-2.5 h-2.5" />
                                                    {{ l.content_type === 'live' ? 'Procesando Grabación' : 'Clase de Video' }}
                                                </template>
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
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] transition-all duration-500"
                                        :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                            ? 'bg-primary text-on-primary shadow-lg shadow-primary/20 scale-110 z-10' 
                                            : (localCompleted.includes(l.id) 
                                                ? 'bg-emerald-500 text-white border border-emerald-400/20' 
                                                : 'bg-surface-container-highest text-on-surface-variant border border-outline-variant/30')">
                                            <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-4 h-4" />
                                            <span v-else>{{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1 p-2.5 rounded-2xl transition-all flex items-start gap-3" 
                                        :class="currentLesson?.id === l.id 
                                            ? 'bg-white border border-primary/30 shadow-sm' 
                                            : (localCompleted.includes(l.id) ? 'bg-emerald-50/30' : 'hover:bg-surface-container border border-transparent')">
                                        <div class="w-14 h-9 rounded-xl flex items-center justify-center flex-shrink-0 border transition-colors mt-0.5"
                                            :class="currentLesson?.id === l.id ? 'bg-primary/10 border-primary/20' : (localCompleted.includes(l.id) ? 'bg-emerald-100/50 border-emerald-200' : 'bg-surface-container-high border-outline-variant/20')">
                                            <Play class="w-3 h-3" :class="currentLesson?.id === l.id ? 'text-primary' : (localCompleted.includes(l.id) ? 'text-emerald-600' : 'text-on-surface-variant')" />
                                        </div>
                                        <div class="min-w-0 flex-1 pt-0.5">
                                            <p class="text-[13px] font-bold leading-tight group-hover:text-primary transition-colors pr-2" 
                                               :class="{ 'text-primary': currentLesson?.id === l.id && !localCompleted.includes(l.id), 'text-emerald-700': localCompleted.includes(l.id), 'text-on-surface': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                               {{ l.title }}
                                            </p>
                                            <p class="text-[9px] uppercase tracking-[0.1em] mt-1.5 flex items-center gap-1.5 font-bold" 
                                               :class="currentLesson?.id === l.id ? 'text-primary' : (localCompleted.includes(l.id) ? 'text-emerald-600' : 'text-on-surface-variant/50')">
                                                <template v-if="localCompleted.includes(l.id)">
                                                    <CheckCircle2 class="w-3 h-3 fill-emerald-600/10" />
                                                    Completado <span v-if="currentLesson?.id === l.id" class="text-[8px] opacity-70 ml-1">• Viendo</span>
                                                </template>
                                                <template v-else-if="currentLesson?.id === l.id">
                                                    <div class="w-1 h-1 rounded-full bg-primary animate-pulse"></div>
                                                    Viendo ahora
                                                </template>
                                                <template v-else>
                                                    <Play class="w-2.5 h-2.5" />
                                                    {{ l.content_type === 'live' ? 'Sesión en Vivo' : 'Clase de Video' }}
                                                </template>
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                             </div>
                        </div>

                        <!-- Botón Examen al final del sidebar -->
                        <div v-if="course.quizzes?.length" class="mt-8 space-y-4">
                             <h3 class="text-sm font-bold text-on-surface uppercase tracking-[0.1em]">Evaluación Final</h3>
                             <button 
                                @click="handleOpenExam"
                                class="w-full flex items-center gap-4 transition-all group relative z-10"
                             >
                                 <div class="flex-shrink-0 bg-surface-container-low py-1">
                                     <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm transition-all" :class="viewingExam ? 'bg-primary text-on-primary shadow-primary/20 scale-105' : 'bg-white text-on-surface-variant border border-outline-variant/30'">
                                         <CheckCircle2 class="w-4 h-4" />
                                     </div>
                                 </div>
                                 <div class="min-w-0 flex-1 p-3 rounded-2xl transition-all flex items-start gap-3 border shadow-sm" :class="viewingExam ? 'bg-primary/5 border-primary/30' : 'bg-surface-container-lowest border-outline-variant/20 hover:bg-surface-container hover:border-outline-variant/40'">
                                     <div class="min-w-0 flex-1 text-left">
                                         <p class="text-[13px] font-bold leading-tight transition-colors" :class="viewingExam ? 'text-primary' : 'text-on-surface group-hover:text-primary'">Tomar Examen</p>
                                         <p class="text-[9px] uppercase tracking-[0.15em] mt-1.5 flex items-center gap-1 font-bold" :class="localAllCompleted ? 'text-emerald-600' : 'text-orange-600'">
                                             <CheckCircle2 v-if="localAllCompleted" class="w-3 h-3" />
                                             <Clock v-else class="w-3 h-3" />
                                             {{ localAllCompleted ? 'Disponible para rendir' : 'Bloqueado (Faltan Videos)' }}
                                         </p>
                                     </div>
                                 </div>
                             </button>
                        </div>
                    </nav>
                </template>
            </aside>
        </main>
        
        <!-- NEW EXAM MODAL INTEGRATION -->
        <ExamModal 
            v-if="course.quizzes?.length"
            :show="viewingExam"
            :quiz="course.quizzes[0]"
            :course-id="course.id"
            @close="viewingExam = false"
        />
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
