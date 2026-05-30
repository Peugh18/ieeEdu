// Types for public landing page data
export interface HomeSlide {
  image: string;
  title?: string;
  subtitle?: string;
}

export interface DynamicCourse {
  id: number;
  title: string;
  slug: string;
  type: 'en vivo' | 'grabado' | 'evento' | 'masterclass';
  thumbnail?: string;
  image?: string;
  short_description?: string;
  video_url?: string;
  price: number;
  sale_price?: number;
  category?: { name: string } | string;
  promotion_title?: string;
  start_date?: string;
  start_time?: string;
}

export interface TeaserBook {
  id: number;
  title: string;
  author: string;
  cover_image?: string;
  category?: string;
  price?: string | number;
  download_url?: string;
  file_path?: string;
}

export interface TeaserArticle {
  id: number;
  title: string;
  excerpt: string;
  image: string;
  url: string;
}
