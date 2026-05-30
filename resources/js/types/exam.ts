export interface Answer {
    id: number;
    answer_text: string;
}

export interface Question {
    id: number;
    question: string;
    answers: Answer[];
}

export interface Quiz {
    id: number;
    title: string;
    course?: { 
        title: string;
        slug: string;
    };
    time_limit: number;
    max_attempts: number;
    questions: Question[];
}

export interface ExamResult {
    status: 'aprobado' | 'reprobado';
    score: number;
    passing_score: number;
    certificate_url: string | null;
}
