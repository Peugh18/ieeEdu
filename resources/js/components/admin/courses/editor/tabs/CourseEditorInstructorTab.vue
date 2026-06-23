<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseEditorCourse } from '@/types/course-editor';
import type { InertiaForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

defineProps<{
    show: boolean;
    form: InertiaForm<Record<string, unknown>>;
    course: CourseEditorCourse;
    instructorPreviewUrl: string | null;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 mb-10 max-w-3xl">
            <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-on-surface">Equipo <span class="font-light italic">Docente</span></h2>
            <p class="font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                Agrega credibilidad mostrando la experiencia directa de quien lidera este programa.
            </p>
        </div>

        <div class="space-y-10">
            <div
                class="relative flex flex-col items-start gap-8 overflow-hidden rounded-[2rem] border border-transparent bg-surface-container-highest p-8 shadow-sm sm:flex-row"
            >
                <div
                    class="pointer-events-none absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-gradient-to-bl from-primary/10 to-transparent blur-2xl"
                />

                <div class="relative z-10 mt-2 flex w-full shrink-0 flex-col items-center gap-5 sm:w-auto">
                    <div
                        class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-surface-container-low shadow-xl sm:h-36 sm:w-36"
                    >
                        <img v-if="instructorPreviewUrl" :src="instructorPreviewUrl" class="h-full w-full object-cover" alt="" />
                        <img v-else-if="course.instructor_image" :src="course.instructor_image" class="h-full w-full object-cover" alt="" />
                        <svg v-else class="h-14 w-14 text-surface-variant/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </div>
                    <label class="w-full cursor-pointer text-center">
                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="
                                (e) => {
                                    form.instructor_image_file = (e.target as HTMLInputElement).files?.[0] ?? null;
                                }
                            "
                        />
                        <span
                            class="inline-block w-full rounded-full border border-primary/10 bg-white px-5 py-2.5 text-[12px] font-bold uppercase tracking-wider text-primary shadow-sm transition-all hover:bg-primary/5"
                            >Cambiar Retrato</span
                        >
                    </label>
                </div>
                <div class="relative z-10 w-full flex-1 space-y-6">
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Nombre del Profesional</label>
                        <input
                            v-model="form.instructor_name"
                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                            placeholder="Ej. Dr. Javier Montenegro"
                        />
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Titulación Oficial</label>
                        <input
                            v-model="form.instructor_title"
                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                            placeholder="Ej. Ingeniero de Software Principal"
                        />
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Biografía Breve</label>
                        <textarea
                            v-model="form.instructor_bio"
                            rows="4"
                            class="w-full resize-none rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] leading-relaxed text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                            placeholder="Detalla su experiencia y reconocimientos..."
                        />
                    </div>
                </div>
            </div>

            <hr class="my-4 border-outline-variant/10" />
            <div class="space-y-5">
                <h3 class="flex items-center gap-3 font-sans text-[18px] font-bold text-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Certificación Oficial
                </h3>
                <div
                    class="flex flex-col items-center justify-between gap-6 rounded-[2rem] border border-transparent bg-primary/5 p-8 shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)] md:flex-row"
                >
                    <p class="font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant md:w-2/3">
                        Personaliza el diseño del diploma que recibirán los alumnos al completar este curso exitosamente.
                    </p>
                    <Link
                        :href="route('admin.courses.certificate-template.edit', { course: course.id })"
                        class="flex items-center justify-center gap-3 rounded-full bg-gradient-to-r from-primary to-[#6b6b34] px-6 py-4 font-sans text-[14px] font-bold text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98] md:w-1/3"
                    >
                        <svg class="h-5 w-5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Diseñar Diploma
                    </Link>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
