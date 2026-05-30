export interface Material {
    id: number;
    title: string;
    kind: string;
    external_url?: string;
    file_path?: string;
}

export interface Lesson {
    id: number;
    title: string;
    description: string;
    content_type: 'video' | 'live' | 'text';
    video_url?: string;
    live_link?: string;
    start_time?: string;
    end_time?: string;
    materials?: Material[];
    module_id?: number;
}

export interface Module {
    id: number;
    title: string;
    lessons: Lesson[];
}

import type { Quiz } from './exam';

export interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    modules: Module[];
    lessons: Lesson[];
    quizzes: Quiz[];
    whatsapp_link?: string;
}

export interface QuizStats {
    quiz_id: number;
    passed: boolean;
    attempts_left: number;
    max_attempts: number;
    certificate_url: string | null;
}
