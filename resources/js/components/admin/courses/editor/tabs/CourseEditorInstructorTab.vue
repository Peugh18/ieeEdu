<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseEditorCourse } from '@/types/course-editor';
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';

defineProps<{
    show: boolean;
    form: InertiaForm<Record<string, unknown>>;
    course: CourseEditorCourse;
    instructorPreviewUrl: string | null;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="max-w-3xl relative z-10 mb-10">
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-3 tracking-tight">
                Equipo <span class="italic font-light">Docente</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium leading-relaxed">
                Agrega credibilidad mostrando la experiencia directa de quien lidera este programa.
            </p>
        </div>

        <div class="space-y-10">
            <div class="flex flex-col sm:flex-row gap-8 items-start bg-surface-container-highest p-8 rounded-[2rem] border border-transparent shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-primary/10 to-transparent rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none" />

                <div class="w-full sm:w-auto flex flex-col items-center gap-5 relative z-10 shrink-0 mt-2">
                    <div class="relative w-32 h-32 sm:w-36 sm:h-36 rounded-full overflow-hidden border-4 border-white shadow-xl bg-surface-container-low flex items-center justify-center">
                        <img v-if="instructorPreviewUrl" :src="instructorPreviewUrl" class="w-full h-full object-cover" alt="" />
                        <img v-else-if="course.instructor_image" :src="course.instructor_image" class="w-full h-full object-cover" alt="" />
                        <svg v-else class="w-14 h-14 text-surface-variant/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <label class="cursor-pointer w-full text-center">
                        <input type="file" accept="image/*" class="hidden" @change="(e) => { form.instructor_image_file = (e.target as HTMLInputElement).files?.[0] ?? null; }" />
                        <span class="inline-block w-full px-5 py-2.5 bg-white rounded-full text-[12px] font-bold uppercase tracking-wider text-primary border border-primary/10 hover:bg-primary/5 shadow-sm transition-all">Cambiar Retrato</span>
                    </label>
                </div>
                <div class="flex-1 space-y-6 relative z-10 w-full">
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Nombre del Profesional</label>
                        <input v-model="form.instructor_name" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Ej. Dr. Javier Montenegro" />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Titulación Oficial</label>
                        <input v-model="form.instructor_title" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Ej. Ingeniero de Software Principal" />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Biografía Breve</label>
                        <textarea v-model="form.instructor_bio" rows="4" class="w-full resize-none rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant leading-relaxed" placeholder="Detalla su experiencia y reconocimientos..." />
                    </div>
                </div>
            </div>

            <hr class="border-outline-variant/10 my-4" />
            <div class="space-y-5">
                <h3 class="font-bold text-[18px] text-primary font-sans flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Certificación Oficial
                </h3>
                <div class="p-8 rounded-[2rem] bg-primary/5 border border-transparent shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)] flex flex-col md:flex-row items-center justify-between gap-6">
                    <p class="text-[15px] text-on-surface-variant font-medium leading-relaxed font-sans md:w-2/3">
                        Personaliza el diseño del diploma que recibirán los alumnos al completar este curso exitosamente.
                    </p>
                    <Link
                        :href="route('admin.courses.certificate-template.edit', { course: course.id })"
                        class="flex items-center justify-center gap-3 md:w-1/3 py-4 px-6 rounded-full bg-gradient-to-r from-primary to-[#6b6b34] text-white text-[14px] font-bold shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all font-sans"
                    >
                        <svg class="w-5 h-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Diseñar Diploma
                    </Link>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
