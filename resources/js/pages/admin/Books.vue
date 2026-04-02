<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Edit2, Trash2, X, Check, ImagePlus, Loader2, BookOpen, FileText, Globe } from 'lucide-vue-next';

interface Book {
    id: number;
    category: 'Libro' | 'Libro en camino' | 'Guía';
    title: string;
    price: string | number;
    author: string;
    description: string;
    cover_image: string | null;
    file_path: string | null;
    download_url: string | null;
    is_available: boolean;
}

const props = defineProps<{
    books: { data: Book[]; links: any[] };
}>();

const showModal = ref(false);
const editingBook = ref<Book | null>(null);
const imagePreview = ref<string | null>(null);
const isFree = ref(false);

const form = useForm({
    category: 'Libro' as 'Libro' | 'Libro en camino' | 'Guía',
    title: '',
    price: 0 as number,
    author: '',
    description: '',
    cover_image: null as File | null,
    file_path: null as File | null,
    download_url: '',
    is_available: true as boolean,
});

// Sync isFree with price
watch(isFree, (val) => {
    if (val) form.price = 0;
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
    form.author = book.author;
    form.description = book.description;
    form.download_url = book.download_url || '';
    form.is_available = book.is_available;
    form.cover_image = null;
    form.file_path = null;
    imagePreview.value = book.cover_image ? `/storage/${book.cover_image}` : null;
    showModal.value = true;
}

function onFileChange(e: Event, field: 'cover_image' | 'file_path') {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form[field] = file;
        if (field === 'cover_image') imagePreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    if (editingBook.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('admin.books.update', { book: editingBook.value.id }), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.books.store'), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este libro?')) {
        form.delete(route('admin.books.destroy', { book: id }));
    }
}
</script>

