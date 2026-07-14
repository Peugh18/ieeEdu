export interface PublicationBook {
    id: number;
    title: string;
    price: string | number;
    sale_price?: string | number | null;
    stock?: number | null;
    author: string;
    description: string;
    cover_image: string | null;
    download_url: string | null;
    is_available: boolean;
    payment_status?: string;
    needs_comprobante?: boolean;
    has_pending_payment?: boolean;
    has_approved_purchase?: boolean;
    can_purchase?: boolean;
    is_out_of_stock?: boolean;
    shipping?: {
        status: string;
        label: string;
        tracking_url?: string | null;
        delivery_mode?: string | null;
        pickup_location?: string | null;
        student_note?: string | null;
        carrier?: string | null;
    } | null;
}

export interface PublicationArticle {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    download_url: string;
}

export interface PublicationBanner {
    heading: string | null;
    subheading: string | null;
    image_path: string | null;
    button_text: string | null;
    button_link: string | null;
    show_text: boolean;
}
