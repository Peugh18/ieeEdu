<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseEditorCategory, CourseEditorCourse } from '@/types/course-editor';
import type { InertiaForm } from '@inertiajs/vue3';

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
        <div class="max-w-3xl relative z-10">
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-3 tracking-tight">
                Comercialización y <span class="italic font-light">Ventas</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium mb-12 leading-relaxed">
                Configura los precios del programa, estrategias promocionales y la naturaleza del acceso al curso.
            </p>

            <div class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Inversión Regular (Precio en S/)</label>
                        <input
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant"
                            :class="form.errors.price ? 'ring-2 ring-red-500 bg-red-50/50' : ''"
                            placeholder="Ej. 199.00"
                        />
                        <p v-if="form.errors.price" class="mt-1.5 ml-1 text-sm font-bold text-red-600 font-sans">{{ form.errors.price }}</p>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Modalidad de Impartición</label>
                        <select v-model="form.type" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent appearance-none">
                            <option value="grabado">Curso Grabado (Video)</option>
                            <option value="en vivo">Curso En Vivo (Streaming)</option>
                            <option value="masterclass">Masterclass Gratuita</option>
                            <option value="evento">Evento Presencial/Virtual</option>
                        </select>
                    </div>
                </div>

                <div v-if="isMasterclass" class="rounded-[1.5rem] border border-transparent bg-surface-container-highest p-6 shadow-sm">
                    <label class="flex items-center gap-4 text-[15px] font-semibold text-on-surface cursor-pointer">
                        <input v-model="form.certificate_enabled" type="checkbox" class="w-5 h-5 accent-primary rounded focus:ring-primary" />
                        <span>Habilitar Certificado de Participación</span>
                    </label>
                    <p class="mt-3 text-[14px] text-on-surface-variant font-medium ml-9 leading-relaxed">
                        Permite a los asistentes descargar una constancia válida de su participación gratuita.
                    </p>
                </div>

                <div class="rounded-[2rem] border border-outline-variant/10 bg-white p-8 shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)]">
                    <label class="flex items-center gap-4 text-[15px] font-bold text-on-surface cursor-pointer mb-6">
                        <input v-model="form.discount_enabled" type="checkbox" class="w-5 h-5 accent-primary rounded focus:ring-primary" />
                        <span>Activar Promoción de Descuento</span>
                    </label>

                    <div class="flex flex-col sm:flex-row gap-6 items-center bg-surface-container-lowest p-6 rounded-[1.5rem] border border-outline-variant/10">
                        <div class="w-full sm:w-1/2 space-y-3">
                            <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Porcentaje (%)</label>
                            <input
                                v-model.number="form.discount"
                                :disabled="!form.discount_enabled"
                                type="number"
                                min="0"
                                max="100"
                                class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant disabled:opacity-50 disabled:bg-surface-container-low"
                                placeholder="Ej. 20"
                            />
                        </div>
                        <div class="hidden sm:block h-12 w-px bg-outline-variant/20" />
                        <div class="w-full sm:w-1/2 flex flex-col justify-center px-4">
                            <span class="text-[14px] text-on-surface font-bold font-sans">Inversión Final Recomendada</span>
                            <span class="text-3xl font-serif font-bold text-primary mt-2">S/ {{ form.sale_price }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Estado en Catálogo</label>
                        <select
                            v-model="form.status"
                            class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent appearance-none font-bold"
                            :class="{ 'text-amber-600': form.status === 'BORRADOR', 'text-emerald-700': form.status === 'PUBLICADO' }"
                        >
                            <option value="BORRADOR">Borrador (Privado)</option>
                            <option value="PUBLICADO">Publicado (Visible)</option>
                            <option value="OCULTO">Oculto (Secreto)</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Categoría Principal</label>
                        <select
                            v-model="form.category_id"
                            class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent appearance-none"
                            :class="form.errors.category_id ? 'ring-2 ring-red-500 bg-red-50/50' : ''"
                        >
                            <option value="">Seleccione Categoría</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1.5 ml-1 text-sm font-bold text-red-600 font-sans">{{ form.errors.category_id }}</p>
                    </div>
                </div>

                <div class="space-y-5 pt-6">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Banner o Portada Gráfica (16:9)</label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-8">
                        <div class="relative w-full sm:w-56 aspect-video rounded-3xl overflow-hidden border border-outline-variant/20 shadow-md bg-surface-container-low flex items-center justify-center">
                            <img v-if="courseImagePreviewUrl" :src="courseImagePreviewUrl" class="absolute inset-0 w-full h-full object-cover" alt="" />
                            <img v-else-if="course.image" :src="course.image" class="absolute inset-0 w-full h-full object-cover" alt="" />
                            <div v-else class="text-center p-6">
                                <svg class="mx-auto h-10 w-10 text-surface-variant mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-[13px] font-bold text-on-surface-variant font-sans">Subir Imagen</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-4 w-full">
                            <input
                                type="file"
                                accept="image/*"
                                class="w-full text-[14px] font-sans file:mr-5 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-[13px] file:font-bold file:bg-primary file:text-white hover:file:opacity-90 transition-all cursor-pointer"
                                @change="(e) => { form.image_file = (e.target as HTMLInputElement).files?.[0] ?? null; }"
                            />
                            <p class="text-[13px] text-on-surface-variant font-medium leading-relaxed max-w-md">Formatos: JPG, PNG, WEBP. Resolución recomendada: 1280x720px.</p>
                            <p v-if="form.errors.image_file" class="text-[14px] font-bold text-red-600 font-sans mt-2">{{ form.errors.image_file }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
