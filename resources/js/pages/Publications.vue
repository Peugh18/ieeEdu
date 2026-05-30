<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PublicationsHero from '@/components/publications/PublicationsHero.vue';
import PublicationsTabs from '@/components/publications/PublicationsTabs.vue';
import BooksGrid from '@/components/publications/BooksGrid.vue';
import ArticlesGrid from '@/components/publications/ArticlesGrid.vue';
import type { PublicationBook, PublicationArticle, PublicationBanner } from '@/types/publications';

const props = defineProps<{
    books: PublicationBook[];
    articles: PublicationArticle[];
    isDashboard?: boolean;
    banner?: PublicationBanner | null;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Publicaciones e Investigación', href: '#' },
];

const currentTab = ref<'libros' | 'articulos'>('libros');
const searchQuery = ref('');
const booksPage = ref(1);
const articlesPage = ref(1);
const itemsPerPage = 6;

watch(searchQuery, () => { booksPage.value = 1; articlesPage.value = 1; });

function changeBooksPage(p: number) { booksPage.value = p; window.scrollTo({ top: 0, behavior: 'smooth' }); }
function changeArticlesPage(p: number) { articlesPage.value = p; window.scrollTo({ top: 0, behavior: 'smooth' }); }

const filteredBooks = computed(() => {
    if (!searchQuery.value.trim()) return props.books;
    const q = searchQuery.value.toLowerCase().trim();
    return props.books.filter(b => b.title.toLowerCase().includes(q) || (b.author && b.author.toLowerCase().includes(q)));
});

const filteredArticles = computed(() => {
    if (!searchQuery.value.trim()) return props.articles;
    const q = searchQuery.value.toLowerCase().trim();
    return props.articles.filter(a => a.title.toLowerCase().includes(q) || a.media.toLowerCase().includes(q));
});

const paginatedBooks = computed(() => {
    const start = (booksPage.value - 1) * itemsPerPage;
    return filteredBooks.value.slice(start, start + itemsPerPage);
});

const booksTotalPages = computed(() => Math.ceil(filteredBooks.value.length / itemsPerPage));

const paginatedArticles = computed(() => {
    const start = (articlesPage.value - 1) * itemsPerPage;
    return filteredArticles.value.slice(start, start + itemsPerPage);
});

const articlesTotalPages = computed(() => Math.ceil(filteredArticles.value.length / itemsPerPage));
</script>

<template>
    <Head title="Investigación y Publicaciones - IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28' : 'pt-0']">
                <PublicationsHero :banner="banner" :is-dashboard="isDashboard" />

                <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <PublicationsTabs v-model:current-tab="currentTab" v-model:search-query="searchQuery" />

                    <BooksGrid v-if="currentTab === 'libros'" :books="paginatedBooks" :total-pages="booksTotalPages" :current-page="booksPage" @change-page="changeBooksPage" />
                    <ArticlesGrid v-else :articles="paginatedArticles" :total-pages="articlesTotalPages" :current-page="articlesPage" @change-page="changeArticlesPage" />
                </section>
            </main>
        </div>
    </component>
</template>

<style scoped>
.limit-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }
</style>
