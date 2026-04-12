<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
    Plus, 
    Edit2, 
    Trash2, 
    X, 
    Check, 
    ImagePlus, 
    Loader2, 
    Link, 
    Calendar, 
    ExternalLink, 
    Globe, 
    Search, 
    LayoutGrid, 
    List, 
    FileText 
} from 'lucide-vue-next';

interface Article {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    download_url: string;
}

interface ArticleProps {
    articles: { data: Article[]; links: any[]; total?: number };
}

const props = defineProps<ArticleProps>();

const showModal = ref(false);
const editingArticle = ref<Article | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({
    title: '',
    media: '',
    published_at: '',
    thumbnail: null as File | null,
    file_path: null as File | null,
    download_url: '',
});

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

function onFileChange(e: Event, field: 'thumbnail' | 'file_path') {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        if (field === 'thumbnail') {
            form.thumbnail = file;
            imagePreview.value = URL.createObjectURL(file);
        } else {
            form.file_path = file;
        }
    }
}

function submit() {
    // If we are uploading a PDF, we might need to bypass the backend's required 'download_url' 
    // depending on how the controller is structured. We'll set a placeholder if empty.
    if (sourceType.value === 'pdf' && !form.download_url) {
        form.download_url = 'PDF_DOCUMENT_PENDING';
    }

    if (editingArticle.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('admin.articles.update', { article: editingArticle.value.id }), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
            onError: (errors) => {
                console.error('Submission Errors:', errors);
            }
        });
    } else {
        form.post(route('admin.articles.store'), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
            onError: (errors) => {
                console.error('Submission Errors:', errors);
            }
        });
    }
}

function destroy(id: number) {
    if (confirm('¿Estás seguro de eliminar este artículo?')) {
        form.delete(route('admin.articles.destroy', { article: id }));
    }
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', {
        month: 'long',
        year: 'numeric'
    });
}

function getDownloadUrl(article: Article) {
    if (article.download_url === 'PDF_DOCUMENT_PENDING' && article.id) {
        // This is a fallback for older records if the artisan fix didn't catch them
        // though the controller now saves the correct URL and we ran a fix.
        return '#';
    }
    return article.download_url;
}

// Client-side Elite Search & Filter
const searchQuery = ref('');
const activeFilter = ref('Todos');
const viewMode = ref<'grid' | 'list'>('grid');

const filteredArticles = ref<Article[]>(props.articles.data);

watch([searchQuery, activeFilter], () => {
    filteredArticles.value = props.articles.data.filter(article => {
        const matchesSearch = !searchQuery.value || 
            article.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            article.media.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesType = activeFilter.value === 'Todos' || article.media === activeFilter.value;
        
        return matchesSearch && matchesType;
    });
}, { immediate: true });

// Sync with props whenever inertia updates the data
watch(() => props.articles.data, (newData) => {
    filteredArticles.value = newData.filter(article => {
        const matchesSearch = !searchQuery.value || 
            article.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            article.media.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesType = activeFilter.value === 'Todos' || article.media === activeFilter.value;
        return matchesSearch && matchesType;
    });
});
</script>

