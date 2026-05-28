<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Book {
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

interface Article {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    file_path?: string | null;
    download_url: string;
}

const props = defineProps<{
    books: Book[];
    articles: Article[];
    isDashboard?: boolean;
    banner?: {
        heading: string | null;
        subheading: string | null;
        image_path: string | null;
        button_text: string | null;
        button_link: string | null;
        show_text: boolean;
    } | null;
}>();

// Dynamic hero values with elegant fallbacks
const heroHeading = computed(() => props.banner?.heading || 'Nuestras Publicaciones Especializadas');
const heroSubheading = computed(() => props.banner?.subheading || 'Acceda a nuestra biblioteca de investigación, libros y artículos técnicos para su desarrollo profesional.');
const heroBg = computed(() => props.banner?.image_path || '/images/landing/publications_hero.png');
const showText = computed(() => props.banner?.show_text !== false);

const currentTab = ref<'libros' | 'articulos'>('libros');
const searchQuery = ref('');

// Client-side pagination state
const booksPage = ref(1);
const articlesPage = ref(1);
const itemsPerPage = 6;

// Reset pages when query changes
watch(searchQuery, () => {
    booksPage.value = 1;
    articlesPage.value = 1;
});

function changeBooksPage(p: number) {
    booksPage.value = p;
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function changeArticlesPage(p: number) {
    articlesPage.value = p;
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Format dates nicely
function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Estimate dynamic reading time based on title length
function estimateReadingTime(title: string) {
    const wpm = 180; // average words per minute reading speed
    const words = title.split(/\s+/).length + 30; // base weight for text reading
    return Math.max(3, Math.ceil(words / wpm) + 2);
}

function getDownloadLink(book: Book) {
    if (book.file_path) return `/storage/${book.file_path}`;
    return book.download_url || undefined;
}

function getArticleDownloadLink(article: Article) {
    if (article.file_path) return `/storage/${article.file_path}`;
    return article.download_url;
}

function getWhatsAppLink(book: Book) {
    const whatsappNumber = '51959166911'; 
    const message = `Hola, estoy interesado en adquirir el material: *${book.title}* que tiene un costo de S/ ${book.price}. ¿Me podrían brindar más información?`;
    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
}

function getBookCoverStyle(id: number) {
    const covers = [
        { bg: 'from-[#2D391B] to-[#171E0E]' }, // Olive / Forest Green
        { bg: 'from-[#14283D] to-[#0A141F]' }, // Midnight Navy Blue
        { bg: 'from-[#4D1A1F] to-[#2B0E11]' }, // Crimson / Burgundy
        { bg: 'from-[#3D3219] to-[#201A0D]' }, // Scholarly Golden Amber
        { bg: 'from-[#242526] to-[#121314]' }  // Premium Deep Charcoal
    ];
    return covers[id % covers.length];
}

// Client-side search filters
const filteredBooks = computed(() => {
    if (!searchQuery.value.trim()) return props.books;
    const query = searchQuery.value.toLowerCase().trim();
    return props.books.filter(
        b => b.title.toLowerCase().includes(query) || (b.author && b.author.toLowerCase().includes(query))
    );
});

const filteredArticles = computed(() => {
    if (!searchQuery.value.trim()) return props.articles;
    const query = searchQuery.value.toLowerCase().trim();
    return props.articles.filter(
        a => a.title.toLowerCase().includes(query) || (a.media && a.media.toLowerCase().includes(query))
    );
});

// Paginating filtered lists
const paginatedBooks = computed(() => {
    const start = (booksPage.value - 1) * itemsPerPage;
    return filteredBooks.value.slice(start, start + itemsPerPage);
});

const booksTotalPages = computed(() => {
    return Math.ceil(filteredBooks.value.length / itemsPerPage);
});

const paginatedArticles = computed(() => {
    const start = (articlesPage.value - 1) * itemsPerPage;
    return filteredArticles.value.slice(start, start + itemsPerPage);
});

const articlesTotalPages = computed(() => {
    return Math.ceil(filteredArticles.value.length / itemsPerPage);
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Publicaciones e Investigación', href: '#' },
];
</script>

<template>
    <Head title="Investigación y Publicaciones - IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28' : 'pt-0']">

                <!-- Hero Banner: Contained Premium Card -->
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                    <div
                        :class="['relative overflow-hidden shadow-2xl transition-all duration-500', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="heroBg" alt="Publicaciones IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>

                        <div v-if="showText" class="relative z-10 h-full px-8 md:px-14 flex flex-col justify-center">
                            <div class="max-w-2xl space-y-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></span>
                                    Investigación de Alto Nivel
                                </span>
                                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                                    {{ heroHeading }}
                                </h1>
                                <p class="text-xs sm:text-sm md:text-base text-white/75 max-w-lg font-light leading-relaxed">
                                    {{ heroSubheading }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs & Interactive Filter Section -->
                <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Modern Tabs (Segmented Control) -->
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12 border-b border-outline-variant/20 pb-6">
                        <div class="inline-flex p-1.5 bg-surface-container rounded-2xl border border-outline-variant/15 shadow-inner">
                            <button 
                                @click="currentTab = 'libros'"
                                :class="[
                                    'px-6 sm:px-8 py-3.5 rounded-xl font-serif text-xs font-bold tracking-[0.05em] uppercase transition-all duration-300 flex items-center gap-2',
                                    currentTab === 'libros' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface'
                                ]"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Libros y Guías
                            </button>
                            <button 
                                @click="currentTab = 'articulos'"
                                :class="[
                                    'px-6 sm:px-8 py-3.5 rounded-xl font-serif text-xs font-bold tracking-[0.05em] uppercase transition-all duration-300 flex items-center gap-2',
                                    currentTab === 'articulos' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface'
                                ]"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                Artículos de Análisis
                            </button>
                        </div>

                        <!-- Real-time Search input -->
                        <div class="relative w-full md:w-80">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input 
                                v-model="searchQuery"
                                type="text"
                                :placeholder="currentTab === 'libros' ? 'Buscar libro o autor...' : 'Buscar artículo o medio...'"
                                class="w-full pl-11 pr-10 py-3 bg-surface-container rounded-2xl border border-outline-variant/15 text-xs focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary focus:bg-surface transition-all text-on-surface placeholder:text-on-surface-variant/50 shadow-sm"
                            />
                            <button 
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full text-on-surface-variant/40 hover:text-on-surface transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ──────────────── TABS CONTENT: BOOKS ──────────────────── -->
                    <div v-if="currentTab === 'libros'">
                        <!-- Empty Search State -->
                        <div v-if="!filteredBooks.length" class="py-24 text-center bg-surface-container-low rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 max-w-3xl mx-auto flex flex-col items-center justify-center p-8 space-y-4">
                            <div class="p-4 rounded-full bg-primary/5 text-primary/40">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="text-xl font-serif font-bold text-on-surface">Sin resultados académicos</h3>
                            <p class="text-xs text-on-surface-variant max-w-sm">No hemos encontrado ningún libro o guía que coincida con la búsqueda "{{ searchQuery }}". Pruebe con otro término.</p>
                            <button @click="searchQuery = ''" class="px-5 py-2 bg-primary text-on-primary text-xs font-bold rounded-xl shadow-md hover:bg-primary/90 transition-all">Mostrar todos</button>
                        </div>

                        <!-- Books Grid -->
                        <div v-else>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                                <article
                                    v-for="book in paginatedBooks"
                                    :key="book.id"
                                    class="flex flex-col bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-2xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 group h-full relative"
                                >
                                    <!-- Book Cover Mockup representation -->
                                    <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container-low shadow-md relative border border-outline-variant/10 mb-5 flex-shrink-0">
                                        <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" :alt="book.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out" />
                                        
                                        <!-- Premium SVG Fallback -->
                                        <div v-else :class="['w-full h-full bg-gradient-to-br p-6 flex flex-col justify-between relative group-hover:scale-105 transition-all duration-700 ease-out select-none', getBookCoverStyle(book.id).bg]">
                                            <!-- Subtle paper texture grid -->
                                            <div class="absolute inset-0 opacity-[0.04] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                                            
                                            <!-- Elegant layout for book cover -->
                                            <div class="flex-1 flex flex-col items-center justify-center py-6 z-10">
                                                <h4 class="font-serif text-[11px] sm:text-xs md:text-sm font-bold text-white text-center leading-snug tracking-tight px-1 line-clamp-4 mb-2">
                                                    {{ book.title }}
                                                </h4>
                                                <div class="w-6 h-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/30 my-2">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                                <span class="text-[8px] font-sans uppercase tracking-widest text-white/40 font-medium">IEE Editorial</span>
                                            </div>
                                            
                                            <div class="flex flex-col items-center pb-2 z-10">
                                                <p class="text-[9px] font-sans font-semibold uppercase tracking-wider text-white/60 text-center line-clamp-1 max-w-full px-2">
                                                    {{ book.author || 'Instituto IEE' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Book Spine Shadow (Subtle vertical crease line mimicking physical binding depth) -->
                                        <div class="absolute inset-y-0 left-0 w-2.5 bg-gradient-to-r from-black/25 via-black/5 to-transparent z-10 pointer-events-none"></div>
                                        <div class="absolute inset-y-0 left-[2.5px] w-[1px] bg-white/10 z-10 pointer-events-none"></div>

                                        <!-- Category Tag -->
                                        <div class="absolute top-3 left-3 z-20">
                                            <span class="px-2.5 py-1 bg-surface/90 backdrop-blur-md rounded-lg text-[9px] font-bold tracking-widest uppercase text-primary border border-outline-variant/20 shadow-sm">
                                                {{ book.category }}
                                            </span>
                                        </div>

                                        <!-- Free Tag Badge -->
                                        <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-3 right-3 z-20">
                                            <span class="px-2.5 py-1 bg-emerald-500 text-white text-[9px] font-bold uppercase tracking-widest shadow-lg rounded-lg">Gratuito</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 flex flex-col">
                                        <!-- Author -->
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#9ca3af] mb-1.5 leading-none h-4 truncate">
                                            {{ book.author || 'Instituto IEE' }}
                                        </span>
                                        <!-- Title -->
                                        <h2 class="font-serif text-base font-bold text-on-surface leading-snug group-hover:text-primary transition-colors line-clamp-2 min-h-[2.5rem] mb-2" :title="book.title">
                                            {{ book.title }}
                                        </h2>
                                        <!-- Description -->
                                        <p class="text-xs text-on-surface-variant/70 line-clamp-3 mb-6 flex-1 leading-relaxed">
                                            {{ book.description }}
                                        </p>

                                        <!-- Pricing and Actions -->
                                        <div class="pt-4 border-t border-outline-variant/10 flex items-center justify-between mt-auto">
                                            <div class="text-lg font-serif font-bold text-on-surface">
                                                <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                                                <template v-else-if="book.category === 'Libro en camino'">---</template>
                                                <template v-else>Libre</template>
                                            </div>
                                            
                                            <!-- Buttons -->
                                            <template v-if="book.category === 'Libro en camino'">
                                                <button disabled class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-4 py-2.5 border border-outline-variant/20 text-on-surface-variant/40 opacity-50 cursor-not-allowed">
                                                    Pronto
                                                </button>
                                            </template>
                                            <template v-else-if="!book.is_available">
                                                <button disabled class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-4 py-2.5 bg-red-500/10 text-red-500 border border-red-500/20">
                                                    Agotado
                                                </button>
                                            </template>
                                            <template v-else>
                                                <a
                                                    v-if="Number(book.price) === 0 && (book.file_path || book.download_url)"
                                                    :href="getDownloadLink(book)"
                                                    target="_blank"
                                                    download
                                                    class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-primary text-on-primary hover:bg-primary/95 hover:shadow-lg transition-all shadow-md flex items-center gap-1.5"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    Descargar
                                                </a>
                                                <button
                                                    v-else-if="Number(book.price) === 0"
                                                    disabled
                                                    class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-surface-container-highest text-on-surface-variant/40 cursor-not-allowed border border-outline-variant/10"
                                                    title="Archivo no disponible"
                                                >
                                                    No disponible
                                                </button>
                                                <a
                                                    v-else
                                                    :href="getWhatsAppLink(book)"
                                                    target="_blank"
                                                    class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-primary text-on-primary hover:bg-primary/95 hover:shadow-lg transition-all shadow-md flex items-center justify-center gap-1.5"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                                    Adquirir
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <!-- Client-side Books Pagination -->
                            <div v-if="booksTotalPages > 1" class="mt-12 flex justify-center">
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="p in booksTotalPages"
                                        :key="p"
                                        @click="changeBooksPage(p)"
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all border"
                                        :class="booksPage === p 
                                            ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' 
                                            : 'bg-surface border-outline-variant/20 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                                    >
                                        {{ p }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────── TABS CONTENT: ARTICLES ───────────────────── -->
                    <div v-if="currentTab === 'articulos'">
                        <!-- Empty Search State -->
                        <div v-if="!filteredArticles.length" class="py-24 text-center bg-surface-container-low rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 max-w-3xl mx-auto flex flex-col items-center justify-center p-8 space-y-4">
                            <div class="p-4 rounded-full bg-primary/5 text-primary/40">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <h3 class="text-xl font-serif font-bold text-on-surface">Sin análisis estratégico</h3>
                            <p class="text-xs text-on-surface-variant max-w-sm">No hemos encontrado ningún artículo o medio que coincida con la búsqueda "{{ searchQuery }}". Pruebe con otro término.</p>
                            <button @click="searchQuery = ''" class="px-5 py-2 bg-primary text-on-primary text-xs font-bold rounded-xl shadow-md hover:bg-primary/90 transition-all">Mostrar todos</button>
                        </div>

                        <!-- Articles Grid -->
                        <div v-else>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                                <article 
                                    v-for="article in paginatedArticles" 
                                    :key="article.id"
                                    class="flex flex-col sm:flex-row gap-6 bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-xl hover:border-primary/20 transition-all duration-500 overflow-hidden group hover:-translate-y-1"
                                >
                                    <!-- Article Thumbnail cover -->
                                    <div class="w-full sm:w-40 h-40 flex-shrink-0 overflow-hidden bg-surface-container-low rounded-2xl border border-outline-variant/10 relative">
                                        <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" :alt="article.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out">
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10 text-primary/20">
                                            <svg class="w-12 h-12 stroke-current" fill="none" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                    </div>
                                    
                                    <div class="flex-1 flex flex-col justify-center space-y-4">
                                        <!-- Meta Row -->
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-primary bg-primary/5 px-3 py-1 rounded-full border border-primary/10">
                                                {{ article.media }}
                                            </span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/50">
                                                {{ formatDate(article.published_at) }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-widest text-[#AA7C11] bg-[#AA7C11]/5 px-2.5 py-1 rounded-full border border-[#AA7C11]/15 shadow-inner">
                                                <span class="material-symbols-outlined text-[10px]" translate="no" style="font-size: 11px;">schedule</span>
                                                Lectura: {{ estimateReadingTime(article.title) }} min
                                            </span>
                                        </div>

                                        <!-- Title -->
                                        <h3 class="font-serif text-lg font-bold text-on-surface group-hover:text-primary transition-colors leading-snug line-clamp-2">
                                            {{ article.title }}
                                        </h3>

                                        <!-- CTA Link -->
                                        <a 
                                            :href="getArticleDownloadLink(article)" 
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-primary border-b border-primary/20 pb-1 w-fit hover:border-primary transition-all group-hover:gap-2.5"
                                        >
                                            Lectura Completa
                                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </article>
                            </div>

                            <!-- Client-side Articles Pagination -->
                            <div v-if="articlesTotalPages > 1" class="mt-12 flex justify-center">
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="p in articlesTotalPages"
                                        :key="p"
                                        @click="changeArticlesPage(p)"
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all border"
                                        :class="articlesPage === p 
                                            ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' 
                                            : 'bg-surface border-outline-variant/20 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                                    >
                                        {{ p }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </component>
</template>

<style scoped>
.limit-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }
</style>
