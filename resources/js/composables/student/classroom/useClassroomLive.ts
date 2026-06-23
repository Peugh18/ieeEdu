import type { Lesson } from '@/types/classroom';
import { onUnmounted, ref } from 'vue';

export function useClassroomLive() {
    const now = ref(new Date());
    const countdown = ref('--:--:--');
    let timerInterval: ReturnType<typeof setInterval> | null = null;
    let nowInterval: ReturnType<typeof setInterval> | null = null;

    const parseSafeDate = (dateStr?: string) => {
        if (!dateStr) return null;
        const normalized = dateStr.replace('Z', '').replace('z', '').replace('T', ' ').substring(0, 19);
        const d = new Date(normalized);
        return isNaN(d.getTime()) ? null : d;
    };

    function getLessonState(lesson: Lesson | null) {
        if (!lesson) return 'recorded';
        if (lesson.video_url) return 'recorded';

        if (lesson.content_type === 'live' || lesson.start_time) {
            const startDate = parseSafeDate(lesson.start_time);
            if (!startDate) return 'recorded';

            const startTime = startDate.getTime();
            const endDate = parseSafeDate(lesson.end_time);

            // If no end time, assume 3 hours duration
            const endTime = endDate ? endDate.getTime() : startTime + 3 * 60 * 60 * 1000;
            const currentTime = now.value.getTime();

            if (currentTime < startTime) return 'scheduled';
            if (currentTime >= startTime && currentTime <= endTime) return 'live';
            return 'processing';
        }

        return 'recorded';
    }

    function startCountdown(lesson: Lesson | null) {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        const state = getLessonState(lesson);
        if (state === 'scheduled' && lesson?.start_time) {
            const startDate = parseSafeDate(lesson.start_time);
            if (!startDate) return;

            const target = startDate.getTime();

            const update = () => {
                const currentTime = new Date().getTime();
                const diff = target - currentTime;

                if (diff <= 0) {
                    countdown.value = 'ENTRANDO...';
                    if (timerInterval) {
                        clearInterval(timerInterval);
                        timerInterval = null;
                    }
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60))
                    .toString()
                    .padStart(2, '0');
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
                    .toString()
                    .padStart(2, '0');
                const seconds = Math.floor((diff % (1000 * 60)) / 1000)
                    .toString()
                    .padStart(2, '0');
                countdown.value = `${hours}:${minutes}:${seconds}`;
            };

            update();
            timerInterval = setInterval(update, 1000);
        }
    }

    function startNowClock() {
        if (!nowInterval) {
            nowInterval = setInterval(() => {
                now.value = new Date();
            }, 1000);
        }
    }

    onUnmounted(() => {
        if (timerInterval) clearInterval(timerInterval);
        if (nowInterval) clearInterval(nowInterval);
    });

    return {
        now,
        countdown,
        parseSafeDate,
        getLessonState,
        startCountdown,
        startNowClock,
    };
}
