<script setup lang="ts">
import type { Category } from '@/types/category';
import { BookMarked, Calendar, Edit2, FolderOpen, Trash2 } from 'lucide-vue-next';

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
    return n
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

const avatarColors = [
    'bg-indigo-50 text-indigo-700',
    'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700',
    'bg-amber-50 text-amber-700',
    'bg-rose-50 text-rose-700',
];
function getACls(id: number) {
    return avatarColors[id % 5];
}
</script>

<template>
    <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-sm">
        <div class="custom-scrollbar overflow-x-auto">
            <table class="w-full min-w-[800px] text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad / Slug</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Cursos Vinculados</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Creación</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!categories.length" class="bg-white">
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-20">
                                <FolderOpen class="h-12 w-12" />
                                <p class="text-sm font-bold uppercase tracking-widest">Sin resultados</p>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="cat in categories" :key="cat.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div
                                    :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${getACls(cat.id)} shadow-sm shadow-slate-200 transition-transform duration-500 group-hover:scale-95`"
                                >
                                    {{ initials(cat.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary">
                                        {{ cat.name }}
                                    </p>
                                    <p class="mt-0.5 line-clamp-1 font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ cat.slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-50 px-4 py-2 text-xs font-bold tracking-tight text-slate-700 ring-1 ring-inset ring-slate-200/50"
                                >
                                    <BookMarked class="h-3.5 w-3.5 text-primary" />
                                    {{ cat.courses_count }} <span class="ml-0.5 font-medium text-slate-400">cursos</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-500">{{ formatDate(cat.created_at) }}</span>
                                <div class="mt-0.5 flex items-center gap-1">
                                    <Calendar class="h-2.5 w-2.5 text-slate-300" />
                                    <span class="text-[9px] font-medium uppercase tracking-widest text-slate-300">Registro Base</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-all duration-300 group-hover:opacity-100">
                                <button
                                    @click="emit('edit', cat)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-primary hover:bg-slate-50 hover:text-primary"
                                    title="Editar Categoría"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button
                                    @click="emit('destroy', cat)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-rose-500 hover:bg-rose-50 hover:text-rose-600"
                                    title="Eliminar Permanente"
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

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    line-clamp: 1;
}
</style>
