<script setup lang="ts">
import { ImagePlus, FileText, Globe } from 'lucide-vue-next';

const form = defineModel<{
    category: 'Libro' | 'Libro en camino' | 'Guía';
    title: string;
    price: number;
    author: string;
    description: string;
    cover_image: File | null;
    file_path: File | null;
    download_url: string;
    is_available: boolean;
}>('form', { required: true });

const imagePreview = defineModel<string | null>('imagePreview', { required: true });
const isFree = defineModel<boolean>('isFree', { required: true });

function onFileChange(e: Event, field: 'cover_image' | 'file_path') {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    (form.value as any)[field] = file;
    if (field === 'cover_image') imagePreview.value = URL.createObjectURL(file);
}
</script>

<template>
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
</template>
