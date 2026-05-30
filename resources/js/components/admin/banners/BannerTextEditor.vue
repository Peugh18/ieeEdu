<script setup lang="ts">
import { Type, Edit3, Link as LinkIcon } from 'lucide-vue-next';

const slide = defineModel<{
    heading: string;
    subheading: string;
    button_text: string;
    button_link: string;
    show_text: boolean;
}>('slide', { required: true });

defineProps<{
    isHome: boolean;
}>();
</script>

<template>
    <div class="rounded-[2.5rem] p-8 shadow-sm border border-outline-variant/20 bg-surface-container-lowest space-y-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xl font-serif flex items-center gap-2 text-on-surface">
                <Type class="w-5 h-5 text-primary" />
                Contenido del Banner
            </h3>
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                    {{ slide.show_text ? 'Texto Visible' : 'Texto Oculto' }}
                </span>
                <button type="button" @click="slide.show_text = !slide.show_text" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none"
                    :class="slide.show_text ? 'bg-primary' : 'bg-outline-variant/50'"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-300" :class="slide.show_text ? 'translate-x-6' : 'translate-x-1'" />
                </button>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Título Principal</label>
                <input v-model="slide.heading" type="text" class="w-full h-14 rounded-2xl px-5 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                    placeholder="Escribe el título..." />
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Subtítulo / Descripción</label>
                <textarea v-model="slide.subheading" rows="3" class="w-full rounded-2xl p-5 text-sm font-medium outline-none transition-all resize-none border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                    placeholder="Escribe la descripción..."></textarea>
            </div>

            <template v-if="isHome">
                <div class="space-y-2">
                    <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Texto del Botón (Opcional)</label>
                    <div class="relative">
                        <Edit3 class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-variant" />
                        <input v-model="slide.button_text" type="text" class="w-full h-12 rounded-xl pl-11 pr-4 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                            placeholder="Ej: Explorar Cursos" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Enlace del Botón (Opcional)</label>
                    <div class="relative">
                        <LinkIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-variant" />
                        <input v-model="slide.button_link" type="text" class="w-full h-12 rounded-xl pl-11 pr-4 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                            placeholder="Ej: /cursos" />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
