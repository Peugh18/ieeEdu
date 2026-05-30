<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import ClassroomHeader from '@/components/student/classroom/ClassroomHeader.vue';
import ClassroomSidebar from '@/components/student/classroom/ClassroomSidebar.vue';
import ClassroomContent from '@/components/student/classroom/ClassroomContent.vue';
import ClassroomResources from '@/components/student/classroom/ClassroomResources.vue';
import ClassroomExamSection from '@/components/student/classroom/ClassroomExamSection.vue';
import ClassroomErrorModal from '@/components/student/classroom/ClassroomErrorModal.vue';
import { useClassroom } from '@/composables/student/classroom/useClassroom';
import type { Course, Lesson, QuizStats, Material } from '@/types/classroom';
import type { SharedData } from '@/types';
import { MessageSquare } from 'lucide-vue-next';

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    prevLessonId?: number;
    nextLessonId?: number;
    allLessonsCount: number;
    currentLessonIndex: number;
    completedLessons: number[];
    allLessonsCompleted: boolean;
    comments: { id: number; user_id: number; user_name: string; content: string; created_at: string; likes_count: number; liked_by_me: boolean }[];
    quizStats: QuizStats | null;
}>();

const page = usePage<SharedData>();
const currentUser = computed(() => page.props.auth?.user);

const { progress, player, comments: commentsApi, live } = useClassroom(
    props.course, currentUser.value?.id ?? 'guest', props.currentLesson, props.completedLessons, props.allLessonsCompleted, props.allLessonsCount
);

const breadcrumbs = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/student/courses' },
    { title: props.course.title, href: route('student.classroom', { course: props.course.slug }) },
]);

const activeSidebarTab = ref<'curriculum' | 'chat' | 'comments'>('curriculum');
const mobileSidebarOpen = ref(false);
const viewingExam = ref(false);
const activeTab = ref<'content' | 'resources'>('content');
const showLocalError = ref(false);
const localErrorMessage = ref('');

function handleOpenExam() {
    if (!progress.localAllCompleted.value) { alert('Debes completar todas las lecciones del curso para poder rendir la evaluación final.'); return; }
    viewingExam.value = true; mobileSidebarOpen.value = false;
}

function handleMaterialClick(mat: Material) {
    try {
        if (mat.external_url) { window.open(mat.external_url, '_blank', 'noopener,noreferrer'); return; }
        if (mat.file_path) {
            let path = mat.file_path.replace(/^public\//, '');
            if (!path.startsWith('/storage/') && !path.startsWith('storage/')) { path = '/storage/' + path; } else if (path.startsWith('storage/')) { path = '/' + path; }
            window.open(path, '_blank', 'noopener,noreferrer');
        }
    } catch (e) { console.error('[iieEdu] Error downloading material:', e); }
}

function toggleMobileSidebar(tab: 'curriculum' | 'chat') { activeSidebarTab.value = tab; mobileSidebarOpen.value = true; }

watch(() => page.props.flash?.error, (newVal) => { if (newVal) { localErrorMessage.value = newVal; showLocalError.value = true; } }, { immediate: true });
</script>

<template>
    <Head :title="`${currentLesson?.title || 'Aula Virtual'} - ${course.title}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-[calc(100svh-4rem)] bg-background text-on-background selection:bg-primary/20 selection:text-primary flex flex-col overflow-hidden">
            <ClassroomHeader :course="course" :current-lesson="currentLesson" :current-lesson-index="currentLessonIndex" :all-lessons-count="allLessonsCount" :prev-lesson-id="prevLessonId" :next-lesson-id="nextLessonId" :comments-count="comments.length" :active-sidebar-tab="activeSidebarTab" @update:active-sidebar-tab="activeSidebarTab = $event" @toggle-mobile-sidebar="toggleMobileSidebar" />
            <main class="flex-1 flex flex-col lg:flex-row overflow-hidden bg-background relative">
                <div class="flex-1 overflow-y-auto custom-scrollbar bg-white relative rounded-br-[4rem] shadow-2xl">
                    <ClassroomExamSection v-if="viewingExam" :course="course" :quiz-stats="quizStats" :local-all-completed="progress.localAllCompleted.value" :local-completed-length="progress.localCompleted.value.length" :all-lessons-count="allLessonsCount" />
                    <ClassroomContent v-else v-model:active-tab="activeTab" :current-lesson="currentLesson" :lesson-state="live.getLessonState(currentLesson)" :video-id="player.getYoutubeId(currentLesson?.video_url)" :countdown="live.countdown.value" @complete-lesson="progress.completeLesson(currentLesson?.id ?? 0)" @enter-forum="toggleMobileSidebar('chat')">
                        <template #resources><ClassroomResources :materials="currentLesson?.materials" @download="handleMaterialClick" /></template>
                    </ClassroomContent>
                    <div class="h-20 lg:h-32"></div>
                    <div class="h-24 lg:hidden"></div>
                    <button @click="toggleMobileSidebar('chat')" class="lg:hidden fixed bottom-20 right-4 z-30 w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-2xl active:scale-95 transition-all">
                        <MessageSquare class="w-6 h-6" />
                        <span v-if="comments.length > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center">{{ comments.length }}</span>
                    </button>
                </div>
                <ClassroomSidebar v-model:new-comment="commentsApi.newComment.value" v-model:mobile-sidebar-open="mobileSidebarOpen" v-model:replying-to="commentsApi.replyingTo.value" v-model:editing-comment="commentsApi.editingComment.value" :course="course" :current-lesson="currentLesson" :current-lesson-index="currentLessonIndex" :all-lessons-count="allLessonsCount" :local-completed="progress.localCompleted.value" :local-all-completed="progress.localAllCompleted.value" :active-sidebar-tab="activeSidebarTab" :comments="comments" :current-user="currentUser" :quiz-stats="quizStats" :time-since="commentsApi.timeSince" @update:active-sidebar-tab="activeSidebarTab = $event" @post-comment="commentsApi.postComment(currentLesson?.id ?? 0)" @toggle-like="commentsApi.toggleLike" @delete-comment="commentsApi.deleteComment" @update-comment="commentsApi.updateComment" @open-exam="handleOpenExam" />
            </main>
            <ClassroomErrorModal :show="showLocalError" :message="localErrorMessage" @close="showLocalError = false" />
        </div>
        <BottomNav active="courses" />
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(87, 87, 42, 0.08); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(87, 87, 42, 0.15); }
.prose { max-width: none; }
iframe { width: 100%; height: 100%; }
:deep(.plyr) { --plyr-color-main: #57572A; --plyr-video-background: #000; --plyr-menu-background: #1A1C19; --plyr-menu-color: #fff; --plyr-video-control-background-hover: rgba(87,87,42,0.5); border-radius: 0; height: 100%; width: 100%; }
@media (min-width: 768px) { :deep(.plyr) { border-radius: 0 0 4rem 0; } }
:deep(.plyr__video-wrapper) { height: 100% !important; padding-bottom: 0 !important; }
:deep(.plyr iframe) { pointer-events: auto; }
:deep(.plyr__poster) { background-size: cover; }
main, aside, section { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
