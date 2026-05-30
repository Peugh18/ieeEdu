<script setup lang="ts">
import { Video, Users, Star, ImagePlus } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';

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
            <h3 class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant mb-6">
                1. Modalidad Académica
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Grabado -->
                <button 
                    type="button" 
                    @click="setModality('grabado')"
                    class="flex flex-col items-start p-6 rounded-[2rem] border-2 text-left transition-all duration-300 cursor-pointer"
                    :class="form.type === 'grabado' ? 'border-primary bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'"
                >
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'grabado' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'">
                        <Video class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-[16px] text-on-surface mb-2">Curso Grabado</h4>
                    <p class="text-[13px] text-on-surface-variant font-medium leading-relaxed">Contenido pregrabado. El alumno avanza a su propio ritmo.</p>
                </button>

                <!-- En Vivo -->
                <button 
                    type="button" 
                    @click="setModality('en vivo')"
                    class="flex flex-col items-start p-6 rounded-[2rem] border-2 text-left transition-all duration-300 cursor-pointer"
                    :class="form.type === 'en vivo' ? 'border-primary bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'"
                >
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'en vivo' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'">
                        <Users class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-[16px] text-on-surface mb-2">Curso En Vivo</h4>
                    <p class="text-[13px] text-on-surface-variant font-medium leading-relaxed">Clases sincrónicas programadas.</p>
                </button>

                <!-- Masterclass -->
                <button 
                    type="button" 
                    @click="setModality('masterclass')"
                    class="flex flex-col items-start p-6 rounded-[2rem] border-2 text-left transition-all duration-300 cursor-pointer"
                    :class="form.type === 'masterclass' ? 'border-primary bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-primary/40 hover:bg-surface-container-lowest'"
                >
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'masterclass' ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-surface-variant'">
                        <Star class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-[16px] text-on-surface mb-2">Masterclass</h4>
                    <p class="text-[13px] text-on-surface-variant font-medium leading-relaxed">Sesión gratuita específica.</p>
                </button>
            </div>
        </section>

        <hr class="border-outline-variant/10" />

        <!-- 2. DATOS COMERCIALES -->
        <section>
            <h3 class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant mb-6">
                2. Identidad y Comercialización
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2 space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Título Oficial del Curso</label>
                    <input v-model="form.title" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Ej. Alta Especialización en Finanzas Públicas" />
                    <p v-if="form.errors.title" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.title }}</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Especialidad</label>
                    <div class="flex gap-2">
                        <select v-model="form.category_id" class="flex-1 rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent appearance-none">
                            <option value="">Seleccione Categoría</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <button type="button" class="rounded-[1.5rem] bg-surface-container-highest px-6 hover:bg-surface-container-high transition-colors font-bold text-primary flex items-center justify-center shadow-sm" @click="emit('openCategoryModal')">
                            +
                        </button>
                    </div>
                    <p v-if="form.errors.category_id" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.category_id }}</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Banner o Portada Gráfica</label>
                    <div class="flex items-center gap-4 bg-surface-container-highest p-2 pr-4 rounded-[1.5rem]">
                        <div v-if="imagePreview" class="h-14 w-20 overflow-hidden rounded-xl border border-outline-variant/10 shadow-sm flex-shrink-0 relative">
                            <img :src="imagePreview" alt="Preview" class="absolute inset-0 w-full h-full object-cover" />
                        </div>
                        <label class="flex-1 cursor-pointer py-3 px-4 rounded-xl hover:bg-surface-container-high transition-colors text-center">
                            <input type="file" accept="image/*" class="hidden" @change="(e) => onPickImage((e.target as HTMLInputElement).files?.[0] ?? null)" />
                            <span class="text-[13px] font-bold text-primary">Elegir Imagen</span>
                            <span class="block text-[11px] text-on-surface-variant font-medium mt-0.5">JPG/PNG/WEBP</span>
                        </label>
                    </div>
                    <p v-if="form.errors.image_file" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.image_file }}</p>
                </div>

                <!-- Fechas -->
                <template v-if="form.type === 'en vivo' || form.type === 'masterclass'">
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Fecha de Lanzamiento</label>
                        <input v-model="form.start_date" type="date" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Hora Programada (Local)</label>
                        <input v-model="form.start_time" type="time" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" />
                    </div>
                </template>
            </div>
        </section>
    </div>
</template>
