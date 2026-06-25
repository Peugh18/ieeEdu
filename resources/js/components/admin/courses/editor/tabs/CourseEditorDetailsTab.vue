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
                Requisitos y <span class="font-light italic">Exigencias</span>
            </h2>
            <p class="mb-12 font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                Aporta métricas e información valiosa para que los alumnos entiendan el esfuerzo requerido y los objetivos.
            </p>

            <div class="space-y-10">
                <div
                    v-if="form.type === 'en vivo' || form.type === 'masterclass' || form.type === 'evento'"
                    class="grid grid-cols-1 gap-8 rounded-[1.5rem] border border-transparent bg-surface-container-highest p-8 shadow-sm md:grid-cols-2"
                >
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-primary">Fecha de Lanzamiento / Inicio</label>
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        />
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-primary">Hora Programada (Local)</label>
                        <input
                            v-model="form.start_time"
                            type="time"
                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        />
                    </div>
                </div>

                <div v-if="form.type === 'grabado'" class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Carga Horaria Aproximada (Hrs)</label>
                    <input
                        v-model.number="form.class_hours"
                        type="number"
                        min="0"
                        class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20 md:w-1/3"
                        placeholder="Ej. 120"
                    />
                </div>

                <div v-if="form.type === 'masterclass' || form.type === 'evento'" class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Enlace a Comunidad Oficial (WhatsApp)</label>
                    <input
                        v-model="form.whatsapp_link"
                        type="url"
                        class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-primary outline-none transition-all placeholder:text-primary/50 focus:ring-2 focus:ring-primary/20"
                        placeholder="https://chat.whatsapp.com/..."
                    />
                </div>

                <div v-if="form.type !== 'masterclass' && form.type !== 'evento'" class="space-y-3">
                    <label class="ml-1 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Metas y Logros del Curso</label>
                    <textarea
                        v-model="form.objectives"
                        rows="4"
                        class="w-full resize-none rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-5 py-4 text-[14px] leading-relaxed text-on-surface shadow-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10"
                        placeholder="Desglosa las habilidades que los estudiantes poseerán al finalizar..."
                    />
                </div>

                <div v-if="form.type !== 'masterclass' && form.type !== 'evento'" class="space-y-3">
                    <label class="ml-1 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant"
                        >Prerrequisitos Académicos</label
                    >
                    <textarea
                        v-model="form.requirements"
                        rows="4"
                        class="w-full resize-none rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-5 py-4 text-[14px] leading-relaxed text-on-surface shadow-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10"
                        placeholder="Conocimientos técnicos, grado académico o software especial requerido..."
                    />
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
