export interface PublicationBook {
    id: number;
    category: 'Libro' | 'Libro en camino' | 'Guía';
    title: string;
    price: string | number;
    author: string;
    description: string;
    cover_image: string | null;
    file_path: string | null;
    download_url: string | null;
    is_available: boolean;
}

export interface PublicationArticle {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    file_path?: string | null;
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
