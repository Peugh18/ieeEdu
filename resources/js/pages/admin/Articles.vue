<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import AdminEmptyState from '@/components/admin/AdminEmptyState.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminModal from '@/components/admin/AdminModal.vue';
import AdminIconButton from '@/components/admin/AdminIconButton.vue';
import AdminFormField from '@/components/admin/AdminFormField.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    Edit2, Trash2, Check, ImagePlus,
    Calendar, ExternalLink, Globe, FileText
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

function formatDate(date?: string) {
    if (!date) return 'Sin fecha';
    try {
        const d = new Date(date);
        if (isNaN(d.getTime())) return date;
        return d.toLocaleDateString('es-PE', {
            month: 'long',
            year: 'numeric'
        });
    } catch (e) {
        return date || 'Sin fecha';
    }
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
        <AdminPageHeader
            badge="Difusión Académica de Elite"
            title="Gestión de"
            title-accent="Artículos"
            subtitle="Publicaciones académicas y de opinión en medios externos."
            action-label="Nuevo Artículo"
            action-order="first"
            @action="openCreate"
        />

        <AdminSearchBar
            v-model="searchQuery"
            v-model:view-mode="viewMode"
            placeholder="Filtrar colección de artículos... (Título, Medio, Editorial)"
            :result-count="filteredArticles.length"
            result-label="Resultados"
            sync-label="Archive"
            sync-accent="Sync"
        >
            <template #filters>
                <div class="flex items-center gap-2 p-1.5 bg-surface-container rounded-full border border-on-background/5 shadow-inner overflow-x-auto whitespace-nowrap custom-scrollbar">
                    <button
                        @click="activeFilter = 'Todos'"
                        class="px-6 py-3 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-500"
                        :class="activeFilter === 'Todos' ? 'bg-on-background text-primary-fixed shadow-lg scale-105' : 'text-on-background/40 hover:text-on-background'"
                    >Todos los Medios</button>
                </div>
            </template>
        </AdminSearchBar>

        <!-- Elite Archive Grid -->
        <div v-if="filteredArticles.length > 0 && viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <div v-for="article in filteredArticles" :key="article.id" class="flex flex-col group bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm hover:shadow-[0_30px_60px_rgba(87,87,42,0.12)] transition-all duration-700 overflow-hidden hover:-translate-y-3">
                <!-- Thumbnail Container -->
                <div class="relative aspect-[4/3] bg-surface-container-highest overflow-hidden border-b border-outline-variant/10">
                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                    <div v-else class="flex h-full w-full items-center justify-center text-primary/10">
                        <Calendar class="h-16 w-16" />
                    </div>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-on-background/60 via-transparent to-transparent opacity-60"></div>
                </div>
                
                <!-- Info Section -->
                <div class="p-6 flex flex-col flex-1">
                    <span class="text-[10px] font-bold text-primary/60 uppercase tracking-[0.2em] mb-2 leading-none">{{ article.media }}</span>
                    <h3 class="font-serif text-xl font-bold text-on-background leading-snug group-hover:text-primary transition-colors italic line-clamp-2 mb-4" :title="article.title">
                        {{ article.title }}
                    </h3>
                    
                    <div class="flex items-center justify-between border-t border-surface-container-highest pt-4 mt-auto">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black uppercase tracking-widest text-[#9ca3af] mb-0.5">Publicado</span>
                            <span class="text-[10px] font-black text-on-surface uppercase tracking-tighter">
                                {{ formatDate(article.published_at) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center bg-surface-container-highest/30 p-1.5 rounded-xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-lg transition-all duration-700">
                            <AdminIconButton as="a" :href="getDownloadUrl(article)" target="_blank" :disabled="article.download_url === 'PDF_DOCUMENT_PENDING'" size="sm" title="Ver enlace">
                                <template #default="{ iconClass }"><ExternalLink :class="iconClass" /></template>
                            </AdminIconButton>
                            <AdminIconButton variant="outline" @click="openEdit(article)" size="sm" title="Editar">
                                <template #default="{ iconClass }"><Edit2 :class="iconClass" /></template>
                            </AdminIconButton>
                            <AdminIconButton variant="danger" @click="destroy(article.id)" size="sm" title="Eliminar">
                                <template #default="{ iconClass }"><Trash2 :class="iconClass" /></template>
                            </AdminIconButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Elite Editorial Table View -->
        <div v-else-if="filteredArticles.length > 0 && viewMode === 'list'" class="rounded-[3rem] border border-outline-variant/10 bg-white overflow-hidden shadow-2xl shadow-surface-tint/5 relative z-0 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-surface-container-highest/40 border-b border-outline-variant/10">
                            <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60">Identidad del Artículo</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Publicación</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        <tr v-for="article in filteredArticles" :key="article.id" class="group transition-all hover:bg-primary/[0.02] duration-500">
                            <td class="px-10 py-7 relative">
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                                <div class="flex items-center gap-6">
                                    <div class="w-20 h-14 bg-surface-container-low border border-outline-variant/10 rounded-2xl overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-500 flex-shrink-0 relative">
                                        <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center text-primary/10">
                                            <Calendar class="h-6 w-6" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <h4 class="text-[15px] font-bold text-on-background leading-tight">{{ article.title }}</h4>
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] mt-1">{{ article.media }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-7 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] font-black text-on-surface uppercase tracking-tighter">{{ formatDate(article.published_at) }}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-primary/40 mt-0.5">Vigente</span>
                                </div>
                            </td>
                            <td class="px-10 py-7">
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center bg-surface-container-highest/30 p-2 rounded-2xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-xl transition-all duration-700 opacity-100 lg:opacity-40 group-hover:opacity-100 transform lg:group-hover:-translate-x-2">
                                        <AdminIconButton as="a" variant="outline" :href="getDownloadUrl(article)" target="_blank" :disabled="article.download_url === 'PDF_DOCUMENT_PENDING'" size="sm" title="Ver enlace">
                                            <template #default="{ iconClass }"><ExternalLink :class="iconClass" /></template>
                                        </AdminIconButton>
                                        <AdminIconButton variant="outline" @click="openEdit(article)" size="sm" title="Editar">
                                            <template #default="{ iconClass }"><Edit2 :class="iconClass" /></template>
                                        </AdminIconButton>
                                        <AdminIconButton variant="danger" @click="destroy(article.id)" size="sm" title="Eliminar">
                                            <template #default="{ iconClass }"><Trash2 :class="iconClass" /></template>
                                        </AdminIconButton>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            
        <AdminEmptyState v-else title="Sin registros en el archivo" :query="searchQuery" />

        <AdminPagination v-if="filteredArticles.length > 0" :links="articles.links" />

        <!-- Modal -->
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
            <AdminFormField label="Título de la Publicación" :error="form.errors.title">
                <input v-model="form.title" type="text" placeholder="Ej: Análisis del sector energético 2024" class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': form.errors.title }">
            </AdminFormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <AdminFormField label="Medio / Editorial" :error="form.errors.media">
                    <input v-model="form.media" type="text" placeholder="Diario Gestión..." class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': form.errors.media }">
                </AdminFormField>
                <AdminFormField label="Fecha" :error="form.errors.published_at">
                    <input v-model="form.published_at" type="date" class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': form.errors.published_at }">
                </AdminFormField>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Origen del Recurso</label>
                    <div class="flex gap-4">
                        <button type="button" @click="sourceType = 'link'" class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-300" :class="sourceType === 'link' ? 'bg-on-background border-on-background text-primary-fixed shadow-xl' : 'bg-white border-on-background/5 text-on-background/40'">
                            <Globe class="h-4 w-4" /><span class="text-[9px] font-black uppercase tracking-widest">Enlace Externo</span>
                        </button>
                        <button type="button" @click="sourceType = 'pdf'" class="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-300" :class="sourceType === 'pdf' ? 'bg-on-background border-on-background text-primary-fixed shadow-xl' : 'bg-white border-on-background/5 text-on-background/40'">
                            <FileText class="h-4 w-4" /><span class="text-[9px] font-black uppercase tracking-widest">Archivo PDF</span>
                        </button>
                    </div>
                </div>

                <div v-if="sourceType === 'link'" class="space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Dirección Web del Artículo</label>
                    <div class="relative">
                        <Globe class="absolute left-5 top-1/2 -translate-y-1/2 h-4 text-on-background/20" />
                        <input v-model="form.download_url" type="url" placeholder="https://example.com/investigacion" class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] pl-14 pr-5 py-3.5 text-xs font-medium focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div v-else class="space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Documento PDF Institucional</label>
                    <label class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-on-background/10 py-10 cursor-pointer bg-white hover:bg-surface-container hover:border-primary transition-all group">
                        <input type="file" @change="e => onFileChange(e, 'file_path')" class="hidden" accept=".pdf">
                        <div class="w-12 h-12 rounded-xl bg-on-background/5 flex items-center justify-center text-on-background group-hover:scale-110 transition-transform"><FileText class="h-6 w-6" /></div>
                        <span class="text-[9px] font-black text-on-background uppercase tracking-widest">{{ form.file_path ? 'Documento Listo' : 'Seleccionar PDF' }}</span>
                        <span v-if="form.file_path" class="text-[8px] text-primary font-bold italic px-6 text-center line-clamp-1">{{ form.file_path.name }}</span>
                    </label>
                </div>
            </div>

            <div class="space-y-4 pt-4 border-t border-on-background/5">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af]">Identidad Visual (Miniatura)</label>
                <div class="flex items-center gap-8">
                    <label class="flex-1 cursor-pointer flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-on-background/10 py-12 bg-white hover:bg-surface-container hover:border-primary transition-all group" :class="{ 'border-red-500 bg-red-50/10': form.errors.thumbnail }">
                        <input type="file" @change="e => onFileChange(e, 'thumbnail')" class="hidden" accept="image/*">
                        <div class="w-12 h-12 rounded-full bg-on-background/5 flex items-center justify-center text-on-background group-hover:scale-110 transition-transform"><ImagePlus class="h-6 w-6" /></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-on-background/30 group-hover:text-on-background">Cargar Miniatura</span>
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
        </AdminModal>
    </AppLayout>
</template>

<style scoped>
.font-serif { font-family: 'Noto Serif', serif; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>
