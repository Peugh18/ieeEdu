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

export interface CertificateTemplate {
    id?: number;
    student_name_X?: number;
    student_name_Y?: number;
    student_name_font_size?: number;
    course_title_X?: number;
    course_title_Y?: number;
    course_title_font_size?: number;
    issue_date_X?: number;
    issue_date_Y?: number;
    issue_date_font_size?: number;
    certificate_code_X?: number;
    certificate_code_Y?: number;
    certificate_code_font_size?: number;
    font_color?: string;
    font_family?: string;
    template_image?: string;
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
    instructor_name?: string;
    instructor_title?: string;
    instructor_image?: string;
    certificate_template?: CertificateTemplate;
}

export interface QuizStats {
    quiz_id: number;
    passed: boolean;
    attempts_left: number;
    max_attempts: number;
    certificate_url: string | null;
    certificate_code?: string;
    certificate_date?: string;
}
