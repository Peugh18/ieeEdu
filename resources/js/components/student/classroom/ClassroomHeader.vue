<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, ListVideo, MessageSquare } from 'lucide-vue-next';
import type { Course, Lesson } from '@/types/classroom';

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
    <header class="h-14 md:h-20 shrink-0 bg-white dark:bg-surface border-b border-outline-variant/10 flex items-center px-4 md:px-10 justify-between relative z-40 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-4 md:gap-6">
            <Link :href="route('student.courses.index')" class="p-2.5 md:p-3 bg-surface-container-low dark:bg-surface-2 hover:bg-surface-container-highest dark:hover:bg-surface-3 rounded-xl border border-outline-variant/10 transition-all text-primary dark:text-primary-fixed group shadow-sm flex items-center justify-center">
                <ChevronLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
            </Link>
            <div class="flex flex-col min-w-0">
                <p class="text-[9px] md:text-[10px] font-bold text-primary/70 dark:text-primary-fixed-dim/80 uppercase tracking-[0.2em] leading-none mb-1.5 truncate max-w-[130px] sm:max-w-md">
                    Módulo {{ currentLessonIndex }} de {{ allLessonsCount }} • {{ course.title }}
                </p>
                <h1 class="text-sm md:text-base font-bold text-on-background dark:text-on-surface truncate max-w-[130px] sm:max-w-xs md:max-w-md">
                    {{ currentLesson?.title }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center bg-surface-container-low dark:bg-surface-2 p-1 rounded-xl border border-outline-variant/10 shadow-inner">
                <Link 
                    v-if="prevLessonId"
                    :href="route('student.classroom', { course: course.slug, lesson: prevLessonId })"
                    class="px-4 py-2 hover:bg-white dark:hover:bg-surface-3 hover:shadow-sm rounded-lg transition-all flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-wider text-on-surface-variant dark:text-on-surface hover:text-primary dark:hover:text-primary-fixed"
                >
                    <ChevronLeft class="w-4 h-4" /> <span class="hidden lg:inline">Anterior</span>
                </Link>
                
                <button 
                    @click="emit('update:activeSidebarTab', activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum')"
                    class="px-5 py-2 bg-primary/5 dark:bg-primary/20 hover:bg-primary/10 dark:hover:bg-primary/30 text-primary dark:text-primary-fixed-dim rounded-lg transition-all flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-wider border border-primary/10 dark:border-primary/30 mx-1"
                >
                    <ListVideo v-if="activeSidebarTab === 'chat' || activeSidebarTab === 'comments'" class="w-4 h-4" />
                    <MessageSquare v-else class="w-4 h-4" />
                    <span class="hidden lg:inline">{{ activeSidebarTab === 'curriculum' ? 'Foro / Chat' : 'Contenido' }}</span>
                </button>

                <Link 
                    v-if="nextLessonId"
                    :href="route('student.classroom', { course: course.slug, lesson: nextLessonId })"
                    class="px-5 py-2 bg-primary dark:bg-primary-fixed dark:text-on-primary-fixed rounded-lg hover:bg-on-background dark:hover:bg-white dark:hover:text-on-background-fixed transition-all flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-wider shadow-md hover:shadow-lg shadow-primary/10 dark:shadow-none"
                >
                    <span class="hidden lg:inline">Siguiente</span> <ChevronRight class="w-4 h-4" />
                </Link>
            </div>
            
            <!-- Mobile Navigation Drawer Toggles -->
            <div class="sm:hidden flex items-center gap-2">
                <button 
                    @click="emit('toggleMobileSidebar', 'curriculum')" 
                    class="p-2.5 bg-surface-container-low dark:bg-surface-2 border border-outline-variant/10 rounded-xl shadow-sm active:scale-95 transition-all"
                    title="Ver módulos"
                >
                    <ListVideo class="w-4 h-4 text-on-surface-variant dark:text-on-surface" />
                </button>
                <button 
                    @click="emit('toggleMobileSidebar', 'chat')" 
                    class="p-2.5 bg-primary dark:bg-primary-fixed dark:text-on-primary-fixed rounded-xl shadow-md active:scale-95 transition-all relative"
                    title="Abrir foro"
                >
                    <MessageSquare class="w-4 h-4" />
                    <span v-if="commentsCount > 0" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ commentsCount }}</span>
                </button>
            </div>
        </div>
    </header>
</template>
