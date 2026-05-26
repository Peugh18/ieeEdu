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
const heroBg = computed(() => props.banner?.image_path || '/images/landing/publications_hero.png');
const showText = computed(() => props.banner?.show_text !== false);

const currentTab = ref<'libros' | 'articulos'>('libros');

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', {
        month: 'long',
        year: 'numeric'
    });
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
                    <div
                        :class="['relative overflow-hidden shadow-2xl', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="heroBg" alt="Publicaciones IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>

                        <div v-if="showText" class="relative z-10 h-full px-10 md:px-14 flex flex-col justify-center">
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
                </div>

                <!-- Tabs Section (Segmented Control) -->
                <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-center mb-12">
                        <div class="inline-flex p-1.5 bg-surface-container rounded-2xl border border-outline-variant/20 shadow-inner">
                            <button 
                                @click="currentTab = 'libros'"
                                :class="[
                                    'px-8 py-3.5 rounded-xl font-serif text-xs font-black tracking-[0.05em] uppercase transition-all duration-300',
                                    currentTab === 'libros' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface'
                                ]"
                            >
                                Libros y Guías
                            </button>
                            <button 
                                @click="currentTab = 'articulos'"
                                :class="[
                                    'px-8 py-3.5 rounded-xl font-serif text-xs font-black tracking-[0.05em] uppercase transition-all duration-300',
                                    currentTab === 'articulos' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface'
                                ]"
                            >
                                Artículos de Análisis
                            </button>
                        </div>
                    </div>

                    <!-- ──────────────── LIBROS ──────────────────── -->
                    <div v-if="currentTab === 'libros'">
                        <div v-if="!props.books.length" class="py-24 text-center">
                            <p class="text-on-surface-variant font-serif italic text-lg opacity-40">Nos encontramos digitalizando nuevos títulos académicos.</p>
                        </div>
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                            <article
                                v-for="book in props.books"
                                :key="book.id"
                                class="flex flex-col bg-surface border border-outline-variant/15 p-5 rounded-[2rem] hover:shadow-2xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 group h-full"
                            >
                                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container-low shadow-sm relative border border-outline-variant/5 mb-5 flex-shrink-0">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out" />
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="px-2.5 py-1 bg-surface/90 backdrop-blur-md rounded-lg text-[8px] font-black tracking-widest uppercase text-primary border border-outline-variant/20 shadow-sm">
                                            {{ book.category }}
                                        </span>
                                    </div>

                                    <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-3 right-3 z-10">
                                        <span class="px-2 py-0.5 bg-primary text-white text-[8px] font-bold uppercase tracking-widest shadow-xl rounded">Gratuito</span>
                                    </div>
                                </div>
                                
                                <div class="flex-1 flex flex-col">
                                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-[#9ca3af] mb-1.5 leading-none">{{ book.author || 'Instituto IEE' }}</span>
                                    <h2 class="font-serif text-base font-black text-on-surface leading-snug group-hover:text-primary transition-colors line-clamp-2 min-h-[2.5rem] mb-2 italic">
                                        {{ book.title }}
                                    </h2>
                                    <p class="text-xs text-on-surface-variant line-clamp-3 mb-6 flex-1 italic leading-relaxed">{{ book.description }}</p>

                                    <div class="pt-4 border-t border-outline-variant/10 flex items-center justify-between mt-auto">
                                        <div class="text-xl font-serif font-black text-on-surface italic">
                                            <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                                            <template v-else-if="book.category === 'Libro en camino'">---</template>
                                            <template v-else>Libre</template>
                                        </div>
                                        
                                        <template v-if="book.category === 'Libro en camino'">
                                            <button disabled class="rounded-xl text-[9px] uppercase tracking-widest font-black px-4 py-3 border border-outline-variant/20 text-on-surface-variant/40 opacity-50 cursor-not-allowed">
                                                Pronto
                                            </button>
                                        </template>
                                        <template v-else-if="!book.is_available">
                                            <button disabled class="rounded-xl text-[9px] uppercase tracking-widest font-black px-4 py-3 bg-red-500/10 text-red-500 border border-red-500/20">
                                                Agotado
                                            </button>
                                        </template>
                                        <template v-else>
                                            <a
                                                v-if="Number(book.price) === 0 && (book.file_path || book.download_url)"
                                                :href="getDownloadLink(book)"
                                                target="_blank"
                                                download
                                                class="rounded-xl text-[9px] uppercase tracking-widest font-black px-5 py-3.5 bg-primary text-on-primary hover:opacity-90 transition-all shadow-lg shadow-primary/10 flex items-center gap-1.5"
                                            >
                                                Descargar
                                            </a>
                                            <button
                                                v-else-if="Number(book.price) === 0"
                                                disabled
                                                class="rounded-xl text-[9px] uppercase tracking-widest font-black px-5 py-3.5 bg-surface-container-highest text-on-surface-variant/40 cursor-not-allowed border border-outline-variant/10"
                                                title="El archivo aún no ha sido subido"
                                            >
                                                No disponible
                                            </button>
                                            <a
                                                v-else
                                                :href="getWhatsAppLink(book)"
                                                target="_blank"
                                                class="rounded-xl text-[9px] uppercase tracking-widest font-black px-5 py-3.5 bg-primary text-on-primary hover:opacity-90 transition-all shadow-lg shadow-primary/10 flex items-center justify-center gap-1.5"
                                            >
                                                Adquirir
                                            </a>
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
                        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <article 
                                v-for="article in props.articles" 
                                :key="article.id"
                                class="flex flex-col sm:flex-row gap-6 bg-surface border border-outline-variant/15 p-6 rounded-[2rem] hover:shadow-2xl hover:border-primary/20 transition-all duration-500 overflow-hidden group hover:-translate-y-1.5"
                            >
                                <!-- Miniatura -->
                                <div class="w-full sm:w-40 h-40 flex-shrink-0 overflow-hidden bg-surface-container-low rounded-2xl border border-outline-variant/10 relative">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out">
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-center space-y-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[8px] font-black uppercase tracking-widest text-primary bg-primary/5 px-3 py-1 rounded-full border border-primary/10">{{ article.media }}</span>
                                        <span class="text-[8px] font-black uppercase tracking-widest text-on-surface-variant/60 italic">{{ formatDate(article.published_at) }}</span>
                                    </div>
                                    <h3 class="font-serif text-xl font-black text-on-surface group-hover:text-primary transition-colors leading-snug line-clamp-2 italic">
                                        {{ article.title }}
                                    </h3>
                                    <a 
                                        :href="getArticleDownloadLink(article)" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-primary border-b border-primary/20 pb-1.5 w-fit hover:border-primary transition-all group-hover:gap-3"
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
