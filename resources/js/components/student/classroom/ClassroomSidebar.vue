<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    X, MessageSquare, ListVideo, Send, CheckCircle2, 
    Heart, Reply, Edit2, Trash2, Trophy 
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
               bg-white lg:bg-background border-l border-outline-variant/20
               flex flex-col shadow-2xl lg:shadow-none
               transition-transform duration-300 ease-out"
        :class="mobileSidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    >
        <template v-if="activeSidebarTab === 'chat'">
            <!-- Foro header: compact on mobile -->
            <header class="flex-shrink-0 px-4 md:px-8 py-3 md:py-5 border-b border-outline-variant/20 flex items-center justify-between bg-white shadow-sm">
                 <div class="flex items-center gap-3">
                     <button @click="emit('update:mobileSidebarOpen', false)" class="lg:hidden p-2 bg-background rounded-xl border border-outline-variant/20 active:scale-90 transition-all">
                         <X class="w-4 h-4 text-on-background" />
                     </button>
                     <div class="w-9 h-9 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-primary/5 flex items-center justify-center border border-primary/10">
                        <MessageSquare class="w-4 h-4 md:w-6 md:h-6 text-primary" />
                     </div>
                     <div>
                        <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.15em] text-on-background">Foro Académico</h3>
                        <p class="text-[9px] text-on-surface-variant/40 font-bold">{{ comments.length }} comentarios</p>
                     </div>
                 </div>
                 <button @click="emit('update:activeSidebarTab', 'curriculum')" class="p-2 md:p-3 hover:bg-surface-container-highest rounded-xl md:rounded-2xl transition-all">
                    <ListVideo class="w-4 h-4 md:w-5 md:h-5 text-outline-variant" />
                 </button>
            </header>

            <div class="flex-1 overflow-y-auto custom-scrollbar bg-background">
                <div class="p-8 space-y-12">
                    <!-- New Comment / Reply Input -->
                    <div class="bg-white rounded-[2.5rem] border border-outline-variant/20 p-8 shadow-sm transition-all focus-within:shadow-2xl focus-within:shadow-primary/5 relative">
                         <div v-if="replyingTo" class="mb-4 flex items-center justify-between p-3 bg-primary/5 rounded-xl">
                             <p class="text-[10px] font-black text-primary italic truncate">En respuesta a: {{ replyingTo.user.name }}</p>
                             <button @click="emit('update:replyingTo', null)" class="text-primary/40 hover:text-red-500"><X class="w-4 h-4" /></button>
                         </div>
                         <textarea 
                            v-model="newCommentModel"
                            :placeholder="replyingTo ? 'Escriba su respuesta...' : 'Escribe tu consulta aquí...'"
                            class="w-full bg-transparent border-none p-0 text-base placeholder:text-on-surface-variant/30 focus:ring-0 min-h-[100px] md:min-h-[120px] resize-none font-serif italic leading-relaxed"
                            @focus="($event.target as HTMLElement)?.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                         ></textarea>
                         <div class="flex items-center justify-between pt-6 border-t border-background">
                             <div class="flex items-center gap-2">
                                 <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></div>
                                 <span class="text-[9px] font-black text-on-surface-variant/30 uppercase tracking-[0.15em]">Espacio de debate</span>
                             </div>
                             <button 
                                @click="emit('postComment')"
                                class="px-8 py-3.5 bg-primary text-white rounded-xl hover:bg-on-background hover:-translate-y-1 transition-all shadow-xl shadow-primary/10 active:scale-95 font-black text-[10px] uppercase tracking-widest flex items-center gap-3"
                             >
                                Enviar <Send class="w-3.5 h-3.5" />
                             </button>
                         </div>
                    </div>

                    <!-- Comments Listing -->
                    <div class="space-y-8 pb-32">
                        <div v-for="c in comments" :key="c.id" class="space-y-4">
                            <div class="p-8 bg-white border border-outline-variant/20 rounded-[3rem] space-y-6 hover:border-primary/30 transition-all group shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-background border border-outline-variant/30 overflow-hidden flex items-center justify-center text-lg font-serif font-bold text-primary italic flex-shrink-0">
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
                                            <p class="text-[13px] font-bold text-on-background flex items-center gap-2">
                                                {{ c.user?.name }}
                                                <CheckCircle2 v-if="c.user?.role === 'admin'" class="w-3.5 h-3.5 text-primary" />
                                            </p>
                                            <p class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-[0.15em]">
                                                {{ c.user?.role === 'admin' ? 'Coordinador Académico' : 'Alumno Verificado' }} • {{ timeSince(c.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                         <button @click="emit('toggleLike', c.id)" class="p-2 transition-transform hover:scale-125" :class="c.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
                                             <Heart class="w-5 h-5" :fill="c.is_liked ? 'currentColor' : 'none'" />
                                         </button>
                                         <span class="text-[9px] font-black text-on-surface-variant/30 -mt-1">{{ c.likes_count }}</span>
                                    </div>
                                </div>
                                
                                <div v-if="editingComment?.id === c.id" class="space-y-4">
                                    <textarea :value="editingComment?.content" @input="$emit('update:editingComment', { ...editingComment!, content: ($event.target as HTMLTextAreaElement).value })" class="w-full bg-background rounded-2xl p-4 text-sm font-serif italic border border-primary/20 focus:ring-0 min-h-[100px]"></textarea>
                                    <div class="flex justify-end gap-3 text-[10px] font-black uppercase tracking-widest">
                                        <button @click="emit('update:editingComment', null)" class="text-on-surface-variant/40 hover:text-red-500 transition-colors">Abortar</button>
                                        <button @click="emit('updateComment')" class="px-6 py-2.5 bg-primary text-white rounded-xl">Actualizar</button>
                                    </div>
                                </div>
                                <p v-else class="text-base text-on-surface-variant leading-[1.8] font-serif italic">{{ c.content }}</p>
                                
                                <div v-if="editingComment?.id !== c.id" class="flex items-center justify-between pt-6 border-t border-background">
                                    <div class="flex items-center gap-6">
                                        <button @click="emit('update:replyingTo', c)" class="text-[10px] font-black uppercase tracking-[0.2em] text-primary hover:text-on-background transition-all flex items-center gap-2"><Reply class="w-3.5 h-3.5" /> Opinar</button>
                                        <button v-if="c.user_id === currentUser?.id" @click="emit('update:editingComment', { ...c })" class="text-[10px] font-black uppercase tracking-[0.2em] text-on-surface-variant/40 hover:text-amber-600 transition-all flex items-center gap-2"><Edit2 class="w-3.5 h-3.5" /> Editar</button>
                                    </div>
                                    <button v-if="c.user_id === currentUser?.id" @click="emit('deleteComment', c.id)" class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500/30 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 flex items-center gap-2"><Trash2 class="w-3.5 h-3.5" /> Eliminar</button>
                                </div>
                            </div>

                            <!-- Indented Replies -->
                            <div v-if="c.replies?.length" class="pl-12 space-y-4">
                                <div v-for="r in c.replies" :key="r.id" class="p-6 bg-white border border-outline-variant/10 rounded-[2.5rem] shadow-sm group/reply relative">
                                    <div class="absolute -left-6 top-1/2 w-6 h-[1px] bg-outline-variant/30"></div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-background border border-outline-variant/20 flex items-center justify-center text-sm font-serif font-bold text-primary italic">
                                                {{ r.user?.name?.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-on-background">{{ r.user?.name }}</p>
                                                <p class="text-[8px] text-on-surface-variant/40 font-bold uppercase tracking-widest">{{ timeSince(r.created_at) }}</p>
                                            </div>
                                        </div>
                                        <button @click="emit('toggleLike', r.id)" class="p-1 transition-all" :class="r.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'">
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
            <!-- Sidebar Tab: Curriculum -->
            <header class="h-28 md:h-48 px-6 md:px-10 flex flex-col justify-center bg-white border-b border-outline-variant/20 relative overflow-hidden">
                 <!-- Mobile close button -->
                 <button @click="emit('update:mobileSidebarOpen', false)" class="lg:hidden absolute top-4 left-4 z-20 p-2 bg-background rounded-xl border border-outline-variant/20 shadow-sm">
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
                     <div class="bg-white rounded-[2.5rem] border border-outline-variant/20 p-8 shadow-sm group hover:border-[#D4AF37]/50 transition-all cursor-pointer relative overflow-hidden" @click="emit('openExam')">
                         <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#D4AF37]/5 rounded-full blur-2xl group-hover:bg-[#D4AF37]/10 transition-colors"></div>
                         <div class="relative z-10 space-y-4 text-center">
                             <div class="w-16 h-16 rounded-[1.25rem] bg-background border border-outline-variant/20 mx-auto flex items-center justify-center transition-all group-hover:scale-110" :class="localAllCompleted ? 'bg-emerald-50 border-emerald-100' : ''">
                                 <Trophy class="w-8 h-8" :class="localAllCompleted ? 'text-[#D4AF37]' : 'text-outline-variant'" />
                             </div>
                             <div>
                                 <h4 class="text-[12px] font-black uppercase tracking-[0.2em] text-on-background">Examen de Titulación</h4>
                                 <p class="text-[9px] font-bold uppercase tracking-[0.15em] mt-2 font-black italic" 
                                    :class="quizStats?.passed ? 'text-emerald-600 opacity-100' : (quizStats?.attempts_left === 0 ? 'text-rose-600 opacity-100' : (localAllCompleted ? 'text-[#D4AF37] opacity-100' : 'text-orange-600'))">
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
