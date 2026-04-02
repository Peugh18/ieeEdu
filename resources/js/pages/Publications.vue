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
    download_url: string;
}

const props = defineProps<{
    books: Book[];
    articles: Article[];
    isDashboard?: boolean;
}>();

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

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Publicaciones e Investigación', href: '#' },
];
</script>

<template>
    <Head title="Investigación y Publicaciones - IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-[#FAFAF5]' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', isDashboard ? 'p-0' : '']">
                <!-- Hero Header -->
                <section :class="['bg-gradient-to-br from-[#57572A] to-[#707040] py-20 px-6 sm:px-12 text-center relative mb-12', isDashboard ? 'rounded-[2rem] mx-6 mt-6 overflow-hidden shadow-2xl' : '']">
                    <div class="relative z-10 max-w-4xl mx-auto">
                        <p class="text-[#FAFAF5]/80 text-xs font-bold uppercase tracking-[0.2em] mb-4">Investigación de Alto Nivel</p>
                        <h1 class="text-3xl md:text-5xl lg:text-[54px] font-serif font-bold text-white mb-6 leading-tight tracking-[-0.01em]">
                            Nuestras <span class="italic text-primary-container">Publicaciones Especializadas</span>
                        </h1>
                        <p class="text-[#FAFAF5]/90 text-lg max-w-2xl mx-auto font-serif italic">
                            Acceda a nuestra biblioteca de investigación, libros y artículos técnicos para su desarrollo profesional.
                        </p>
                    </div>
                </section>

                <!-- Tabs Section -->
                <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-center gap-0 sm:gap-4 md:gap-8 mb-16 border-b border-[#C9C7B8]/20 pb-0.5">
                        <button 
                            @click="currentTab = 'libros'"
                            :class="[
                                'px-4 md:px-10 py-5 font-serif text-lg font-bold transition-all border-b-2 relative italic',
                                currentTab === 'libros' ? 'border-[#57572A] text-[#57572A]' : 'border-transparent text-[#5F5E5E] hover:text-[#1A1C19]'
                            ]"
                        >
                            Libros y Guías Técnicas
                        </button>
                        <button 
                            @click="currentTab = 'articulos'"
                            :class="[
                                'px-4 md:px-10 py-5 font-serif text-lg font-bold transition-all border-b-2 relative italic',
                                currentTab === 'articulos' ? 'border-[#57572A] text-[#57572A]' : 'border-transparent text-[#5F5E5E] hover:text-[#1A1C19]'
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
                                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-[#F4F4EF] shadow-md hover:shadow-2xl transition-all duration-700 relative border border-outline-variant/10 group-hover:-translate-y-2">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out" />
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    
                                    <div class="absolute top-4 left-4 z-10">
                                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-[8px] font-bold tracking-[0.2em] uppercase text-primary border border-white">
                                            {{ book.category }}
                                        </span>
                                    </div>

                                    <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-4 right-4 z-10">
                                        <span class="px-2 py-1 bg-[#57572A] text-white text-[8px] font-bold uppercase tracking-widest shadow-xl rounded">Gratuito</span>
                                    </div>
                                </div>
                                
                                <div class="pt-8 flex-1 flex flex-col space-y-3">
                                    <h2 class="font-serif text-xl font-bold text-[#1A1C19] leading-tight group-hover:text-[#57572A] transition-colors italic min-h-[3rem]">
                                        {{ book.title }}
                                    </h2>
                                    <p class="text-[9px] font-bold tracking-[0.2em] uppercase text-on-surface-variant/40 italic">{{ book.author || 'Instituto IEE' }}</p>
                                    <p class="text-xs text-[#5F5E5E] line-clamp-3 mb-6 flex-1 italic leading-relaxed">{{ book.description }}</p>

                                    <div class="mt-auto pt-6 border-t border-outline-variant/10 flex items-center justify-between">
                                        <div class="text-2xl font-serif font-bold text-[#1A1C19] italic">
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
                                            <button disabled class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-4 py-3 bg-red-50 text-red-600 border border-red-100">
                                                Agotado
                                            </button>
                                        </template>
                                        <template v-else>
                                            <a
                                                v-if="Number(book.price) === 0"
                                                :href="getDownloadLink(book)"
                                                target="_blank"
                                                class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-6 py-3 bg-[#57572A] text-white hover:bg-black transition-all shadow-xl shadow-[#57572A]/10 flex items-center gap-2"
                                            >
                                                Descargar
                                            </a>
                                            <Link
                                                v-else
                                                href="#"
                                                class="rounded-xl text-[10px] uppercase tracking-widest font-bold px-6 py-3 bg-[#57572A] text-white hover:bg-black transition-all shadow-xl shadow-[#57572A]/10"
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
                                class="flex flex-col md:flex-row gap-8 bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 hover:shadow-2xl transition-all duration-500 overflow-hidden group hover:-translate-y-1"
                            >
                                <!-- Miniatura -->
                                <div class="w-full md:w-44 h-44 flex-shrink-0 overflow-hidden bg-[#F4F4EF] rounded-2xl border border-outline-variant/10">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-center space-y-4">
                                    <div class="flex items-center gap-4">
                                        <span class="text-[8px] font-bold uppercase tracking-[0.3em] text-primary bg-primary/5 px-3 py-1 rounded-full border border-primary/10">{{ article.media }}</span>
                                        <span class="text-[8px] font-bold uppercase tracking-widest text-[#5F5E5E] italic">{{ formatDate(article.published_at) }}</span>
                                    </div>
                                    <h3 class="font-serif text-2xl font-bold text-[#1A1C19] group-hover:text-primary transition-colors leading-tight italic">
                                        {{ article.title }}
                                    </h3>
                                    <a 
                                        :href="article.download_url" 
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
