<script setup lang="ts">
import type { Book } from '@/types/book';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import { BookOpen, Edit2, Trash2 } from 'lucide-vue-next';

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
        <div v-for="book in books" :key="book.id" class="flex flex-col group bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm hover:shadow-[0_30px_60px_rgba(87,87,42,0.12)] transition-all duration-700 overflow-hidden hover:-translate-y-3">
            <div class="relative aspect-[3/4] bg-surface-container-highest overflow-hidden border-b border-outline-variant/10">
                <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                <div v-else class="flex h-full w-full items-center justify-center text-primary/10"><BookOpen class="h-16 w-16" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-on-background/60 via-transparent to-transparent opacity-60"></div>
                <div class="absolute top-6 left-6"><span class="px-4 py-2 rounded-sm bg-white/10 backdrop-blur-xl text-[10px] font-bold tracking-widest uppercase text-white border border-white/20">{{ book.category }}</span></div>
                <div v-if="Number(book.price) === 0" class="absolute bottom-6 right-6"><span class="px-4 py-2 rounded-full bg-emerald-500 text-[10px] font-black tracking-widest uppercase text-white shadow-lg">Gratis</span></div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <span class="text-[10px] font-bold text-primary/60 uppercase tracking-[0.2em] mb-2 leading-none">{{ book.author || 'Instituto IEE' }}</span>
                <h3 class="font-serif text-xl font-bold text-on-background leading-snug group-hover:text-primary transition-colors italic line-clamp-2 mb-4" :title="book.title">{{ book.title }}</h3>
                <div class="flex items-center justify-between border-t border-surface-container-highest pt-4 mt-auto">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Inversión</span>
                        <span class="text-base font-serif font-black text-on-surface">{{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'Acceso Gratuito' }}</span>
                    </div>
                    <div class="flex items-center bg-surface-container-highest/30 p-1.5 rounded-xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-lg transition-all duration-700">
                        <AdminIconButton variant="outline" @click="emit('edit', book)" size="sm" title="Editar"><template #default="{ iconClass }"><Edit2 :class="iconClass" /></template></AdminIconButton>
                        <AdminIconButton variant="danger" @click="emit('destroy', book.id)" size="sm" title="Eliminar"><template #default="{ iconClass }"><Trash2 :class="iconClass" /></template></AdminIconButton>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="rounded-[3rem] border border-outline-variant/10 bg-white overflow-hidden shadow-2xl shadow-surface-tint/5 relative z-0 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead><tr class="bg-surface-container-highest/40 border-b border-outline-variant/10">
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60">Identidad de la Obra</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60">Categoría</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Estado de Acceso</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-right">Inversión</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <tr v-for="book in books" :key="book.id" class="group transition-all hover:bg-primary/[0.02] duration-500">
                        <td class="px-10 py-7 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-20 bg-surface-container-low border border-outline-variant/10 rounded-[1rem] overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-primary/10"><BookOpen class="h-6 w-6" /></div>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-[15px] font-bold text-on-background leading-tight">{{ book.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ book.author || 'Instituto IEE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7"><span class="inline-flex rounded-xl bg-white px-4 py-2 text-[11px] font-black uppercase tracking-widest text-primary border border-outline-variant/20 shadow-sm group-hover:border-primary/30 transition-colors">{{ book.category }}</span></td>
                        <td class="px-10 py-7 text-center">
                            <div class="flex justify-center"><div class="flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white border border-outline-variant/10 shadow-sm w-fit group-hover:shadow-md transition-all duration-500" :class="Number(book.price) === 0 ? 'border-emerald-500/20 shadow-emerald-500/[0.03]' : 'border-amber-500/20 shadow-amber-500/[0.03]'">
                                <div class="relative flex h-2 w-2"><span v-if="Number(book.price) === 0" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2" :class="Number(book.price) === 0 ? 'bg-emerald-500' : 'bg-amber-400'"></span></div>
                                <span class="text-[11px] font-black uppercase tracking-[0.15em] mt-[0.5px]" :class="Number(book.price) === 0 ? 'text-emerald-700' : 'text-amber-700'">{{ Number(book.price) === 0 ? 'Abierto' : 'Exclusivo' }}</span>
                            </div></div>
                        </td>
                        <td class="px-10 py-7 text-right"><span class="text-[15px] font-bold text-on-surface tabular-nums tracking-tighter">{{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'S/ 0.00' }}</span></td>
                        <td class="px-10 py-7">
                            <div class="flex items-center justify-center"><div class="flex items-center bg-surface-container-highest/30 p-2 rounded-2xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-xl transition-all duration-700 opacity-100 lg:opacity-40 group-hover:opacity-100 transform lg:group-hover:-translate-x-2">
                                <AdminIconButton variant="outline" @click="emit('edit', book)" size="sm" title="Editar"><template #default="{ iconClass }"><Edit2 :class="iconClass" /></template></AdminIconButton>
                                <AdminIconButton variant="danger" @click="emit('destroy', book.id)" size="sm" title="Eliminar"><template #default="{ iconClass }"><Trash2 :class="iconClass" /></template></AdminIconButton>
                            </div></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
