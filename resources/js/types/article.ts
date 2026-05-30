export interface Article {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    file_path?: string | null;
    download_url: string;
}
