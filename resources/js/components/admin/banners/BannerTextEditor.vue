<script setup lang="ts">
import { Edit3, Link as LinkIcon, Type } from 'lucide-vue-next';

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
    <div class="space-y-6 rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
            <h3 class="flex items-center gap-2 font-serif text-xl text-on-surface">
                <Type class="h-5 w-5 text-primary" />
                Contenido del Banner
            </h3>
            <label class="flex cursor-pointer select-none items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                    {{ slide.show_text ? 'Texto Visible' : 'Texto Oculto' }}
                </span>
                <button
                    type="button"
                    @click="slide.show_text = !slide.show_text"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none"
                    :class="slide.show_text ? 'bg-primary' : 'bg-outline-variant/50'"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-300"
                        :class="slide.show_text ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </label>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
                <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant">Título Principal</label>
                <input
                    v-model="slide.heading"
                    type="text"
                    class="h-14 w-full rounded-2xl border border-outline-variant/30 bg-surface-container-high px-5 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                    placeholder="Escribe el título..."
                />
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant">Subtítulo / Descripción</label>
                <textarea
                    v-model="slide.subheading"
                    rows="3"
                    class="w-full resize-none rounded-2xl border border-outline-variant/30 bg-surface-container-high p-5 text-sm font-medium text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                    placeholder="Escribe la descripción..."
                ></textarea>
            </div>

            <template v-if="isHome">
                <div class="space-y-2">
                    <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant"
                        >Texto del Botón (Opcional)</label
                    >
                    <div class="relative">
                        <Edit3 class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant" />
                        <input
                            v-model="slide.button_text"
                            type="text"
                            class="h-12 w-full rounded-xl border border-outline-variant/30 bg-surface-container-high pl-11 pr-4 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                            placeholder="Ej: Explorar Cursos"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant"
                        >Enlace del Botón (Opcional)</label
                    >
                    <div class="relative">
                        <LinkIcon class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant" />
                        <input
                            v-model="slide.button_link"
                            type="text"
                            class="h-12 w-full rounded-xl border border-outline-variant/30 bg-surface-container-high pl-11 pr-4 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                            placeholder="Ej: /cursos"
                        />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
