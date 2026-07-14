<script setup lang="ts">
import { BookOpen, X, Download, ShoppingBag, Info } from 'lucide-vue-next';
import type { PublicationBook } from '@/types/publications';

const props = defineProps<{
    show: boolean;
    book: PublicationBook;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'action', book: PublicationBook): void;
}>();

function storageUrl(path: string | null) {
    if (!path) return '';
    return path.startsWith('http') ? path : `/storage/${path}`;
}

const getBookCoverStyle = (id: number) => {
    const styles = [
        { bg: 'from-amber-600/20 to-amber-950/40 text-amber-300' },
        { bg: 'from-emerald-600/20 to-emerald-950/40 text-emerald-300' },
        { bg: 'from-blue-600/20 to-blue-950/40 text-blue-300' },
        { bg: 'from-purple-600/20 to-purple-950/40 text-purple-300' },
    ];
    return styles[id % styles.length];
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <!-- Modal Body -->
                <div
                    class="relative w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 dark:border-outline-variant/10 bg-white dark:bg-slate-950 p-6 shadow-2xl md:p-8 animate-in fade-in zoom-in-95 duration-300"
                >
                    <!-- Close button -->
                    <button
                        @click="emit('close')"
                        class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 dark:border-outline-variant/10 bg-slate-100 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 transition-all hover:bg-slate-200 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white"
                    >
                        <X class="h-4 w-4" />
                    </button>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-5 md:gap-8">
                        <!-- Left Column (Cover) -->
                        <div class="flex flex-col items-center md:col-span-2">
                            <div
                                class="relative aspect-[3/4] w-full max-w-[180px] overflow-hidden rounded-2xl border border-slate-200 dark:border-outline-variant/10 bg-slate-100 dark:bg-slate-900 shadow-xl md:max-w-none"
                            >
                                <img
                                    v-if="book.cover_image"
                                    :src="storageUrl(book.cover_image)"
                                    :alt="book.title"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    :class="['flex h-full w-full flex-col items-center justify-center bg-gradient-to-br p-6', getBookCoverStyle(book.id).bg]"
                                >
                                    <BookOpen class="h-10 w-10 opacity-40 mb-2" />
                                    <h4 class="line-clamp-4 text-center font-serif text-xs font-bold text-white">{{ book.title }}</h4>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                <span
                                    class="rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-wider"
                                    :class="Number(book.price) > 0 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'"
                                >
                                    {{ Number(book.price) > 0 ? 'Libro Físico' : 'Recurso Digital' }}
                                </span>
                                <span
                                    v-if="Number(book.price) > 0 && book.stock !== undefined && book.stock <= 3 && book.stock > 0"
                                    class="rounded-full bg-amber-500 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-white shadow-lg shadow-amber-500/20 animate-pulse"
                                >
                                    ¡Últimos {{ book.stock }}!
                                </span>
                                <span
                                    v-else-if="Number(book.price) > 0 && book.stock === 0"
                                    class="rounded-full bg-red-500/20 border border-red-500/30 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-red-400"
                                >
                                    Agotado
                                </span>
                            </div>
                        </div>

                        <!-- Right Column (Info) -->
                        <div class="flex flex-col md:col-span-3">
                             <span class="text-[9px] font-black uppercase tracking-[0.25em] text-primary dark:text-primary/60 mb-1">Publicación Académica</span>
                            <h2 class="font-serif text-xl font-bold leading-tight text-slate-900 dark:text-white md:text-2xl mb-1.5">
                                {{ book.title }}
                            </h2>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-on-surface-variant/80 mb-5">
                                Por: <span class="text-slate-900 dark:text-white">{{ book.author || 'Instituto IEE' }}</span>
                            </p>

                            <!-- Synopsis container -->
                            <div class="flex-1">
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-2 flex items-center gap-1.5">
                                    <Info class="h-3.5 w-3.5" />
                                    Sinopsis de la obra
                                </h3>
                                 <div class="max-h-[180px] overflow-y-auto pr-2 text-xs leading-relaxed text-slate-600 dark:text-on-surface-variant/90 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
                                    <p class="whitespace-pre-line">{{ book.description }}</p>
                                </div>
                            </div>

                            <!-- Footer Section inside Modal -->
                            <div class="mt-6 border-t border-outline-variant/10 pt-4 flex items-center justify-between">
                                 <div class="font-serif">
                                    <template v-if="Number(book.price) > 0">
                                        <span v-if="Number(book.sale_price) > 0" class="text-xs text-slate-400 dark:text-on-surface-variant/60 line-through mr-1.5">S/ {{ book.price }}</span>
                                        <span class="text-xl font-bold text-slate-900 dark:text-white">S/ {{ Number(book.sale_price) > 0 ? book.sale_price : book.price }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Gratuito</span>
                                    </template>
                                </div>

                                <div class="flex items-center gap-3">
                                     <button
                                         @click="emit('close')"
                                         class="rounded-xl border border-slate-200 dark:border-outline-variant/10 bg-slate-50 dark:bg-slate-900 px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 transition-all hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white"
                                     >
                                         Cerrar
                                     </button>

                                    <!-- Main Action Button -->
                                    <button
                                        v-if="Number(book.price) > 0"
                                        :disabled="!book.can_purchase"
                                        @click="emit('action', book)"
                                        class="flex items-center gap-1.5 rounded-xl px-5 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white transition-all"
                                        :class="book.can_purchase 
                                            ? 'bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 hover:-translate-y-0.5' 
                                            : 'bg-red-500/10 text-red-500 cursor-not-allowed border border-red-500/20'"
                                    >
                                        <ShoppingBag class="h-3.5 w-3.5" />
                                        {{ book.can_purchase ? 'Comprar' : 'Agotado' }}
                                    </button>

                                    <button
                                        v-else
                                        @click="emit('action', book)"
                                        class="flex items-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-[10px] font-bold uppercase tracking-wider text-on-primary transition-all hover:bg-primary/95 shadow-lg shadow-primary/10 hover:-translate-y-0.5"
                                    >
                                        <Download class="h-3.5 w-3.5" />
                                        Descargar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #334155;
}
</style>
