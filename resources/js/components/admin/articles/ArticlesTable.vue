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
        <div v-for="article in articles" :key="article.id" class="flex flex-col group bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm hover:shadow-[0_30px_60px_rgba(87,87,42,0.12)] transition-all duration-700 overflow-hidden hover:-translate-y-3">
            <div class="relative aspect-[4/3] bg-surface-container-highest overflow-hidden border-b border-outline-variant/10">
                <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                <div v-else class="flex h-full w-full items-center justify-center text-primary/10"><Calendar class="h-16 w-16" /></div>
                <div class="absolute inset-0 bg-gradient-to-t from-on-background/60 via-transparent to-transparent opacity-60"></div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <span class="text-[10px] font-bold text-primary/60 uppercase tracking-[0.2em] mb-2 leading-none">{{ article.media }}</span>
                <h3 class="font-serif text-xl font-bold text-on-background leading-snug group-hover:text-primary transition-colors italic line-clamp-2 mb-4" :title="article.title">{{ article.title }}</h3>
                <div class="flex items-center justify-between border-t border-surface-container-highest pt-4 mt-auto">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Publicado</span>
                        <span class="text-[10px] font-black text-on-surface uppercase tracking-tighter">{{ formatDate(article.published_at) }}</span>
                    </div>
                    <div class="flex items-center bg-surface-container-highest/30 p-1.5 rounded-xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-lg transition-all duration-700">
                        <AdminIconButton as="a" :href="getDownloadUrl(article)" target="_blank" :disabled="article.download_url === 'PDF_DOCUMENT_PENDING'" size="sm" title="Ver enlace"><template #default="{ iconClass }"><ExternalLink :class="iconClass" /></template></AdminIconButton>
                        <AdminIconButton variant="outline" @click="emit('edit', article)" size="sm" title="Editar"><template #default="{ iconClass }"><Edit2 :class="iconClass" /></template></AdminIconButton>
                        <AdminIconButton variant="danger" @click="emit('destroy', article.id)" size="sm" title="Eliminar"><template #default="{ iconClass }"><Trash2 :class="iconClass" /></template></AdminIconButton>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="rounded-[3rem] border border-outline-variant/10 bg-white overflow-hidden shadow-2xl shadow-surface-tint/5 relative z-0 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead><tr class="bg-surface-container-highest/40 border-b border-outline-variant/10">
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60">Identidad del Artículo</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Publicación</th>
                    <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <tr v-for="article in articles" :key="article.id" class="group transition-all hover:bg-primary/[0.02] duration-500">
                        <td class="px-10 py-7 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-14 bg-surface-container-low border border-outline-variant/10 rounded-2xl overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-primary/10"><Calendar class="h-6 w-6" /></div>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-[15px] font-bold text-on-background leading-tight">{{ article.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ article.media }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-black text-on-surface uppercase tracking-tighter">{{ formatDate(article.published_at) }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-widest text-primary/40 mt-0.5">Vigente</span>
                            </div>
                        </td>
                        <td class="px-10 py-7">
                            <div class="flex items-center justify-center">
                                <div class="flex items-center bg-surface-container-highest/30 p-2 rounded-2xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-xl transition-all duration-700 opacity-100 lg:opacity-40 group-hover:opacity-100 transform lg:group-hover:-translate-x-2">
                                    <AdminIconButton as="a" variant="outline" :href="getDownloadUrl(article)" target="_blank" :disabled="article.download_url === 'PDF_DOCUMENT_PENDING'" size="sm" title="Ver enlace"><template #default="{ iconClass }"><ExternalLink :class="iconClass" /></template></AdminIconButton>
                                    <AdminIconButton variant="outline" @click="emit('edit', article)" size="sm" title="Editar"><template #default="{ iconClass }"><Edit2 :class="iconClass" /></template></AdminIconButton>
                                    <AdminIconButton variant="danger" @click="emit('destroy', article.id)" size="sm" title="Eliminar"><template #default="{ iconClass }"><Trash2 :class="iconClass" /></template></AdminIconButton>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
