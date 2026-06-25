export interface LessonStatusBadge {
    label: string;
    class: string;
}

export function normalizeLessonDateTime(dateStr: string): string {
    return dateStr.replace('z', '').replace('Z', '').replace('T', ' ').substring(0, 19);
}

export function formatLocalTime(dateStr: string): string {
    if (!dateStr) return '';
    return new Date(normalizeLessonDateTime(dateStr)).toLocaleString();
}

export function getLessonStatus(
    lesson: {
        video_url?: string | null;
        content_type: string;
        start_time?: string | null;
        end_time?: string | null;
    },
    now: Date = new Date(),
): LessonStatusBadge {
    if (lesson.video_url) {
        return { label: 'Grabada', class: 'bg-blue-100 text-blue-700' };
    }
    if (lesson.content_type !== 'live') {
        return { label: 'Texto/Otro', class: 'bg-gray-100 text-gray-600' };
    }
    if (!lesson.start_time) {
        return { label: 'Pendiente Programar', class: 'bg-amber-100 text-amber-700' };
    }

    const start = new Date(normalizeLessonDateTime(lesson.start_time)).getTime();
    const end = lesson.end_time ? new Date(normalizeLessonDateTime(lesson.end_time)).getTime() : start + 3 * 60 * 60 * 1000;
    const currentTime = now.getTime();

    if (currentTime < start) {
        return { label: 'Programada', class: 'bg-indigo-100 text-indigo-700' };
    }
    if (currentTime >= start && currentTime <= end) {
        return { label: '¡EN VIVO!', class: 'bg-emerald-100 text-emerald-700 animate-pulse' };
    }
    return { label: 'En Procesamiento', class: 'bg-orange-100 text-orange-700' };
}
