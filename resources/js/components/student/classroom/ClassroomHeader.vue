<script setup lang="ts">
import type { Course, Lesson } from '@/types/classroom';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, ListVideo, MessageSquare } from 'lucide-vue-next';

defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    currentLessonIndex: number;
    allLessonsCount: number;
    prevLessonId?: number;
    nextLessonId?: number;
    commentsCount: number;
    activeSidebarTab: 'curriculum' | 'chat' | 'comments';
}>();

const emit = defineEmits<{
    (e: 'update:activeSidebarTab', val: 'curriculum' | 'chat' | 'comments'): void;
    (e: 'toggleMobileSidebar', tab: 'curriculum' | 'chat'): void;
}>();
</script>

<template>
    <header
        class="relative z-40 flex h-14 shrink-0 items-center justify-between border-b border-outline-variant/10 bg-white px-4 shadow-sm transition-all duration-300 dark:bg-surface md:h-20 md:px-10"
    >
        <div class="flex items-center gap-4 md:gap-6">
            <Link
                :href="route('student.courses.index')"
                class="dark:bg-surface-2 dark:hover:bg-surface-3 group flex items-center justify-center rounded-xl border border-outline-variant/10 bg-surface-container-low p-2.5 text-primary shadow-sm transition-all hover:bg-surface-container-highest dark:text-primary-fixed md:p-3"
            >
                <ChevronLeft class="h-5 w-5 transition-transform group-hover:-translate-x-1" />
            </Link>
            <div class="flex min-w-0 flex-col">
                <p
                    class="mb-1.5 max-w-[130px] truncate text-[9px] font-bold uppercase leading-none tracking-[0.2em] text-primary/70 dark:text-primary-fixed-dim/80 sm:max-w-md md:text-[10px]"
                >
                    Módulo {{ currentLessonIndex }} de {{ allLessonsCount }} • {{ course.title }}
                </p>
                <h1 class="max-w-[130px] truncate text-sm font-bold text-on-background dark:text-on-surface sm:max-w-xs md:max-w-md md:text-base">
                    {{ currentLesson?.title }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div
                class="dark:bg-surface-2 hidden items-center rounded-xl border border-outline-variant/10 bg-surface-container-low p-1 shadow-inner sm:flex"
            >
                <Link
                    v-if="prevLessonId"
                    :href="route('student.classroom', { course: course.slug, lesson: prevLessonId })"
                    class="dark:hover:bg-surface-3 flex items-center gap-2 rounded-lg px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-on-surface-variant transition-all hover:bg-white hover:text-primary hover:shadow-sm dark:text-on-surface dark:hover:text-primary-fixed"
                >
                    <ChevronLeft class="h-4 w-4" /> <span class="hidden lg:inline">Anterior</span>
                </Link>

                <button
                    @click="emit('update:activeSidebarTab', activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum')"
                    class="mx-1 flex items-center gap-2 rounded-lg border border-primary/10 bg-primary/5 px-5 py-2 text-[10px] font-extrabold uppercase tracking-wider text-primary transition-all hover:bg-primary/10 dark:border-primary/30 dark:bg-primary/20 dark:text-primary-fixed-dim dark:hover:bg-primary/30"
                >
                    <ListVideo v-if="activeSidebarTab === 'chat' || activeSidebarTab === 'comments'" class="h-4 w-4" />
                    <MessageSquare v-else class="h-4 w-4" />
                    <span class="hidden lg:inline">{{ activeSidebarTab === 'curriculum' ? 'Foro / Chat' : 'Contenido' }}</span>
                </button>

                <Link
                    v-if="nextLessonId"
                    :href="route('student.classroom', { course: course.slug, lesson: nextLessonId })"
                    class="dark:hover:text-on-background-fixed flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-[10px] font-extrabold uppercase tracking-wider shadow-md shadow-primary/10 transition-all hover:bg-on-background hover:shadow-lg dark:bg-primary-fixed dark:text-on-primary-fixed dark:shadow-none dark:hover:bg-white"
                >
                    <span class="hidden lg:inline">Siguiente</span> <ChevronRight class="h-4 w-4" />
                </Link>
            </div>

            <!-- Mobile Navigation Drawer Toggles -->
            <div class="flex items-center gap-2 sm:hidden">
                <button
                    @click="emit('toggleMobileSidebar', 'curriculum')"
                    class="dark:bg-surface-2 rounded-xl border border-outline-variant/10 bg-surface-container-low p-2.5 shadow-sm transition-all active:scale-95"
                    title="Ver módulos"
                >
                    <ListVideo class="h-4 w-4 text-on-surface-variant dark:text-on-surface" />
                </button>
                <button
                    @click="emit('toggleMobileSidebar', 'chat')"
                    class="relative rounded-xl bg-primary p-2.5 shadow-md transition-all active:scale-95 dark:bg-primary-fixed dark:text-on-primary-fixed"
                    title="Abrir foro"
                >
                    <MessageSquare class="h-4 w-4" />
                    <span
                        v-if="commentsCount > 0"
                        class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-black text-white"
                        >{{ commentsCount }}</span
                    >
                </button>
            </div>
        </div>
    </header>
</template>
