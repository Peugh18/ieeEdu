<script setup lang="ts">
import type { Book } from '@/types/book';
import { BookOpen, Download, Edit2, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    books: Book[];
    viewMode: 'grid' | 'list';
}>();

const emit = defineEmits<{
    (e: 'edit', book: Book): void;
    (e: 'destroy', id: number): void;
}>();

function fmt(n: string | number) {
    const val = typeof n === 'string' ? parseFloat(n) : n;
    return isNaN(val) ? '0.00' : val.toLocaleString('es-PE', { minimumFractionDigits: 2 });
}
</script>

<template>
    <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div
            v-for="book in books"
            :key="book.id"
            class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm transition-all duration-500 hover:-translate-y-1.5 hover:border-slate-200/80 hover:shadow-[0_20px_40px_rgba(15,23,42,0.06)]"
        >
            <div
                class="relative flex h-48 items-center justify-center overflow-hidden border-b border-slate-100 bg-gradient-to-br from-slate-50 to-slate-100/50"
            >
                <img
                    v-if="book.cover_image"
                    :src="`/storage/${book.cover_image}`"
                    class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
                />
                <div v-else class="flex h-full w-full items-center justify-center text-slate-300"><BookOpen class="h-10 w-10 stroke-[1.5]" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-transparent opacity-60"></div>
                <div class="absolute left-4 top-4">
                    <span
                        class="rounded-md border border-slate-200/50 bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur-md"
                        >{{ Number(book.price) > 0 ? 'Físico' : 'Digital' }}</span
                    >
                </div>
                <div v-if="Number(book.price) === 0" class="absolute bottom-4 right-4">
                    <span class="rounded-full bg-emerald-500 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-white shadow-md"
                        >Gratis</span
                    >
                </div>
            </div>
            <div class="flex flex-1 flex-col p-5">
                <span class="mb-1 text-[9px] font-bold uppercase leading-none tracking-[0.15em] text-slate-400">{{
                    book.author || 'Instituto IEE'
                }}</span>
                <h3
                    class="mb-3 line-clamp-2 font-serif text-base font-bold italic leading-snug text-slate-900 transition-colors group-hover:text-primary"
                    :title="book.title"
                >
                    {{ book.title }}
                </h3>

                <!-- Stats Indicators -->
                <div class="mb-4 flex flex-wrap gap-1">
                    <span v-if="Number(book.price) === 0" class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[9px] font-semibold text-blue-700">
                        <Download class="h-3 w-3" />
                        {{ book.downloads_count ?? 0 }} desc.
                    </span>
                    <span
                        v-if="Number(book.approved_sales_count) > 0"
                        class="inline-flex items-center gap-1 rounded-md bg-violet-50 px-2 py-0.5 text-[9px] font-semibold text-violet-700"
                    >
                        {{ book.approved_sales_count }} ventas
                    </span>
                    <span
                        v-if="Number(book.price) > 0 && book.stock !== null && book.stock !== undefined"
                        class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[9px] font-semibold"
                        :class="book.stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                    >
                        Stock: {{ book.stock }}
                    </span>
                </div>

                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Inversión</span>
                        <span class="font-serif text-sm font-black text-slate-900">{{
                            Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'Gratuito'
                        }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 opacity-80 transition-opacity group-hover:opacity-100">
                        <button
                            @click="emit('edit', book)"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 transition-all duration-300 hover:border-primary hover:bg-white hover:text-primary"
                            title="Editar"
                        >
                            <Edit2 class="h-3.5 w-3.5" />
                        </button>
                        <button
                            @click="emit('destroy', book.id)"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 transition-all duration-300 hover:border-rose-500 hover:bg-rose-50 hover:text-rose-600"
                            title="Eliminar"
                        >
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-else
        class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm duration-700 animate-in fade-in slide-in-from-bottom-4"
    >
        <div class="custom-scrollbar overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad de la Obra</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Tipo</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estado de Acceso</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Stock</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Descargas</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Ventas</th>
                        <th class="px-6 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Inversión</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="book in books" :key="book.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                        <td class="relative px-8 py-5">
                            <div
                                class="absolute left-0 top-1/2 h-0 w-1 -translate-y-1/2 rounded-r-full bg-primary transition-all duration-500 group-hover:h-12"
                            ></div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="relative h-16 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200/50 bg-slate-50 shadow-inner"
                                >
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                        <BookOpen class="h-5 w-5" />
                                    </div>
                                </div>
                                <div class="flex min-w-0 flex-col">
                                    <h4
                                        class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary"
                                    >
                                        {{ book.title }}
                                    </h4>
                                    <span class="mt-1 text-[9px] font-bold uppercase tracking-widest text-[#9ca3af]">{{
                                        book.author || 'Instituto IEE'
                                    }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span
                                class="inline-flex rounded-xl border border-slate-200/50 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition-colors group-hover:border-slate-300"
                            >
                                {{ Number(book.price) > 0 ? 'Físico' : 'Digital' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                    :class="
                                        Number(book.price) === 0
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-700/10'
                                            : 'bg-amber-50 text-amber-700 ring-amber-700/10'
                                    "
                                >
                                    <div class="h-1.5 w-1.5 rounded-full bg-current" :class="Number(book.price) === 0 ? 'animate-pulse' : ''"></div>
                                    {{ Number(book.price) === 0 ? 'Abierto' : 'Exclusivo' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                v-if="Number(book.price) > 0"
                                class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold tabular-nums"
                                :class="(book.stock ?? 0) > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                            >
                                {{ book.stock ?? 0 }}
                            </span>
                            <span v-else class="text-[10px] font-bold text-slate-400">-</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                v-if="Number(book.price) === 0"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-bold tabular-nums text-blue-700"
                            >
                                <Download class="h-3.5 w-3.5" />
                                {{ book.downloads_count ?? 0 }}
                            </span>
                            <span v-else class="text-[10px] font-bold text-slate-400">-</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                v-if="Number(book.price) > 0"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-1.5 text-xs font-bold tabular-nums text-indigo-700"
                            >
                                {{ book.approved_sales_count ?? 0 }}
                                <span v-if="Number(book.total_earned) > 0" class="text-[10px] font-medium"
                                    >· S/ {{ Number(book.total_earned).toFixed(2) }}</span
                                >
                            </span>
                            <span v-else class="text-[10px] font-bold text-slate-400">-</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-bold tabular-nums tracking-tighter text-slate-900">{{
                                Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'Gratuito'
                            }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-opacity group-hover:opacity-100">
                                <button
                                    @click="emit('edit', book)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all duration-300 hover:border-primary hover:bg-slate-50 hover:text-primary"
                                    title="Editar"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button
                                    @click="emit('destroy', book.id)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all duration-300 hover:border-rose-500 hover:bg-rose-50 hover:text-rose-600"
                                    title="Eliminar"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
