<script setup lang="ts">
import PurchaseCoordinationModal from '@/components/purchase/PurchaseCoordinationModal.vue';
import BookShippingTracker from '@/components/student/payments/BookShippingTracker.vue';
import { storageUrl } from '@/lib/storageUrl';
import type { PublicationBook } from '@/types/publications';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

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
        <div
            v-if="!books.length"
            class="mx-auto flex max-w-3xl flex-col items-center justify-center space-y-4 rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 bg-surface-container-low p-8 py-24 text-center"
        >
            <h3 class="font-serif text-xl font-bold text-on-surface">Sin resultados académicos</h3>
            <p class="max-w-sm text-xs text-on-surface-variant">No hemos encontrado ningún libro o guía que coincida con la búsqueda.</p>
        </div>

        <div v-else class="mb-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <article
                v-for="book in books"
                :key="book.id"
                class="group flex h-full flex-col rounded-3xl border border-outline-variant/15 bg-surface p-5 transition-all duration-500 hover:-translate-y-1.5 hover:border-primary/20 hover:shadow-2xl"
            >
                <div
                    class="relative mb-5 aspect-[3/4] flex-shrink-0 overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-low shadow-md"
                >
                    <img
                        v-if="book.cover_image"
                        :src="storageUrl(book.cover_image)"
                        :alt="book.title"
                        class="duration-[1.5s] h-full w-full object-cover transition-transform group-hover:scale-105"
                    />
                    <div
                        v-else
                        :class="['flex h-full w-full flex-col items-center justify-center bg-gradient-to-br p-6', getBookCoverStyle(book.id).bg]"
                    >
                        <h4 class="line-clamp-4 text-center font-serif text-sm font-bold text-white">{{ book.title }}</h4>
                    </div>
                    <div v-if="Number(book.price) === 0 && book.category !== 'Libro en camino'" class="absolute bottom-3 right-3 z-20">
                        <span class="rounded-lg bg-emerald-500 px-2.5 py-1 text-[9px] font-bold uppercase text-white">Gratis</span>
                    </div>
                    <div v-else-if="book.is_out_of_stock && !book.has_approved_purchase" class="absolute bottom-3 right-3 z-20">
                        <span class="rounded-lg bg-red-500 px-2.5 py-1 text-[9px] font-bold uppercase text-white">Agotado</span>
                    </div>
                    <div v-else-if="book.shipping" class="absolute bottom-3 right-3 z-20">
                        <span class="rounded-lg bg-blue-600 px-2.5 py-1 text-[9px] font-bold uppercase text-white">{{ book.shipping.label }}</span>
                    </div>
                </div>

                <div class="flex flex-1 flex-col">
                    <h2 class="mb-2 line-clamp-2 font-serif text-base font-bold text-on-surface">{{ book.title }}</h2>
                    <p class="mb-3 line-clamp-3 flex-1 text-xs text-on-surface-variant/70">{{ book.description }}</p>

                    <BookShippingTracker v-if="book.shipping" :shipping="book.shipping" compact class="mb-4" />

                    <div class="mt-auto flex items-center justify-between border-t border-outline-variant/10 pt-4">
                        <div class="font-serif text-lg font-bold text-on-surface">
                            <template v-if="Number(book.price) > 0">S/ {{ book.price }}</template>
                            <template v-else-if="book.category === 'Libro en camino'">---</template>
                            <template v-else>Libre</template>
                        </div>

                        <template v-if="book.category === 'Libro en camino'">
                            <button disabled class="cursor-not-allowed rounded-xl px-4 py-2.5 text-[10px] font-bold opacity-50">Pronto</button>
                        </template>
                        <template v-else-if="!book.is_available">
                            <button disabled class="rounded-xl bg-red-500/10 px-4 py-2.5 text-[10px] font-bold text-red-500">No disponible</button>
                        </template>
                        <template v-else-if="book.has_approved_purchase">
                            <Link :href="route('student.payments.index')" class="rounded-xl bg-blue-600 px-4 py-2.5 text-[10px] font-bold text-white"
                                >Ver pedido</Link
                            >
                        </template>
                        <template v-else-if="book.needs_comprobante">
                            <Link :href="route('student.payments.index')" class="rounded-xl bg-amber-500 px-4 py-2.5 text-[10px] font-bold text-white"
                                >Subir comprobante</Link
                            >
                        </template>
                        <template v-else-if="book.has_pending_payment">
                            <Link
                                :href="route('student.payments.index')"
                                class="rounded-xl bg-amber-500/80 px-4 py-2.5 text-[10px] font-bold text-white"
                                >En revisión</Link
                            >
                        </template>
                        <template v-else-if="Number(book.price) > 0">
                            <button
                                v-if="book.can_purchase"
                                type="button"
                                @click="openCoordination(book)"
                                class="rounded-xl bg-emerald-600 px-5 py-2.5 text-[10px] font-bold text-white transition-all hover:bg-emerald-700"
                            >
                                Comprar
                            </button>
                            <button
                                v-else
                                disabled
                                class="cursor-not-allowed rounded-xl bg-red-500/10 px-4 py-2.5 text-[10px] font-bold text-red-500"
                            >
                                Agotado
                            </button>
                        </template>
                        <template v-else>
                            <a :href="getDownloadLink(book)" class="rounded-xl bg-primary px-5 py-2.5 text-[10px] font-bold text-on-primary"
                                >Descargar</a
                            >
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
