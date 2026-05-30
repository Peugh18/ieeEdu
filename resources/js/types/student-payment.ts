import type { BookOrder } from '@/types/book-order';

export interface StudentPayment {
    id: number;
    course: {
        id: number;
        title: string;
        slug: string;
        image: string | null;
    } | null;
    book: {
        id: number;
        title: string;
        cover_image: string | null;
    } | null;
    book_order?: BookOrder | null;
    amount: number;
    status: 'pendiente' | 'en_revision' | 'aprobado' | 'rechazado';
    comprobante: string | null;
    subscription_type: string | null;
    created_at: string;
}
