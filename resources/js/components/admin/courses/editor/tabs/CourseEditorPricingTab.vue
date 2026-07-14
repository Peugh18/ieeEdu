<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseEditorCategory, CourseEditorCourse } from '@/types/course-editor';
import type { InertiaForm } from '@inertiajs/vue3';
import AppSelect from '@/components/ui/AppSelect.vue';

defineProps<{
    show: boolean;
    form: InertiaForm<Record<string, unknown>>;
    categories: CourseEditorCategory[];
    course: CourseEditorCourse;
    isMasterclass: boolean;
    courseImagePreviewUrl: string | null;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 max-w-3xl">
            <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-on-surface">
                Comercialización y <span class="font-light italic">Ventas</span>
            </h2>
            <p class="mb-12 font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                Configura los precios del programa, estrategias promocionales y la naturaleza del acceso al curso.
            </p>

            <div class="space-y-10">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Inversión Regular (Precio en S/)</label>
                        <input
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                            :class="form.errors.price ? 'bg-red-50/50 ring-2 ring-red-500' : ''"
                            placeholder="Ej. 199.00"
                        />
                        <p v-if="form.errors.price" class="ml-1 mt-1.5 font-sans text-sm font-bold text-red-600">{{ form.errors.price }}</p>
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Modalidad de Impartición</label>
                        <AppSelect
                            v-model="form.type"
                            :options="[
                                { value: 'grabado', label: 'Curso Grabado (Video)' },
                                { value: 'en vivo', label: 'Curso En Vivo (Streaming)' },
                                { value: 'masterclass', label: 'Masterclass Gratuita' },
                                { value: 'evento', label: 'Evento Presencial/Virtual' }
                            ]"
                            class="bg-surface-container-highest border-0 shadow-sm text-[15px]"
                        />
                    </div>
                </div>

                <div v-if="isMasterclass" class="rounded-[1.5rem] border border-transparent bg-surface-container-highest p-6 shadow-sm">
                    <label class="flex cursor-pointer items-center gap-4 text-[15px] font-semibold text-on-surface">
                        <input v-model="form.certificate_enabled" type="checkbox" class="h-5 w-5 rounded accent-primary focus:ring-primary" />
                        <span>Habilitar Certificado de Participación</span>
                    </label>
                    <p class="ml-9 mt-3 text-[14px] font-medium leading-relaxed text-on-surface-variant">
                        Permite a los asistentes descargar una constancia válida de su participación gratuita.
                    </p>
                </div>

                <div class="rounded-[2rem] border border-outline-variant/10 bg-white p-8 shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)]">
                    <label class="mb-6 flex cursor-pointer items-center gap-4 text-[15px] font-bold text-on-surface">
                        <input v-model="form.discount_enabled" type="checkbox" class="h-5 w-5 rounded accent-primary focus:ring-primary" />
                        <span>Activar Promoción de Descuento</span>
                    </label>

                    <div
                        class="flex flex-col items-center gap-6 rounded-[1.5rem] border border-outline-variant/10 bg-surface-container-lowest p-6 sm:flex-row"
                    >
                        <div class="w-full space-y-3 sm:w-1/2">
                            <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Porcentaje (%)</label>
                            <input
                                v-model.number="form.discount"
                                :disabled="!form.discount_enabled"
                                type="number"
                                min="0"
                                max="100"
                                class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20 disabled:bg-surface-container-low disabled:opacity-50"
                                placeholder="Ej. 20"
                            />
                        </div>
                        <div class="hidden h-12 w-px bg-outline-variant/20 sm:block" />
                        <div class="flex w-full flex-col justify-center px-4 sm:w-1/2">
                            <span class="font-sans text-[14px] font-bold text-on-surface">Inversión Final Recomendada</span>
                            <span class="mt-2 font-serif text-3xl font-bold text-primary">S/ {{ form.sale_price }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 pt-4 md:grid-cols-2">
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Estado en Catálogo</label>
                        <div
                            class="w-full rounded-[1.5rem] border border-outline-variant/10 bg-surface-container-highest px-6 py-5 font-sans text-[15px] font-bold"
                            :class="{
                                'text-amber-600': form.status === 'BORRADOR',
                                'text-emerald-700': form.status === 'PUBLICADO',
                                'text-slate-500': form.status === 'OCULTO',
                            }"
                        >
                            {{ form.status === 'PUBLICADO' ? 'Publicado (Visible)' : form.status === 'OCULTO' ? 'Oculto' : 'Borrador (Privado)' }}
                        </div>
                        <p class="ml-1 text-[12px] text-on-surface-variant">
                            Usa los botones <strong>Publicar Oficial</strong> u <strong>Ocultar</strong> en la cabecera del editor.
                        </p>
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Categoría Principal</label>
                        <AppSelect
                            v-model="form.category_id"
                            :options="[
                                { value: '', label: 'Seleccione Categoría' },
                                ...categories.map(c => ({ value: c.id, label: c.name }))
                            ]"
                            class="bg-surface-container-highest border-0 shadow-sm text-[15px]"
                            :class="form.errors.category_id ? 'bg-red-50/50 ring-2 ring-red-500' : ''"
                        />
                        <p v-if="form.errors.category_id" class="ml-1 mt-1.5 font-sans text-sm font-bold text-red-600">
                            {{ form.errors.category_id }}
                        </p>
                    </div>
                </div>

                <div class="space-y-5 pt-6">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Banner o Portada Gráfica (16:9)</label>
                    <div class="flex flex-col items-start gap-8 sm:flex-row sm:items-center">
                        <div
                            class="relative flex aspect-video w-full items-center justify-center overflow-hidden rounded-3xl border border-outline-variant/20 bg-surface-container-low shadow-md sm:w-56"
                        >
                            <img
                                v-if="courseImagePreviewUrl"
                                :src="courseImagePreviewUrl"
                                class="absolute inset-0 h-full w-full object-cover"
                                alt=""
                            />
                            <img v-else-if="course.image" :src="course.image" class="absolute inset-0 h-full w-full object-cover" alt="" />
                            <div v-else class="p-6 text-center">
                                <svg class="mx-auto mb-3 h-10 w-10 text-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <span class="font-sans text-[13px] font-bold text-on-surface-variant">Subir Imagen</span>
                            </div>
                        </div>
                        <div class="w-full flex-1 space-y-4">
                            <input
                                type="file"
                                accept="image/*"
                                class="w-full cursor-pointer font-sans text-[14px] transition-all file:mr-5 file:rounded-full file:border-0 file:bg-primary file:px-6 file:py-3 file:text-[13px] file:font-bold file:text-white hover:file:opacity-90"
                                @change="
                                    (e) => {
                                        form.image_file = (e.target as HTMLInputElement).files?.[0] ?? null;
                                    }
                                "
                            />
                            <p class="max-w-md text-[13px] font-medium leading-relaxed text-on-surface-variant">
                                Formatos: JPG, PNG, WEBP. Resolución recomendada: 1280x720px.
                            </p>
                            <p v-if="form.errors.image_file" class="mt-2 font-sans text-[14px] font-bold text-red-600">
                                {{ form.errors.image_file }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
