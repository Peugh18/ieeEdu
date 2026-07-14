<script setup lang="ts">
import { storageUrl } from '@/lib/storageUrl';
import type { PublicationArticle } from '@/types/publications';

defineProps<{
    articles: PublicationArticle[];
}>();

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric' });
}


function getArticleDownloadLink(article: PublicationArticle) {
    if (article.download_url && article.download_url.startsWith('http')) return article.download_url;
    // @ts-expect-error ziggy
    return route('student.publications.articles.download', article.id);
}
</script>

<template>
    <div>
        <div
            v-if="!articles.length"
            class="mx-auto flex max-w-3xl flex-col items-center justify-center space-y-4 rounded-[2.5rem] border-2 border-dashed border-outline-variant/30 bg-surface-container-low p-8 py-24 text-center"
        >
            <div class="rounded-full bg-primary/5 p-4 text-primary/40">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                    />
                </svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-on-surface">Sin análisis estratégico</h3>
            <p class="max-w-sm text-xs text-on-surface-variant">No hemos encontrado ningún artículo o medio que coincida con la búsqueda.</p>
        </div>

        <div v-else>
            <div class="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <article
                    v-for="article in articles"
                    :key="article.id"
                    class="group flex flex-col gap-6 overflow-hidden rounded-3xl border border-outline-variant/15 bg-surface p-5 transition-all duration-500 hover:-translate-y-1 hover:border-primary/20 hover:shadow-xl sm:flex-row"
                >
                    <div
                        class="relative h-40 w-full flex-shrink-0 overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-low sm:w-40"
                    >
                        <img
                            v-if="article.thumbnail"
                            :src="storageUrl(article.thumbnail)"
                            :alt="article.title"
                            class="duration-[1.5s] h-full w-full object-cover transition-transform ease-out group-hover:scale-105"
                        />
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10 text-primary/20">
                            <svg class="h-12 w-12 stroke-current" fill="none" stroke-width="1.5" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                                />
                            </svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                    </div>

                    <div class="flex flex-1 flex-col justify-center space-y-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span
                                class="rounded-full border border-primary/10 bg-primary/5 px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-primary"
                                >{{ article.media }}</span
                            >
                            <span class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/50">{{
                                formatDate(article.published_at)
                            }}</span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full border border-[#AA7C11]/15 bg-[#AA7C11]/5 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-[#AA7C11] shadow-inner"
                            >
                                <span class="material-symbols-outlined text-[10px]" translate="no" style="font-size: 11px">menu_book</span>
                                Lectura Recomendada
                            </span>
                        </div>

                        <h3 class="line-clamp-2 font-serif text-lg font-bold leading-snug text-on-surface transition-colors group-hover:text-primary">
                            {{ article.title }}
                        </h3>

                        <a
                            :href="getArticleDownloadLink(article)"
                            target="_blank"
                            class="inline-flex w-fit items-center gap-1.5 border-b border-primary/20 pb-1 text-[10px] font-bold uppercase tracking-widest text-primary transition-all hover:border-primary group-hover:gap-2.5"
                        >
                            Lectura Completa
                            <svg
                                class="h-3.5 w-3.5 transform transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                />
                            </svg>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</template>
