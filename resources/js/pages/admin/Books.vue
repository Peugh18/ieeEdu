<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Edit2, Trash2, Check, ImagePlus, BookOpen, FileText, Globe } from 'lucide-vue-next';

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

interface BookProps {
    books: { data: Book[]; links: any[]; total?: number };
}

const props = defineProps<BookProps>();

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
function fmt(n: string | number) {
    const val = typeof n === 'string' ? parseFloat(n) : n;
    return val.toLocaleString('es-PE', { minimumFractionDigits: 2 });
}

// Client-side Elite Search & Filter
const searchQuery = ref('');
const activeFilter = ref('Todos');
const viewMode = ref<'grid' | 'list'>('grid');

const filteredBooks = ref<Book[]>(props.books.data);

watch([searchQuery, activeFilter], () => {
    filteredBooks.value = props.books.data.filter(book => {
        const matchesSearch = !searchQuery.value || 
            book.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (book.author && book.author.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            book.category.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesCategory = activeFilter.value === 'Todos' || book.category === activeFilter.value;
        
        return matchesSearch && matchesCategory;
    });
}, { immediate: true });

// Sync with props whenever inertia updates the data
watch(() => props.books.data, (newData) => {
    filteredBooks.value = newData.filter(book => {
        const matchesSearch = !searchQuery.value || 
            book.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (book.author && book.author.toLowerCase().includes(searchQuery.value.toLowerCase()));
        const matchesCategory = activeFilter.value === 'Todos' || book.category === activeFilter.value;
        return matchesSearch && matchesCategory;
    });
});
</script>

<template>
    <Head title="Admin - Libros" />
    <AppLayout>
        <AdminPageHeader
            badge="Biblioteca Digital de Elite"
            title="Gestión de"
            title-accent="Publicaciones"
            subtitle="Edición, Publicación y Distribución de Conocimiento Académico."
            action-label="Registrar Nueva Obra"
            action-order="first"
            @action="openCreate"
        />

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
                <div class="flex items-center gap-2 p-1.5 bg-surface-container rounded-full border border-on-background/5 shadow-inner">
                    <button
                        v-for="cat in ['Todos', 'Libro', 'Libro en camino', 'Guía']"
                        :key="cat"
                        @click="activeFilter = cat"
                        class="px-6 py-3 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-500"
                        :class="activeFilter === cat ? 'bg-on-background text-primary-fixed shadow-lg scale-105' : 'text-on-background/40 hover:text-on-background'"
                    >{{ cat }}</button>
                </div>
            </template>
        </AdminSearchBar>

        <!-- Elite Gallery Grid -->
        <div v-if="filteredBooks.length > 0 && viewMode === 'grid'" class="grid gap-x-8 gap-y-16 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <div v-for="book in filteredBooks" :key="book.id" class="flex flex-col group">
                <!-- Card Image Container -->
                <div class="aspect-[2/3] bg-white rounded-[2rem] p-3 shadow-md ring-1 ring-on-background/5 group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-500 relative">
                    <div class="h-full w-full rounded-[1.5rem] overflow-hidden bg-[#fdfdfc] relative">
                        <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                        <div v-else class="flex h-full w-full items-center justify-center text-on-background/5">
                            <BookOpen class="h-16 w-16" />
                        </div>
                        
                        <!-- Badges -->
                        <div class="absolute inset-0 p-3 flex flex-col justify-between pointer-events-none">
                            <div class="flex justify-between items-start">
                                <span class="bg-white/90 backdrop-blur-md px-2 py-1 rounded-lg text-[7px] font-black uppercase tracking-widest text-on-background shadow-sm">
                                    {{ book.category }}
                                </span>
                                <div v-if="Number(book.price) === 0" class="w-6 h-6 rounded-full bg-primary flex items-center justify-center shadow-lg">
                                    <Check class="h-3 w-3 text-primary-fixed" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Info -->
                <div class="mt-5 px-2 flex flex-col">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-[#9ca3af] mb-1 leading-none">{{ book.author || 'Miguelito' }}</span>
                    <h3 class="font-serif text-lg font-black text-on-background line-clamp-1 mb-4 leading-tight group-hover:text-primary transition-colors">
                        {{ book.title }}
                    </h3>
                    
                    <div class="flex items-center justify-between border-t border-on-background/5 pt-4">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Valor Comercial</span>
                            <span class="text-sm font-serif font-black text-on-background tabular-nums">
                                {{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'GRATUITO' }}
                            </span>
                        </div>
                        
                        <div class="flex gap-2">
                            <AdminIconButton @click="openEdit(book)" size="sm">
                                <template #default="{ iconClass }"><Edit2 :class="iconClass" /></template>
                            </AdminIconButton>
                            <AdminIconButton variant="danger" @click="destroy(book.id)" size="sm">
                                <template #default="{ iconClass }"><Trash2 :class="iconClass" /></template>
                            </AdminIconButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Elite Editorial Table View -->
        <div v-else-if="filteredBooks.length > 0 && viewMode === 'list'" class="bg-white rounded-[2.5rem] border border-on-background/5 shadow-xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-on-background/5">
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Identidad de la Obra</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Categoría</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Estado de Acceso</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-right">Inversión</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-on-background/5">
                    <tr v-for="book in filteredBooks" :key="book.id" class="group hover:bg-surface-container/40 transition-all duration-500">
                        <td class="px-10 py-7">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-20 bg-[#fdfdfc] rounded-[1rem] border border-on-background/5 overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-on-background/5">
                                        <BookOpen class="h-6 w-6" />
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-[15px] font-bold text-on-background leading-tight">{{ book.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ book.author || 'Instituto IEE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7">
                            <span class="inline-flex px-4 py-1.5 rounded-full bg-[#f4f7fe] text-[9px] font-black uppercase tracking-widest text-on-background/60 border border-on-background/5 transition-colors">
                                {{ book.category }}
                            </span>
                        </td>
                        <td class="px-10 py-7 text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full border-2 border-white shadow-sm" :class="Number(book.price) === 0 ? 'bg-[#00D1A0]' : 'bg-amber-400'"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest" :class="Number(book.price) === 0 ? 'text-[#00D1A0]' : 'text-amber-500'">
                                        {{ Number(book.price) === 0 ? 'Abierto' : 'Exclusivo' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7 text-right">
                            <span class="text-[15px] font-bold text-on-background tabular-nums tracking-tighter">
                                {{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'S/ 0.00' }}
                            </span>
                        </td>
                        <td class="px-10 py-7">
                            <div class="flex items-center justify-center gap-3">
                                <AdminIconButton variant="outline" @click="openEdit(book)" size="md">
                                    <template #default="{ iconClass }"><Edit2 :class="iconClass" /></template>
                                </AdminIconButton>
                                <AdminIconButton variant="danger" @click="destroy(book.id)" size="md">
                                    <template #default="{ iconClass }"><Trash2 :class="iconClass" /></template>
                                </AdminIconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminEmptyState v-else title="Sin hallazgos en la biblioteca" :query="searchQuery" />

        <AdminPagination v-if="filteredBooks.length > 0" :links="books.links" />

        <!-- Modal -->
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
            <!-- keep all form content exactly as-is inside --> 
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Categoría</label>
                    <select v-model="form.category" class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all cursor-pointer">
                        <option value="Libro">Libro</option>
                        <option value="Libro en camino">Libro en camino</option>
                        <option value="Guía">Guía</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Título de la Obra</label>
                    <input v-model="form.title" type="text" class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" placeholder="Ej: Introducción al Derecho...">
                </div>
            </div>

            <template v-if="form.category !== 'Libro en camino'">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Autor / Responsable</label>
                    <input v-model="form.author" type="text" class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" placeholder="Nombre completo...">
                </div>

                <div class="bg-surface-dim p-8 rounded-[2rem] space-y-6 border border-primary/10">
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Modelo de Adquisición</label>
                        <p class="text-[11px] text-on-surface-variant/60 font-medium italic">Define si el contenido es transaccional o de libre acceso.</p>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300" :class="!isFree ? 'bg-primary text-primary-fixed border-primary shadow-xl shadow-primary/20' : 'bg-white border-outline-variant/10 text-on-surface-variant/50 hover:border-primary/30'">
                            <input type="radio" v-model="isFree" :value="false" class="hidden">
                            <span class="text-[10px] font-black uppercase tracking-widest text-inherit">Premium (Pago)</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300" :class="isFree ? 'bg-primary text-primary-fixed border-primary shadow-xl shadow-primary/20' : 'bg-white border-outline-variant/10 text-on-surface-variant/50 hover:border-primary/30'">
                            <input type="radio" v-model="isFree" :value="true" class="hidden">
                            <span class="text-[10px] font-black uppercase tracking-widest text-inherit">Gratuito</span>
                        </label>
                    </div>
                    <div v-if="!isFree" class="space-y-2 pt-2 animate-in fade-in slide-in-from-top-4 duration-500">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Precio Sugerido (S/)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-bold text-primary/40">S/</span>
                            <input v-model="form.price" type="number" step="0.01" class="w-full rounded-2xl border border-outline-variant/20 bg-white pl-12 pr-5 py-3.5 text-sm font-black tabular-nums focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                    </div>
                    <div v-if="isFree" class="space-y-6 pt-2 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Recurso Digital (PDF)</label>
                                <label class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-primary/20 py-6 cursor-pointer bg-white hover:bg-surface-dim hover:border-primary transition-all group">
                                    <input type="file" @change="e => onFileChange(e, 'file_path')" class="hidden" accept=".pdf,.doc,.docx,.zip">
                                    <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary group-hover:scale-110 transition-transform"><FileText class="h-5 w-5" /></div>
                                    <span class="text-[9px] font-black text-on-surface uppercase tracking-widest">{{ form.file_path ? 'Cambiar Archivo' : 'Cargar Documento' }}</span>
                                    <span v-if="form.file_path" class="text-[9px] text-primary font-bold italic limit-text px-4 text-center">{{ form.file_path.name }}</span>
                                </label>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Enlace Externo (Opcional)</label>
                                <div class="relative h-full">
                                    <Globe class="absolute left-5 top-1/2 -translate-y-1/2 h-4 text-primary/40" />
                                    <input v-model="form.download_url" type="url" class="w-full h-[88px] rounded-2xl border border-outline-variant/20 bg-white pl-14 pr-5 py-3.5 text-xs font-medium focus:border-primary outline-none transition-all" placeholder="https://docs.google.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Sinopsis Informativa</label>
                <textarea v-model="form.description" rows="5" class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-4 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all resize-none" placeholder="Escribe un resumen breve y profesional del contenido..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Identidad Visual (Portada)</label>
                <div class="flex items-center gap-6">
                    <label class="flex-1 cursor-pointer flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-outline-variant/20 py-10 bg-white hover:bg-surface-container-lowest hover:border-primary transition-all group">
                        <input type="file" @change="e => onFileChange(e, 'cover_image')" class="hidden" accept="image/*">
                        <div class="w-12 h-12 rounded-full bg-primary/5 flex items-center justify-center text-primary group-hover:scale-110 transition-transform"><ImagePlus class="h-6 w-6" /></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Seleccionar Portada</span>
                    </label>
                    <div v-if="imagePreview" class="h-40 w-28 overflow-hidden rounded-2xl border-2 border-white shadow-2xl flex-shrink-0 relative group/preview">
                        <img :src="imagePreview" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-[8px] font-black text-white uppercase tracking-tighter">Vista Previa</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.category !== 'Libro en camino'" class="flex items-center gap-3 p-5 rounded-2xl bg-surface-container-low/20 border border-outline-variant/5">
                <input v-model="form.is_available" type="checkbox" class="h-5 w-5 rounded-lg border-outline-variant/30 text-primary focus:ring-primary/20 transition-all cursor-pointer">
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-on-surface">Disponibilidad en Catálogo</label>
                    <span class="text-[10px] text-on-surface-variant/60 font-medium italic">Marcar si el contenido está listo para los estudiantes.</span>
                </div>
            </div>
        </AdminModal>
    </AppLayout>
</template>

<style scoped>
.font-serif { font-family: 'Noto Serif', serif; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
.limit-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px; }
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>
