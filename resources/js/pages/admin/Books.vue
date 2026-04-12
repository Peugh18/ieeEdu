<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Edit2, Trash2, X, Check, ImagePlus, Loader2, BookOpen, FileText, Globe, Search, ChevronLeft, ChevronRight, LayoutGrid, List } from 'lucide-vue-next';

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
        <!-- Elite Header Section -->
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-6">
                <span class="px-4 py-1.5 rounded-full bg-[#1a1a10] text-[8px] font-black uppercase tracking-[0.3em] text-[#e7e6ab] shadow-sm">Biblioteca Digital de Elite</span>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="space-y-2">
                    <h2 class="font-serif text-5xl font-bold text-[#1a1a10] leading-tight">Gestión de <span class="italic text-[#57572A]">Publicaciones</span></h2>
                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Edición, Publicación y Distribución de Conocimiento Académico.</p>
                </div>
                
                <button
                    @click="openCreate"
                    class="order-first md:order-last inline-flex items-center gap-3 rounded-full bg-[#1a1a10] px-8 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#e7e6ab] shadow-2xl hover:bg-[#2a2a1a] hover:scale-[1.05] active:scale-95 transition-all duration-300"
                >
                    <Plus class="h-3 w-3" stroke-width="4" />
                    Registrar Nueva Obra
                </button>
            </div>
        </div>

        <!-- Elite Search & Filter Bar (The Library Controller) -->
        <div class="mb-12 flex flex-col gap-8">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="relative flex-1 group">
                    <Search class="absolute left-6 top-1/2 -translate-y-1/2 h-5 w-5 text-[#1a1a10]/30 group-focus-within:text-[#57572A] transition-all duration-500" />
                    <input 
                        v-model="searchQuery"
                        type="search" 
                        placeholder="Explorar el archivo digital... (Título, Autor, Categoría)" 
                        class="w-full bg-white border-2 border-[#1a1a10]/5 rounded-[2rem] py-5 pl-16 pr-8 text-[11px] font-black uppercase tracking-[0.2em] text-[#1a1a10] outline-none focus:ring-8 focus:ring-[#57572A]/5 focus:border-[#57572A]/20 transition-all placeholder:text-[#1a1a10]/20 shadow-xl shadow-[#1a1a10]/2 cursor-text"
                    >
                </div>
                
                <div class="flex items-center gap-2 p-1.5 bg-[#f7f6f0] rounded-full border border-[#1a1a10]/5 shadow-inner">
                    <button 
                        v-for="cat in ['Todos', 'Libro', 'Libro en camino', 'Guía']" 
                        :key="cat"
                        @click="activeFilter = cat"
                        class="px-6 py-3 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-500"
                        :class="activeFilter === cat ? 'bg-[#1a1a10] text-[#e7e6ab] shadow-lg scale-105' : 'text-[#1a1a10]/40 hover:text-[#1a1a10]'"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <div class="h-[1px] w-12 bg-[#1a1a10]/10"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Catálogo <span class="text-[#1a1a10]">Sincronizado</span></span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center p-1 bg-[#f7f6f0] rounded-lg border border-[#1a1a10]/5">
                        <button 
                            @click="viewMode = 'grid'"
                            class="p-2 rounded-md transition-all duration-300"
                            :class="viewMode === 'grid' ? 'bg-white shadow-sm text-[#1a1a10]' : 'text-[#1a1a10]/20 hover:text-[#1a1a10]'"
                        >
                            <LayoutGrid class="h-4 w-4" />
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            class="p-2 rounded-md transition-all duration-300"
                            :class="viewMode === 'list' ? 'bg-white shadow-sm text-[#1a1a10]' : 'text-[#1a1a10]/20 hover:text-[#1a1a10]'"
                        >
                            <List class="h-4 w-4" />
                        </button>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Resultados: <span class="text-[#1a1a10] text-xs font-serif italic">{{ filteredBooks.length }}</span> Obras</span>
                </div>
            </div>
        </div>

        <!-- Elite Gallery Grid -->
        <div v-if="filteredBooks.length > 0 && viewMode === 'grid'" class="grid gap-x-8 gap-y-16 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <div v-for="book in filteredBooks" :key="book.id" class="flex flex-col group">
                <!-- Card Image Container -->
                <div class="aspect-[2/3] bg-white rounded-[2rem] p-3 shadow-md ring-1 ring-[#1a1a10]/5 group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-500 relative">
                    <div class="h-full w-full rounded-[1.5rem] overflow-hidden bg-[#fdfdfc] relative">
                        <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                        <div v-else class="flex h-full w-full items-center justify-center text-[#1a1a10]/5">
                            <BookOpen class="h-16 w-16" />
                        </div>
                        
                        <!-- Badges -->
                        <div class="absolute inset-0 p-3 flex flex-col justify-between pointer-events-none">
                            <div class="flex justify-between items-start">
                                <span class="bg-white/90 backdrop-blur-md px-2 py-1 rounded-lg text-[7px] font-black uppercase tracking-widest text-[#1a1a10] shadow-sm">
                                    {{ book.category }}
                                </span>
                                <div v-if="Number(book.price) === 0" class="w-6 h-6 rounded-full bg-[#57572A] flex items-center justify-center shadow-lg">
                                    <Check class="h-3 w-3 text-[#e7e6ab]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Info -->
                <div class="mt-5 px-2 flex flex-col">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-[#9ca3af] mb-1 leading-none">{{ book.author || 'Miguelito' }}</span>
                    <h3 class="font-serif text-lg font-black text-[#1a1a10] line-clamp-1 mb-4 leading-tight group-hover:text-[#57572A] transition-colors">
                        {{ book.title }}
                    </h3>
                    
                    <div class="flex items-center justify-between border-t border-[#1a1a10]/5 pt-4">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Valor Comercial</span>
                            <span class="text-sm font-serif font-black text-[#1a1a10] tabular-nums">
                                {{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'GRATUITO' }}
                            </span>
                        </div>
                        
                        <div class="flex gap-2">
                            <button @click="openEdit(book)" class="w-8 h-8 rounded-full bg-[#f7f6f0] flex items-center justify-center text-[#1a1a10] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300">
                                <Edit2 class="h-3 w-3" />
                            </button>
                            <button @click="destroy(book.id)" class="w-8 h-8 rounded-full bg-[#f7f6f0] flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300">
                                <Trash2 class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Elite Editorial Table View -->
        <div v-else-if="filteredBooks.length > 0 && viewMode === 'list'" class="bg-white rounded-[2.5rem] border border-[#1a1a10]/5 shadow-xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-[#1a1a10]/5">
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Identidad de la Obra</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Categoría</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Estado de Acceso</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-right">Inversión</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a1a10]/5">
                    <tr v-for="book in filteredBooks" :key="book.id" class="group hover:bg-[#f7f6f0]/40 transition-all duration-500">
                        <td class="px-10 py-7">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-20 bg-[#fdfdfc] rounded-[1rem] border border-[#1a1a10]/5 overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                    <img v-if="book.cover_image" :src="`/storage/${book.cover_image}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-[#1a1a10]/5">
                                        <BookOpen class="h-6 w-6" />
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-[15px] font-bold text-[#1a1a10] leading-tight">{{ book.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ book.author || 'Instituto IEE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7">
                            <span class="inline-flex px-4 py-1.5 rounded-full bg-[#f4f7fe] text-[9px] font-black uppercase tracking-widest text-[#1a1a10]/60 border border-[#1a1a10]/5 transition-colors">
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
                            <span class="text-[15px] font-bold text-[#1a1a10] tabular-nums tracking-tighter">
                                {{ Number(book.price) > 0 ? `S/ ${fmt(book.price)}` : 'S/ 0.00' }}
                            </span>
                        </td>
                        <td class="px-10 py-7">
                            <div class="flex items-center justify-center gap-3">
                                <button @click="openEdit(book)" class="w-10 h-10 rounded-full bg-white border border-[#1a1a10]/5 flex items-center justify-center text-[#9ca3af] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300 shadow-sm hover:shadow-md">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="destroy(book.id)" class="w-10 h-10 rounded-full bg-white border border-[#1a1a10]/5 flex items-center justify-center text-[#9ca3af] hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-md">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="py-40 flex flex-col items-center justify-center text-center animate-in fade-in duration-700">
            <div class="w-24 h-24 rounded-full bg-[#f7f6f0] flex items-center justify-center text-[#1a1a10]/10 mb-8 ring-1 ring-[#1a1a10]/5">
                <Search class="h-10 w-10" />
            </div>
            <h3 class="font-serif text-2xl font-bold text-[#1a1a10] mb-2">Sin hallazgos en la biblioteca</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">No hemos encontrado registros que coincidan con "<span class="text-[#1a1a10]">{{ searchQuery }}</span>"</p>
        </div>

        <!-- Elite Pagination (Archive Controller) -->
        <div v-if="filteredBooks.length > 0 && books.links.length > 3" class="mt-20 flex flex-col items-center gap-6 pb-20 border-t border-[#1a1a10]/5 pt-12">
            <span class="text-[8px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Navegación de Archivo</span>
            <div class="flex items-center gap-2">
                <template v-for="link in books.links" :key="link.label">
                    <span 
                        v-if="!link.url" 
                        v-html="link.label" 
                        class="px-5 py-2 text-[9px] font-black uppercase tracking-widest text-[#1a1a10]/20 pointer-events-none"
                    ></span>
                    <a 
                        v-else 
                        :href="link.url" 
                        v-html="link.label" 
                        class="min-w-[40px] h-[40px] flex items-center justify-center rounded-full text-[9px] font-black uppercase tracking-widest transition-all"
                        :class="[
                            link.active 
                            ? 'bg-[#1a1a10] text-[#e7e6ab] shadow-xl scale-110' 
                            : 'bg-white text-[#1a1a10] hover:bg-[#1a1a10]/5 ring-1 ring-[#1a1a10]/5'
                        ]"
                    ></a>
                </template>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#1a1a10]/60 p-4 backdrop-blur-md" @click.self="showModal = false">
            <div class="w-full max-w-xl rounded-[2.5rem] bg-white shadow-2xl overflow-hidden animate-in fade-in zoom-in slide-in-from-bottom-8 duration-500">
                <div class="flex items-center justify-between border-b border-outline-variant/10 px-10 py-8 bg-surface-container-lowest/50">
                    <div class="space-y-1">
                        <h2 class="font-serif text-3xl font-bold text-on-surface leading-none">{{ editingBook ? 'Editar' : 'Nueva' }} <span class="italic text-primary">Publicación</span></h2>
                        <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">Configuración de catálogo digital</p>
                    </div>
                    <button @click="showModal = false" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container-low transition-colors">
                        <X class="h-5 w-5 text-on-surface-variant" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-10 space-y-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
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

                        <div class="bg-[#FBF9F2] p-8 rounded-[2rem] space-y-6 border border-[#57572A]/10">
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Modelo de Adquisición</label>
                                <p class="text-[11px] text-on-surface-variant/60 font-medium italic">Define si el contenido es transaccional o de libre acceso.</p>
                            </div>
                            
                            <div class="flex gap-4">
                                <label class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300" :class="!isFree ? 'bg-[#57572A] text-[#e7e6ab] border-[#57572A] shadow-xl shadow-primary/20' : 'bg-white border-outline-variant/10 text-on-surface-variant/50 hover:border-primary/30'">
                                    <input type="radio" v-model="isFree" :value="false" class="hidden">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-inherit">Premium (Pago)</span>
                                </label>
                                <label class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300" :class="isFree ? 'bg-[#57572A] text-[#e7e6ab] border-[#57572A] shadow-xl shadow-primary/20' : 'bg-white border-outline-variant/10 text-on-surface-variant/50 hover:border-primary/30'">
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
                                        <label class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-[#57572A]/20 py-6 cursor-pointer bg-white hover:bg-[#FBF9F2] hover:border-primary transition-all group">
                                            <input type="file" @change="e => onFileChange(e, 'file_path')" class="hidden" accept=".pdf,.doc,.docx,.zip">
                                            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                                <FileText class="h-5 w-5" />
                                            </div>
                                            <span class="text-[9px] font-black text-on-surface uppercase tracking-widest">
                                                {{ form.file_path ? 'Cambiar Archivo' : 'Cargar Documento' }}
                                            </span>
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
                                <div class="w-12 h-12 rounded-full bg-primary/5 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                    <ImagePlus class="h-6 w-6" />
                                </div>
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
                        <div class="relative flex items-center">
                            <input v-model="form.is_available" type="checkbox" class="h-5 w-5 rounded-lg border-outline-variant/30 text-primary focus:ring-primary/20 transition-all cursor-pointer">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-bold text-on-surface">Disponibilidad en Catálogo</label>
                            <span class="text-[10px] text-on-surface-variant/60 font-medium italic">Marcar si el contenido está listo para los estudiantes.</span>
                        </div>
                    </div>
                </form>

                <!-- Elite Footer Actions -->
                <div class="flex justify-end gap-6 px-12 py-10 bg-[#f7f6f0]/50 border-t border-[#1a1a10]/5">
                    <button @click="showModal = false" class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1a1a10]/40 hover:text-[#1a1a10] transition-colors">Cancelar Operación</button>
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center gap-3 rounded-full bg-[#1a1a10] px-12 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-[#e7e6ab] shadow-2xl hover:bg-[#2a2a1a] hover:scale-[1.05] disabled:opacity-50 transition-all shadow-[#1a1a10]/20"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <Check v-else class="h-4 w-4" />
                        {{ editingBook ? 'Confirmar Cambios' : 'Lanzar al Catálogo' }}
                    </button>
                </div>
            </div>
        </div>
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
