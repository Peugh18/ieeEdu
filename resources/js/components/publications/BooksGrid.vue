<script setup lang="ts">
import type { PublicationBook } from '@/types/publications';
import { storageUrl } from '@/lib/storageUrl';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PurchaseCoordinationModal from '@/components/purchase/PurchaseCoordinationModal.vue';
import BookShippingTracker from '@/components/student/payments/BookShippingTracker.vue';

defineProps<{
    books: PublicationBook[];
}>();

const showModal = ref(false);
const selectedBook = ref<PublicationBook | null>(null);

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
    // @ts-expect-error ziggy
    return route('student.publications.books.download', book.id);
}

function openCoordination(book: PublicationBook) {
    selectedBook.value = book;
    showModal.value = true;
}
</script>

<template>
    <div>
        <div v-if="!books.length" class="py-24 text-center bg-surface-container-low rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 max-w-3xl mx-auto flex flex-col items-center justify-center p-8 space-y-4">
            <h3 class="text-xl font-serif font-bold text-on-surface">Sin resultados académicos</h3>
            <p class="text-xs text-on-surface-variant max-w-sm">No hemos encontrado ningún libro o guía que coincida con la búsqueda.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
            <article v-for="book in books" :key="book.id" class="flex flex-col bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-2xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 group h-full">
                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container-low shadow-md relative border border-outline-variant/10 mb-5 flex-shrink-0">
                    <img v-if="book.cover_image" :src="storageUrl(book.cover_image)" :alt="book.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s]" />
                    <div v-else :class="['w-full h-full bg-gradient-to-br p-6 flex flex-col justify-center items-center', getBookCoverStyle(book.id).bg]">
                        <h4 class="font-serif text-sm font-bold text-white text-center line-clamp-4">{{ book.title }}</h4>
                    </div>
                    <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-3 right-3 z-20">
                        <span class="px-2.5 py-1 bg-emerald-500 text-white text-[9px] font-bold uppercase rounded-lg">Gratis</span>
                    </div>
                    <div v-else-if="book.is_out_of_stock && !book.has_approved_purchase" class="absolute bottom-3 right-3 z-20">
                        <span class="px-2.5 py-1 bg-red-500 text-white text-[9px] font-bold uppercase rounded-lg">Agotado</span>
                    </div>
                    <div v-else-if="book.shipping" class="absolute bottom-3 right-3 z-20">
                        <span class="px-2.5 py-1 bg-blue-600 text-white text-[9px] font-bold uppercase rounded-lg">{{ book.shipping.label }}</span>
                    </div>
                </div>

                <div class="flex-1 flex flex-col">
                    <h2 class="font-serif text-base font-bold text-on-surface line-clamp-2 mb-2">{{ book.title }}</h2>
                    <p class="text-xs text-on-surface-variant/70 line-clamp-3 mb-3 flex-1">{{ book.description }}</p>

                    <BookShippingTracker v-if="book.shipping" :shipping="book.shipping" compact class="mb-4" />

                    <div class="pt-4 border-t border-outline-variant/10 flex items-center justify-between mt-auto">
                        <div class="text-lg font-serif font-bold text-on-surface">
                            <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                            <template v-else-if="book.category === 'Libro en camino'">---</template>
                            <template v-else>Libre</template>
                        </div>

                        <template v-if="book.category === 'Libro en camino'">
                            <button disabled class="rounded-xl text-[10px] font-bold px-4 py-2.5 opacity-50 cursor-not-allowed">Pronto</button>
                        </template>
                        <template v-else-if="!book.is_available">
                            <button disabled class="rounded-xl text-[10px] font-bold px-4 py-2.5 bg-red-500/10 text-red-500">No disponible</button>
                        </template>
                        <template v-else-if="book.has_approved_purchase">
                            <Link :href="route('student.payments.index')" class="rounded-xl text-[10px] font-bold px-4 py-2.5 bg-blue-600 text-white">Ver pedido</Link>
                        </template>
                        <template v-else-if="book.needs_comprobante">
                            <Link :href="route('student.payments.index')" class="rounded-xl text-[10px] font-bold px-4 py-2.5 bg-amber-500 text-white">Subir comprobante</Link>
                        </template>
                        <template v-else-if="book.has_pending_payment">
                            <Link :href="route('student.payments.index')" class="rounded-xl text-[10px] font-bold px-4 py-2.5 bg-amber-500/80 text-white">En revisión</Link>
                        </template>
                        <template v-else-if="Number(book.price) > 0">
                            <button v-if="book.can_purchase" type="button" @click="openCoordination(book)" class="rounded-xl text-[10px] font-bold px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 transition-all">
                                Comprar
                            </button>
                            <button v-else disabled class="rounded-xl text-[10px] font-bold px-4 py-2.5 bg-red-500/10 text-red-500 cursor-not-allowed">Agotado</button>
                        </template>
                        <template v-else>
                            <a :href="getDownloadLink(book)" class="rounded-xl text-[10px] font-bold px-5 py-2.5 bg-primary text-on-primary">Descargar</a>
                        </template>
                    </div>
                </div>
            </article>
        </div>

        <PurchaseCoordinationModal
            v-if="selectedBook && showModal"
            :show="showModal"
            product-type="book"
            :product-id="selectedBook.id"
            :product-title="selectedBook.title"
            :amount="Number(selectedBook.price)"
            @close="showModal = false"
        />
    </div>
</template>
