<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    X, MessageSquare, ListVideo, Send, CheckCircle2, 
    Heart, Reply, Edit2, Trash2, Trophy, Play 
} from 'lucide-vue-next';
import type { Course, Lesson, QuizStats } from '@/types/classroom';

defineProps<{
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
               lg:relative lg:top-auto lg:right-auto lg:h-full lg:w-[460px] lg:z-40 lg:max-w-none
               bg-white dark:bg-surface lg:bg-background lg:dark:bg-background border-l border-outline-variant/10 dark:border-outline-variant/20
               flex flex-col shadow-2xl lg:shadow-none
               transition-transform duration-300 ease-out"
        :class="mobileSidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    >
        <template v-if="activeSidebarTab === 'chat'">
            <!-- Foro header: compact on mobile -->
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
                                         <button @click="emit('toggleLike', r.id)" class="p-1 transition-all" :class="r.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
                                              <Heart class="w-3.5 h-3.5" :fill="r.is_liked ? 'currentColor' : 'none'" />
                                         </button>
                                     </div>
                                     <p class="text-sm text-on-surface-variant/90 dark:text-on-surface-variant/80 leading-relaxed font-sans">{{ r.content }}</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                </div>
            </div>
        </template>

        <template v-else>
            <!-- Sidebar Tab: Curriculum -->
            <header class="h-24 md:h-36 px-6 md:px-10 flex flex-col justify-center bg-white dark:bg-surface border-b border-outline-variant/10 dark:border-outline-variant/20 relative overflow-hidden">
                 <!-- Mobile close button -->
                 <button @click="emit('update:mobileSidebarOpen', false)" class="lg:hidden absolute top-4 left-4 z-20 p-2 bg-background dark:bg-surface-2 rounded-xl border border-outline-variant/10 shadow-sm">
                     <X class="w-4 h-4 text-on-background dark:text-on-surface" />
                 </button>
                 <div class="absolute inset-x-0 bottom-0 h-1 bg-surface-container dark:bg-surface-3">
                     <div class="h-full bg-[#D4AF37] transition-all duration-1000" :style="{ width: `${(localCompleted.length / allLessonsCount) * 100}%` }"></div>
                 </div>
                 
                 <div class="relative z-10 space-y-1">
                      <div class="flex justify-between items-end">
                          <h2 class="text-lg md:text-xl font-serif font-bold text-on-background dark:text-on-surface">Módulos Académicos</h2>
                          <span class="text-[9px] md:text-[10px] font-extrabold text-primary dark:text-primary-fixed uppercase tracking-wider">{{ Math.round((localCompleted.length / allLessonsCount) * 100) }}% Finalizado</span>
                      </div>
                      <p class="text-[9px] md:text-[10px] font-bold text-on-surface-variant/40 dark:text-on-surface-variant/50 uppercase tracking-widest">Estás en la clase {{ currentLessonIndex }} de {{ allLessonsCount }}</p>
                 </div>
            </header>

            <nav class="flex-1 overflow-y-auto custom-scrollbar bg-background dark:bg-background p-6 md:p-8 pb-32 space-y-10">
                <!-- Module Section -->
                <div 
                    v-for="m in course.modules" :key="m.id" 
                    class="space-y-4 p-4 -mx-4 rounded-2xl transition-all duration-300"
                    :class="currentLesson?.module_id === m.id 
                        ? 'bg-surface-container-low/60 dark:bg-surface-2/40 border border-outline-variant/10 dark:border-outline-variant/20 shadow-sm' 
                        : 'border border-transparent'"
                >
                    <div class="flex items-center justify-between pl-1">
                        <h3 class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-wider"
                            :class="currentLesson?.module_id === m.id ? 'text-primary dark:text-primary-fixed-dim font-black' : 'text-on-background/60 dark:text-on-surface/60'">
                            <span class="w-1.5 h-1.5 rounded-full transition-all"
                                  :class="currentLesson?.module_id === m.id ? 'bg-[#D4AF37] scale-125 shadow-sm' : 'bg-primary dark:bg-primary-fixed'"></span>
                            {{ m.title }}
                        </h3>
                        <span v-if="currentLesson?.module_id === m.id" class="text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-[#D4AF37]/10 dark:bg-amber-400/20 text-[#A68010] dark:text-amber-400 border border-[#D4AF37]/20 dark:border-amber-400/30">
                            Módulo Actual
                        </span>
                    </div>
                    
                    <div class="space-y-3 relative pl-3 border-l ml-3 transition-colors"
                         :class="currentLesson?.module_id === m.id ? 'border-primary/30 dark:border-primary-fixed/30' : 'border-outline-variant/10 dark:border-outline-variant/20'">
                        <Link 
                            v-for="(l, i) in m.lessons" :key="l.id"
                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                            class="flex items-start gap-4 transition-all group relative py-1.5"
                        >
                            <!-- Status Icon Container -->
                            <div class="flex-shrink-0 mt-0.5 relative">
                                <div class="absolute -left-[1.38rem] top-1/2 -translate-y-1/2 w-3 h-[1px] bg-outline-variant/20 dark:bg-outline-variant/30"></div>
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300"
                                :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                    ? 'bg-primary dark:bg-primary-fixed text-white dark:text-on-primary-fixed shadow-md scale-105' 
                                    : (localCompleted.includes(l.id) 
                                        ? 'bg-primary/10 dark:bg-primary/20 text-primary dark:text-primary-fixed border border-primary/20 dark:border-primary/45' 
                                        : 'bg-white dark:bg-surface border border-outline-variant/30 dark:border-outline-variant/10 text-outline-variant group-hover:border-primary dark:group-hover:border-primary-fixed')">
                                    <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-4 h-4 text-[#D4AF37]" />
                                    <Play v-else-if="currentLesson?.id === l.id" class="w-3.5 h-3.5 text-white dark:text-on-primary-fixed" />
                                    <span v-else class="text-[9px] font-bold">{{ i + 1 }}</span>
                                </div>
                            </div>

                            <!-- Lesson Metadata -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs md:text-sm font-semibold leading-tight group-hover:text-primary dark:group-hover:text-primary-fixed-dim transition-colors" 
                                    :class="{ 'text-primary dark:text-primary-fixed-dim font-bold': currentLesson?.id === l.id, 'text-on-surface-variant/90 dark:text-on-surface-variant/40': localCompleted.includes(l.id) && currentLesson?.id !== l.id, 'text-on-background dark:text-on-surface': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                   {{ l.title }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-[9px] font-bold uppercase tracking-wider"
                                     :class="currentLesson?.id === l.id ? 'text-[#D4AF37] dark:text-amber-400' : (localCompleted.includes(l.id) ? 'text-primary/90 dark:text-primary-fixed/50' : 'text-on-surface-variant/80 dark:text-on-surface-variant/50')">
                                    <template v-if="localCompleted.includes(l.id)">
                                        Finalizado
                                    </template>
                                    <template v-else-if="currentLesson?.id === l.id">
                                        <div class="w-1 h-1 rounded-full bg-[#D4AF37] dark:bg-amber-400 animate-pulse"></div>
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
                <div 
                    v-if="course.lessons.filter(l => !l.module_id).length" 
                    class="space-y-4 p-4 -mx-4 rounded-2xl transition-all duration-300"
                    :class="!currentLesson?.module_id 
                        ? 'bg-surface-container-low/60 dark:bg-surface-2/40 border border-outline-variant/10 dark:border-outline-variant/20 shadow-sm' 
                        : 'border border-transparent'"
                >
                     <div class="flex items-center justify-between pl-1">
                         <h3 class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-wider"
                             :class="!currentLesson?.module_id ? 'text-primary dark:text-primary-fixed-dim font-black' : 'text-on-background/60 dark:text-on-surface/60'">
                             <span class="w-1.5 h-1.5 rounded-full transition-all"
                                   :class="!currentLesson?.module_id ? 'bg-[#D4AF37] scale-125 shadow-sm' : 'bg-primary dark:bg-primary-fixed'"></span>
                             Sesiones Complementarias
                         </h3>
                         <span v-if="!currentLesson?.module_id" class="text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-[#D4AF37]/10 dark:bg-amber-400/20 text-[#A68010] dark:text-amber-400 border border-[#D4AF37]/20 dark:border-amber-400/30">
                             Módulo Actual
                         </span>
                     </div>
                     <div class="space-y-3 relative pl-3 border-l ml-3 transition-colors"
                          :class="!currentLesson?.module_id ? 'border-primary/30 dark:border-primary-fixed/30' : 'border-outline-variant/10 dark:border-outline-variant/20'">
                        <Link 
                            v-for="(l, i) in course.lessons.filter(l => !l.module_id)" :key="l.id"
                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                            class="flex items-start gap-4 transition-all group relative py-1.5"
                        >
                            <div class="flex-shrink-0 mt-0.5 relative">
                                <div class="absolute -left-[1.38rem] top-1/2 -translate-y-1/2 w-3 h-[1px] bg-outline-variant/20 dark:bg-outline-variant/30"></div>
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300"
                                :class="currentLesson?.id === l.id && !localCompleted.includes(l.id)
                                    ? 'bg-primary dark:bg-primary-fixed text-white dark:text-on-primary-fixed shadow-md scale-105' 
                                    : (localCompleted.includes(l.id) 
                                        ? 'bg-primary/10 dark:bg-primary/20 text-primary dark:text-primary-fixed border border-primary/20 dark:border-primary/45' 
                                        : 'bg-white dark:bg-surface border border-outline-variant/30 dark:border-outline-variant/10 text-outline-variant group-hover:border-primary dark:group-hover:border-primary-fixed')">
                                    <CheckCircle2 v-if="localCompleted.includes(l.id)" class="w-4 h-4 text-[#D4AF37]" />
                                    <Play v-else-if="currentLesson?.id === l.id" class="w-3.5 h-3.5 text-white dark:text-on-primary-fixed" />
                                    <span v-else class="text-[9px] font-bold">{{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs md:text-sm font-semibold leading-tight group-hover:text-primary dark:group-hover:text-primary-fixed-dim transition-colors" 
                                    :class="{ 'text-primary dark:text-primary-fixed-dim font-bold': currentLesson?.id === l.id, 'text-on-surface-variant/80 dark:text-on-surface-variant/50': localCompleted.includes(l.id) && currentLesson?.id !== l.id, 'text-on-background dark:text-on-surface': !localCompleted.includes(l.id) && currentLesson?.id !== l.id }">
                                   {{ l.title }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-[9px] font-bold uppercase tracking-wider"
                                     :class="currentLesson?.id === l.id ? 'text-[#D4AF37] dark:text-amber-400' : (localCompleted.includes(l.id) ? 'text-primary/75 dark:text-primary-fixed/50' : 'text-on-surface-variant/65 dark:text-on-surface-variant/50')">
                                    {{ l.content_type === 'live' ? 'Transmisión Guardada' : 'Grabado Editorial' }}
                                </div>
                            </div>
                        </Link>
                     </div>
                </div>

                <!-- Evaluación Block: Sidebar Design -->
                <div v-if="course.quizzes?.length" class="pt-6">
                     <div class="bg-white dark:bg-surface rounded-2xl border border-outline-variant/10 dark:border-outline-variant/20 p-6 shadow-sm group hover:border-[#D4AF37]/50 transition-all cursor-pointer relative overflow-hidden" @click="emit('openExam')">
                         <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#D4AF37]/5 rounded-full blur-2xl group-hover:bg-[#D4AF37]/10 transition-colors"></div>
                         <div class="relative z-10 space-y-3 text-center">
                             <div class="w-12 h-12 rounded-xl bg-surface-container-low dark:bg-surface-2 border border-outline-variant/10 mx-auto flex items-center justify-center transition-all duration-300 group-hover:scale-110" :class="localAllCompleted ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30' : ''">
                                 <Trophy class="w-6 h-6" :class="localAllCompleted ? 'text-[#D4AF37]' : 'text-outline-variant'" />
                             </div>
                             <div>
                                 <h4 class="text-xs font-bold uppercase tracking-wider text-on-background dark:text-on-surface">Examen de Titulación</h4>
                                 <p class="text-[9px] font-extrabold uppercase tracking-widest mt-1.5" 
                                    :class="quizStats?.passed ? 'text-emerald-600 dark:text-emerald-400' : (quizStats?.attempts_left === 0 ? 'text-rose-600 dark:text-rose-400' : (localAllCompleted ? 'text-[#D4AF37] dark:text-amber-400' : 'text-orange-600 dark:text-orange-400'))">
                                     {{ quizStats?.passed ? 'Certificado Obtenido' : (quizStats?.attempts_left === 0 ? 'Intentos Agotados' : (localAllCompleted ? 'Candidato Apto' : 'Requisito: Ver Clases')) }}
                                 </p>
                             </div>
                         </div>
                     </div>
                </div>
            </nav>
        </template>
    </aside>
</template>
