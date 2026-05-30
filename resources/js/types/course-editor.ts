import type { CourseLesson, CourseMaterial, CourseModule, Question, Quiz } from '@/types/course';

export type CourseEditorTab =
    | 'general'
    | 'pricing'
    | 'details'
    | 'instructor'
    | 'curriculum'
    | 'exams'
    | 'students';

export type CourseStatus = 'BORRADOR' | 'PUBLICADO' | 'OCULTO';

export interface CourseEditorCategory {
    id: number;
    name: string;
}

export interface CourseEditorQuiz extends Quiz {
    questions: Question[];
    attempts?: Array<Record<string, unknown>>;
}

export interface CourseEditorCourse {
    id: number;
    title: string;
    description?: string;
    price: number;
    discount?: number | null;
    sale_price?: number | null;
    type: string;
    status: CourseStatus;
    category_id?: number | string | null;
    certificate_enabled?: boolean;
    image?: string;
    instructor_name?: string;
    instructor_title?: string;
    instructor_bio?: string;
    instructor_image?: string;
    start_date?: string;
    start_time?: string;
    class_hours?: number | string;
    whatsapp_link?: string;
    objectives?: string;
    requirements?: string;
    modules?: CourseModule[];
    lessons?: CourseLesson[];
    quizzes?: CourseEditorQuiz[];
    enrollments?: Array<{ id: number; user?: { id: number; name: string; email: string }; created_at: string }>;
    certificates?: Array<{ user_id: number }>;
}

export interface NewLessonDraft {
    module_id: number | null;
    title: string;
    description: string;
    content_type: 'video' | 'live' | 'event' | 'text';
    video_url: string;
    live_link: string;
    start_time: string;
    end_time: string;
}

export interface NewMaterialDraft {
    title: string;
    external_url: string;
    file: File | null;
}

export type MaterialsByLesson = Record<number, CourseMaterial[]>;
