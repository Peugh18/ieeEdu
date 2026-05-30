<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import BookForm from '@/components/admin/books/BookForm.vue';
import BooksTable from '@/components/admin/books/BooksTable.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Download, BookOpen } from 'lucide-vue-next';
import type { Book } from '@/types/book';
import type { PaginatedResponse } from '@/types/pagination';

const props = defineProps<{ books: PaginatedResponse<Book>; stats: { total: number; available: number; downloads: number; downloads_month: number; book_income: number; book_sales: number } }>();

const showModal = ref(false);
const editingBook = ref<Book | null>(null);
const imagePreview = ref<string | null>(null);
const isFree = ref(false);

const form = useForm({ category: 'Libro' as 'Libro' | 'Libro en camino' | 'Guía', title: '', price: 0, stock: 10, author: '', description: '', cover_image: null as File | null, file_path: null as File | null, download_url: '', is_available: true as boolean });

watch(isFree, (v) => { if (v) { form.price = 0; form.stock = 0; } else if (!form.stock) form.stock = 10; });

function openCreate() {
    editingBook.value = null;
    form.reset();
    isFree.value = false;
    imagePreview.value = null;
    showModal.value = true;
}

function openEdit(book: Book) {
    editingBook.value = book;
    form.category = book.category;
    form.title = book.title;
    form.price = typeof book.price === 'string' ? parseFloat(book.price) : book.price;
    isFree.value = Number(form.price) === 0;
    form.stock = book.stock ?? (isFree.value ? 0 : 10);
    form.author = book.author;
    form.description = book.description;
    form.download_url = book.download_url || '';
    form.is_available = book.is_available;
    form.cover_image = null;
    form.file_path = null;
    imagePreview.value = book.cover_image ? `/storage/${book.cover_image}` : null;
    showModal.value = true;
}

function submit() {
    const url = editingBook.value ? route('admin.books.update', { book: editingBook.value.id }) : route('admin.books.store');
    const method = editingBook.value ? 'put' : 'post';
    form.transform(d => editingBook.value ? { ...d, _method: 'PUT' } : d)[method](url, {
        forceFormData: true,
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este libro?')) form.delete(route('admin.books.destroy', { book: id }));
}

const searchQuery = ref('');
const activeFilter = ref('Todos');
const viewMode = ref<'grid' | 'list'>('grid');
const filteredBooks = ref<Book[]>(props.books.data);

watch([searchQuery, activeFilter], () => {
    filteredBooks.value = props.books.data.filter(b => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = !q || b.title.toLowerCase().includes(q) || (b.author && b.author.toLowerCase().includes(q)) || b.category.toLowerCase().includes(q);
        return matchesSearch && (activeFilter.value === 'Todos' || b.category === activeFilter.value);
    });
}, { immediate: true });

watch(() => props.books.data, (newData) => {
    filteredBooks.value = newData.filter(b => {
        const q = searchQuery.value.toLowerCase();
        return (!q || b.title.toLowerCase().includes(q) || (b.author && b.author.toLowerCase().includes(q))) && (activeFilter.value === 'Todos' || b.category === activeFilter.value);
    });
});
</script>

<template>
    <Head title="Admin - Libros" />
    <AppLayout>
        <AdminPageHeader title="Libros" subtitle="Catálogo de publicaciones físicas y digitales." action-label="Registrar libro" action-order="first" compact @action="openCreate" />

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="rounded-2xl bg-white border border-outline-variant/15 p-4 text-center">
                <BookOpen class="w-5 h-5 text-primary mx-auto mb-2" />
                <p class="text-2xl font-black tabular-nums">{{ stats.total }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/60 mt-1">Libros</p>
            </div>
            <div class="rounded-2xl bg-violet-50 border border-violet-100 p-4 text-center">
                <p class="text-lg font-black tabular-nums text-violet-700">S/ {{ Number(stats.book_income).toLocaleString() }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-violet-500 mt-1">Recaudado</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4 text-center">
                <p class="text-2xl font-black tabular-nums">{{ stats.book_sales }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-1">Ventas</p>
            </div>
            <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-center">
                <Download class="w-5 h-5 text-emerald-600 mx-auto mb-2" />
                <p class="text-2xl font-black tabular-nums text-emerald-700">{{ stats.downloads }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-500 mt-1">Descargas</p>
            </div>
            <div class="rounded-2xl bg-amber-50 border border-amber-100 p-4 text-center">
                <p class="text-2xl font-black tabular-nums text-amber-700">{{ stats.downloads_month }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-amber-500 mt-1">Descargas / mes</p>
            </div>
            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4 text-center">
                <p class="text-2xl font-black tabular-nums text-blue-700">{{ stats.available }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-blue-500 mt-1">Disponibles</p>
            </div>
        </div>
        <AdminSearchBar v-model="searchQuery" v-model:view-mode="viewMode" placeholder="Explorar el archivo digital... (Título, Autor, Categoría)" :result-count="filteredBooks.length" result-label="Resultados" sync-label="Catálogo" sync-accent="Sincronizado">
            <template #filters>
                <div class="flex items-center gap-2 p-1.5 bg-surface-container rounded-full border border-on-background/5 shadow-inner overflow-x-auto whitespace-nowrap custom-scrollbar">
                    <button v-for="cat in ['Todos', 'Libro', 'Libro en camino', 'Guía']" :key="cat" @click="activeFilter = cat" class="px-6 py-3 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-500" :class="activeFilter === cat ? 'bg-on-background text-primary-fixed shadow-lg scale-105' : 'text-on-background/40 hover:text-on-background'">{{ cat }}</button>
                </div>
            </template>
        </AdminSearchBar>

        <BooksTable v-if="filteredBooks.length > 0" :books="filteredBooks" :view-mode="viewMode" @edit="openEdit" @destroy="destroy" />
        <AdminEmptyState v-else title="Sin hallazgos en la biblioteca" :query="searchQuery" />
        <AdminPagination v-if="filteredBooks.length > 0" :links="books.links" />

        <AdminModal :show="showModal" :title="editingBook ? 'Editar' : 'Nueva'" title-accent="Publicación" subtitle="Configuración de catálogo digital" :processing="form.processing" :submit-label="editingBook ? 'Confirmar Cambios' : 'Lanzar al Catálogo'" @close="showModal = false" @submit="submit">
            <BookForm v-model:form="form" v-model:imagePreview="imagePreview" v-model:isFree="isFree" />
        </AdminModal>
    </AppLayout>
</template>

<style scoped>
.font-serif { font-family: 'Noto Serif', serif; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
