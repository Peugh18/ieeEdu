import type { Course, Lesson } from '@/types/classroom';
import { onMounted, watch } from 'vue';
import { useClassroomComments } from './useClassroomComments';
import { useClassroomLive } from './useClassroomLive';
import { useClassroomPlayer } from './useClassroomPlayer';
import { useClassroomProgress } from './useClassroomProgress';

export function useClassroom(
    course: Course,
    userId: number | string,
    currentLesson: Lesson | null,
    completedLessonsServer: number[],
    allLessonsCompletedServer: boolean,
    allLessonsCount: number,
) {
    const progress = useClassroomProgress(course.id, userId, completedLessonsServer, allLessonsCompletedServer, allLessonsCount);

    const player = useClassroomPlayer(() => {
        if (currentLesson) {
            progress.completeLesson(currentLesson.id);
        }
    });

    const comments = useClassroomComments();
    const live = useClassroomLive();

    watch(
        () => currentLesson?.id,
        () => {
            live.startCountdown(currentLesson);
        },
        { immediate: true },
    );

    onMounted(() => {
        progress.syncMissingProgress();
        live.startNowClock();

        player.initPlyr((currentTime, duration) => {
            const percentage = currentTime / duration;
            if (percentage >= 0.8 && currentLesson) {
                progress.completeLesson(currentLesson.id);
            }
        });
    });

    return {
        progress,
        player,
        comments,
        live,
    };
}
