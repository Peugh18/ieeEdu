<script setup lang="ts">
import { ImagePlus, Globe, FileText } from 'lucide-vue-next';
import AdminFormField from '@/components/admin/AdminFormField.vue';

const form = defineModel<{
    title: string;
    media: string;
    published_at: string;
    thumbnail: File | null;
    file_path: File | null;
    download_url: string;
}>('form', { required: true });

const imagePreview = defineModel<string | null>('imagePreview', { required: true });
const sourceType = defineModel<'link' | 'pdf'>('sourceType', { required: true });

function onFileChange(e: Event, field: 'thumbnail' | 'file_path') {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    if (field === 'thumbnail') {
        form.value.thumbnail = file;
        imagePreview.value = URL.createObjectURL(file);
    } else {
        form.value.file_path = file;
    }
}
</script>

<template>
    <AdminFormField label="Título de la Publicación" :error="(form as any).errors?.title">
        <input v-model="form.title" type="text" placeholder="Ej: Análisis del sector energético 2024" class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': (form as any).errors?.title }">
    </AdminFormField>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <AdminFormField label="Medio / Editorial" :error="(form as any).errors?.media">
            <input v-model="form.media" type="text" placeholder="Diario Gestión..." class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': (form as any).errors?.media }">
        </AdminFormField>
        <AdminFormField label="Fecha" :error="(form as any).errors?.published_at">
            <input v-model="form.published_at" type="date" class="w-full rounded-2xl border border-on-background/10 bg-[#fdfdfc] px-5 py-3.5 text-sm font-medium focus:border-primary outline-none transition-all" :class="{ 'border-red-500': (form as any).errors?.published_at }">
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
            <label class="flex-1 cursor-pointer flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-on-background/10 py-12 bg-white hover:bg-surface-container hover:border-primary transition-all group" :class="{ 'border-red-500 bg-red-50/10': (form as any).errors?.thumbnail }">
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
        <p v-if="(form as any).errors?.thumbnail" class="text-[8px] font-bold text-red-500 uppercase tracking-widest leading-tight">{{ (form as any).errors?.thumbnail }}</p>
    </div>
</template>
