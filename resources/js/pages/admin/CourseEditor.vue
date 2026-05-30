<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CourseEditorHeader from '@/components/admin/courses/editor/CourseEditorHeader.vue';
import CourseEditorNotifications from '@/components/admin/courses/editor/CourseEditorNotifications.vue';
import CourseEditorTabNav from '@/components/admin/courses/editor/CourseEditorTabNav.vue';
import CourseEditorCurriculumTab from '@/components/admin/courses/editor/tabs/CourseEditorCurriculumTab.vue';
import CourseEditorDetailsTab from '@/components/admin/courses/editor/tabs/CourseEditorDetailsTab.vue';
import CourseEditorExamsTab from '@/components/admin/courses/editor/tabs/CourseEditorExamsTab.vue';
import CourseEditorGeneralTab from '@/components/admin/courses/editor/tabs/CourseEditorGeneralTab.vue';
import CourseEditorInstructorTab from '@/components/admin/courses/editor/tabs/CourseEditorInstructorTab.vue';
import CourseEditorPricingTab from '@/components/admin/courses/editor/tabs/CourseEditorPricingTab.vue';
import CourseEditorStudentsTab from '@/components/admin/courses/editor/tabs/CourseEditorStudentsTab.vue';
import { useCourseEditor } from '@/composables/admin/course-editor/useCourseEditor';
import type { CourseEditorCategory, CourseEditorCourse } from '@/types/course-editor';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    course: CourseEditorCourse;
    categories: CourseEditorCategory[];
}>();

const editor = useCourseEditor(props.course, props.categories);

const {
    notifications: {
        showSuccess: showSuccessNotification,
        showError: showErrorNotification,
        showPublishSuccess: showPublishSuccessNotification,
    },
    form,
    activeTab,
    isMasterclass,
    canPublish,
    instructorPreviewUrl,
    courseImagePreviewUrl,
    saveCourse,
    publishCourse,
    hideCourse,
    curriculum,
    quizzes,
    students: { studentFilter, filteredStudents },
    schedule: { lessonStatus: getLessonStatus, formatLocalTime },
} = editor;

const studentsList = computed(() => filteredStudents.value);
const course = props.course;
const categories = props.categories;
</script>

<template>
    <Head title="Editor de Curso" />
    <AppLayout>
        <CourseEditorNotifications
            :show-success="showSuccessNotification"
            :show-publish-success="showPublishSuccessNotification"
            :show-error="showErrorNotification"
        />

        <div class="space-y-6 max-w-7xl mx-auto bg-surface p-4 sm:p-8 rounded-[2.5rem]">
            <CourseEditorHeader
                :status="form.status"
                :module-count="curriculum.modules.length"
                :lesson-count="curriculum.lessons.length"
                :processing="form.processing"
                :can-publish="canPublish"
                @hide="hideCourse"
                @save="saveCourse"
                @publish="publishCourse"
            />

            <CourseEditorTabNav
                :active-tab="activeTab"
                :lesson-count="curriculum.lessons.length"
                @change="activeTab = $event"
            />

            <div class="grid grid-cols-1 gap-8 w-full mt-4">
                <CourseEditorGeneralTab :show="activeTab === 'general'" :form="form" />

                <CourseEditorPricingTab
                    :show="activeTab === 'pricing'"
                    :form="form"
                    :categories="categories"
                    :course="course"
                    :is-masterclass="isMasterclass"
                    :course-image-preview-url="courseImagePreviewUrl"
                />

                <CourseEditorDetailsTab :show="activeTab === 'details'" :form="form" />

                <CourseEditorInstructorTab
                    :show="activeTab === 'instructor'"
                    :form="form"
                    :course="course"
                    :instructor-preview-url="instructorPreviewUrl"
                />

                <CourseEditorCurriculumTab
                    :show="activeTab === 'curriculum'"
                    :is-masterclass="isMasterclass"
                    :curriculum="curriculum"
                    :get-lesson-status="getLessonStatus"
                    :format-local-time="formatLocalTime"
                />

                <CourseEditorExamsTab :show="activeTab === 'exams'" :quizzes="quizzes" />

                <CourseEditorStudentsTab
                    v-model:filter="studentFilter"
                    :show="activeTab === 'students'"
                    :filtered-students="studentsList"
                />
            </div>
        </div>
    </AppLayout>
</template>
