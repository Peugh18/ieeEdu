import type { CourseEditorCategory, CourseEditorCourse } from '@/types/course-editor';
import { ref, watch } from 'vue';
import { useCourseCurriculum } from './useCourseCurriculum';
import { useCourseEditorNotifications } from './useCourseEditorNotifications';
import { useCourseForm } from './useCourseForm';
import { useCourseQuizzes } from './useCourseQuizzes';
import { useCourseStudents } from './useCourseStudents';
import { useLessonSchedule } from './useLessonSchedule';

export function useCourseEditor(course: CourseEditorCourse, categories: CourseEditorCategory[]) {
    const activeTab = ref('general');
    const notifications = useCourseEditorNotifications();
    const lessonsCount = ref(course.lessons?.length ?? 0);

    const formApi = useCourseForm(course, notifications, lessonsCount);

    const curriculum = useCourseCurriculum(
        course.id,
        course.modules ?? [],
        course.lessons ?? [],
        formApi.isMasterclass,
        notifications.notifySuccess,
    );

    watch(
        () => curriculum.lessons.value.length,
        (count) => {
            lessonsCount.value = count;
        },
        { immediate: true },
    );

    const quizzesApi = useCourseQuizzes(course.id, course.quizzes ?? []);
    const studentsApi = useCourseStudents(course);
    const schedule = useLessonSchedule();

    return {
        course,
        categories,
        activeTab,
        notifications,
        form: formApi.form,
        isMasterclass: formApi.isMasterclass,
        canPublish: formApi.canPublish,
        instructorPreviewUrl: formApi.instructorPreviewUrl,
        courseImagePreviewUrl: formApi.courseImagePreviewUrl,
        saveCourse: formApi.saveCourse,
        publishCourse: formApi.publishCourse,
        hideCourse: formApi.hideCourse,
        curriculum,
        quizzes: quizzesApi,
        students: studentsApi,
        schedule,
    };
}
