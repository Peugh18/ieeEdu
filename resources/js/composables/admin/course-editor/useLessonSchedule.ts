import { formatLocalTime, getLessonStatus } from '@/lib/lesson-datetime';
import { onUnmounted, ref } from 'vue';

export function useLessonSchedule() {
    const now = ref(new Date());
    const intervalId = window.setInterval(() => {
        now.value = new Date();
    }, 30000);

    onUnmounted(() => clearInterval(intervalId));

    function lessonStatus(lesson: Parameters<typeof getLessonStatus>[0]) {
        return getLessonStatus(lesson, now.value);
    }

    return { lessonStatus, formatLocalTime };
}
