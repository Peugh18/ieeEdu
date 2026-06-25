<script setup lang="ts">
import type { Article } from '@/types/article';
import { Calendar, Edit2, ExternalLink, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    articles: Article[];
    viewMode: 'grid' | 'list';
}>();

const emit = defineEmits<{
    (e: 'edit', article: Article): void;
    (e: 'destroy', id: number): void;
}>();

function formatDate(date?: string) {
    if (!date) return 'Sin fecha';
    try {
        return new Date(date).toLocaleDateString('es-PE', { month: 'long', year: 'numeric' });
    } catch {
        return date;
    }
}

function getDownloadUrl(article: Article) {
    return article.download_url === 'PDF_DOCUMENT_PENDING' && article.id ? '#' : article.download_url;
}
</script>

<template>
    <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div
            v-for="article in articles"
            :key="article.id"
            class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm transition-all duration-500 hover:-translate-y-1.5 hover:border-slate-200/80 hover:shadow-[0_20px_40px_rgba(15,23,42,0.06)]"
        >
            <div
                class="relative flex h-44 items-center justify-center overflow-hidden border-b border-slate-100 bg-gradient-to-br from-slate-50 to-slate-100/50"
            >
                <img
                    v-if="article.thumbnail"
                    :src="`/storage/${article.thumbnail}`"
                    class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
                />
                <div v-else class="flex h-full w-full items-center justify-center text-slate-300"><Calendar class="h-10 w-10 stroke-[1.5]" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-transparent opacity-60"></div>
                <div class="absolute left-4 top-4">
                    <span
                        class="rounded-md border border-slate-200/50 bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur-md"
                        >Prensa</span
                    >
                </div>
            </div>
            <div class="flex flex-1 flex-col p-5">
                <span class="mb-1 text-[9px] font-bold uppercase leading-none tracking-[0.15em] text-slate-400">{{ article.media }}</span>
                <h3
                    class="mb-3 line-clamp-2 font-serif text-base font-bold italic leading-snug text-slate-900 transition-colors group-hover:text-primary"
                    :title="article.title"
                >
                    {{ article.title }}
                </h3>

                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Publicado</span>
                        <span class="text-xs font-semibold text-slate-700">{{ formatDate(article.published_at) }}</span>
                    </div>
                    <div class="flex items-center gap-1 opacity-80 transition-opacity group-hover:opacity-100">
                        <a
                            :href="getDownloadUrl(article)"
                            target="_blank"
                            :class="[
                                'flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 transition-all duration-300 hover:border-primary hover:bg-white hover:text-primary',
                                { 'pointer-events-none opacity-50': article.download_url === 'PDF_DOCUMENT_PENDING' },
                            ]"
                            title="Ver enlace"
                        >
                            <ExternalLink class="h-3.5 w-3.5" />
                        </a>
                        <button
                            @click="emit('edit', article)"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 transition-all duration-300 hover:border-primary hover:bg-white hover:text-primary"
                            title="Editar"
                        >
                            <Edit2 class="h-3.5 w-3.5" />
                        </button>
                        <button
                            @click="emit('destroy', article.id)"
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
            <table class="w-full min-w-[700px] border-collapse text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad del Artículo</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Publicación</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="article in articles" :key="article.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                        <td class="relative px-8 py-5">
                            <div
                                class="absolute left-0 top-1/2 h-0 w-1 -translate-y-1/2 rounded-r-full bg-primary transition-all duration-500 group-hover:h-12"
                            ></div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="relative h-12 w-16 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200/50 bg-slate-50 shadow-inner"
                                >
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                        <Calendar class="h-5 w-5" />
                                    </div>
                                </div>
                                <div class="flex min-w-0 flex-col">
                                    <h4
                                        class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary"
                                        :title="article.title"
                                    >
                                        {{ article.title }}
                                    </h4>
                                    <span class="mt-1 text-[9px] font-bold uppercase tracking-widest text-[#9ca3af]">{{ article.media }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200/50 bg-slate-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-700"
                            >
                                {{ formatDate(article.published_at) }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-opacity group-hover:opacity-100">
                                <a
                                    :href="getDownloadUrl(article)"
                                    target="_blank"
                                    :class="[
                                        'flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all duration-300 hover:border-primary hover:bg-slate-50 hover:text-primary',
                                        { 'pointer-events-none opacity-50': article.download_url === 'PDF_DOCUMENT_PENDING' },
                                    ]"
                                    title="Ver enlace"
                                >
                                    <ExternalLink class="h-4 w-4" />
                                </a>
                                <button
                                    @click="emit('edit', article)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all duration-300 hover:border-primary hover:bg-slate-50 hover:text-primary"
                                    title="Editar"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button
                                    @click="emit('destroy', article.id)"
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
