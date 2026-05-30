export interface EnrollmentItem {
    id: number;
    course_id: number;
    enrolled_at: string;
    completed_at: string | null;
    passed: boolean | null;
    course: { id: number; title: string; image: string | null; type: string } | null;
}

export interface PaymentItem {
    id: number;
    status: string;
    amount: number;
    created_at: string;
    course: { id: number; title: string } | null;
}

export interface UserDetail {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
    profile_photo_url?: string;
    created_at: string;
    enrollments: EnrollmentItem[];
    payments: PaymentItem[];
}
