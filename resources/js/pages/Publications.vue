<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';

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
</script>

<template>
    <Head title="IEE - Publicaciones" />

    <div class="flex min-h-screen flex-col bg-[#FAFAF5] text-[#1A1C19] font-sans">
        <Navigation />

        <main class="flex-1 pb-20">
            <!-- Hero Header -->
            <section class="bg-gradient-to-br from-[#57572A] to-[#707040] py-24 px-6 sm:px-12 text-center relative mb-12">
                <div class="relative z-10 max-w-4xl mx-auto">
                    <p class="text-[#FAFAF5]/80 text-sm font-bold uppercase tracking-[0.15em] mb-4">Investigación y Conocimiento</p>
                    <h1 class="text-4xl md:text-5xl lg:text-[64px] font-serif font-bold text-white mb-6 leading-tight tracking-[-0.02em]">
                        Nuestras <span class="italic">Publicaciones</span>
                    </h1>
                    <p class="text-[#FAFAF5]/90 text-lg md:text-xl max-w-2xl mx-auto font-serif">
                        Explora nuestra biblioteca especializada en economía, gestión pública y liderazgo empresarial.
                    </p>
                </div>
                <div class="absolute inset-0 opacity-10 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] pointer-events-none"></div>
            </section>

            <!-- Tabs Section -->
            <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-2 md:gap-8 mb-16 border-b border-[#C9C7B8]/20 pb-0.5">
                    <button 
                        @click="currentTab = 'libros'"
                        :class="[
                            'px-4 md:px-10 py-5 font-serif text-xl font-bold transition-all border-b-2 relative overflow-hidden',
                            currentTab === 'libros' ? 'border-[#57572A] text-[#57572A]' : 'border-transparent text-[#5F5E5E] hover:text-[#1A1C19]'
                        ]"
                    >
                        Libros y Guías
                    </button>
                    <button 
                        @click="currentTab = 'articulos'"
                        :class="[
                            'px-4 md:px-10 py-5 font-serif text-xl font-bold transition-all border-b-2 relative overflow-hidden',
                            currentTab === 'articulos' ? 'border-[#57572A] text-[#57572A]' : 'border-transparent text-[#5F5E5E] hover:text-[#1A1C19]'
                        ]"
                    >
                        Artículos Especializados
                    </button>
                </div>

                <!-- ──────────────── LIBROS ──────────────────── -->
                <div v-if="currentTab === 'libros'">
                    <div v-if="!props.books.length" class="py-24 text-center">
                        <p class="text-on-surface-variant font-serif italic text-lg">Próximamente compartiremos nuestros nuevos títulos.</p>
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
                        <article
                            v-for="book in props.books"
                            :key="book.id"
                            class="flex flex-col group"
                        >
                            <div class="aspect-[3/4] rounded-sm overflow-hidden bg-[#F4F4EF] shadow-md hover:shadow-2xl transition-all duration-700 relative border border-[#C9C7B8]/20">
                                <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out" />
                                <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                
                                <div class="absolute top-4 left-4 z-10">
                                    <span :class="[
                                        'px-2.5 py-1 text-[9px] font-bold tracking-[0.1em] uppercase shadow-sm bg-white/95 text-[#1A1C19]',
                                    ]">
                                        {{ book.category }}
                                    </span>
                                </div>

                                <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-4 right-4 z-10">
                                    <span class="px-2 py-1 bg-[#57572A] text-white text-[8px] font-bold uppercase tracking-widest shadow-xl">Gratuito</span>
                                </div>
                            </div>
                            
                            <div class="pt-6 flex-1 flex flex-col">
                                <h2 class="font-serif text-xl font-bold text-[#1A1C19] mb-2 leading-tight group-hover:text-[#57572A] transition-colors line-clamp-2 min-h-[3rem]">
                                    {{ book.title }}
                                </h2>
                                <p class="text-[11px] font-bold tracking-[0.05em] uppercase text-[#5F5E5E] mb-4">{{ book.author || 'Instituto IEE' }}</p>
                                <p class="text-[13px] text-[#5F5E5E] line-clamp-3 mb-6 flex-1 italic leading-relaxed">{{ book.description }}</p>

                                <div class="mt-auto pt-6 border-t border-[#C9C7B8]/20 flex items-center justify-between">
                                    <div class="text-xl font-serif font-bold text-[#1A1C19]">
                                        <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                                        <template v-else-if="book.category === 'Libro en camino'">---</template>
                                        <template v-else>Gratis</template>
                                    </div>
                                    
                                    <template v-if="book.category === 'Libro en camino'">
                                        <div class="flex flex-col items-end">
                                            <span class="text-[9px] font-bold text-on-surface-variant/40 mb-1 uppercase tracking-tighter">Disponible pronto</span>
                                            <button disabled class="rounded text-[10px] uppercase tracking-[0.05em] font-bold px-4 py-2 border border-gray-200 text-gray-400 cursor-not-allowed">
                                                Bloqueado
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else-if="!book.is_available">
                                        <button disabled class="rounded text-[10px] uppercase tracking-[0.05em] font-bold px-4 py-2 bg-red-50 text-red-600 border border-red-100">
                                            Agotado
                                        </button>
                                    </template>
                                    <template v-else>
                                        <a
                                            v-if="Number(book.price) === 0 && (book.file_path || book.download_url)"
                                            :href="getDownloadLink(book)"
                                            target="_blank"
                                            class="rounded text-[10px] uppercase tracking-[0.05em] font-bold px-5 py-2.5 bg-[#57572A] text-white hover:opacity-90 transition-opacity flex items-center gap-2 shadow-lg shadow-[#57572A]/15"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Descargar
                                        </a>
                                        <Link
                                            v-else
                                            href="#"
                                            class="rounded text-[10px] uppercase tracking-[0.05em] font-bold px-5 py-2.5 bg-[#57572A] text-white hover:opacity-90 transition-opacity shadow-lg shadow-[#57572A]/15"
                                        >
                                            {{ Number(book.price) > 0 ? 'Comprar' : 'Adquirir' }}
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
                        <p class="text-on-surface-variant font-serif italic text-lg">Nuestros investigadores están preparando nuevo material académico.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <article 
                            v-for="article in props.articles" 
                            :key="article.id"
                            class="flex flex-col sm:flex-row gap-8 bg-white p-8 rounded border border-[#C9C7B8]/30 hover:shadow-2xl transition-all duration-500 overflow-hidden group"
                        >
                            <!-- Miniatura -->
                            <div class="w-full sm:w-44 h-44 flex-shrink-0 overflow-hidden bg-[#F4F4EF] border border-[#C9C7B8]/15 shadow-sm">
                                <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <div v-else class="flex h-full w-full items-center justify-center opacity-10">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                            </div>
                            
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="flex items-center gap-4 mb-3">
                                    <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#57572A] bg-[#57572A]/5 px-2.5 py-1">{{ article.media }}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-[#5F5E5E]">{{ formatDate(article.published_at) }}</span>
                                </div>
                                <h3 class="font-serif text-2xl font-bold text-[#1A1C19] mb-4 group-hover:text-[#57572A] transition-colors leading-tight">
                                    {{ article.title }}
                                </h3>
                                <a 
                                    :href="article.download_url" 
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-[#57572A] border-b-2 border-[#57572A]/20 pb-1.5 w-fit hover:border-[#57572A] transition-all"
                                >
                                    Abrir Publicación
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.limit-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }
</style>
