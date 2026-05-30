<script setup lang="ts">
import type { PublicationBook } from '@/types/publications';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';
import { storageUrl } from '@/lib/storageUrl';

defineProps<{
    books: PublicationBook[];
    totalPages: number;
    currentPage: number;
}>();

const emit = defineEmits<{
    (e: 'changePage', page: number): void;
}>();

function getBookCoverStyle(id: number) {
    const covers = [
        { bg: 'from-[#2D391B] to-[#171E0E]' },
        { bg: 'from-[#14283D] to-[#0A141F]' },
        { bg: 'from-[#4D1A1F] to-[#2B0E11]' },
        { bg: 'from-[#3D3219] to-[#201A0D]' },
        { bg: 'from-[#242526] to-[#121314]' },
    ];
    return covers[id % covers.length];
}

function getDownloadLink(book: PublicationBook) {
    if (book.download_url && book.download_url.startsWith('http')) return book.download_url;
    // @ts-expect-error ziggy
    return route('student.publications.books.download', book.id);
}

const page = usePage<SharedData>();
const whatsappNumber = computed(() => (page.props.whatsapp_sales as string) || '51959166911');

function getWhatsAppLink(book: PublicationBook) {
    const message = `Hola, estoy interesado en adquirir el material: *${book.title}* que tiene un costo de S/ ${book.price}. ¿Me podrían brindar más información?`;
    return `https://wa.me/${whatsappNumber.value}?text=${encodeURIComponent(message)}`;
}
</script>

<template>
    <div>
        <div v-if="!books.length" class="py-24 text-center bg-surface-container-low rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 max-w-3xl mx-auto flex flex-col items-center justify-center p-8 space-y-4">
            <div class="p-4 rounded-full bg-primary/5 text-primary/40">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-on-surface">Sin resultados académicos</h3>
            <p class="text-xs text-on-surface-variant max-w-sm">No hemos encontrado ningún libro o guía que coincida con la búsqueda.</p>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                <article v-for="book in books" :key="book.id" class="flex flex-col bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-2xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 group h-full relative">
                    <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container-low shadow-md relative border border-outline-variant/10 mb-5 flex-shrink-0">
                        <img v-if="book.cover_image" :src="storageUrl(book.cover_image)" :alt="book.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out" />
                        <div v-else :class="['w-full h-full bg-gradient-to-br p-6 flex flex-col justify-between relative group-hover:scale-105 transition-all duration-700 ease-out select-none', getBookCoverStyle(book.id).bg]">
                            <div class="absolute inset-0 opacity-[0.04] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                            <div class="flex-1 flex flex-col items-center justify-center py-6 z-10">
                                <h4 class="font-serif text-[11px] sm:text-xs md:text-sm font-bold text-white text-center leading-snug tracking-tight px-1 line-clamp-4 mb-2">{{ book.title }}</h4>
                                <div class="w-6 h-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/30 my-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                </div>
                                <span class="text-[8px] font-sans uppercase tracking-widest text-white/40 font-medium">IEE Editorial</span>
                            </div>
                            <div class="flex flex-col items-center pb-2 z-10">
                                <p class="text-[9px] font-sans font-semibold uppercase tracking-wider text-white/60 text-center line-clamp-1 max-w-full px-2">{{ book.author || 'Instituto IEE' }}</p>
                            </div>
                        </div>
                        <div class="absolute inset-y-0 left-0 w-2.5 bg-gradient-to-r from-black/25 via-black/5 to-transparent z-10 pointer-events-none"></div>
                        <div class="absolute inset-y-0 left-[2.5px] w-[1px] bg-white/10 z-10 pointer-events-none"></div>
                        <div class="absolute top-3 left-3 z-20">
                            <span class="px-2.5 py-1 bg-surface/90 backdrop-blur-md rounded-lg text-[9px] font-bold tracking-widest uppercase text-primary border border-outline-variant/20 shadow-sm">{{ book.category }}</span>
                        </div>
                        <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-3 right-3 z-20">
                            <span class="px-2.5 py-1 bg-emerald-500 text-white text-[9px] font-bold uppercase tracking-widest shadow-lg rounded-lg">Gratuito</span>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#9ca3af] mb-1.5 leading-none h-4 truncate">{{ book.author || 'Instituto IEE' }}</span>
                        <h2 class="font-serif text-base font-bold text-on-surface leading-snug group-hover:text-primary transition-colors line-clamp-2 min-h-[2.5rem] mb-2" :title="book.title">{{ book.title }}</h2>
                        <p class="text-xs text-on-surface-variant/70 line-clamp-3 mb-6 flex-1 leading-relaxed">{{ book.description }}</p>

                        <div class="pt-4 border-t border-outline-variant/10 flex items-center justify-between mt-auto">
                            <div class="text-lg font-serif font-bold text-on-surface">
                                <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                                <template v-else-if="book.category === 'Libro en camino'">---</template>
                                <template v-else>Libre</template>
                            </div>

                            <template v-if="book.category === 'Libro en camino'">
                                <button disabled class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-4 py-2.5 border border-outline-variant/20 text-on-surface-variant/40 opacity-50 cursor-not-allowed">Pronto</button>
                            </template>
                            <template v-else-if="!book.is_available">
                                <button disabled class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-4 py-2.5 bg-red-500/10 text-red-500 border border-red-500/20">Agotado</button>
                            </template>
                            <template v-else>
                                <a v-if="Number(book.price) === 0" :href="getDownloadLink(book)" target="_blank" download class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-primary text-on-primary hover:bg-primary/95 hover:shadow-lg transition-all shadow-md flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Descargar
                                </a>
                                <button v-else-if="Number(book.price) === 0" disabled class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-surface-container-highest text-on-surface-variant/40 cursor-not-allowed border border-outline-variant/10" title="Archivo no disponible">No disponible</button>
                                <a v-else :href="getWhatsAppLink(book)" target="_blank" class="rounded-xl text-[10px] uppercase tracking-wider font-bold px-5 py-2.5 bg-primary text-on-primary hover:bg-primary/95 hover:shadow-lg transition-all shadow-md flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                    Adquirir
                                </a>
                            </template>
                        </div>
                    </div>
                </article>
            </div>

            <div v-if="totalPages > 1" class="mt-12 flex justify-center">
                <div class="flex flex-wrap gap-2">
                    <button v-for="p in totalPages" :key="p" @click="emit('changePage', p)" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all border" :class="currentPage === p ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' : 'bg-surface border-outline-variant/20 text-on-surface-variant hover:border-primary/40 hover:text-primary'">{{ p }}</button>
                </div>
            </div>
        </div>
    </div>
</template>
