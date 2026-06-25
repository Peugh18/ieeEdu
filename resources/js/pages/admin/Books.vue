<script setup lang="ts">
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import BookForm from '@/components/admin/books/BookForm.vue';
import BooksTable from '@/components/admin/books/BooksTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Book } from '@/types/book';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, Download } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    books: PaginatedResponse<Book>;
    stats: { total: number; available: number; downloads: number; downloads_month: number; book_income: number; book_sales: number };
}>();

const showModal = ref(false);
const editingBook = ref<Book | null>(null);
const imagePreview = ref<string | null>(null);
const isFree = ref(false);

const form = useForm({
    category: 'Libro' as 'Libro' | 'Libro en camino' | 'Guía',
    title: '',
    price: 0,
    stock: 10,
    author: '',
    description: '',
    cover_image: null as File | null,
    file_path: null as File | null,
    download_url: '',
    is_available: true as boolean,
});

watch(isFree, (v) => {
    if (v) {
        form.price = 0;
        form.stock = 0;
    } else if (!form.stock) form.stock = 10;
});

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
    form.transform((d) => (editingBook.value ? { ...d, _method: 'PUT' } : d))[method](url, {
        forceFormData: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este libro?')) form.delete(route('admin.books.destroy', { book: id }));
}

const searchQuery = ref('');
const activeFilter = ref('Todos');
const viewMode = ref<'grid' | 'list'>('list');
const filteredBooks = ref<Book[]>(props.books.data);

watch(
    [searchQuery, activeFilter],
    () => {
        filteredBooks.value = props.books.data.filter((b) => {
            const q = searchQuery.value.toLowerCase();
            const matchesSearch =
                !q || b.title.toLowerCase().includes(q) || (b.author && b.author.toLowerCase().includes(q)) || b.category.toLowerCase().includes(q);
            return matchesSearch && (activeFilter.value === 'Todos' || b.category === activeFilter.value);
        });
    },
    { immediate: true },
);

watch(
    () => props.books.data,
    (newData) => {
        filteredBooks.value = newData.filter((b) => {
            const q = searchQuery.value.toLowerCase();
            return (
                (!q || b.title.toLowerCase().includes(q) || (b.author && b.author.toLowerCase().includes(q))) &&
                (activeFilter.value === 'Todos' || b.category === activeFilter.value)
            );
        });
    },
);
</script>

<template>
    <Head title="Gestión de Libros - iieEdu Admin" />
    <AppLayout>
        <div class="mx-auto max-w-[1400px] space-y-8 px-4 py-8">
            <!-- PAGE HEADER -->
            <AdminPageHeader
                title="Gestión de "
                titleAccent="Libros"
                subtitle="Catálogo de publicaciones físicas y digitales."
                actionLabel="Nuevo Libro"
                @action="openCreate"
            />

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                <!-- Total Books -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Total Libros</span>
                            <BookOpen class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-3xl font-black leading-none text-slate-900">{{ stats.total }}</p>
                    </div>
                </div>

                <!-- Recaudado -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Recaudado</span>
                            <BookOpen class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-2xl font-black leading-none text-violet-700">S/ {{ Number(stats.book_income).toLocaleString() }}</p>
                    </div>
                </div>

                <!-- Ventas -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Ventas</span>
                            <BookOpen class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-3xl font-black leading-none text-slate-900">{{ stats.book_sales }}</p>
                    </div>
                </div>

                <!-- Descargas -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Descargas</span>
                            <Download class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-3xl font-black leading-none text-emerald-600">{{ stats.downloads }}</p>
                    </div>
                </div>

                <!-- Descargas / mes -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Descargas / Mes</span>
                            <Download class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-3xl font-black leading-none text-amber-600">{{ stats.downloads_month }}</p>
                    </div>
                </div>

                <!-- Disponibles -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Disponibles</span>
                            <BookOpen class="h-4 w-4 text-slate-300" />
                        </div>
                        <p class="text-3xl font-black leading-none text-blue-600">{{ stats.available }}</p>
                    </div>
                </div>
            </div>

            <!-- SEARCH AND FILTERS -->
            <AdminSearchBar
                v-model="searchQuery"
                v-model:view-mode="viewMode"
                placeholder="Explorar el archivo digital... (Título, Autor, Categoría)"
                :result-count="filteredBooks.length"
                result-label="Resultados"
                sync-label="Catálogo"
                sync-accent="Sincronizado"
            >
                <template #filters>
                    <div
                        class="custom-scrollbar flex items-center gap-2 overflow-x-auto whitespace-nowrap rounded-full border border-slate-200/50 bg-slate-50 p-1.5 shadow-inner"
                    >
                        <button
                            v-for="cat in ['Todos', 'Libro', 'Libro en camino', 'Guía']"
                            :key="cat"
                            @click="activeFilter = cat"
                            class="rounded-full px-6 py-3 text-[9px] font-black uppercase tracking-widest transition-all duration-500"
                            :class="activeFilter === cat ? 'scale-105 bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-700'"
                        >
                            {{ cat }}
                        </button>
                    </div>
                </template>
            </AdminSearchBar>

            <!-- DATA VIEW -->
            <BooksTable v-if="filteredBooks.length > 0" :books="filteredBooks" :view-mode="viewMode" @edit="openEdit" @destroy="destroy" />
            <AdminEmptyState v-else title="Sin hallazgos en la biblioteca" :query="searchQuery" />
            <AdminPagination v-if="filteredBooks.length > 0" :links="books.links" />
        </div>

        <AdminModal
            :show="showModal"
            :title="editingBook ? 'Editar' : 'Nueva'"
            title-accent="Publicación"
            subtitle="Configuración de catálogo digital"
            :processing="form.processing"
            :submit-label="editingBook ? 'Confirmar Cambios' : 'Lanzar al Catálogo'"
            @close="showModal = false"
            @submit="submit"
        >
            <BookForm v-model:form="form" v-model:imagePreview="imagePreview" v-model:isFree="isFree" />
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
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