<template>
    <Head title="Admin - Artículos" />
    <AppLayout>
        <!-- Elite Header Section -->
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-6">
                <span class="px-4 py-1.5 rounded-full bg-[#1a1a10] text-[8px] font-black uppercase tracking-[0.3em] text-[#e7e6ab] shadow-sm">Difusión Académica de Elite</span>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="space-y-2">
                    <h2 class="font-serif text-5xl font-bold text-[#1a1a10] leading-tight">Gestión de <span class="italic text-[#57572A]">Artículos</span></h2>
                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Publicaciones académicas y de opinión en medios externos.</p>
                </div>
                
                <button
                    @click="openCreate"
                    class="order-first md:order-last inline-flex items-center gap-3 rounded-full bg-[#1a1a10] px-8 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#e7e6ab] shadow-2xl hover:bg-[#2a2a1a] hover:scale-[1.05] active:scale-95 transition-all duration-300"
                >
                    <Plus class="h-3 w-3" stroke-width="4" />
                    Nuevo Artículo
                </button>
            </div>
        </div>

        <!-- Elite Search & Filter Bar (The Archive Controller) -->
        <div class="mb-12 flex flex-col gap-8">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="relative flex-1 group">
                    <Search class="absolute left-6 top-1/2 -translate-y-1/2 h-5 w-5 text-[#1a1a10]/30 group-focus-within:text-[#57572A] transition-all duration-500" />
                    <input 
                        v-model="searchQuery"
                        type="search" 
                        placeholder="Filtrar colección de artículos... (Título, Medio, Editorial)" 
                        class="w-full bg-white border-2 border-[#1a1a10]/5 rounded-[2rem] py-5 pl-16 pr-8 text-[11px] font-black uppercase tracking-[0.2em] text-[#1a1a10] outline-none focus:ring-8 focus:ring-[#57572A]/5 focus:border-[#57572A]/20 transition-all placeholder:text-[#1a1a10]/20 shadow-xl shadow-[#1a1a10]/2 cursor-text"
                    >
                </div>
                
                <div class="flex items-center gap-2 p-1.5 bg-[#f7f6f0] rounded-full border border-[#1a1a10]/5 shadow-inner">
                    <button 
                        @click="activeFilter = 'Todos'"
                        class="px-6 py-3 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-500"
                        :class="activeFilter === 'Todos' ? 'bg-[#1a1a10] text-[#e7e6ab] shadow-lg scale-105' : 'text-[#1a1a10]/40 hover:text-[#1a1a10]'"
                    >
                        Todos los Medios
                    </button>
                    <!-- Additional filters can be dynamically generated or hardcoded if specific media are common -->
                </div>
            </div>

            <div class="flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <div class="h-[1px] w-12 bg-[#1a1a10]/10"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Archive <span class="text-[#1a1a10]">Sync</span></span>
                </div>
                
                <div class="flex items-center gap-4 ml-auto">
                    <div class="flex items-center p-1 bg-[#f7f6f0] rounded-lg border border-[#1a1a10]/5 mr-4">
                        <button 
                            @click="viewMode = 'grid'"
                            type="button"
                            class="p-2 rounded-md transition-all duration-300"
                            :class="viewMode === 'grid' ? 'bg-white shadow-sm text-[#1a1a10]' : 'text-[#1a1a10]/20 hover:text-[#1a1a10]'"
                        >
                            <LayoutGrid class="h-4 w-4" />
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            type="button"
                            class="p-2 rounded-md transition-all duration-300"
                            :class="viewMode === 'list' ? 'bg-white shadow-sm text-[#1a1a10]' : 'text-[#1a1a10]/20 hover:text-[#1a1a10]'"
                        >
                            <List class="h-4 w-4" />
                        </button>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Resultados: <span class="text-[#1a1a10] text-xs font-serif italic">{{ filteredArticles.length }}</span> Entradas</span>
                </div>
            </div>
        </div>

        <!-- Elite Archive Grid -->
        <div v-if="filteredArticles.length > 0 && viewMode === 'grid'" class="grid gap-x-8 gap-y-16 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 px-6">
            <div v-for="article in filteredArticles" :key="article.id" class="flex flex-col group">
                <!-- Archive Container -->
                <div class="aspect-[3/4] bg-white rounded-[2rem] p-3 shadow-md ring-1 ring-[#1a1a10]/5 group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-500 relative">
                    <div class="h-full w-full rounded-[1.5rem] overflow-hidden bg-[#fdfdfc] relative">
                        <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                        <div v-else class="flex h-full w-full items-center justify-center text-[#1a1a10]/5">
                            <Calendar class="h-16 w-16" />
                        </div>
                        
                        <!-- Badges -->
                        <div class="absolute inset-x-0 top-0 p-3 flex justify-between pointer-events-none">
                            <span class="bg-white/90 backdrop-blur-md px-2 py-0.5 rounded-lg text-[7px] font-black uppercase tracking-widest text-[#1a1a10] shadow-sm">
                                External Media
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Archive Info -->
                <div class="mt-5 px-2 flex flex-col">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-[#9ca3af] mb-1 leading-none">{{ article.media }}</span>
                    <h3 class="font-serif text-lg font-black text-[#1a1a10] line-clamp-2 mb-4 leading-tight group-hover:text-[#57572A] transition-colors">
                        {{ article.title }}
                    </h3>
                    
                    <div class="flex items-center justify-between border-t border-[#1a1a10]/5 pt-4">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Fecha Publicación</span>
                            <span class="text-[10px] font-black text-[#1a1a10] uppercase tracking-tighter">
                                {{ formatDate(article.published_at) }}
                            </span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a :href="getDownloadUrl(article)" target="_blank" :class="{ 'opacity-20 pointer-events-none': article.download_url === 'PDF_DOCUMENT_PENDING' }" class="w-8 h-8 rounded-full bg-[#f7f6f0] flex items-center justify-center text-[#1a1a10] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300 shadow-sm">
                                <ExternalLink class="h-3 w-3" />
                            </a>
                            <button @click="openEdit(article)" class="w-8 h-8 rounded-full bg-[#f7f6f0] flex items-center justify-center text-[#1a1a10] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300 shadow-sm">
                                <Edit2 class="h-3 w-3" />
                            </button>
                            <button @click="destroy(article.id)" class="w-8 h-8 rounded-full bg-[#f7f6f0] flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm">
                                <Trash2 class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Elite Editorial Table View -->
        <div v-else-if="filteredArticles.length > 0 && viewMode === 'list'" class="bg-white rounded-[2.5rem] border border-[#1a1a10]/5 shadow-xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-[#1a1a10]/5">
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af]">Identidad del Artículo</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Publicación</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase tracking-[0.25em] text-[#9ca3af] text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a1a10]/5">
                    <tr v-for="article in filteredArticles" :key="article.id" class="group hover:bg-[#f7f6f0]/40 transition-all duration-500">
                        <td class="px-10 py-7">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 bg-[#fdfdfc] rounded-2xl border border-[#1a1a10]/5 overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-[#1a1a10]/5">
                                        <Calendar class="h-6 w-6" />
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-[15px] font-bold text-[#1a1a10] leading-tight">{{ article.title }}</h4>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ article.media }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-7 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-black text-[#1a1a10] uppercase tracking-tighter">{{ formatDate(article.published_at) }}</span>
                                <span class="text-[7px] font-black uppercase tracking-widest text-[#9ca3af] mt-0.5 opacity-60">Vigente</span>
                            </div>
                        </td>
                        <td class="px-10 py-7">
                            <div class="flex items-center justify-center gap-3">
                                <a :href="getDownloadUrl(article)" target="_blank" :class="{ 'opacity-20 pointer-events-none': article.download_url === 'PDF_DOCUMENT_PENDING' }" class="w-10 h-10 rounded-full bg-white border border-[#1a1a10]/5 flex items-center justify-center text-[#9ca3af] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300 shadow-sm hover:shadow-md">
                                    <ExternalLink class="h-4 w-4" />
                                </a>
                                <button @click="openEdit(article)" class="w-10 h-10 rounded-full bg-white border border-[#1a1a10]/5 flex items-center justify-center text-[#9ca3af] hover:bg-[#1a1a10] hover:text-[#e7e6ab] transition-all duration-300 shadow-sm hover:shadow-md">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="destroy(article.id)" class="w-10 h-10 rounded-full bg-white border border-[#1a1a10]/5 flex items-center justify-center text-[#9ca3af] hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-md">
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
            <h3 class="font-serif text-2xl font-bold text-[#1a1a10] mb-2">Sin registros en el archivo</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">No hemos encontrado resultados que coincidan con "<span class="text-[#1a1a10]">{{ searchQuery }}</span>"</p>
        </div>

        <!-- Elite Pagination (Archive Controller) -->
        <div v-if="filteredArticles.length > 0 && articles.links.length > 3" class="mt-20 flex flex-col items-center gap-6 pb-20 border-t border-[#1a1a10]/5 pt-12">
            <span class="text-[8px] font-black uppercase tracking-[0.4em] text-[#9ca3af]">Navegación de Archivo</span>
            <div class="flex items-center gap-2">
                <template v-for="link in articles.links" :key="link.label">
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
                        <h2 class="font-serif text-3xl font-bold text-on-surface leading-none">{{ editingArticle ? 'Editar' : 'Nuevo' }} <span class="italic text-primary">Artículo</span></h2>
                        <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">Difusión y Prensa Institucional</p>
                    </div>
                    <button @click="showModal = false" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container-low transition-colors">
                        <X class="h-5 w-5 text-on-surface-variant" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-10 space-y-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1a1a10]/60">Título de la Publicación</label>
                        <input v-model="form.title" type="text" placeholder="Ej: Análisis del sector energético 2024" class="w-full rounded-2xl border border-[#1a1a10]/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-[#57572A] outline-none transition-all" :class="{ 'border-red-500': form.errors.title }">
                        <p v-if="form.errors.title" class="text-[8px] font-bold text-red-500 uppercase tracking-widest mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1a1a10]/60">Medio / Editorial</label>
                            <input v-model="form.media" type="text" placeholder="Diario Gestión..." class="w-full rounded-2xl border border-[#1a1a10]/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-[#57572A] outline-none transition-all" :class="{ 'border-red-500': form.errors.media }">
                            <p v-if="form.errors.media" class="text-[8px] font-bold text-red-500 uppercase tracking-widest mt-1">{{ form.errors.media }}</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1a1a10]/60">Fecha</label>
                            <input v-model="form.published_at" type="date" class="w-full rounded-2xl border border-[#1a1a10]/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-[#57572A] outline-none transition-all" :class="{ 'border-red-500': form.errors.published_at }">
                            <p v-if="form.errors.published_at" class="text-[8px] font-bold text-red-500 uppercase tracking-widest mt-1">{{ form.errors.published_at }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#57572A]">Origen del Recurso</label>
                            <div class="flex gap-4">
                                <button 
                                    type="button"
                                    @click="sourceType = 'link'"
                                    class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-300"
                                    :class="sourceType === 'link' ? 'bg-[#1a1a10] border-[#1a1a10] text-[#e7e6ab] shadow-xl' : 'bg-white border-[#1a1a10]/5 text-[#1a1a10]/40'"
                                >
                                    <Globe class="h-4 w-4" />
                                    <span class="text-[9px] font-black uppercase tracking-widest text-center">Enlace Externo</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="sourceType = 'pdf'"
                                    class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-300"
                                    :class="sourceType === 'pdf' ? 'bg-[#1a1a10] border-[#1a1a10] text-[#e7e6ab] shadow-xl' : 'bg-white border-[#1a1a10]/5 text-[#1a1a10]/40'"
                                >
                                    <FileText class="h-4 w-4" />
                                    <span class="text-[9px] font-black uppercase tracking-widest text-center">Archivo PDF</span>
                                </button>
                            </div>
                        </div>

                        <div v-if="sourceType === 'link'" class="space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Dirección Web del Artículo</label>
                            <div class="relative">
                                <Globe class="absolute left-5 top-1/2 -translate-y-1/2 h-4 text-[#1a1a10]/20" />
                                <input v-model="form.download_url" type="url" placeholder="https://example.com/investigacion" class="w-full rounded-2xl border border-[#1a1a10]/10 bg-[#fdfdfc] pl-14 pr-5 py-3.5 text-xs font-medium focus:border-[#57572A] outline-none transition-all">
                            </div>
                        </div>

                        <div v-else class="space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Documento PDF Institucional</label>
                            <label class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-[#1a1a10]/10 py-10 cursor-pointer bg-white hover:bg-[#f7f6f0] hover:border-[#57572A] transition-all group">
                                <input type="file" @change="e => onFileChange(e, 'file_path')" class="hidden" accept=".pdf">
                                <div class="w-12 h-12 rounded-xl bg-[#1a1a10]/5 flex items-center justify-center text-[#1a1a10] group-hover:scale-110 transition-transform">
                                    <FileText class="h-6 w-6" />
                                </div>
                                <span class="text-[9px] font-black text-[#1a1a10] uppercase tracking-widest">
                                    {{ form.file_path ? 'Documento Listo' : 'Seleccionar PDF' }}
                                </span>
                                <span v-if="form.file_path" class="text-[8px] text-[#57572A] font-bold italic px-6 text-center line-clamp-1">{{ form.file_path.name }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-[#1a1a10]/5">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Identidad Visual (Miniatura)</label>
                        <div class="flex items-center gap-8">
                            <label class="flex-1 cursor-pointer flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-[#1a1a10]/10 py-12 bg-white hover:bg-[#f7f6f0] hover:border-[#57572A] transition-all group" :class="{ 'border-red-500 bg-red-50/10': form.errors.thumbnail }">
                                <input type="file" @change="e => onFileChange(e, 'thumbnail')" class="hidden" accept="image/*">
                                <div class="w-12 h-12 rounded-full bg-[#1a1a10]/5 flex items-center justify-center text-[#1a1a10] group-hover:scale-110 transition-transform">
                                    <ImagePlus class="h-6 w-6" />
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-[#1a1a10]/30 group-hover:text-[#1a1a10]">Cargar Miniatura</span>
                            </label>
                            <div v-if="imagePreview" class="h-32 w-32 overflow-hidden rounded-2xl border-2 border-white shadow-2xl flex-shrink-0 relative group/preview">
                                <img :src="imagePreview" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-[7px] font-black text-white uppercase tracking-tighter">Vista Final</span>
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.thumbnail" class="text-[8px] font-bold text-red-500 uppercase tracking-widest leading-tight">{{ form.errors.thumbnail }}</p>
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
                        {{ editingArticle ? 'Confirmar Cambios' : 'Lanzar al Archivo' }}
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
</style>
