export interface SummaryStats {
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

export interface Certificate {
    id: number;
    course_title: string;
    issue_date: string;
    code: string;
    download_url: string;
}

export interface NextLiveClass {
    id: number;
    title: string;
    course_title: string;
    start_time: string;
    start_time_human: string;
    course_slug: string;
}
