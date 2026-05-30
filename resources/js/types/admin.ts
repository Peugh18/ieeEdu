export interface UserListItem {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
    enrollments_count: number;
    created_at: string;
}

export interface PaymentListItem {
    id: number;
    status: string;
    amount: number;
    comprobante: string | null;
    created_at: string;
    user: { 
        id?: number; 
        name: string; 
        email: string; 
    };
    course: { 
        id?: number; 
        title: string; 
    } | null;
    subscription_type: string | null;
}
