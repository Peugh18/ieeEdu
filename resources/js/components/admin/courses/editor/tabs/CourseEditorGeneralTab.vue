<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { InertiaForm } from '@inertiajs/vue3';

defineProps<{
    show: boolean;
    form: InertiaForm<Record<string, unknown>>;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 max-w-3xl">
            <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-on-surface">
                Identidad <span class="font-light italic">Principal</span>
            </h2>
            <p class="mb-12 font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                El título y descripción formarán la base del marketing de tu curso. Asegúrate de que sean claros, persuasivos y detallados para
                maximizar la conversión en matrícula.
            </p>

            <div class="space-y-10">
                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Título Comercial Oficial</label>
                    <input
                        v-model="form.title"
                        class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        :class="form.errors.title ? 'bg-red-50/50 ring-2 ring-red-500' : ''"
                        placeholder="Ej. Máster Especializado en Finanzas Públicas..."
                    />
                    <p v-if="form.errors.title" class="ml-1 mt-1.5 font-sans text-sm font-bold text-red-600">{{ form.errors.title }}</p>
                </div>

                <div class="space-y-3">
                    <label class="ml-1 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant"
                        >Pitch De Venta (Descripción completa)</label
                    >
                    <textarea
                        v-model="form.description"
                        rows="6"
                        class="w-full resize-none rounded-2xl border bg-surface-container-lowest px-5 py-4 text-[14px] leading-relaxed text-on-surface shadow-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10"
                        :class="form.errors.description ? 'border-red-500 bg-red-50/50' : 'border-outline-variant/30'"
                        placeholder="Utiliza este espacio para destacar el valor diferencial del programa..."
                    />
                    <p v-if="form.errors.description" class="ml-1 mt-1.5 text-xs font-bold text-red-600">{{ form.errors.description }}</p>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
