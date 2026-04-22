<script setup lang="ts">
import { ref, computed } from 'vue';
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

// Valores dinámicos con fallback
const heroHeading = computed(() => props.banner?.heading || 'Nuestras Publicaciones Especializadas');
const heroSubheading = computed(() => props.banner?.subheading || 'Acceda a nuestra biblioteca de investigación, libros y artículos técnicos para su desarrollo profesional.');

const currentTab = ref<'libros' | 'articulos'>('libros');

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', {
        month: 'long',
        year: 'numeric'
    });
}

function getDownloadLink(book: Book) {
    if (book.file_path) return `/storage/${book.file_path}`;
    return book.download_url;
}

function getArticleDownloadLink(article: Article) {
    if (article.file_path) return `/storage/${article.file_path}`;
    return article.download_url;
}

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

                <!-- Hero Banner: Tarjeta Contenida Premium -->
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                    <!-- Con imagen -->
                    <div
                        v-if="banner?.image_path"
                        :class="['relative overflow-hidden shadow-2xl', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="banner.image_path" alt="Publicaciones IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>

                        <div v-if="banner.show_text" class="relative z-10 h-full px-10 md:px-14 flex flex-col justify-center">
                            <div class="max-w-2xl space-y-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                    Investigación de Alto Nivel
                                </span>
                                <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                                    {{ heroHeading }}
                                </h1>
                                <p class="text-base text-white/75 max-w-lg font-light leading-relaxed">
                                    {{ heroSubheading }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback sin imagen -->
                    <div v-else
                        class="relative overflow-hidden rounded-[2rem] bg-surface-container-low py-20 px-10 md:px-14 flex flex-col justify-center"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <div class="absolute inset-0 pointer-events-none overflow-hidden">
                            <div class="absolute -top-20 right-1/4 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                            <div class="absolute -bottom-20 left-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                        </div>
                        <div class="relative z-10 max-w-2xl space-y-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Investigación de Alto Nivel</p>
                            <h1 class="text-3xl md:text-5xl lg:text-[54px] font-serif font-bold leading-tight tracking-[-0.01em] text-on-surface">
                                {{ heroHeading }}
                            </h1>
                            <p class="text-lg text-on-surface-variant">{{ heroSubheading }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs Section -->
                <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-center gap-0 sm:gap-4 md:gap-8 mb-16 border-b border-outline-variant/20 pb-0.5">
                        <button 
                            @click="currentTab = 'libros'"
                            :class="[
                                'px-4 md:px-10 py-5 font-serif text-lg font-bold transition-all border-b-2 relative italic',
                                currentTab === 'libros' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-background'
                            ]"
                        >
                            Libros y Guías Técnicas
                        </button>
                        <button 
                            @click="currentTab = 'articulos'"
                            :class="[
                                'px-4 md:px-10 py-5 font-serif text-lg font-bold transition-all border-b-2 relative italic',
                                currentTab === 'articulos' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-background'
                            ]"
                        >
                            Artículos de Análisis
                        </button>
                    </div>

                    <!-- ──────────────── LIBROS ──────────────────── -->
                    <div v-if="currentTab === 'libros'">
                        <div v-if="!props.books.length" class="py-24 text-center">
                            <p class="text-on-surface-variant font-serif italic text-lg opacity-40">Nos encontramos digitalizando nuevos títulos académicos.</p>
                        </div>
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
                            <article
                                v-for="book in props.books"
                                :key="book.id"
                                class="flex flex-col group"
                            >
                                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container-highest shadow-md hover:shadow-2xl transition-all duration-700 relative border border-outline-variant/10 group-hover:-translate-y-2">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out" />
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    
                                    <div class="absolute top-4 left-4 z-10">
                                        <span class="px-3 py-1 bg-surface/90 backdrop-blur-md rounded-full text-[8px] font-bold tracking-[0.2em] uppercase text-primary border border-outline-variant/20">
                                            {{ book.category }}
                                        </span>
                                    </div>

                                    <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-4 right-4 z-10">
                                        <span class="px-2 py-1 bg-primary text-white text-[8px] font-bold uppercase tracking-widest shadow-xl rounded">Gratuito</span>
                                    </div>
                                </div>
                                
                                <div class="pt-8 flex-1 flex flex-col space-y-3">
                                    <h2 class="font-serif text-xl font-bold text-on-background leading-tight group-hover:text-primary transition-colors italic min-h-[3rem]">
                                        {{ book.title }}
                                    </h2>
                                    <p class="text-[9px] font-bold tracking-[0.2em] uppercase text-on-surface-variant/40 italic">{{ book.author || 'Instituto IEE' }}</p>
                                    <p class="text-xs text-on-surface-variant line-clamp-3 mb-6 flex-1 italic leading-relaxed">{{ book.description }}</p>

                                    <div class="mt-auto pt-6 border-t border-outline-variant/10 flex items-center justify-between">
                                        <div class="text-2xl font-serif font-bold text-on-background italic">
                                            <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                                            <template v-else-if="book.category === 'Libro en camino'">---</template>
                                            <template v-else>Libre</template>
                                        </div>
                                        
                                        <template v-if="book.category === 'Libro en camino'">
                                            <button disabled class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-4 py-3 border border-outline-variant/20 text-on-surface-variant/40 opacity-50 cursor-not-allowed">
                                                Pronto
                                            </button>
                                        </template>
                                        <template v-else-if="!book.is_available">
                                            <button disabled class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-4 py-3 bg-red-500/10 text-red-500 border border-red-500/20">
                                                Agotado
                                            </button>
                                        </template>
                                        <template v-else>
                                            <a
                                                v-if="Number(book.price) === 0"
                                                :href="getDownloadLink(book)"
                                                target="_blank"
                                                class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-6 py-3 bg-primary text-on-primary hover:opacity-90 transition-all shadow-lg shadow-primary/10 flex items-center gap-2"
                                            >
                                                Descargar
                                            </a>
                                            <Link
                                                v-else
                                                href="#"
                                                class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-6 py-3 bg-primary text-on-primary hover:opacity-90 transition-all shadow-lg shadow-primary/10"
                                            >
                                                Adquirir
                                            </Link>
                                        </template>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- ──────────────── ARTÍCULOS ───────────────────── -->
                    <div v-if="currentTab === 'articulos'">
                        <div v-if="!props.articles.length" class="py-24 text-center">
                            <p class="text-on-surface-variant font-serif italic text-lg opacity-40">Publicando nuevos artículos de análisis estratégico.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <article 
                                v-for="article in props.articles" 
                                :key="article.id"
                                class="flex flex-col md:flex-row gap-8 bg-surface-container p-8 rounded-2xl border border-outline-variant/15 hover:shadow-xl hover:border-primary/20 transition-all duration-500 overflow-hidden group hover:-translate-y-1"
                            >
                                <!-- Miniatura -->
                                <div class="w-full md:w-44 h-44 flex-shrink-0 overflow-hidden bg-surface-container-highest rounded-2xl border border-outline-variant/10">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-center space-y-4">
                                    <div class="flex items-center gap-4">
                                        <span class="text-[8px] font-bold uppercase tracking-[0.3em] text-primary bg-primary/5 px-3 py-1 rounded-full border border-primary/10">{{ article.media }}</span>
                                        <span class="text-[8px] font-bold uppercase tracking-widest text-on-surface-variant italic">{{ formatDate(article.published_at) }}</span>
                                    </div>
                                    <h3 class="font-serif text-2xl font-bold text-on-background group-hover:text-primary transition-colors leading-tight italic">
                                        {{ article.title }}
                                    </h3>
                                    <a 
                                        :href="getArticleDownloadLink(article)" 
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-primary border-b border-primary/20 pb-1.5 w-fit hover:border-primary transition-all group-hover:gap-4"
                                    >
                                        Lectura Completa
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </article>
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
