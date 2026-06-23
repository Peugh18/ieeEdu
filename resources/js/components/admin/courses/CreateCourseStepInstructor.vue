<script setup lang="ts">
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';
import type { InertiaForm } from '@inertiajs/vue3';
import { ImagePlus } from 'lucide-vue-next';

defineProps<{
    form: InertiaForm<CreateCourseForm>;
    instructorImagePreview: string | null;
    onPickInstructorImage: (file: File | null) => void;
}>();
</script>

<template>
    <div class="space-y-12">
        <section>
            <h3 class="mb-6 flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant">
                3. Docente o Especialista
            </h3>
            <div
                class="relative grid grid-cols-1 gap-8 overflow-hidden rounded-[2.5rem] border border-transparent bg-surface-container-highest p-8 sm:p-10 md:grid-cols-2"
            >
                <!-- Decor BG -->
                <div
                    class="pointer-events-none absolute right-0 top-0 -mr-16 -mt-16 h-40 w-40 rounded-full bg-gradient-to-bl from-white/60 to-transparent blur-3xl"
                ></div>

                <div
                    class="relative z-10 flex flex-col items-center justify-center gap-5 rounded-[2rem] border-2 border-dashed border-outline-variant/20 bg-surface-container-lowest p-8 md:row-span-2"
                >
                    <div
                        class="relative flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-surface-container-low text-surface-variant shadow-xl sm:h-32 sm:w-32"
                    >
                        <img v-if="instructorImagePreview" :src="instructorImagePreview" class="absolute inset-0 h-full w-full object-cover" />
                        <ImagePlus v-else class="h-10 w-10" />
                    </div>
                    <div class="w-full">
                        <label
                            class="flex w-full cursor-pointer items-center justify-center rounded-full border border-outline-variant/20 bg-white px-6 py-3.5 text-[12px] font-bold uppercase tracking-wider text-primary shadow-sm transition-all hover:border-primary/20 hover:bg-primary/5"
                        >
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="(e) => onPickInstructorImage((e.target as HTMLInputElement).files?.[0] ?? null)"
                            />
                            Subir Foto
                        </label>
                    </div>
                </div>

                <div class="relative z-10 space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Nombre Evidenciado</label>
                    <input
                        v-model="form.instructor_name"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Mgtr. Carlos Fernández"
                    />
                </div>

                <div class="relative z-10 space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Titulación Oficial</label>
                    <input
                        v-model="form.instructor_title"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Gerente de Finanzas"
                    />
                </div>

                <div class="relative z-10 mt-2 space-y-3 md:col-span-2">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Biografía Breve</label>
                    <textarea
                        v-model="form.instructor_bio"
                        rows="3"
                        class="w-full resize-none rounded-[1.5rem] border-transparent bg-white px-6 py-5 font-sans text-[15px] leading-relaxed text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Biografía profesional..."
                    ></textarea>
                </div>
            </div>
        </section>
    </div>
</template>
