<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    X, MessageSquare, ListVideo, Send, CheckCircle2, 
    Heart, Reply, Edit2, Trash2, Trophy, Play, Lock,
    Flame, Rocket, Sparkles, ChevronDown, ChevronUp
} from 'lucide-vue-next';
import type { Course, Lesson, QuizStats } from '@/types/classroom';

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    currentLessonIndex: number;
    allLessonsCount: number;
    localCompleted: number[];
    localAllCompleted: boolean;
    activeSidebarTab: 'curriculum' | 'chat' | 'comments';
    mobileSidebarOpen: boolean;
    comments: { id: number; user_id: number; user: { id: number; name: string; avatar?: string; role?: string }; content: string; created_at: string; likes_count: number; liked_by_me: boolean; is_liked?: boolean; replies?: { id: number; user: { name: string }; content: string; created_at: string; is_liked?: boolean }[] }[];
    currentUser: { id: number; name: string; avatar?: string } | null;
    replyingTo: { id: number; user: { name: string } } | null;
    editingComment: { id: number; content: string } | null;
    quizStats: QuizStats | null;
    timeSince: (date: string) => string;
}>();

const emit = defineEmits<{
    (e: 'update:activeSidebarTab', val: 'curriculum' | 'chat' | 'comments'): void;
    (e: 'update:mobileSidebarOpen', val: boolean): void;
    (e: 'update:replyingTo', val: number | null): void;
    (e: 'update:editingComment', val: number | null): void;
    (e: 'postComment'): void;
    (e: 'toggleLike', commentId: number): void;
    (e: 'deleteComment', commentId: number): void;
    (e: 'updateComment'): void;
    (e: 'openExam'): void;
}>();

const newCommentModel = defineModel<string>('newComment', { default: '' });

// Accordion for modules
const expandedModules = ref<Record<number, boolean>>({});

// Keep the current lesson's module open by default
watch(() => props.currentLesson?.module_id, (newModId) => {
    if (newModId) {
        expandedModules.value[newModId] = true;
    }
}, { immediate: true });

function toggleModule(moduleId: number) {
    expandedModules.value[moduleId] = !expandedModules.value[moduleId];
}

// Flat list of lessons to calculate sequence and lock status
const flatLessons = computed(() => {
    const list: Lesson[] = [];
    props.course.modules.forEach(m => {
        m.lessons.forEach(l => {
            list.push(l);
        });
    });
    props.course.lessons.forEach(l => {
        if (!l.module_id) {
            list.push(l);
        }
    });
    return list;
});

const currentLessonIndexInFlat = computed(() => {
    if (!props.currentLesson) return -1;
    return flatLessons.value.findIndex(l => l.id === props.currentLesson?.id);
});

function isLessonLocked(lessonId: number) {
    const idx = flatLessons.value.findIndex(l => l.id === lessonId);
    if (idx === -1) return false;
    // Locked if its index is greater than the current lesson index AND it's not completed
    return idx > currentLessonIndexInFlat.value && !props.localCompleted.includes(lessonId);
}

// Calculate SVG stroke offset for radial progress (radius is 24, circumference is 150.8)
const circumference = 2 * Math.PI * 24;
const progressPercentage = computed(() => {
    if (!props.allLessonsCount) return 0;
    return Math.round((props.localCompleted.length / props.allLessonsCount) * 100);
});
const strokeDashoffset = computed(() => {
    return circumference - (progressPercentage.value / 100) * circumference;
});
</script>

