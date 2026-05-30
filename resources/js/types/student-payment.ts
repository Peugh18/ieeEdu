export interface StudentPayment {
    id: number;
    course: {
        id: number;
        title: string;
        slug: string;
        image: string | null;
    } | null;
    amount: number;
    status: 'pendiente' | 'en_revision' | 'aprobado' | 'rechazado';
    comprobante: string | null;
    subscription_type: string | null;
    created_at: string;
}
