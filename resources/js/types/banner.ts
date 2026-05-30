import type { LucideIcon } from 'lucide-vue-next';

export interface DbBanner {
    section: string;
    order: number;
    image_path: string | null;
    heading: string | null;
    subheading: string | null;
    button_text: string | null;
    button_link: string | null;
    show_text: boolean | number;
    whatsapp_number?: string | null;
    contact_email?: string | null;
    contact_address?: string | null;
}

export interface BannerSlide {
    section: string;
    order: number;
    image_path: string | null;
    imagePreview: string | null;
    file: File | null;
    heading: string;
    subheading: string;
    button_text: string;
    button_link: string;
    show_text: boolean;
    whatsapp_number?: string;
    contact_email?: string;
    contact_address?: string;
}

export interface BannerSection {
    id: string;
    title: string;
    icon: LucideIcon;
    isCarousel: boolean;
    slides: BannerSlide[];
}

export interface SectionSpec {
    minWidth: number;
    minHeight: number;
    idealW: number;
    idealH: number;
    label: string;
}
