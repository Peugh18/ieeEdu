<script setup lang="ts">
import type { Article } from '@/types/article';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import { Edit2, Trash2, Calendar, ExternalLink } from 'lucide-vue-next';

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
    try { return new Date(date).toLocaleDateString('es-PE', { month: 'long', year: 'numeric' }); } catch { return date; }
}

function getDownloadUrl(article: Article) {
    return article.download_url === 'PDF_DOCUMENT_PENDING' && article.id ? '#' : article.download_url;
}
</script>

<template>
    <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div v-for="article in articles" :key="article.id" class="flex flex-col group bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-[0_20px_40px_rgba(15,23,42,0.06)] hover:border-slate-200/80 transition-all duration-500 overflow-hidden hover:-translate-y-1.5">
            <div class="relative h-44 bg-gradient-to-br from-slate-50 to-slate-100/50 overflow-hidden border-b border-slate-100 flex items-center justify-center">
                <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                <div v-else class="flex h-full w-full items-center justify-center text-slate-300"><Calendar class="h-10 w-10 stroke-[1.5]" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-transparent opacity-60"></div>
                <div class="absolute top-4 left-4"><span class="px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-md text-[9px] font-bold tracking-widest uppercase text-slate-700 shadow-sm border border-slate-200/50">Prensa</span></div>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.15em] mb-1 leading-none">{{ article.media }}</span>
                <h3 class="font-serif text-base font-bold text-slate-900 leading-snug group-hover:text-primary transition-colors italic line-clamp-2 mb-3" :title="article.title">{{ article.title }}</h3>
                
                <div class="flex items-center justify-between border-t border-slate-100 pt-3 mt-auto">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Publicado</span>
                        <span class="text-xs font-semibold text-slate-700">{{ formatDate(article.published_at) }}</span>
                    </div>
                    <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                        <a :href="getDownloadUrl(article)" target="_blank" :class="[
                            'w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-white transition-all duration-300',
                            { 'opacity-50 pointer-events-none': article.download_url === 'PDF_DOCUMENT_PENDING' }
                        ]" title="Ver enlace">
                            <ExternalLink class="h-3.5 w-3.5" />
                        </a>
                        <button @click="emit('edit', article)" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-white transition-all duration-300" title="Editar">
                            <Edit2 class="h-3.5 w-3.5" />
                        </button>
                        <button @click="emit('destroy', article.id)" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all duration-300" title="Eliminar">
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad del Artículo</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Publicación</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="article in articles" :key="article.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-5 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-12 bg-slate-50 border border-slate-200/50 rounded-xl overflow-hidden shadow-inner flex-shrink-0 relative">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300"><Calendar class="h-5 w-5" /></div>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1" :title="article.title">{{ article.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ article.media }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-slate-50 text-slate-700 border border-slate-200/50">
                                {{ formatDate(article.published_at) }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                <a :href="getDownloadUrl(article)" target="_blank" :class="[
                                    'w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all duration-300',
                                    { 'opacity-50 pointer-events-none': article.download_url === 'PDF_DOCUMENT_PENDING' }
                                ]" title="Ver enlace">
                                    <ExternalLink class="h-4 w-4" />
                                </a>
                                <button @click="emit('edit', article)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all duration-300" title="Editar">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="emit('destroy', article.id)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all duration-300" title="Eliminar">
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
