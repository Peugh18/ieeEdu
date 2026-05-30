import type { CourseEditorCategory, CourseEditorCourse } from '@/types/course-editor';
import { reactive, ref, watch } from 'vue';
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

    // reactive() desenvuelve los Ref internos para que los templates accedan arrays/objetos directamente
    const curriculum = reactive(
        useCourseCurriculum(
            course.id,
            course.modules ?? [],
            course.lessons ?? [],
            formApi.isMasterclass,
            notifications.notifySuccess,
        ),
    );

    watch(
        () => curriculum.lessons.length,
        (count) => {
            lessonsCount.value = count;
        },
        { immediate: true },
    );

    const quizzes = reactive(useCourseQuizzes(course.id, course.quizzes ?? []));
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
        quizzes,
        students: studentsApi,
        schedule,
    };
}