<template>
    <Head title="Admin - Libros" />
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="font-serif text-3xl font-bold text-on-surface">Gestión de <span class="italic">Publicaciones</span></h2>
                <p class="text-sm text-on-surface-variant">Libros, Guías y Material Académico.</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow hover:opacity-90 transition-all active:scale-95 shadow-primary/20"
            >
                <Plus class="h-4 w-4" />
                Nueva Publicación
            </button>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div v-for="book in props.books.data" :key="book.id" class="flex flex-col rounded-3xl border bg-white shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                <div class="aspect-[3/4] bg-surface-container-low relative overflow-hidden">
                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
                    <div v-else class="flex h-full w-full items-center justify-center text-on-surface-variant/30">
                        <BookOpen class="h-12 w-12" />
                    </div>
                    <div class="absolute top-3 left-3">
                        <span class="rounded-full bg-white/90 px-3 py-1 text-[10px] font-bold uppercase tracking-widest shadow-sm">
                            {{ book.category }}
                        </span>
                    </div>
                    <div v-if="Number(book.price) === 0" class="absolute top-3 right-3">
                        <span class="rounded-full bg-primary px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm">Gratis</span>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-serif text-lg font-bold text-on-surface line-clamp-1" :title="book.title">{{ book.title }}</h3>
                    <p class="text-xs text-on-surface-variant mb-4">{{ book.author || 'IEE' }}</p>
                    <div class="mt-auto flex items-center justify-between">
                        <span class="text-lg font-serif font-bold text-primary">
                            {{ Number(book.price) > 0 ? `S/ ${book.price}` : 'GRATUITO' }}
                        </span>
                        <div class="flex gap-1">
                            <button @click="openEdit(book)" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors">
                                <Edit2 class="h-4 w-4" />
                            </button>
                            <button @click="destroy(book.id)" class="rounded-lg p-2 text-red-500 hover:bg-red-50 transition-colors">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm" @click.self="showModal = false">
            <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b px-8 py-6">
                    <h2 class="font-serif text-2xl text-on-surface">{{ editingBook ? 'Editar' : 'Nueva' }} Publicación</h2>
                    <button @click="showModal = false"><X class="h-5 w-5 text-on-surface-variant" /></button>
                </div>

                <form @submit.prevent="submit" class="p-8 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Categoría</label>
                        <select v-model="form.category" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none">
                            <option value="Libro">Libro</option>
                            <option value="Libro en camino">Libro en camino</option>
                            <option value="Guía">Guía</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Título</label>
                        <input v-model="form.title" type="text" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none" placeholder="Nombre de la publicación...">
                    </div>

                    <!-- Layout Condicional para Libro en Camino -->
                    <template v-if="form.category !== 'Libro en camino'">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Autor</label>
                            <input v-model="form.author" type="text" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none" placeholder="Nombre del autor...">
                        </div>

                        <div class="bg-surface-container-low/30 p-5 rounded-2xl space-y-4 border border-outline-variant/10">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Tipo de Material</label>
                            <div class="flex gap-4">
                                <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="!isFree ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-white border-outline-variant/30 text-on-surface-variant'">
                                    <input type="radio" v-model="isFree" :value="false" class="hidden">
                                    <span class="text-xs font-bold uppercase tracking-wider">De Pago</span>
                                </label>
                                <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all" :class="isFree ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-white border-outline-variant/30 text-on-surface-variant'">
                                    <input type="radio" v-model="isFree" :value="true" class="hidden">
                                    <span class="text-xs font-bold uppercase tracking-wider">Gratuito</span>
                                </label>
                            </div>

                            <div v-if="!isFree" class="space-y-1.5 animate-in slide-in-from-top-1">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Precio (S/)</label>
                                <input v-model="form.price" type="number" step="0.01" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm focus:border-primary outline-none">
                            </div>

                            <div v-if="isFree" class="space-y-4 animate-in slide-in-from-top-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Subir Archivo (PDF/DOC)</label>
                                        <label class="flex flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-outline-variant/30 py-3 cursor-pointer bg-white hover:bg-surface-container-lowest transition-colors">
                                            <input type="file" @change="e => onFileChange(e, 'file_path')" class="hidden" accept=".pdf,.doc,.docx,.zip">
                                            <FileText class="h-4 w-4 text-on-surface-variant" />
                                            <span class="text-[10px] font-bold text-on-surface-variant uppercase">
                                                {{ form.file_path ? 'Cambiar Archivo' : 'Elegir Archivo' }}
                                            </span>
                                            <span v-if="form.file_path" class="text-[9px] text-primary limit-text">{{ form.file_path.name }}</span>
                                        </label>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">O Link Externo (URL)</label>
                                        <div class="relative">
                                            <Globe class="absolute left-3 top-3.5 h-3 text-on-surface-variant" />
                                            <input v-model="form.download_url" type="url" class="w-full rounded-xl border border-outline-variant/30 bg-white pl-9 pr-4 py-3 text-sm focus:border-primary outline-none" placeholder="https://...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Descripción / Sinopsis</label>
                        <textarea v-model="form.description" rows="4" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none resize-none" placeholder="Resumen del contenido..."></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Imagen de Portada</label>
                        <div class="flex items-center gap-4">
                            <label class="flex-1 cursor-pointer flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-outline-variant/30 py-6 hover:bg-surface-container-lowest transition-colors">
                                <input type="file" @change="e => onFileChange(e, 'cover_image')" class="hidden" accept="image/*">
                                <ImagePlus class="h-5 w-5 text-on-surface-variant" />
                                <span class="text-xs font-semibold text-on-surface-variant">Subir Imagen</span>
                            </label>
                            <div v-if="imagePreview" class="h-20 w-16 overflow-hidden rounded-xl border shadow-sm flex-shrink-0">
                                <img :src="imagePreview" class="h-full w-full object-cover">
                            </div>
                        </div>
                    </div>

                    <div v-if="form.category !== 'Libro en camino'" class="flex items-center gap-2">
                        <input v-model="form.is_available" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <label class="text-sm font-medium text-on-surface">Disponible para adquirir / Con Stock</label>
                    </div>
                </form>

                <div class="flex justify-end gap-3 border-t px-8 py-6">
                    <button @click="showModal = false" class="rounded-xl px-6 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">Cancelar</button>
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-90 disabled:opacity-50 transition-all shadow-primary/20"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <Check v-else class="h-4 w-4" />
                        {{ editingBook ? 'Guardar Cambios' : 'Crear Publicación' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
.limit-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px; }
</style>
