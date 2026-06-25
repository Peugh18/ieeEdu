import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

export function useClassroomProgress(
    courseId: number,
    userId: number | string,
    completedLessonsServer: number[],
    allLessonsCompletedServer: boolean,
    allLessonsCount: number,
) {
    const progressKey = computed(() => `iie_progress_${courseId}_${userId ?? 'guest'}`);

    function loadFromStorage(): number[] {
        try {
            const raw = localStorage.getItem(progressKey.value);
            if (!raw) return [];
            return JSON.parse(raw) as number[];
        } catch {
            return [];
        }
    }

    function saveToStorage(ids: number[]): void {
        try {
            localStorage.setItem(progressKey.value, JSON.stringify(ids));
        } catch {}
    }

    function buildInitialCompleted(): number[] {
        const fromServer = completedLessonsServer.map((id) => Number(id));
        const fromStorage = loadFromStorage();
        const merged = [...fromServer];
        fromStorage.forEach((id) => {
            if (!merged.includes(id)) merged.push(id);
        });
        return merged;
    }

    const localCompleted = ref<number[]>(buildInitialCompleted());
    const localAllCompleted = ref(allLessonsCompletedServer || localCompleted.value.length >= allLessonsCount);

    watch(
        () => completedLessonsServer,
        (serverCompleted) => {
            const current = loadFromStorage();
            const serverIds = serverCompleted.map((id) => Number(id));
            serverIds.forEach((id) => {
                if (!current.includes(id)) current.push(id);
            });
            localCompleted.value.forEach((id) => {
                if (!current.includes(id)) current.push(id);
            });
            localCompleted.value = current;
            saveToStorage(current);
            if (current.length >= allLessonsCount && allLessonsCount > 0) {
                localAllCompleted.value = true;
            }
        },
        { deep: true },
    );

    watch(
        () => allLessonsCompletedServer,
        (newVal) => {
            if (newVal === true) localAllCompleted.value = true;
        },
    );

    async function completeLesson(lessonId: number) {
        if (!localCompleted.value.includes(lessonId)) {
            localCompleted.value.push(lessonId);
            saveToStorage(localCompleted.value);
            if (localCompleted.value.length >= allLessonsCount) {
                localAllCompleted.value = true;
            }
            router.post(
                route('student.classroom.progress'),
                { lesson_id: lessonId },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        console.log('[iieEdu] Progress synced with server');
                    },
                },
            );
        }
    }

    function syncMissingProgress() {
        const missingOnServer = localCompleted.value.filter((id) => !completedLessonsServer.map(Number).includes(Number(id)));

        if (missingOnServer.length > 0) {
            console.log('Synchronizing missing progress to server...', missingOnServer);
            axios
                .post(route('student.classroom.progress'), {
                    lesson_ids: missingOnServer,
                })
                .then(() => {
                    console.log('Progress synchronized successfully');
                })
                .catch((err) => {
                    console.error('Failed to sync progress:', err);
                });
        }
    }

    return {
        localCompleted,
        localAllCompleted,
        completeLesson,
        syncMissingProgress,
    };
}
