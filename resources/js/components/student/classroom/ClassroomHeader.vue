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
    <header class="h-14 md:h-20 shrink-0 bg-white border-b border-outline-variant/20 flex items-center px-4 md:px-10 justify-between relative z-40 shadow-sm">
        <div class="flex items-center gap-6">
            <Link :href="route('student.courses.index')" class="p-3 bg-background hover:bg-surface-container-highest rounded-[1.25rem] border border-outline-variant/20 transition-all text-primary group shadow-inner">
                <ChevronLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
            </Link>
            <div class="flex flex-col">
                <p class="text-[10px] font-black text-primary/60 uppercase tracking-[0.25em] leading-none mb-2 italic">
                    Módulo {{ currentLessonIndex }} de {{ allLessonsCount }} • {{ course.title }}
                </p>
                <h1 class="text-sm md:text-base font-serif font-bold text-on-background truncate max-w-[180px] sm:max-w-xs md:max-w-md italic">
                    {{ currentLesson?.title }}
                </h1>
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
                    @click="emit('update:activeSidebarTab', activeSidebarTab === 'curriculum' ? 'chat' : 'curriculum')"
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
            
            <!-- Mobile Navigation Drawer Toggles -->
            <div class="sm:hidden flex items-center gap-2">
                <button 
                    @click="emit('toggleMobileSidebar', 'curriculum')" 
                    class="p-2.5 bg-background border border-outline-variant/20 rounded-xl shadow-sm active:scale-95 transition-all"
                    title="Ver módulos"
                >
                    <ListVideo class="w-4 h-4 text-on-surface-variant" />
                </button>
                <button 
                    @click="emit('toggleMobileSidebar', 'chat')" 
                    class="p-2.5 bg-primary text-white rounded-xl shadow-lg active:scale-95 transition-all relative"
                    title="Abrir foro"
                >
                    <MessageSquare class="w-4 h-4" />
                    <span v-if="commentsCount > 0" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ commentsCount }}</span>
                </button>
            </div>
        </div>
    </header>
</template>
