<script setup lang="ts">
import { ImagePlus } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';

defineProps<{
    form: InertiaForm<CreateCourseForm>;
    instructorImagePreview: string | null;
    onPickInstructorImage: (file: File | null) => void;
}>();
</script>

<template>
    <div class="space-y-12">
        <section>
            <h3 class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant mb-6">
                3. Docente o Especialista
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 sm:p-10 rounded-[2.5rem] bg-surface-container-highest border border-transparent relative overflow-hidden">
                <!-- Decor BG -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-white/60 to-transparent rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                <div class="md:row-span-2 flex flex-col items-center justify-center gap-5 p-8 border-2 border-dashed border-outline-variant/20 rounded-[2rem] bg-surface-container-lowest relative z-10">
                    <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-white shadow-xl bg-surface-container-low flex items-center justify-center text-surface-variant">
                        <img v-if="instructorImagePreview" :src="instructorImagePreview" class="absolute inset-0 w-full h-full object-cover" />
                        <ImagePlus v-else class="w-10 h-10" />
                    </div>
                    <div class="w-full">
                        <label class="cursor-pointer flex items-center justify-center w-full px-6 py-3.5 bg-white rounded-full text-[12px] font-bold uppercase tracking-wider text-primary border border-outline-variant/20 hover:bg-primary/5 hover:border-primary/20 shadow-sm transition-all">
                            <input type="file" accept="image/*" class="hidden" @change="(e) => onPickInstructorImage((e.target as HTMLInputElement).files?.[0] ?? null)" />
                            Subir Foto
                        </label>
                    </div>
                </div>

                <div class="space-y-3 relative z-10">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Nombre Evidenciado</label>
                    <input v-model="form.instructor_name" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Mgtr. Carlos Fernández" />
                </div>

                <div class="space-y-3 relative z-10">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Titulación Oficial</label>
                    <input v-model="form.instructor_title" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Gerente de Finanzas" />
                </div>

                <div class="md:col-span-2 space-y-3 relative z-10 mt-2">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Biografía Breve</label>
                    <textarea v-model="form.instructor_bio" rows="3" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent resize-none leading-relaxed placeholder:text-outline-variant" placeholder="Biografía profesional..."></textarea>
                </div>
            </div>
        </section>
    </div>
</template>