<template>
    <!-- Mobile sidebar backdrop -->
    <div
        v-if="mobileSidebarOpen"
        @click="emit('update:mobileSidebarOpen', false)"
        class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-45 transition-opacity"
    ></div>

    <!-- Interaction Sidebar: fixed drawer on mobile, inline on desktop -->
    <aside
        class="fixed top-0 right-0 h-[100dvh] z-50 w-[92vw] max-w-sm
               lg:relative lg:top-auto lg:right-auto lg:h-full lg:w-[420px] lg:z-40 lg:max-w-none
               bg-white dark:bg-surface lg:bg-background lg:dark:bg-background border-l border-outline-variant/10 dark:border-outline-variant/20
               flex flex-col shadow-2xl lg:shadow-none
               transition-transform duration-300 ease-out"
        :class="mobileSidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    >
        <!-- Tab: Chat / Forum for Mobile Drawer -->
        <template v-if="activeSidebarTab === 'chat'">
            <header class="flex-shrink-0 px-4 md:px-8 py-3 md:py-5 border-b border-outline-variant/10 dark:border-outline-variant/20 flex items-center justify-between bg-white dark:bg-surface shadow-sm">
                 <div class="flex items-center gap-3">
                     <button @click="emit('update:mobileSidebarOpen', false)" class="lg:hidden p-2 bg-background dark:bg-surface-2 rounded-xl border border-outline-variant/10 active:scale-90 transition-all">
                         <X class="w-4 h-4 text-on-background dark:text-on-surface" />
                     </button>
                     <div class="w-9 h-9 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-primary/5 dark:bg-primary/20 flex items-center justify-center border border-primary/10 dark:border-primary/30">
                        <MessageSquare class="w-4 h-4 md:w-5 md:h-5 text-primary dark:text-primary-fixed" />
                     </div>
                     <div>
                        <h3 class="text-xs md:text-sm font-bold uppercase tracking-wider text-on-background dark:text-on-surface">Foro Académico</h3>
                        <p class="text-[9px] text-on-surface-variant/50 dark:text-on-surface-variant/60 font-bold uppercase tracking-wider">{{ comments.length }} comentarios</p>
                     </div>
                 </div>
                 <button @click="emit('update:activeSidebarTab', 'curriculum')" class="p-2 md:p-3 hover:bg-surface-container-highest dark:hover:bg-surface-3 rounded-xl md:rounded-2xl transition-all">
                    <ListVideo class="w-4 h-4 md:w-5 md:h-5 text-outline-variant" />
                 </button>
            </header>
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-background dark:bg-background">
                <div class="p-4 md:p-8 space-y-8">
                    <!-- New Comment / Reply Input -->
                    <div class="bg-white dark:bg-surface rounded-2xl border border-outline-variant/10 dark:border-outline-variant/20 p-6 md:p-8 shadow-sm transition-all focus-within:shadow-xl focus-within:shadow-primary/5 focus-within:border-primary/30 relative">
                         <div v-if="replyingTo" class="mb-4 flex items-center justify-between p-3 bg-primary/5 dark:bg-primary/10 rounded-xl">
                             <p class="text-[10px] font-bold text-primary dark:text-primary-fixed truncate">En respuesta a: {{ replyingTo.user.name }}</p>
                             <button @click="emit('update:replyingTo', null)" class="text-primary/40 hover:text-red-500"><X class="w-4 h-4" /></button>
                         </div>
                         <textarea 
                            v-model="newCommentModel"
                            :placeholder="replyingTo ? 'Escriba su respuesta...' : 'Escribe tu consulta aquí...'"
                            class="w-full bg-transparent border-none p-0 text-sm md:text-base placeholder:text-on-surface-variant/40 dark:placeholder:text-on-surface-variant/50 focus:ring-0 min-h-[90px] md:min-h-[110px] resize-none font-sans leading-relaxed text-on-surface dark:text-on-surface"
                            @focus="($event.target as HTMLElement)?.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                         ></textarea>
                         <div class="flex items-center justify-between pt-4 border-t border-outline-variant/10 dark:border-outline-variant/20">
                             <div class="flex items-center gap-2">
                                 <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></div>
                                 <span class="text-[9px] font-bold text-on-surface-variant/40 dark:text-on-surface-variant/60 uppercase tracking-wider">Foro Institucional</span>
                             </div>
                             <button 
                                @click="emit('postComment')"
                                class="px-6 py-2.5 bg-primary dark:bg-primary-fixed dark:text-on-primary-fixed text-white rounded-xl hover:bg-on-background dark:hover:bg-white dark:hover:text-on-background transition-all shadow-md active:scale-95 font-bold text-[10px] uppercase tracking-wider flex items-center gap-2"
                             >
                                Enviar <Send class="w-3.5 h-3.5" />
                             </button>
                         </div>
                    </div>

                    <!-- Comments Listing -->
                    <div class="space-y-6 pb-32">
                        <div v-for="c in comments" :key="c.id" class="space-y-4">
                            <div class="p-6 md:p-8 bg-white dark:bg-surface border border-outline-variant/10 dark:border-outline-variant/20 rounded-2xl md:rounded-[2rem] space-y-4 hover:border-primary/20 dark:hover:border-primary/30 transition-all group shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-surface-container-low dark:bg-surface-2 border border-outline-variant/10 overflow-hidden flex items-center justify-center text-base font-serif font-bold text-primary dark:text-primary-fixed-dim italic flex-shrink-0">
                                            <img 
                                                v-if="c.user?.avatar"
                                                :src="'/storage/' + c.user.avatar"
                                                :alt="c.user.name"
                                                class="w-full h-full object-cover"
                                                @error="($event.target as HTMLImageElement).style.display='none'"
                                            />
                                            <span v-else>{{ c.user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-xs md:text-sm font-bold text-on-background dark:text-on-surface flex items-center gap-1.5">
                                                {{ c.user?.name }}
                                                <CheckCircle2 v-if="c.user?.role === 'admin'" class="w-3.5 h-3.5 text-primary dark:text-primary-fixed-dim" />
                                            </p>
                                            <p class="text-[9px] text-on-surface-variant/40 dark:text-on-surface-variant/50 font-bold uppercase tracking-wider">
                                                {{ c.user?.role === 'admin' ? 'Coordinador Académico' : 'Alumno Verificado' }} • {{ timeSince(c.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                         <button @click="emit('toggleLike', c.id)" class="p-1.5 transition-transform hover:scale-125" :class="c.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
                                             <Heart class="w-4.5 h-4.5" :fill="c.is_liked ? 'currentColor' : 'none'" />
                                         </button>
                                         <span class="text-[9px] font-bold text-on-surface-variant/40 -mt-1">{{ c.likes_count }}</span>
                                    </div>
                                </div>

                                <div v-if="editingComment?.id === c.id" class="space-y-3">
                                    <textarea :value="editingComment?.content" @input="$emit('update:editingComment', { ...editingComment!, content: ($event.target as HTMLTextAreaElement).value })" class="w-full bg-background dark:bg-surface-2 rounded-xl p-3 text-sm font-sans border border-primary/20 focus:ring-0 min-h-[90px] text-on-surface dark:text-on-surface"></textarea>
                                    <div class="flex justify-end gap-3 text-[9px] font-bold uppercase tracking-wider">
                                        <button @click="emit('update:editingComment', null)" class="text-on-surface-variant/40 hover:text-red-500 transition-colors">Abortar</button>
                                        <button @click="emit('updateComment')" class="px-4 py-2 bg-primary text-white rounded-lg">Actualizar</button>
                                    </div>
                                </div>
                                <p v-else class="text-sm md:text-base text-on-surface-variant dark:text-on-surface-variant/90 leading-relaxed font-sans">{{ c.content }}</p>
                                
                                <div v-if="editingComment?.id !== c.id" class="flex items-center justify-between pt-4 border-t border-outline-variant/10 dark:border-outline-variant/20">
                                    <div class="flex items-center gap-4">
                                        <button @click="emit('update:replyingTo', c)" class="text-[9px] font-bold uppercase tracking-wider text-primary dark:text-primary-fixed-dim hover:text-on-background dark:hover:text-white transition-all flex items-center gap-1.5"><Reply class="w-3.5 h-3.5" /> Opinar</button>
                                        <button v-if="c.user_id === currentUser?.id" @click="emit('update:editingComment', { ...c })" class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/45 hover:text-amber-600 transition-all flex items-center gap-1.5"><Edit2 class="w-3.5 h-3.5" /> Editar</button>
                                    </div>
                                    <button v-if="c.user_id === currentUser?.id" @click="emit('deleteComment', c.id)" class="text-[9px] font-bold uppercase tracking-wider text-red-500/40 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 flex items-center gap-1.5"><Trash2 class="w-3.5 h-3.5" /> Eliminar</button>
                                </div>
                            </div>

                            <!-- Indented Replies -->
                            <div v-if="c.replies?.length" class="pl-8 md:pl-12 space-y-3">
                                <div v-for="r in c.replies" :key="r.id" class="p-5 bg-white dark:bg-surface border border-outline-variant/10 rounded-2xl shadow-sm group/reply relative">
                                    <div class="absolute -left-6 top-1/2 w-6 h-[1px] bg-outline-variant/20"></div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-surface-2 border border-outline-variant/10 flex items-center justify-center text-xs font-serif font-bold text-primary dark:text-primary-fixed-dim italic">
                                                {{ r.user?.name?.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-on-background dark:text-on-surface">{{ r.user?.name }}</p>
                                                <p class="text-[8px] text-on-surface-variant/40 font-bold uppercase tracking-widest">{{ timeSince(r.created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-on-surface-variant/90 dark:text-on-surface-variant/80 leading-relaxed font-sans">{{ r.content }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Tab: Curriculum (Default Sidebar Layout) -->
        <template v-else>
            <!-- Mobile header only -->
            <header class="lg:hidden shrink-0 h-16 px-6 flex items-center bg-white dark:bg-surface border-b border-outline-variant/10 justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-on-background">Contenido de Curso</h2>
                <button @click="emit('update:mobileSidebarOpen', false)" class="p-2 bg-background dark:bg-surface-2 rounded-xl border border-outline-variant/10">
                    <X class="w-4 h-4 text-on-background" />
                </button>
            </header>

            <div class="flex-1 overflow-y-auto custom-scrollbar bg-background dark:bg-background p-6 space-y-6">
                <!-- Course Progress Premium Card -->
                <div class="bg-white dark:bg-surface border border-outline-variant/15 rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-on-background dark:text-on-surface">Progreso de Curso</span>
                        <span class="px-2.5 py-1 text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/45 text-amber-800 dark:text-amber-300 rounded-full">
                            {{ progressPercentage }}% Completado
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- SVG Radial Progress -->
                        <div class="relative w-14 h-14 flex-shrink-0">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="28" cy="28" r="24" fill="transparent" stroke="#F1F1EC" stroke-width="4.5" />
                                <circle 
                                    cx="28" 
                                    cy="28" 
                                    r="24" 
                                    fill="transparent" 
                                    stroke="#57572a" 
                                    stroke-width="4.5"
                                    :stroke-dasharray="circumference"
                                    :stroke-dashoffset="strokeDashoffset"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000"
                                />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-xs font-black text-on-background dark:text-on-surface">
                                {{ progressPercentage }}%
                            </span>
                        </div>

                        <!-- Text showing class count progress -->
                        <div class="flex-1">
                            <p class="text-xs font-bold text-on-surface dark:text-on-surface">
                                Clases Visualizadas
                            </p>
                            <p class="text-[10px] text-on-surface-variant/60 font-semibold mt-0.5">
                                {{ localCompleted.length }} de {{ allLessonsCount }} clases completadas
                            </p>
                        </div>
                    </div>
                </div>

                <!-- COURSE CONTENT Section Header -->
                <div class="flex justify-between items-center px-1 pt-2">
                    <span class="text-[10px] font-black text-on-surface-variant/50 uppercase tracking-widest">Contenido Académico</span>
                    <span class="text-[10px] font-black text-on-surface-variant/50 uppercase tracking-widest">
                        {{ allLessonsCount }} Clases
                    </span>
                </div>

                <!-- Modules Accordion List -->
                <nav class="space-y-4 pb-24">
                    <div 
                        v-for="m in course.modules" 
                        :key="m.id" 
                        class="bg-white dark:bg-surface border border-outline-variant/10 rounded-2xl overflow-hidden transition-all shadow-sm"
                    >
                        <!-- Module Header -->
                        <button 
                            @click="toggleModule(m.id)"
                            class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-surface-container-low transition-colors"
                        >
                            <h3 class="text-xs font-bold text-on-background dark:text-on-surface flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full" :class="currentLesson?.module_id === m.id ? 'bg-primary' : 'bg-outline-variant/60'"></span>
                                {{ m.title }}
                            </h3>
                            <ChevronDown v-if="expandedModules[m.id]" class="w-4 h-4 text-primary shrink-0" />
                            <ChevronUp v-else class="w-4 h-4 text-outline-variant/60 shrink-0" />
                        </button>

                        <!-- Module Lessons (Expanded) -->
                        <div v-show="expandedModules[m.id]" class="border-t border-outline-variant/5 px-2 py-2 space-y-1 bg-surface-container-lowest/30">
                            <div class="relative">
                                <!-- Vertical Connector Line -->
                                <div class="absolute left-6 top-4 bottom-4 w-[1.5px] bg-outline-variant/15"></div>

                                <div 
                                    v-for="(l, i) in m.lessons" 
                                    :key="l.id"
                                    class="relative"
                                >
                                    <!-- Lesson Row Content -->
                                    <template v-if="currentLesson?.id === l.id">
                                        <!-- Active Lesson Highlighted in Dark Olive -->
                                        <div class="flex items-center gap-3.5 p-3 rounded-xl bg-[#1E2511] text-white shadow-md my-1 z-10 relative border border-[#2b3319]">
                                            <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center text-on-primary-fixed shadow-sm shrink-0">
                                                <Play class="w-3.5 h-3.5 text-[#1E2511] fill-[#1E2511] ml-0.5" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-bold truncate text-white leading-tight">
                                                    {{ i + 1 < 10 ? '0' + (i + 1) : i + 1 }}. {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] font-medium text-amber-200/90 mt-0.5">
                                                    {{ l.content_type === 'live' ? 'En Vivo' : '12:45' }} • Ahora Reproduciendo
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <template v-else>
                                        <!-- Standard/Locked/Completed Lesson Link -->
                                        <Link 
                                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                            class="flex items-center gap-3.5 p-3 rounded-xl transition-all group my-1 z-10 relative hover:bg-surface-container-low"
                                            :class="isLessonLocked(l.id) ? 'opacity-55' : ''"
                                        >
                                            <!-- Icon Column -->
                                            <div class="w-7 h-7 flex items-center justify-center shrink-0">
                                                <div 
                                                    v-if="localCompleted.includes(l.id)"
                                                    class="w-6 h-6 rounded-full bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 flex items-center justify-center"
                                                    title="Clase completada"
                                                >
                                                    <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 fill-emerald-50 dark:fill-emerald-950/20" />
                                                </div>
                                                <div 
                                                    v-else-if="isLessonLocked(l.id)"
                                                    class="w-6 h-6 rounded-full border border-outline-variant/35 flex items-center justify-center bg-white dark:bg-surface"
                                                    title="Lección bloqueada"
                                                >
                                                    <Lock class="w-3 h-3 text-outline-variant" />
                                                </div>
                                                <span 
                                                    v-else 
                                                    class="text-[10px] font-black text-outline-variant/60 group-hover:text-primary"
                                                >
                                                    {{ i + 1 < 10 ? '0' + (i + 1) : i + 1 }}
                                                </span>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-semibold truncate group-hover:text-primary transition-colors text-on-surface dark:text-on-surface-variant">
                                                    {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] text-on-surface-variant/40 font-medium">
                                                    {{ l.content_type === 'live' ? 'Clase en Vivo' : 'Cátedra Grabada' }}
                                                </p>
                                            </div>
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- One-off Lessons (Módulos Flexibles) -->
                    <div 
                        v-if="course.lessons.filter(l => !l.module_id).length" 
                        class="bg-white dark:bg-surface border border-outline-variant/10 rounded-2xl overflow-hidden transition-all shadow-sm"
                    >
                        <button 
                            @click="toggleModule(-1)"
                            class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-surface-container-low transition-colors"
                        >
                            <h3 class="text-xs font-bold text-on-background dark:text-on-surface flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full" :class="!currentLesson?.module_id ? 'bg-primary' : 'bg-outline-variant/60'"></span>
                                Sesiones Complementarias
                            </h3>
                            <ChevronDown v-if="expandedModules[-1]" class="w-4 h-4 text-primary shrink-0" />
                            <ChevronUp v-else class="w-4 h-4 text-outline-variant/60 shrink-0" />
                        </button>

                        <div v-show="expandedModules[-1]" class="border-t border-outline-variant/5 px-2 py-2 space-y-1 bg-surface-container-lowest/30">
                            <div class="relative">
                                <div class="absolute left-6 top-4 bottom-4 w-[1.5px] bg-outline-variant/15"></div>

                                <div 
                                    v-for="(l, i) in course.lessons.filter(l => !l.module_id)" 
                                    :key="l.id"
                                    class="relative"
                                >
                                    <template v-if="currentLesson?.id === l.id">
                                        <div class="flex items-center gap-3.5 p-3 rounded-xl bg-[#1E2511] text-white shadow-md my-1 z-10 relative border border-[#2b3319]">
                                            <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center text-on-primary-fixed shadow-sm shrink-0">
                                                <Play class="w-3.5 h-3.5 text-[#1E2511] fill-[#1E2511] ml-0.5" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-bold truncate text-white leading-tight">
                                                    {{ i + 1 }}. {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] font-medium text-amber-200/90 mt-0.5">Ahora Reproduciendo</p>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <template v-else>
                                        <Link 
                                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                            class="flex items-center gap-3.5 p-3 rounded-xl transition-all group my-1 z-10 relative hover:bg-surface-container-low"
                                            :class="isLessonLocked(l.id) ? 'opacity-55' : ''"
                                        >
                                            <div class="w-7 h-7 flex items-center justify-center shrink-0">
                                                <div 
                                                    v-if="localCompleted.includes(l.id)"
                                                    class="w-6 h-6 rounded-full bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 flex items-center justify-center"
                                                >
                                                    <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 fill-emerald-50 dark:fill-emerald-950/20" />
                                                </div>
                                                <div 
                                                    v-else-if="isLessonLocked(l.id)"
                                                    class="w-6 h-6 rounded-full border border-outline-variant/35 flex items-center justify-center bg-white dark:bg-surface"
                                                >
                                                    <Lock class="w-3 h-3 text-outline-variant" />
                                                </div>
                                                <span 
                                                    v-else 
                                                    class="text-[10px] font-black text-outline-variant/60 group-hover:text-primary"
                                                >
                                                    {{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}
                                                </span>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-semibold truncate group-hover:text-primary transition-colors text-on-surface dark:text-on-surface-variant">
                                                    {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] text-on-surface-variant/40 font-medium">Clase Complementaria</p>
                                            </div>
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Exam Card -->
                    <div v-if="course.quizzes?.length" class="pt-2">
                         <div 
                            @click="emit('openExam')"
                            class="bg-white dark:bg-surface border border-outline-variant/10 rounded-2xl p-5 shadow-sm group hover:border-[#D4AF37]/50 transition-all cursor-pointer relative overflow-hidden"
                         >
                             <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#D4AF37]/5 rounded-full blur-2xl group-hover:bg-[#D4AF37]/10 transition-colors"></div>
                             <div class="relative z-10 space-y-3 text-center">
                                 <div 
                                    class="w-11 h-11 rounded-xl bg-surface-container-low dark:bg-surface-2 border border-outline-variant/10 mx-auto flex items-center justify-center transition-all duration-300 group-hover:scale-110" 
                                    :class="localAllCompleted ? 'bg-emerald-50 border-emerald-100' : ''"
                                 >
                                     <Trophy class="w-5.5 h-5.5" :class="localAllCompleted ? 'text-[#D4AF37]' : 'text-outline-variant'" />
                                 </div>
                                 <div>
                                     <h4 class="text-xs font-bold uppercase tracking-wider text-on-background dark:text-on-surface">Examen de Titulación</h4>
                                     <p 
                                        class="text-[9px] font-extrabold uppercase tracking-widest mt-1.5" 
                                        :class="quizStats?.passed ? 'text-emerald-600 dark:text-emerald-400' : (quizStats?.attempts_left === 0 ? 'text-rose-600 dark:text-rose-400' : (localAllCompleted ? 'text-[#D4AF37] dark:text-amber-400' : 'text-orange-600 dark:text-orange-400'))"
                                     >
                                         {{ quizStats?.passed ? 'Certificado Obtenido' : (quizStats?.attempts_left === 0 ? 'Intentos Agotados' : (localAllCompleted ? 'Candidato Apto' : 'Requisito: Ver Clases')) }}
                                     </p>
                                 </div>
                             </div>
                         </div>
                    </div>
                </nav>
            </div>
        </template>
    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(87, 87, 42, 0.08); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(87, 87, 42, 0.15); }
</style>
