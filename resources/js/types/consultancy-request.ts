export interface ConsultancyRequest {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    company: string | null;
    area: string;
    message: string;
    status: 'pendiente' | 'en_contacto' | 'cerrado';
    created_at: string;
}

export interface ConsultancyStats {
    total: number;
    pending: number;
    contacted: number;
    resolved: number;
}

export interface ConsultancyFilters {
    search?: string;
    status?: string;
}
