<script setup lang="ts">
import { Edit2, Trash2, Calendar, FolderOpen, BookMarked } from 'lucide-vue-next';
import type { Category } from '@/types/category';

defineProps<{
    categories: Category[];
}>();

const emit = defineEmits<{
    (e: 'edit', cat: Category): void;
    (e: 'destroy', cat: Category): void;
}>();

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

function initials(n: string) {
    return n.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

const avatarColors = ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'];
function getACls(id: number) { return avatarColors[id % 5]; }
</script>

<template>
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left min-w-[800px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad / Slug</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Cursos Vinculados</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Creación</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!categories.length" class="bg-white">
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-20">
                                <FolderOpen class="w-12 h-12" />
                                <p class="text-sm font-bold uppercase tracking-widest">Sin resultados</p>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="cat in categories" :key="cat.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${getACls(cat.id)} shadow-sm shadow-slate-200 group-hover:scale-95 transition-transform duration-500`">
                                    {{ initials(cat.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ cat.name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5 uppercase tracking-wider line-clamp-1">{{ cat.slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold tracking-tight bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200/50">
                                    <BookMarked class="h-3.5 w-3.5 text-primary" />
                                    {{ cat.courses_count }} <span class="font-medium text-slate-400 ml-0.5">cursos</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-500">{{ formatDate(cat.created_at) }}</span>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <Calendar class="h-2.5 w-2.5 text-slate-300" />
                                    <span class="text-[9px] text-slate-300 font-medium uppercase tracking-widest">Registro Base</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-all duration-300">
                                <button @click="emit('edit', cat)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Editar Categoría">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="emit('destroy', cat)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Eliminar Permanente">
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

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; line-clamp: 1; }
</style>
