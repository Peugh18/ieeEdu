export interface BookOrderShipping {
    status: string;
    label: string;
    tracking_url?: string | null;
    tracking_code?: string | null;
    delivery_mode?: string | null;
    pickup_location?: string | null;
    student_note?: string | null;
    carrier?: string | null;
    shipping_address?: string | null;
    district?: string | null;
    province?: string | null;
    department?: string | null;
    shipping_phone?: string | null;
}

export interface BookOrder {
    id: number;
    shipping_status: string;
    shipping_address: string | null;
    district: string | null;
    province: string | null;
    department: string | null;
    shipping_phone: string | null;
    delivery_mode: 'home' | 'pickup' | null;
    pickup_location: string | null;
    carrier: string | null;
    tracking_url: string | null;
    tracking_code: string | null;
    student_note: string | null;
    admin_notes: string | null;
    shipped_at: string | null;
    delivered_at: string | null;
    book?: { id: number; title: string; cover_image: string | null; price: number | string };
    user?: { id: number; name: string; email: string; telefono: string | null };
    payment?: { id: number; status: string; amount: number; comprobante: string | null; created_at: string };
}
