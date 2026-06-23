<script setup lang="ts">
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';
import type { InertiaForm } from '@inertiajs/vue3';
import { Star, Users, Video } from 'lucide-vue-next';

defineProps<{
    form: InertiaForm<CreateCourseForm>;
    categories: Array<{ id: number; name: string }>;
    imagePreview: string | null;
    setModality: (t: 'grabado' | 'en vivo' | 'masterclass') => void;
    onPickImage: (file: File | null) => void;
}>();

const emit = defineEmits<{
    (e: 'openCategoryModal'): void;
}>();
</script>

<template>
    <div class="space-y-12">
        <!-- 1. MODALITY SELECTOR -->
        <section>
            <h3 class="mb-6 flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant">
                1. Modalidad Académica
            </h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <!-- Grabado -->
                <button
                    type="button"
                    @click="setModality('grabado')"
                    class="flex cursor-pointer flex-col items-start rounded-[2rem] border-2 p-6 text-left transition-all duration-300"
                    :class="
                        form.type === 'grabado'
                            ? 'scale-[1.02] border-primary bg-surface-container-highest shadow-md'
                            : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'
                    "
                >
                    <div
                        class="mb-5 flex h-12 w-12 items-center justify-center rounded-full text-white transition-colors"
                        :class="form.type === 'grabado' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'"
                    >
                        <Video class="h-5 w-5" />
                    </div>
                    <h4 class="mb-2 text-[16px] font-bold text-on-surface">Curso Grabado</h4>
                    <p class="text-[13px] font-medium leading-relaxed text-on-surface-variant">
                        Contenido pregrabado. El alumno avanza a su propio ritmo.
                    </p>
                </button>

                <!-- En Vivo -->
                <button
                    type="button"
                    @click="setModality('en vivo')"
                    class="flex cursor-pointer flex-col items-start rounded-[2rem] border-2 p-6 text-left transition-all duration-300"
                    :class="
                        form.type === 'en vivo'
                            ? 'scale-[1.02] border-primary bg-surface-container-highest shadow-md'
                            : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'
                    "
                >
                    <div
                        class="mb-5 flex h-12 w-12 items-center justify-center rounded-full text-white transition-colors"
                        :class="form.type === 'en vivo' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'"
                    >
                        <Users class="h-5 w-5" />
                    </div>
                    <h4 class="mb-2 text-[16px] font-bold text-on-surface">Curso En Vivo</h4>
                    <p class="text-[13px] font-medium leading-relaxed text-on-surface-variant">Clases sincrónicas programadas.</p>
                </button>

                <!-- Masterclass -->
                <button
                    type="button"
                    @click="setModality('masterclass')"
                    class="flex cursor-pointer flex-col items-start rounded-[2rem] border-2 p-6 text-left transition-all duration-300"
                    :class="
                        form.type === 'masterclass'
                            ? 'scale-[1.02] border-primary bg-surface-container-highest shadow-md'
                            : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'
                    "
                >
                    <div
                        class="mb-5 flex h-12 w-12 items-center justify-center rounded-full text-white transition-colors"
                        :class="form.type === 'masterclass' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'"
                    >
                        <Star class="h-5 w-5" />
                    </div>
                    <h4 class="mb-2 text-[16px] font-bold text-on-surface">Masterclass</h4>
                    <p class="text-[13px] font-medium leading-relaxed text-on-surface-variant">Sesión gratuita específica.</p>
                </button>
            </div>
        </section>

        <hr class="border-outline-variant/10" />

        <!-- 2. DATOS COMERCIALES -->
        <section>
            <h3 class="mb-6 flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant">
                2. Identidad y Comercialización
            </h3>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-3 md:col-span-2">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Título Oficial del Curso</label>
                    <input
                        v-model="form.title"
                        class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Ej. Alta Especialización en Finanzas Públicas"
                    />
                    <p v-if="form.errors.title" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.title }}</p>
                </div>

                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Especialidad</label>
                    <div class="flex gap-2">
                        <select
                            v-model="form.category_id"
                            class="flex-1 appearance-none rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        >
                            <option value="">Seleccione Categoría</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <button
                            type="button"
                            class="flex items-center justify-center rounded-[1.5rem] bg-surface-container-highest px-6 font-bold text-primary shadow-sm transition-colors hover:bg-surface-container-high"
                            @click="emit('openCategoryModal')"
                        >
                            +
                        </button>
                    </div>
                    <p v-if="form.errors.category_id" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.category_id }}</p>
                </div>

                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Banner o Portada Gráfica</label>
                    <div class="flex items-center gap-4 rounded-[1.5rem] bg-surface-container-highest p-2 pr-4">
                        <div
                            v-if="imagePreview"
                            class="relative h-14 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-outline-variant/10 shadow-sm"
                        >
                            <img :src="imagePreview" alt="Preview" class="absolute inset-0 h-full w-full object-cover" />
                        </div>
                        <label class="flex-1 cursor-pointer rounded-xl px-4 py-3 text-center transition-colors hover:bg-surface-container-high">
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="(e) => onPickImage((e.target as HTMLInputElement).files?.[0] ?? null)"
                            />
                            <span class="text-[13px] font-bold text-primary">Elegir Imagen</span>
                            <span class="mt-0.5 block text-[11px] font-medium text-on-surface-variant">JPG/PNG/WEBP</span>
                        </label>
                    </div>
                    <p v-if="form.errors.image_file" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.image_file }}</p>
                </div>

                <!-- Fechas -->
                <template v-if="form.type === 'en vivo' || form.type === 'masterclass'">
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Fecha de Lanzamiento</label>
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        />
                    </div>
                    <div class="space-y-3">
                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Hora Programada (Local)</label>
                        <input
                            v-model="form.start_time"
                            type="time"
                            class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        />
                    </div>
                </template>
            </div>
        </section>
    </div>
</template>
