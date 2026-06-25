<script setup lang="ts">
import CatalogPagination from '@/components/catalog/CatalogPagination.vue';
import Navigation from '@/components/landing/Navigation.vue';
import ArticlesGrid from '@/components/publications/ArticlesGrid.vue';
import BooksGrid from '@/components/publications/BooksGrid.vue';
import PublicationsHero from '@/components/publications/PublicationsHero.vue';
import PublicationsTabs from '@/components/publications/PublicationsTabs.vue';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginatedResponse } from '@/types/pagination';
import type { PublicationArticle, PublicationBanner, PublicationBook } from '@/types/publications';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    books: PaginatedResponse<PublicationBook>;
    articles: PaginatedResponse<PublicationArticle>;
    isDashboard?: boolean;
    banner?: PublicationBanner | null;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Publicaciones e Investigación', href: '#' },
];

const currentTab = ref<'libros' | 'articulos'>('libros');
const searchQuery = ref('');

const booksLinks = usePaginationLinks(props.books.links);
const articlesLinks = usePaginationLinks(props.articles.links);

function handleSearch(q: string) {
    searchQuery.value = q;
    const routeName = props.isDashboard ? 'student.explore.publications' : 'publicaciones.index';
    router.get(route(routeName), { search: q || undefined }, { preserveState: true, preserveScroll: true, replace: true });
}
</script>

<template>
    <Head title="Investigación y Publicaciones - IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28' : 'pt-0']">
                <PublicationsHero :banner="banner" :is-dashboard="isDashboard" />

                <section class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
                    <PublicationsTabs v-model:current-tab="currentTab" v-model:search-query="searchQuery" @update:search-query="handleSearch" />

                    <BooksGrid v-if="currentTab === 'libros'" :books="books.data" />
                    <CatalogPagination v-if="currentTab === 'libros' && booksLinks.length > 0" :links="booksLinks" />

                    <ArticlesGrid v-else :articles="articles.data" />
                    <CatalogPagination v-if="currentTab === 'articulos' && articlesLinks.length > 0" :links="articlesLinks" />
                </section>
            </main>
        </div>
    </component>
</template>

<style scoped>
.limit-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 130px;
}
</style>
