<script setup lang="ts">
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import ArticleForm from '@/components/admin/articles/ArticleForm.vue';
import ArticlesTable from '@/components/admin/articles/ArticlesTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Article } from '@/types/article';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

const props = defineProps<{ articles: PaginatedResponse<Article> }>();

const showModal = ref(false);
const editingArticle = ref<Article | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({ title: '', media: '', published_at: '', thumbnail: null as File | null, file_path: null as File | null, download_url: '' });
const sourceType = ref<'link' | 'pdf'>('link');

function openCreate() {
    editingArticle.value = null;
    form.reset();
    sourceType.value = 'link';
    imagePreview.value = null;
    showModal.value = true;
}

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openCreate') === '1') {
        openCreate();
    }
});

function openEdit(article: Article) {
    editingArticle.value = article;
    form.title = article.title;
    form.media = article.media;
    form.published_at = article.published_at;
    form.download_url = article.download_url;
    form.thumbnail = null;
    form.file_path = null;
    sourceType.value = article.download_url?.startsWith('http') ? 'link' : 'pdf';
    imagePreview.value = article.thumbnail ? `/storage/${article.thumbnail}` : null;
    showModal.value = true;
}

function submit() {
    if (sourceType.value === 'pdf' && !form.download_url) form.download_url = 'PDF_DOCUMENT_PENDING';
    const url = editingArticle.value ? route('admin.articles.update', { article: editingArticle.value.id }) : route('admin.articles.store');
    
    // In Laravel/Inertia, file uploads must ALWAYS use POST, even when updating. We fake the PUT request via _method.
    form.transform((d) => (editingArticle.value ? { ...d, _method: 'PUT' } : d)).post(url, {
        forceFormData: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este artículo?')) form.delete(route('admin.articles.destroy', { article: id }));
}

const searchQuery = ref('');
const viewMode = ref<'grid' | 'list'>('list');
const filteredArticles = ref<Article[]>(props.articles.data);

watch(
    [searchQuery],
    () => {
        filteredArticles.value = props.articles.data.filter((a) => {
            const q = searchQuery.value.toLowerCase();
            return !q || a.title.toLowerCase().includes(q) || a.media.toLowerCase().includes(q);
        });
    },
    { immediate: true },
);

watch(
    () => props.articles.data,
    (newData) => {
        filteredArticles.value = newData.filter((a) => {
            const q = searchQuery.value.toLowerCase();
            return !q || a.title.toLowerCase().includes(q) || a.media.toLowerCase().includes(q);
        });
    },
);
</script>

<template>
    <Head title="Gestión de Artículos - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- PAGE HEADER + TABS IN ONE ROW -->
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div class="min-w-0 space-y-1.5">
                    <h1 class="font-serif text-3xl font-bold leading-tight text-on-surface md:text-4xl lg:text-5xl">
                        Gestión de <span class="italic text-primary">Publicaciones</span>
                    </h1>
                    <p class="text-sm font-medium text-on-surface-variant">Administra el catálogo de libros y artículos.</p>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <Link
                        :href="route('admin.books.index', { openCreate: 1 })"
                        class="inline-flex items-center gap-2 rounded-2xl border border-outline-variant/30 bg-surface-container-low px-6 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant shadow-sm transition-all hover:bg-surface-container-high hover:text-on-surface active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo Libro
                    </Link>
                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg transition-all hover:bg-primary/90 active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo Artículo
                    </button>
                </div>
            </div>

            <!-- SUB-NAV TABS -->
            <div class="flex gap-6 border-b border-outline-variant/20">
                <Link
                    :href="route('admin.books.index')"
                    class="border-b-2 px-1 pb-3 text-xs font-black uppercase tracking-wider transition-colors border-transparent text-on-surface-variant hover:border-outline-variant hover:text-on-surface"
                >
                    📚 Libros
                </Link>
                <Link
                    :href="route('admin.articles.index')"
                    class="border-b-2 px-1 pb-3 text-xs font-black uppercase tracking-wider transition-colors border-primary text-primary"
                >
                    📰 Artículos
                </Link>
            </div>

            <!-- SEARCH AND FILTERS -->
            <AdminSearchBar
                v-model="searchQuery"
                v-model:view-mode="viewMode"
                placeholder="Filtrar colección de artículos... (Título, Medio, Editorial)"
                :result-count="filteredArticles.length"
                result-label="Resultados"
                sync-label="Archive"
                sync-accent="Sync"
            />

            <!-- DATA VIEW -->
            <ArticlesTable
                v-if="filteredArticles.length > 0"
                :articles="filteredArticles"
                :view-mode="viewMode"
                @edit="openEdit"
                @destroy="destroy"
            />
            <AdminEmptyState v-else title="Sin registros en el archivo" :query="searchQuery" />
            <AdminPagination v-if="filteredArticles.length > 0" :links="articles.links" />
        </div>

        <AdminModal
            :show="showModal"
            :title="editingArticle ? 'Editar' : 'Nuevo'"
            title-accent="Artículo"
            subtitle="Difusión y Prensa Institucional"
            :processing="form.processing"
            :submit-label="editingArticle ? 'Confirmar Cambios' : 'Lanzar al Archivo'"
            @close="showModal = false"
            @submit="submit"
        >
            <ArticleForm v-model:form="form" v-model:imagePreview="imagePreview" v-model:sourceType="sourceType" />
        </AdminModal>
    </AppLayout>
</template>

<style scoped>
.font-serif {
    font-family: 'Noto Serif', serif;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
</style>
