import type { CourseEditorCourse } from '@/types/course-editor';
import { computed, ref } from 'vue';

export function useCourseStudents(course: CourseEditorCourse) {
    const studentFilter = ref<'all' | 'certified' | 'pending'>('all');

    const enrolledStudentsWithCerts = computed(() => {
        if (!course.enrollments) return [];
        return course.enrollments.map((enrollment) => {
            const user = enrollment.user;
            const cert = course.certificates?.find((c) => c.user_id === user?.id);
            return {
                id: enrollment.id,
                user,
                has_certificate: !!cert,
                certificate: cert || null,
                enrolled_at: enrollment.created_at,
            };
        });
    });

    const filteredStudents = computed(() => {
        let list = enrolledStudentsWithCerts.value;
        if (studentFilter.value === 'certified') {
            list = list.filter((s) => s.has_certificate);
        } else if (studentFilter.value === 'pending') {
            list = list.filter((s) => !s.has_certificate);
        }
        return list;
    });

    return { studentFilter, filteredStudents };
}
