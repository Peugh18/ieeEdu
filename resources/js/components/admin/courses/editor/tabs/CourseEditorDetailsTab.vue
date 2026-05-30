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
                Requisitos y <span class="italic font-light">Exigencias</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium mb-12 leading-relaxed">
                Aporta métricas e información valiosa para que los alumnos entiendan el esfuerzo requerido y los objetivos.
            </p>

            <div class="space-y-10">
                <div
                    v-if="form.type === 'en vivo' || form.type === 'masterclass' || form.type === 'evento'"
                    class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-surface-container-highest p-8 rounded-[1.5rem] border border-transparent shadow-sm"
                >
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-primary font-sans ml-1">Fecha de Lanzamiento / Inicio</label>
                        <input v-model="form.start_date" type="date" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-primary font-sans ml-1">Hora Programada (Local)</label>
                        <input v-model="form.start_time" type="time" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" />
                    </div>
                </div>

                <div v-if="form.type === 'grabado'" class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Carga Horaria Aproximada (Hrs)</label>
                    <input v-model.number="form.class_hours" type="number" min="0" class="w-full md:w-1/3 rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Ej. 120" />
                </div>

                <div v-if="form.type === 'masterclass' || form.type === 'evento'" class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Enlace a Comunidad Oficial (WhatsApp)</label>
                    <input v-model="form.whatsapp_link" type="url" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-primary/50" placeholder="https://chat.whatsapp.com/..." />
                </div>

                <div v-if="form.type !== 'masterclass' && form.type !== 'evento'" class="space-y-3">
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest ml-1">Metas y Logros del Curso</label>
                    <textarea v-model="form.objectives" rows="4" class="w-full resize-none rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-5 py-4 text-[14px] text-on-surface shadow-sm focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none leading-relaxed" placeholder="Desglosa las habilidades que los estudiantes poseerán al finalizar..." />
                </div>

                <div v-if="form.type !== 'masterclass' && form.type !== 'evento'" class="space-y-3">
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest ml-1">Prerrequisitos Académicos</label>
                    <textarea v-model="form.requirements" rows="4" class="w-full resize-none rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-5 py-4 text-[14px] text-on-surface shadow-sm focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none leading-relaxed" placeholder="Conocimientos técnicos, grado académico o software especial requerido..." />
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
