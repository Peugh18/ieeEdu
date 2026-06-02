<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import ArticleForm from '@/components/admin/articles/ArticleForm.vue';
import ArticlesTable from '@/components/admin/articles/ArticlesTable.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { Article } from '@/types/article';
import type { PaginatedResponse } from '@/types/pagination';

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
    const method = editingArticle.value ? 'put' : 'post';
    form.transform(d => editingArticle.value ? { ...d, _method: 'PUT' } : d)[method](url, {
        forceFormData: true,
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este artículo?')) form.delete(route('admin.articles.destroy', { article: id }));
}

const searchQuery = ref('');
const viewMode = ref<'grid' | 'list'>('list');
const filteredArticles = ref<Article[]>(props.articles.data);

watch([searchQuery], () => {
    filteredArticles.value = props.articles.data.filter(a => {
        const q = searchQuery.value.toLowerCase();
        return !q || a.title.toLowerCase().includes(q) || a.media.toLowerCase().includes(q);
    });
}, { immediate: true });

watch(() => props.articles.data, (newData) => {
    filteredArticles.value = newData.filter(a => {
        const q = searchQuery.value.toLowerCase();
        return !q || a.title.toLowerCase().includes(q) || a.media.toLowerCase().includes(q);
    });
});
</script>

<template>
    <Head title="Gestión de Artículos - iieEdu Admin" />
    <AppLayout>
        <div class="max-w-[1400px] mx-auto px-4 py-8 space-y-8">
            <!-- PAGE HEADER -->
            <AdminPageHeader
                title="Gestión de "
                titleAccent="Artículos"
                subtitle="Difusión académica y prensa institucional."
                actionLabel="Nuevo Artículo"
                @action="openCreate"
            />

            <!-- SEARCH AND FILTERS -->
            <AdminSearchBar v-model="searchQuery" v-model:view-mode="viewMode" placeholder="Filtrar colección de artículos... (Título, Medio, Editorial)" :result-count="filteredArticles.length" result-label="Resultados" sync-label="Archive" sync-accent="Sync" />

            <!-- DATA VIEW -->
            <ArticlesTable v-if="filteredArticles.length > 0" :articles="filteredArticles" :view-mode="viewMode" @edit="openEdit" @destroy="destroy" />
            <AdminEmptyState v-else title="Sin registros en el archivo" :query="searchQuery" />
            <AdminPagination v-if="filteredArticles.length > 0" :links="articles.links" />
        </div>

        <AdminModal :show="showModal" :title="editingArticle ? 'Editar' : 'Nuevo'" title-accent="Artículo" subtitle="Difusión y Prensa Institucional" :processing="form.processing" :submit-label="editingArticle ? 'Confirmar Cambios' : 'Lanzar al Archivo'" @close="showModal = false" @submit="submit">
            <ArticleForm v-model:form="form" v-model:imagePreview="imagePreview" v-model:sourceType="sourceType" />
        </AdminModal>
    </AppLayout>
</template>

<style scoped>
.font-serif { font-family: 'Noto Serif', serif; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>
