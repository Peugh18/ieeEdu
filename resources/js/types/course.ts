/**
 * Shared Course types used across the application.
 * Import this instead of re-declaring Course interfaces in each page/component.
 */

export interface CourseCategory {
    id?: number;
    name: string;
}

export interface Course {
    id: number | string;
    slug: string;
    title: string;
    description?: string;
    image?: string;
    category?: CourseCategory | string;
    type: 'en vivo' | 'grabado' | 'evento' | 'masterclass' | 'hibrido';
    price: number;
    sale_price?: number;
    promotion_title?: string;
    start_date?: string;
    start_time?: string;
    duration_weeks?: number;
    class_hours?: number;
    instructor_name?: string;
    instructor_image?: string;
    whatsapp_link?: string;
}

export interface CourseMaterial {
    id: number;
    title: string;
    kind: 'pdf' | 'excel' | 'url' | 'other';
    file_path?: string;
    external_url?: string;
}

export interface CourseModule {
    id: number;
    title: string;
    description?: string;
    sort_order: number;
    lessons?: CourseLesson[];
}

export interface CourseLesson {
    id: number;
    title: string;
    description?: string;
    content_type: 'video' | 'live' | 'event' | 'text';
    video_url?: string;
    live_link?: string;
    start_time?: string;
    end_time?: string;
    sort_order: number;
    materials?: CourseMaterial[];
    completed?: boolean;
}

export interface Quiz {
    id: number;
    title: string;
    time_limit: number;
    max_attempts: number;
    minimum_score: number;
    randomize_questions?: boolean;
    course?: { title: string };
    questions?: Question[];
    current_attempt?: number;
}

export interface Question {
    id: number;
    question: string;
    type: 'multiple_choice' | 'true_false';
    points: number;
    answers: Answer[];
}

export interface Answer {
    id: number;
    answer_text: string;
    is_correct?: boolean;
}

export interface Certificate {
    id: number;
    course_title: string;
    issue_date: string;
    code: string;
    download_url: string;
}

export interface Enrollment {
    id: number;
    course_id: number;
    user_id: number;
    progress: number;
    last_lesson_id?: number;
    enrolled_at: string;
    completed_at?: string;
    passed?: boolean;
    subscription_granted?: boolean;
    subscription_active?: boolean;
    course?: Course;
}

export interface CommentLike {
    id: number;
    user_id: number;
    lesson_comment_id: number;
    created_at: string;
}

export interface LessonComment {
    id: number;
    user_id: number;
    user_name: string;
    content: string;
    parent_id?: number;
    is_edited: boolean;
    likes_count: number;
    liked_by_me: boolean;
    created_at: string;
    replies?: LessonComment[];
}

export interface DashboardStats {
    active_courses: number;
    completed_courses: number;
    upcoming_classes: number;
    available_exams: number;
    average_score: number;
    certificate_count: number;
}

export interface ContinueLearning {
    course_title: string;
    course_slug: string;
    lesson_title: string;
    lesson_id: number;
    progress: number;
    image: string;
}

export interface Recommendation {
    id: number;
    title: string;
    slug: string;
    image: string;
    category: { name: string };
}

export interface NextLiveClass {
    id: number;
    title: string;
    course_title: string;
    start_time: string;
    start_time_human: string;
    course_slug: string;
}

export interface CartItem {
    id: number | string;
    title: string;
    slug: string;
    price: number;
    category?: string | { name: string };
    image?: string;
    type: string;
}

export interface Book {
    id: number;
    category: string;
    title: string;
    price: number;
    author: string;
    description: string;
    cover_image: string;
    file_path?: string;
    download_url?: string;
    is_available: boolean;
}

export interface Article {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string;
    download_url: string;
}

export interface Banner {
    heading?: string;
    subheading?: string;
    image_path?: string;
    button_text?: string;
    button_link?: string;
    position?: string;
    whatsapp_number?: string;
    contact_email?: string;
    contact_address?: string;
}

export interface ConsultancyFormData {
    name: string;
    email: string;
    phone: string;
    company?: string;
    area?: string;
    message: string;
}

export interface Payment {
    id: number;
    amount: number;
    course_id?: number;
    status: string;
    created_at: string;
    user?: { id: number; name: string; email: string };
    course?: { id: number; title: string };
}

export interface Subscription {
    id: number;
    name: string;
    description: string;
    price: number;
    period: 'monthly' | 'quarterly' | 'annual';
    is_active: boolean;
    created_at: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    telefono?: string;
    role: 'admin' | 'usuario';
    status: 'activo' | 'inactivo';
    has_subscription?: boolean;
}

export interface FlashMessages {
    success?: string;
    error?: string;
    exam_result?: any;
}

export interface SharedData {
    auth: {
        user: User | null;
    };
    flash: FlashMessages;
    appName: string;
    quote?: string;
    ziggy: any;
}

export type BreadcrumbItem = {
    title: string;
    href: string;
};
