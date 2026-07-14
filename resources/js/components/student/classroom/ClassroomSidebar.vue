<script setup lang="ts">
import type { Course, Lesson, QuizStats } from '@/types/classroom';
import { Link } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import {
    CheckCircle2,
    ChevronDown,
    ChevronUp,
    Edit2,
    Heart,
    ListVideo,
    Lock,
    MessageSquare,
    Play,
    Reply,
    Send,
    Trash2,
    Trophy,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    currentLessonIndex: number;
    allLessonsCount: number;
    localCompleted: number[];
    localAllCompleted: boolean;
    activeSidebarTab: 'curriculum' | 'chat' | 'comments';
    mobileSidebarOpen: boolean;
    comments: {
        id: number;
        user_id: number;
        user: { id: number; name: string; avatar?: string; role?: string };
        content: string;
        created_at: string;
        likes_count: number;
        liked_by_me: boolean;
        is_liked?: boolean;
        replies?: { id: number; user: { name: string }; content: string; created_at: string; is_liked?: boolean }[];
    }[];
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

// Comment deletion state
const commentToDelete = ref<number | null>(null);

function confirmDeleteComment(commentId: number) {
    commentToDelete.value = commentId;
}

function executeDeleteComment() {
    if (commentToDelete.value) {
        emit('deleteComment', commentToDelete.value);
        commentToDelete.value = null;
    }
}

// Accordion for modules
const expandedModules = ref<Record<number, boolean>>({});

// Keep the current lesson's module open by default
watch(
    () => props.currentLesson?.module_id,
    (newModId) => {
        if (newModId) {
            expandedModules.value[newModId] = true;
        }
    },
    { immediate: true },
);

function toggleModule(moduleId: number) {
    expandedModules.value[moduleId] = !expandedModules.value[moduleId];
}

// Flat list of lessons to calculate sequence and lock status
const flatLessons = computed(() => {
    const list: Lesson[] = [];
    props.course.modules.forEach((m) => {
        m.lessons.forEach((l) => {
            list.push(l);
        });
    });
    props.course.lessons.forEach((l) => {
        if (!l.module_id) {
            list.push(l);
        }
    });
    return list;
});

const currentLessonIndexInFlat = computed(() => {
    if (!props.currentLesson) return -1;
    return flatLessons.value.findIndex((l) => l.id === props.currentLesson?.id);
});

function isLessonLocked(lessonId: number) {
    const idx = flatLessons.value.findIndex((l) => l.id === lessonId);
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
        class="z-45 fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity lg:hidden"
    ></div>

    <!-- Interaction Sidebar: fixed drawer on mobile, inline on desktop -->
    <aside
        class="fixed right-0 top-0 z-50 flex h-[100dvh] w-[92vw] max-w-sm flex-col border-l border-outline-variant/10 bg-white shadow-2xl transition-transform duration-300 ease-out dark:border-outline-variant/20 dark:bg-surface lg:relative lg:right-auto lg:top-auto lg:z-40 lg:h-full lg:w-[420px] lg:max-w-none lg:bg-background lg:shadow-none lg:dark:bg-background"
        :class="mobileSidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    >
        <!-- Tab: Chat / Forum for Mobile Drawer -->
        <template v-if="activeSidebarTab === 'chat'">
            <header
                class="flex flex-shrink-0 items-center justify-between border-b border-outline-variant/10 bg-white px-4 py-3 shadow-sm dark:border-outline-variant/20 dark:bg-surface md:px-8 md:py-5"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="emit('update:mobileSidebarOpen', false)"
                        class="dark:bg-surface-2 rounded-xl border border-outline-variant/10 bg-background p-2 transition-all active:scale-90 lg:hidden"
                    >
                        <X class="h-4 w-4 text-on-background dark:text-on-surface" />
                    </button>
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-primary/10 bg-primary/5 dark:border-primary/30 dark:bg-primary/20 md:h-12 md:w-12 md:rounded-2xl"
                    >
                        <MessageSquare class="h-4 w-4 text-primary dark:text-primary-fixed md:h-5 md:w-5" />
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-on-background dark:text-on-surface md:text-sm">Foro Académico</h3>
                        <p class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/50 dark:text-on-surface-variant/60">
                            {{ comments.length }} comentarios
                        </p>
                    </div>
                </div>
                <button
                    @click="emit('update:activeSidebarTab', 'curriculum')"
                    class="dark:hover:bg-surface-3 rounded-xl p-2 transition-all hover:bg-surface-container-highest md:rounded-2xl md:p-3"
                >
                    <ListVideo class="h-4 w-4 text-outline-variant md:h-5 md:w-5" />
                </button>
            </header>
            <div class="custom-scrollbar flex-1 overflow-y-auto bg-background dark:bg-background">
                <div class="space-y-8 p-4 md:p-8">
                    <!-- New Comment / Reply Input -->
                    <div
                        class="relative rounded-2xl border border-outline-variant/10 bg-white p-6 shadow-sm transition-all focus-within:border-primary/30 focus-within:shadow-xl focus-within:shadow-primary/5 dark:border-outline-variant/20 dark:bg-surface md:p-8"
                    >
                        <div v-if="replyingTo" class="mb-4 flex items-center justify-between rounded-xl bg-primary/5 p-3 dark:bg-primary/10">
                            <p class="truncate text-[10px] font-bold text-primary dark:text-primary-fixed">
                                En respuesta a: {{ replyingTo.user.name }}
                            </p>
                            <button @click="emit('update:replyingTo', null)" class="text-primary/40 hover:text-red-500"><X class="h-4 w-4" /></button>
                        </div>
                        <textarea
                            v-model="newCommentModel"
                            :placeholder="replyingTo ? 'Escriba su respuesta...' : 'Escribe tu consulta aquí...'"
                            class="min-h-[90px] w-full resize-none border-none bg-transparent p-0 font-sans text-sm leading-relaxed text-on-surface placeholder:text-on-surface-variant/40 focus:ring-0 dark:text-on-surface dark:placeholder:text-on-surface-variant/50 md:min-h-[110px] md:text-base"
                            @focus="($event.target as HTMLElement)?.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                        ></textarea>
                        <div class="flex items-center justify-between border-t border-outline-variant/10 pt-4 dark:border-outline-variant/20">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#D4AF37]"></div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/40 dark:text-on-surface-variant/60"
                                    >Foro Institucional</span
                                >
                            </div>
                            <button
                                @click="emit('postComment')"
                                class="flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-md transition-all hover:bg-on-background active:scale-95 dark:bg-primary-fixed dark:text-on-primary-fixed dark:hover:bg-white dark:hover:text-on-background"
                            >
                                Enviar <Send class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Comments Listing -->
                    <div class="space-y-6 pb-32">
                        <div v-for="c in comments" :key="c.id" class="space-y-4">
                            <div
                                class="group space-y-4 rounded-2xl border border-outline-variant/10 bg-white p-6 shadow-sm transition-all hover:border-primary/20 dark:border-outline-variant/20 dark:bg-surface dark:hover:border-primary/30 md:rounded-[2rem] md:p-8"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="dark:bg-surface-2 flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/10 bg-surface-container-low font-serif text-base font-bold italic text-primary dark:text-primary-fixed-dim"
                                        >
                                            <img
                                                v-if="c.user?.avatar"
                                                :src="'/storage/' + c.user.avatar"
                                                :alt="c.user.name"
                                                class="h-full w-full object-cover"
                                                @error="($event.target as HTMLImageElement).style.display = 'none'"
                                            />
                                            <span v-else>{{ c.user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="flex items-center gap-1.5 text-xs font-bold text-on-background dark:text-on-surface md:text-sm">
                                                {{ c.user?.name }}
                                                <CheckCircle2
                                                    v-if="c.user?.role === 'admin'"
                                                    class="h-3.5 w-3.5 text-primary dark:text-primary-fixed-dim"
                                                />
                                            </p>
                                            <p
                                                class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/40 dark:text-on-surface-variant/50"
                                            >
                                                {{ c.user?.role === 'admin' ? 'Coordinador Académico' : 'Alumno Verificado' }} •
                                                {{ timeSince(c.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <button
                                            @click="emit('toggleLike', c.id)"
                                            class="p-1.5 transition-transform hover:scale-125"
                                            :class="c.is_liked ? 'text-red-500' : 'text-outline-variant hover:text-primary'"
                                        >
                                            <Heart class="w-4.5 h-4.5" :fill="c.is_liked ? 'currentColor' : 'none'" />
                                        </button>
                                        <span class="-mt-1 text-[9px] font-bold text-on-surface-variant/40">{{ c.likes_count }}</span>
                                    </div>
                                </div>

                                <div v-if="editingComment?.id === c.id" class="space-y-3">
                                    <textarea
                                        :value="editingComment?.content"
                                        @input="
                                            $emit('update:editingComment', {
                                                ...editingComment!,
                                                content: ($event.target as HTMLTextAreaElement).value,
                                            })
                                        "
                                        class="dark:bg-surface-2 min-h-[90px] w-full rounded-xl border border-primary/20 bg-background p-3 font-sans text-sm text-on-surface focus:ring-0 dark:text-on-surface"
                                    ></textarea>
                                    <div class="flex justify-end gap-3 text-[9px] font-bold uppercase tracking-wider">
                                        <button
                                            @click="emit('update:editingComment', null)"
                                            class="text-on-surface-variant/40 transition-colors hover:text-red-500"
                                        >
                                            Abortar
                                        </button>
                                        <button @click="emit('updateComment')" class="rounded-lg bg-primary px-4 py-2 text-white">Actualizar</button>
                                    </div>
                                </div>
                                <p
                                    v-else
                                    class="font-sans text-sm leading-relaxed text-on-surface-variant dark:text-on-surface-variant/90 md:text-base"
                                >
                                    {{ c.content }}
                                </p>

                                <div
                                    v-if="editingComment?.id !== c.id"
                                    class="flex items-center justify-between border-t border-outline-variant/10 pt-4 dark:border-outline-variant/20"
                                >
                                    <div class="flex items-center gap-4">
                                        <button
                                            @click="emit('update:replyingTo', c)"
                                            class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-primary transition-all hover:text-on-background dark:text-primary-fixed-dim dark:hover:text-white"
                                        >
                                            <Reply class="h-3.5 w-3.5" /> Opinar
                                        </button>
                                        <button
                                            v-if="c.user_id === currentUser?.id"
                                            @click="emit('update:editingComment', { ...c })"
                                            class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/45 transition-all hover:text-amber-600"
                                        >
                                            <Edit2 class="h-3.5 w-3.5" /> Editar
                                        </button>
                                    </div>
                                    <button
                                        v-if="c.user_id === currentUser?.id"
                                        @click="confirmDeleteComment(c.id)"
                                        class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-red-500/40 opacity-0 transition-all hover:text-red-600 group-hover:opacity-100"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" /> Eliminar
                                    </button>
                                </div>
                            </div>

                            <!-- Indented Replies -->
                            <div v-if="c.replies?.length" class="space-y-3 pl-8 md:pl-12">
                                <div
                                    v-for="r in c.replies"
                                    :key="r.id"
                                    class="group/reply relative rounded-2xl border border-outline-variant/10 bg-white p-5 shadow-sm dark:bg-surface"
                                >
                                    <div class="absolute -left-6 top-1/2 h-[1px] w-6 bg-outline-variant/20"></div>
                                    <div class="mb-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="dark:bg-surface-2 flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/10 bg-surface-container-low font-serif text-xs font-bold italic text-primary dark:text-primary-fixed-dim"
                                            >
                                                {{ r.user?.name?.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-on-background dark:text-on-surface">{{ r.user?.name }}</p>
                                                <p class="text-[8px] font-bold uppercase tracking-widest text-on-surface-variant/40">
                                                    {{ timeSince(r.created_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="font-sans text-sm leading-relaxed text-on-surface-variant/90 dark:text-on-surface-variant/80">
                                        {{ r.content }}
                                    </p>
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
            <header
                class="flex h-16 shrink-0 items-center justify-between border-b border-outline-variant/10 bg-white px-6 dark:bg-surface lg:hidden"
            >
                <h2 class="text-sm font-bold uppercase tracking-wider text-on-background">Contenido de Curso</h2>
                <button
                    @click="emit('update:mobileSidebarOpen', false)"
                    class="dark:bg-surface-2 rounded-xl border border-outline-variant/10 bg-background p-2"
                >
                    <X class="h-4 w-4 text-on-background" />
                </button>
            </header>

            <div class="custom-scrollbar flex-1 space-y-6 overflow-y-auto bg-background p-6 dark:bg-background">
                <!-- Course Progress Premium Card -->
                <div class="space-y-4 rounded-3xl border border-outline-variant/15 bg-white p-5 shadow-sm dark:bg-surface">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-on-background dark:text-on-surface">Progreso de Curso</span>
                        <span
                            class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-extrabold text-amber-800 dark:bg-amber-950/45 dark:text-amber-300"
                        >
                            {{ progressPercentage }}% Completado
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- SVG Radial Progress -->
                        <div class="relative h-14 w-14 flex-shrink-0">
                            <svg class="h-full w-full -rotate-90">
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
                            <span
                                class="absolute inset-0 flex items-center justify-center text-xs font-black text-on-background dark:text-on-surface"
                            >
                                {{ progressPercentage }}%
                            </span>
                        </div>

                        <!-- Text showing class count progress -->
                        <div class="flex-1">
                            <p class="text-xs font-bold text-on-surface dark:text-on-surface">Clases Visualizadas</p>
                            <p class="mt-0.5 text-[10px] font-semibold text-on-surface-variant/60">
                                {{ localCompleted.length }} de {{ allLessonsCount }} clases completadas
                            </p>
                        </div>
                    </div>
                </div>

                <!-- COURSE CONTENT Section Header -->
                <div class="flex items-center justify-between px-1 pt-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/50">Contenido Académico</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/50"> {{ allLessonsCount }} Clases </span>
                </div>

                <!-- Modules Accordion List -->
                <nav class="space-y-4 pb-24">
                    <div
                        v-for="m in course.modules"
                        :key="m.id"
                        class="overflow-hidden rounded-2xl border border-outline-variant/10 bg-white shadow-sm transition-all dark:bg-surface"
                    >
                        <!-- Module Header -->
                        <button
                            @click="toggleModule(m.id)"
                            class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-surface-container-low"
                        >
                            <h3 class="flex items-center gap-2 text-xs font-bold text-on-background dark:text-on-surface">
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="currentLesson?.module_id === m.id ? 'bg-primary' : 'bg-outline-variant/60'"
                                ></span>
                                {{ m.title }}
                            </h3>
                            <ChevronDown v-if="expandedModules[m.id]" class="h-4 w-4 shrink-0 text-primary" />
                            <ChevronUp v-else class="h-4 w-4 shrink-0 text-outline-variant/60" />
                        </button>

                        <!-- Module Lessons (Expanded) -->
                        <div
                            v-show="expandedModules[m.id]"
                            class="space-y-1 border-t border-outline-variant/5 bg-surface-container-lowest/30 px-2 py-2 dark:bg-black/20"
                        >
                            <div class="relative">
                                <!-- Vertical Connector Line -->
                                <div class="absolute bottom-4 left-6 top-4 w-[1.5px] bg-outline-variant/15"></div>

                                <div v-for="(l, i) in m.lessons" :key="l.id" class="relative">
                                    <!-- Lesson Row Content -->
                                    <template v-if="currentLesson?.id === l.id">
                                        <!-- Active Lesson Highlighted in Dark Olive -->
                                        <div
                                            class="relative z-10 my-1 flex items-center gap-3.5 rounded-xl border border-[#2b3319] bg-[#1E2511] p-3 text-white shadow-md"
                                        >
                                            <div
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-on-primary-fixed shadow-sm"
                                            >
                                                <Play class="ml-0.5 h-3.5 w-3.5 fill-[#1E2511] text-[#1E2511]" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="truncate text-xs font-bold leading-tight text-white">
                                                    {{ i + 1 < 10 ? '0' + (i + 1) : i + 1 }}. {{ l.title }}
                                                </h4>
                                                <p class="mt-0.5 text-[9px] font-medium text-amber-200/90">
                                                    {{ l.content_type === 'live' ? 'En Vivo' : '12:45' }} • Ahora Reproduciendo
                                                </p>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <!-- Standard/Locked/Completed Lesson Link -->
                                        <Link
                                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                            class="group relative z-10 my-1 flex items-center gap-3.5 rounded-xl p-3 transition-all hover:bg-surface-container-low"
                                            :class="isLessonLocked(l.id) ? 'opacity-55' : ''"
                                        >
                                            <!-- Icon Column -->
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center">
                                                <div
                                                    v-if="localCompleted.includes(l.id)"
                                                    class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 dark:border-emerald-900/40 dark:bg-emerald-950/20"
                                                    title="Clase completada"
                                                >
                                                    <CheckCircle2 class="h-3.5 w-3.5 fill-emerald-50 text-emerald-600 dark:fill-emerald-950/20" />
                                                </div>
                                                <div
                                                    v-else-if="isLessonLocked(l.id)"
                                                    class="flex h-6 w-6 items-center justify-center rounded-full border border-outline-variant/35 bg-white dark:bg-surface"
                                                    title="Lección bloqueada"
                                                >
                                                    <Lock class="h-3 w-3 text-outline-variant" />
                                                </div>
                                                <span v-else class="text-[10px] font-black text-outline-variant/60 group-hover:text-primary">
                                                    {{ i + 1 < 10 ? '0' + (i + 1) : i + 1 }}
                                                </span>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h4
                                                    class="truncate text-xs font-semibold text-on-surface transition-colors group-hover:text-primary dark:text-on-surface-variant"
                                                >
                                                    {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] font-medium text-on-surface-variant/40">
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
                        v-if="course.lessons.filter((l) => !l.module_id).length"
                        class="overflow-hidden rounded-2xl border border-outline-variant/10 bg-white shadow-sm transition-all dark:bg-surface"
                    >
                        <button
                            @click="toggleModule(-1)"
                            class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-surface-container-low"
                        >
                            <h3 class="flex items-center gap-2 text-xs font-bold text-on-background dark:text-on-surface">
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="!currentLesson?.module_id ? 'bg-primary' : 'bg-outline-variant/60'"
                                ></span>
                                Sesiones Complementarias
                            </h3>
                            <ChevronDown v-if="expandedModules[-1]" class="h-4 w-4 shrink-0 text-primary" />
                            <ChevronUp v-else class="h-4 w-4 shrink-0 text-outline-variant/60" />
                        </button>

                        <div
                            v-show="expandedModules[-1]"
                            class="space-y-1 border-t border-outline-variant/5 bg-surface-container-lowest/30 px-2 py-2 dark:bg-black/20"
                        >
                            <div class="relative">
                                <div class="absolute bottom-4 left-6 top-4 w-[1.5px] bg-outline-variant/15"></div>

                                <div v-for="(l, i) in course.lessons.filter((l) => !l.module_id)" :key="l.id" class="relative">
                                    <template v-if="currentLesson?.id === l.id">
                                        <div
                                            class="relative z-10 my-1 flex items-center gap-3.5 rounded-xl border border-[#2b3319] bg-[#1E2511] p-3 text-white shadow-md"
                                        >
                                            <div
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-on-primary-fixed shadow-sm"
                                            >
                                                <Play class="ml-0.5 h-3.5 w-3.5 fill-[#1E2511] text-[#1E2511]" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="truncate text-xs font-bold leading-tight text-white">{{ i + 1 }}. {{ l.title }}</h4>
                                                <p class="mt-0.5 text-[9px] font-medium text-amber-200/90">Ahora Reproduciendo</p>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <Link
                                            :href="route('student.classroom', { course: course.slug, lesson: l.id })"
                                            class="group relative z-10 my-1 flex items-center gap-3.5 rounded-xl p-3 transition-all hover:bg-surface-container-low"
                                            :class="isLessonLocked(l.id) ? 'opacity-55' : ''"
                                        >
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center">
                                                <div
                                                    v-if="localCompleted.includes(l.id)"
                                                    class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 dark:border-emerald-900/40 dark:bg-emerald-950/20"
                                                >
                                                    <CheckCircle2 class="h-3.5 w-3.5 fill-emerald-50 text-emerald-600 dark:fill-emerald-950/20" />
                                                </div>
                                                <div
                                                    v-else-if="isLessonLocked(l.id)"
                                                    class="flex h-6 w-6 items-center justify-center rounded-full border border-outline-variant/35 bg-white dark:bg-surface"
                                                >
                                                    <Lock class="h-3 w-3 text-outline-variant" />
                                                </div>
                                                <span v-else class="text-[10px] font-black text-outline-variant/60 group-hover:text-primary">
                                                    {{ course.modules.reduce((acc, m) => acc + m.lessons.length, 0) + i + 1 }}
                                                </span>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h4
                                                    class="truncate text-xs font-semibold text-on-surface transition-colors group-hover:text-primary dark:text-on-surface-variant"
                                                >
                                                    {{ l.title }}
                                                </h4>
                                                <p class="text-[9px] font-medium text-on-surface-variant/40">Clase Complementaria</p>
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
                            class="group relative cursor-pointer overflow-hidden rounded-2xl border border-outline-variant/10 bg-white p-5 shadow-sm transition-all hover:border-[#D4AF37]/50 dark:bg-surface"
                        >
                            <div
                                class="absolute -bottom-8 -right-8 h-32 w-32 rounded-full bg-[#D4AF37]/5 blur-2xl transition-colors group-hover:bg-[#D4AF37]/10"
                            ></div>
                            <div class="relative z-10 space-y-3 text-center">
                                <div
                                    class="dark:bg-surface-2 mx-auto flex h-11 w-11 items-center justify-center rounded-xl border border-outline-variant/10 bg-surface-container-low transition-all duration-300 group-hover:scale-110"
                                    :class="localAllCompleted ? 'border-emerald-100 bg-emerald-50' : ''"
                                >
                                    <Trophy class="w-5.5 h-5.5" :class="localAllCompleted ? 'text-[#D4AF37]' : 'text-outline-variant'" />
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-on-background dark:text-on-surface">
                                        Examen de Titulación
                                    </h4>
                                    <p
                                        class="mt-1.5 text-[9px] font-extrabold uppercase tracking-widest"
                                        :class="
                                            quizStats?.passed
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : quizStats?.attempts_left === 0
                                                  ? 'text-rose-600 dark:text-rose-400'
                                                  : localAllCompleted
                                                    ? 'text-[#D4AF37] dark:text-amber-400'
                                                    : 'text-orange-600 dark:text-orange-400'
                                        "
                                    >
                                        {{
                                            quizStats?.passed
                                                ? 'Certificado Obtenido'
                                                : quizStats?.attempts_left === 0
                                                  ? 'Intentos Agotados'
                                                  : localAllCompleted
                                                    ? 'Candidato Apto'
                                                    : 'Requisito: Ver Clases'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </template>
    </aside>

    <!-- Delete Comment Confirmation Modal -->
    <Dialog :open="commentToDelete !== null" @update:open="(val) => { if (!val) commentToDelete = null }">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Eliminar comentario</DialogTitle>
                <DialogDescription>
                    ¿Estás seguro de que deseas eliminar este comentario? Esta acción no se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="sm:justify-start">
                <Button variant="outline" @click="commentToDelete = null">
                    Cancelar
                </Button>
                <Button variant="destructive" @click="executeDeleteComment">
                    Sí, eliminar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
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
</style>
