<script setup lang="ts">
import { FileText, Globe, ImagePlus } from 'lucide-vue-next';

const form = defineModel<{
    category: 'Libro' | 'Libro en camino' | 'Guía';
    title: string;
    price: number;
    stock: number;
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
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Categoría</label>
            <select
                v-model="form.category"
                class="w-full cursor-pointer rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
            >
                <option value="Libro">Libro</option>
                <option value="Libro en camino">Libro en camino</option>
                <option value="Guía">Guía</option>
            </select>
        </div>
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Título de la Obra</label>
            <input
                v-model="form.title"
                type="text"
                class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                placeholder="Ej: Introducción al Derecho..."
            />
        </div>
    </div>

    <template v-if="form.category !== 'Libro en camino'">
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Autor / Responsable</label>
            <input
                v-model="form.author"
                type="text"
                class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-3.5 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                placeholder="Nombre completo..."
            />
        </div>

        <div class="space-y-6 rounded-[2rem] border border-primary/10 bg-surface-dim p-8">
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Modelo de Adquisición</label>
                <p class="text-[11px] font-medium italic text-on-surface-variant/60">Define si el contenido es transaccional o de libre acceso.</p>
            </div>
            <div class="flex gap-4">
                <label
                    class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all duration-300"
                    :class="
                        !isFree
                            ? 'border-primary bg-primary text-primary-fixed shadow-xl shadow-primary/20'
                            : 'border-outline-variant/10 bg-white text-on-surface-variant/50 hover:border-primary/30'
                    "
                >
                    <input type="radio" v-model="isFree" :value="false" class="hidden" />
                    <span class="text-[10px] font-black uppercase tracking-widest text-inherit">Premium (Pago)</span>
                </label>
                <label
                    class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all duration-300"
                    :class="
                        isFree
                            ? 'border-primary bg-primary text-primary-fixed shadow-xl shadow-primary/20'
                            : 'border-outline-variant/10 bg-white text-on-surface-variant/50 hover:border-primary/30'
                    "
                >
                    <input type="radio" v-model="isFree" :value="true" class="hidden" />
                    <span class="text-[10px] font-black uppercase tracking-widest text-inherit">Gratuito</span>
                </label>
            </div>
            <div v-if="!isFree" class="space-y-4 pt-2 duration-500 animate-in fade-in slide-in-from-top-4">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Precio Sugerido (S/)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-bold text-primary/40">S/</span>
                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                class="w-full rounded-2xl border border-outline-variant/20 bg-white py-3.5 pl-12 pr-5 text-sm font-black tabular-nums outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                            />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Stock Disponible</label>
                        <input
                            v-model.number="form.stock"
                            type="number"
                            min="0"
                            step="1"
                            class="w-full rounded-2xl border border-outline-variant/20 bg-white px-5 py-3.5 text-sm font-black tabular-nums outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                            placeholder="Ej: 50"
                        />
                        <p class="text-[10px] italic text-on-surface-variant/60">Unidades disponibles para venta.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Recurso Digital (PDF)</label>
                        <label
                            class="group flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-primary/20 bg-white py-6 transition-all hover:border-primary hover:bg-surface-dim"
                        >
                            <input type="file" @change="(e) => onFileChange(e, 'file_path')" class="hidden" accept=".pdf,.doc,.docx,.zip" />
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary transition-transform group-hover:scale-110"
                            >
                                <FileText class="h-5 w-5" />
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-on-surface">{{
                                form.file_path ? 'Cambiar Archivo' : 'Cargar Documento'
                            }}</span>
                            <span v-if="form.file_path" class="limit-text px-4 text-center text-[9px] font-bold italic text-primary">{{
                                form.file_path.name
                            }}</span>
                        </label>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Enlace Externo (Opcional)</label>
                        <div class="relative h-full">
                            <Globe class="absolute left-5 top-1/2 h-4 -translate-y-1/2 text-primary/40" />
                            <input
                                v-model="form.download_url"
                                type="url"
                                class="h-[88px] w-full rounded-2xl border border-outline-variant/20 bg-white py-3.5 pl-14 pr-5 text-xs font-medium outline-none transition-all focus:border-primary"
                                placeholder="https://docs.google.com/..."
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isFree" class="space-y-6 pt-2 duration-500 animate-in fade-in slide-in-from-top-4">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Recurso Digital (PDF)</label>
                        <label
                            class="group flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-primary/20 bg-white py-6 transition-all hover:border-primary hover:bg-surface-dim"
                        >
                            <input type="file" @change="(e) => onFileChange(e, 'file_path')" class="hidden" accept=".pdf,.doc,.docx,.zip" />
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary transition-transform group-hover:scale-110"
                            >
                                <FileText class="h-5 w-5" />
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-on-surface">{{
                                form.file_path ? 'Cambiar Archivo' : 'Cargar Documento'
                            }}</span>
                            <span v-if="form.file_path" class="limit-text px-4 text-center text-[9px] font-bold italic text-primary">{{
                                form.file_path.name
                            }}</span>
                        </label>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Enlace Externo (Opcional)</label>
                        <div class="relative h-full">
                            <Globe class="absolute left-5 top-1/2 h-4 -translate-y-1/2 text-primary/40" />
                            <input
                                v-model="form.download_url"
                                type="url"
                                class="h-[88px] w-full rounded-2xl border border-outline-variant/20 bg-white py-3.5 pl-14 pr-5 text-xs font-medium outline-none transition-all focus:border-primary"
                                placeholder="https://docs.google.com/..."
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="space-y-2">
        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Sinopsis Informativa</label>
        <textarea
            v-model="form.description"
            rows="5"
            class="w-full resize-none rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-5 py-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
            placeholder="Escribe un resumen breve y profesional del contenido..."
        ></textarea>
    </div>

    <div class="space-y-2">
        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Identidad Visual (Portada)</label>
        <div class="flex items-center gap-6">
            <label
                class="group flex flex-1 cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-outline-variant/20 bg-white py-10 transition-all hover:border-primary hover:bg-surface-container-lowest"
            >
                <input type="file" @change="(e) => onFileChange(e, 'cover_image')" class="hidden" accept="image/*" />
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/5 text-primary transition-transform group-hover:scale-110"
                >
                    <ImagePlus class="h-6 w-6" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Seleccionar Portada</span>
            </label>
            <div
                v-if="imagePreview"
                class="group/preview relative h-40 w-28 flex-shrink-0 overflow-hidden rounded-2xl border-2 border-white shadow-2xl"
            >
                <img :src="imagePreview" class="h-full w-full object-cover" />
                <div
                    class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover/preview:opacity-100"
                >
                    <span class="text-[8px] font-black uppercase tracking-tighter text-white">Vista Previa</span>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="form.category !== 'Libro en camino'"
        class="flex items-center gap-3 rounded-2xl border border-outline-variant/5 bg-surface-container-low/20 p-5"
    >
        <input
            v-model="form.is_available"
            type="checkbox"
            class="h-5 w-5 cursor-pointer rounded-lg border-outline-variant/30 text-primary transition-all focus:ring-primary/20"
        />
        <div class="flex flex-col">
            <label class="text-xs font-bold text-on-surface">Disponibilidad en Catálogo</label>
            <span class="text-[10px] font-medium italic text-on-surface-variant/60">Marcar si el contenido está listo para los estudiantes.</span>
        </div>
    </div>
</template>
