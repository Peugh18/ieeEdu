<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Edit2, Trash2, X, Check, ImagePlus, Loader2, Link, Calendar, ExternalLink } from 'lucide-vue-next';

interface Article {
    id: number;
    title: string;
    media: string;
    published_at: string;
    thumbnail: string | null;
    download_url: string;
}

const props = defineProps<{
    articles: { data: Article[]; links: any[] };
}>();

const showModal = ref(false);
const editingArticle = ref<Article | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({
    title: '',
    media: '',
    published_at: '',
    thumbnail: null as File | null,
    download_url: '',
});

function openCreate() {
    editingArticle.value = null;
    form.reset();
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
    imagePreview.value = article.thumbnail ? `/storage/${article.thumbnail}` : null;
    showModal.value = true;
}

function onFileChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.thumbnail = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    if (editingArticle.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('admin.articles.update', { article: editingArticle.value.id }), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.articles.store'), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
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
</script>

<template>
    <Head title="Admin - Artículos" />
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-3xl text-on-surface">Gestión de <span class="italic">Artículos</span></h1>
                <p class="text-sm text-on-surface-variant">Publicaciones académicas y de opinión.</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow hover:opacity-90 transition-all active:scale-95"
            >
                <Plus class="h-4 w-4" />
                Nuevo Artículo
            </button>
        </div>

        <div class="overflow-hidden rounded-3xl border bg-white shadow-sm overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low border-b">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Artículo</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Medio de difusión</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant text-center">Publicación</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Link</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <tr v-for="article in props.articles.data" :key="article.id" class="hover:bg-surface-container-lowest transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-lg border bg-surface-container-low">
                                    <img v-if="article.thumbnail" :src="`/storage/${article.thumbnail}`" class="h-full w-full object-cover">
                                    <div v-else class="flex h-full w-full items-center justify-center opacity-20"><Calendar class="h-5 w-5" /></div>
                                </div>
                                <span class="text-sm font-bold text-on-surface line-clamp-1 max-w-xs" :title="article.title">{{ article.title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-on-surface-variant">{{ article.media }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-container/50 px-3 py-1 text-xs font-bold text-on-surface-variant">
                                <Calendar class="h-3 w-3" />
                                {{ formatDate(article.published_at) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a :href="article.download_url" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                                URL <ExternalLink class="h-3 w-3" />
                            </a>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="openEdit(article)" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors">
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button @click="destroy(article.id)" class="rounded-lg p-2 text-red-500 hover:bg-red-50 transition-colors">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!articles.data.length" class="py-20 text-center text-sm text-on-surface-variant">No se encontraron artículos.</div>
        </div>

        <!-- Pagination -->
        <div v-if="articles.links.length > 3" class="mt-4 flex justify-end">
            <!-- Add pagination components if available, or just standard links -->
            <div class="flex gap-1">
                <template v-for="link in articles.links" :key="link.label">
                    <span v-if="!link.url" v-html="link.label" class="px-3 py-1 text-xs text-gray-400 border border-transparent"></span>
                    <a v-else :href="link.url" v-html="link.label" 
                        class="px-3 py-1 text-xs rounded border transition-colors border-outline-variant/30 hover:border-primary hover:text-primary"
                        :class="[link.active ? 'bg-primary text-white border-primary' : 'bg-white']"></a>
                </template>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm" @click.self="showModal = false">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b px-8 py-6">
                    <h2 class="font-serif text-2xl text-on-surface">{{ editingArticle ? 'Editar' : 'Nuevo' }} Artículo</h2>
                    <button @click="showModal = false"><X class="h-5 w-5 text-on-surface-variant" /></button>
                </div>

                <form @submit.prevent="submit" class="p-8 space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Nombre del Artículo</label>
                        <input v-model="form.title" type="text" placeholder="Ej. El impacto de la IA en la gestión pública" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Medio de Difusión</label>
                        <input v-model="form.media" type="text" placeholder="Revista Económica, Blog Institucional..." class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none">
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Fecha de Publicación</label>
                            <input v-model="form.published_at" type="date" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none">
                            <p class="text-[10px] text-on-surface-variant/60">Selecciona cualquier día del mes deseado.</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">URL de Descarga / Enlace Externo</label>
                        <input v-model="form.download_url" type="url" placeholder="https://..." class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Imagen de Portada / Miniatura</label>
                        <div class="flex items-center gap-4">
                            <label class="flex-1 cursor-pointer flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-outline-variant/30 py-6 hover:bg-surface-container-lowest transition-colors">
                                <input type="file" @change="onFileChange" class="hidden" accept="image/*">
                                <ImagePlus class="h-5 w-5 text-on-surface-variant" />
                                <span class="text-xs font-semibold text-on-surface-variant">Subir Imagen</span>
                            </label>
                            <div v-if="imagePreview" class="h-16 w-16 overflow-hidden rounded-xl border shadow-sm flex-shrink-0">
                                <img :src="imagePreview" class="h-full w-full object-cover">
                            </div>
                        </div>
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
                        {{ editingArticle ? 'Guardar Cambios' : 'Crear Artículo' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
