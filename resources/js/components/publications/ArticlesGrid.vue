<script setup lang="ts">
import type { PublicationArticle } from '@/types/publications';
import { storageUrl } from '@/lib/storageUrl';

defineProps<{
    articles: PublicationArticle[];
}>();

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric' });
}

function estimateReadingTime(title: string) {
    const wpm = 180;
    const words = title.split(/\s+/).length + 30;
    return Math.max(3, Math.ceil(words / wpm) + 2);
}

function getArticleDownloadLink(article: PublicationArticle) {
    if (article.download_url && article.download_url.startsWith('http')) return article.download_url;
    // @ts-expect-error ziggy
    return route('student.publications.articles.download', article.id);
}
</script>

<template>
    <div>
        <div v-if="!articles.length" class="py-24 text-center bg-surface-container-low rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 max-w-3xl mx-auto flex flex-col items-center justify-center p-8 space-y-4">
            <div class="p-4 rounded-full bg-primary/5 text-primary/40">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-on-surface">Sin análisis estratégico</h3>
            <p class="text-xs text-on-surface-variant max-w-sm">No hemos encontrado ningún artículo o medio que coincida con la búsqueda.</p>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <article v-for="article in articles" :key="article.id" class="flex flex-col sm:flex-row gap-6 bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-xl hover:border-primary/20 transition-all duration-500 overflow-hidden group hover:-translate-y-1">
                    <div class="w-full sm:w-40 h-40 flex-shrink-0 overflow-hidden bg-surface-container-low rounded-2xl border border-outline-variant/10 relative">
                        <img v-if="article.thumbnail" :src="storageUrl(article.thumbnail)" :alt="article.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out">
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10 text-primary/20">
                            <svg class="w-12 h-12 stroke-current" fill="none" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center space-y-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-primary bg-primary/5 px-3 py-1 rounded-full border border-primary/10">{{ article.media }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/50">{{ formatDate(article.published_at) }}</span>
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-widest text-[#AA7C11] bg-[#AA7C11]/5 px-2.5 py-1 rounded-full border border-[#AA7C11]/15 shadow-inner">
                                <span class="material-symbols-outlined text-[10px]" translate="no" style="font-size: 11px;">schedule</span>
                                Lectura: {{ estimateReadingTime(article.title) }} min
                            </span>
                        </div>

                        <h3 class="font-serif text-lg font-bold text-on-surface group-hover:text-primary transition-colors leading-snug line-clamp-2">{{ article.title }}</h3>

                        <a :href="getArticleDownloadLink(article)" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-primary border-b border-primary/20 pb-1 w-fit hover:border-primary transition-all group-hover:gap-2.5">
                            Lectura Completa
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </article>
            </div>

        </div>
    </div>
</template>
