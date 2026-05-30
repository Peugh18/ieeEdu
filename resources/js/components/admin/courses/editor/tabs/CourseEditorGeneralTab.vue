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
        <div class="max-w-3xl relative z-10">
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-3 tracking-tight">
                Identidad <span class="italic font-light">Principal</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium mb-12 leading-relaxed">
                El título y descripción formarán la base del marketing de tu curso. Asegúrate de que sean claros, persuasivos y detallados para maximizar la conversión en matrícula.
            </p>

            <div class="space-y-10">
                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Título Comercial Oficial</label>
                    <input
                        v-model="form.title"
                        class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant"
                        :class="form.errors.title ? 'ring-2 ring-red-500 bg-red-50/50' : ''"
                        placeholder="Ej. Máster Especializado en Finanzas Públicas..."
                    />
                    <p v-if="form.errors.title" class="mt-1.5 ml-1 text-sm font-bold text-red-600 font-sans">{{ form.errors.title }}</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest ml-1">Pitch De Venta (Descripción completa)</label>
                    <textarea
                        v-model="form.description"
                        rows="6"
                        class="w-full resize-none rounded-2xl border bg-surface-container-lowest px-5 py-4 text-[14px] text-on-surface shadow-sm focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none leading-relaxed"
                        :class="form.errors.description ? 'border-red-500 bg-red-50/50' : 'border-outline-variant/30'"
                        placeholder="Utiliza este espacio para destacar el valor diferencial del programa..."
                    />
                    <p v-if="form.errors.description" class="mt-1.5 ml-1 text-xs font-bold text-red-600">{{ form.errors.description }}</p>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
