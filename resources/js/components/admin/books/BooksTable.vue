<script setup lang="ts">
import type { Book } from '@/types/book';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import { BookOpen, Edit2, Trash2, Download } from 'lucide-vue-next';

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
        <div v-for="book in books" :key="book.id" class="flex flex-col group bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-[0_20px_40px_rgba(15,23,42,0.06)] hover:border-slate-200/80 transition-all duration-500 overflow-hidden hover:-translate-y-1.5">
            <div class="relative h-48 bg-gradient-to-br from-slate-50 to-slate-100/50 overflow-hidden border-b border-slate-100 flex items-center justify-center">
                <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                <div v-else class="flex h-full w-full items-center justify-center text-slate-300"><BookOpen class="h-10 w-10 stroke-[1.5]" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-transparent opacity-60"></div>
                <div class="absolute top-4 left-4"><span class="px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-md text-[9px] font-bold tracking-widest uppercase text-slate-700 shadow-sm border border-slate-200/50">{{ book.category }}</span></div>
                <div v-if="Number(book.price) === 0" class="absolute bottom-4 right-4"><span class="px-2.5 py-1 rounded-full bg-emerald-500 text-[9px] font-black tracking-widest uppercase text-white shadow-md">Gratis</span></div>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.15em] mb-1 leading-none">{{ book.author || 'Instituto IEE' }}</span>
                <h3 class="font-serif text-base font-bold text-slate-900 leading-snug group-hover:text-primary transition-colors italic line-clamp-2 mb-3" :title="book.title">{{ book.title }}</h3>
                
                <!-- Stats Indicators -->
                <div class="flex flex-wrap gap-1 mb-4">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-[9px] font-semibold text-blue-700">
                        <Download class="w-3 h-3" />
                        {{ book.downloads_count ?? 0 }} desc.
                    </span>
                    <span v-if="Number(book.approved_sales_count) > 0" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-violet-50 text-[9px] font-semibold text-violet-700">
                        {{ book.approved_sales_count }} ventas
                    </span>
                    <span v-if="Number(book.price) > 0 && book.stock !== null && book.stock !== undefined" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-semibold" :class="book.stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                        Stock: {{ book.stock }}
                    </span>
                </div>

                <div class="flex items-center justify-between border-t border-slate-100 pt-3 mt-auto">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Inversión</span>
                        <span class="text-sm font-serif font-black text-slate-900">{{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'Gratuito' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                        <button @click="emit('edit', book)" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-white transition-all duration-300" title="Editar">
                            <Edit2 class="h-3.5 w-3.5" />
                        </button>
                        <button @click="emit('destroy', book.id)" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all duration-300" title="Eliminar">
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad de la Obra</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Categoría</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estado de Acceso</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Stock</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Descargas</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Ventas</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Inversión</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="book in books" :key="book.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-5 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 bg-slate-50 border border-slate-200/50 rounded-xl overflow-hidden shadow-inner flex-shrink-0 relative">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300"><BookOpen class="h-5 w-5" /></div>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ book.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ book.author || 'Instituto IEE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex rounded-xl bg-slate-50 border border-slate-200/50 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm group-hover:border-slate-300 transition-colors">
                                {{ book.category }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                    :class="Number(book.price) === 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-700/10' : 'bg-amber-50 text-amber-700 ring-amber-700/10'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-current" :class="Number(book.price) === 0 ? 'animate-pulse' : ''"></div>
                                    {{ Number(book.price) === 0 ? 'Abierto' : 'Exclusivo' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span v-if="Number(book.price) > 0 && book.stock !== null && book.stock !== undefined" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold tabular-nums" :class="book.stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                                {{ book.stock }}
                            </span>
                            <span v-else class="text-[10px] font-bold text-slate-400">∞</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold tabular-nums">
                                <Download class="w-3.5 h-3.5" />
                                {{ book.downloads_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold tabular-nums">
                                {{ book.approved_sales_count ?? 0 }}
                                <span v-if="Number(book.total_earned) > 0" class="text-[10px] font-medium">· S/ {{ Number(book.total_earned).toFixed(2) }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-bold text-slate-900 tabular-nums tracking-tighter">{{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'Gratuito' }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                <button @click="emit('edit', book)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all duration-300" title="Editar">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="emit('destroy', book.id)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all duration-300" title="Eliminar">
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
