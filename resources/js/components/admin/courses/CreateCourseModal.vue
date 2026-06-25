<script setup lang="ts">
import { useCreateCourse } from '@/composables/admin/courses/useCreateCourse';
import { ArrowRight, ChevronLeft, Loader2, X } from 'lucide-vue-next';
import { watch } from 'vue';
import CreateCategoryMiniModal from './CreateCategoryMiniModal.vue';
import CreateCourseStepBasic from './CreateCourseStepBasic.vue';
import CreateCourseStepInstructor from './CreateCourseStepInstructor.vue';
import CreateCourseStepPricing from './CreateCourseStepPricing.vue';

const props = defineProps<{
    open: boolean;
    categories: Array<{ id: number; name: string }>;
    duplicateData?: Record<string, any> | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'categoryCreated', category: { id: number; name: string }): void;
}>();

const creator = useCreateCourse(props.categories, emit);

const {
    form,
    step,
    totalSteps,
    nextStep,
    prevStep,
    showCategoryModal,
    imagePreview,
    instructorImagePreview,
    setModality,
    canSubmit,
    onPickImage,
    onPickInstructorImage,
    submit,
    onCategoryCreated,
    initForm,
} = creator;

watch(
    () => props.open,
    (isOpen) => {
        initForm(isOpen, props.duplicateData);
    },
    { immediate: true },
);
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-surface-tint/60 p-4 backdrop-blur-md sm:p-6">
            <!-- WIZARD CONTAINER -->
            <div
                class="relative flex max-h-[95vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2.5rem] border border-outline-variant/10 bg-white shadow-2xl shadow-surface-tint/10"
            >
                <!-- BACKGROUND BLUR -->
                <div
                    class="pointer-events-none absolute right-0 top-0 -mr-20 -mt-20 h-64 w-64 rounded-full bg-gradient-to-br from-primary/10 to-transparent blur-3xl"
                ></div>

                <!-- HEADER VISUAL AND STEPPER -->
                <div
                    class="relative z-10 flex shrink-0 flex-col gap-6 border-b border-outline-variant/10 bg-white/70 px-8 pb-6 pt-8 backdrop-blur-md sm:px-10"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="mb-2 font-serif text-3xl font-bold tracking-tight text-on-surface">
                                Crear <span class="font-light italic">Programa</span>
                            </h2>
                            <p class="text-[14px] font-medium text-on-surface-variant">
                                Define los datos principales para abrir un nuevo espacio en el campus.
                            </p>
                        </div>
                        <button
                            class="rounded-full border border-outline-variant/10 bg-surface-container-lowest p-2.5 shadow-sm transition-colors hover:bg-surface-container-low"
                            @click="emit('close')"
                            aria-label="Cerrar"
                        >
                            <X class="h-5 w-5 text-on-surface-variant" />
                        </button>
                    </div>

                    <!-- STEP INDICATOR -->
                    <div class="flex items-center gap-3">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface-container">
                            <div
                                class="h-full rounded-full bg-primary transition-all duration-500 ease-out"
                                :style="{ width: `${(step / totalSteps) * 100}%` }"
                            ></div>
                        </div>
                        <span class="whitespace-nowrap text-[12px] font-bold uppercase tracking-widest text-primary"
                            >Paso {{ step }} de {{ totalSteps }}</span
                        >
                    </div>
                </div>

                <!-- SCROLLABLE CONTENT -->
                <div class="custom-scrollbar relative z-10 flex-1 overflow-y-auto overflow-x-hidden bg-white p-8 sm:p-10">
                    <!-- ========================= -->
                    <!-- STEP 1: MODALIDAD & DATOS COMERCIALES -->
                    <!-- ========================= -->
                    <transition name="slide">
                        <div v-show="step === 1" class="space-y-12">
                            <CreateCourseStepBasic
                                :form="form"
                                :categories="categories"
                                :image-preview="imagePreview"
                                :set-modality="setModality"
                                :on-pick-image="onPickImage"
                                @open-category-modal="showCategoryModal = true"
                            />
                            <CreateCourseStepPricing :form="form" />
                        </div>
                    </transition>

                    <!-- ========================= -->
                    <!-- STEP 2: DOCENTE -->
                    <!-- ========================= -->
                    <transition name="slide">
                        <div v-show="step === 2" class="space-y-12">
                            <CreateCourseStepInstructor
                                :form="form"
                                :instructor-image-preview="instructorImagePreview"
                                :on-pick-instructor-image="onPickInstructorImage"
                            />
                        </div>
                    </transition>

                    <!-- ========================= -->
                    <!-- STEP 3: ACADÉMICO -->
                    <!-- ========================= -->
                    <transition name="slide">
                        <div v-show="step === 3" class="space-y-12">
                            <section class="pb-8">
                                <h3 class="mb-6 flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant">
                                    4. Detalles Académicos
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-8 rounded-[2.5rem] border border-transparent bg-surface-container-highest p-8 sm:p-10 md:grid-cols-2"
                                >
                                    <div class="space-y-3 md:col-span-2">
                                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Sinopsis</label>
                                        <textarea
                                            v-model="form.description"
                                            rows="3"
                                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 text-[14px] leading-relaxed outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                                            placeholder="Descripción atractiva..."
                                        ></textarea>
                                        <p v-if="form.errors.description" class="ml-1 mt-1 text-[13px] font-bold text-red-600">
                                            {{ form.errors.description }}
                                        </p>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface"
                                            >Metas (Un objetivo por línea)</label
                                        >
                                        <textarea
                                            v-model="form.objectives"
                                            rows="5"
                                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 text-[14px] leading-relaxed outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                                            placeholder="Identificar riesgos financieros..."
                                        ></textarea>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Requisitos Previos</label>
                                        <textarea
                                            v-model="form.requirements"
                                            rows="5"
                                            class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-5 text-[14px] leading-relaxed outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                                            placeholder="Conocimientos de Excel..."
                                        ></textarea>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </transition>
                </div>

                <!-- FOOTER ACTIONS -->
                <div
                    class="z-20 flex shrink-0 flex-col items-center justify-between gap-6 border-t border-outline-variant/10 bg-surface-container-lowest px-8 py-6 sm:px-10 md:flex-row"
                >
                    <div class="hidden flex-col md:flex">
                        <span class="mb-0.5 text-[14px] font-bold text-on-surface">
                            {{ step === 1 ? 'Configuración Comercial' : step === 2 ? 'Perfil Docente' : 'Rigores Académicos' }}
                        </span>
                        <span class="text-[11px] font-medium tracking-wide text-on-surface-variant">
                            {{
                                step === 1
                                    ? 'Paso inicial para crear tu espacio.'
                                    : step === 2
                                      ? 'Humaniza tu propuesta educativa.'
                                      : 'Listos para crear el borrador seguro.'
                            }}
                        </span>
                    </div>

                    <div class="flex w-full gap-4 md:w-auto">
                        <!-- CANCEL BUTTON (Only Step 1) -->
                        <button
                            v-if="step === 1"
                            type="button"
                            class="flex-1 rounded-full border border-outline-variant/10 bg-surface-container-lowest px-8 py-3.5 text-[13px] font-bold text-on-surface shadow-sm transition-all hover:bg-surface-container-low md:flex-none"
                            @click="emit('close')"
                        >
                            Cancelar
                        </button>

                        <!-- BACK BUTTON (Steps 2, 3) -->
                        <button
                            v-if="step > 1"
                            type="button"
                            class="flex flex-1 items-center justify-center gap-2 rounded-full border border-outline-variant/10 bg-surface-container-lowest px-8 py-3.5 text-[13px] font-bold text-on-surface shadow-sm transition-all hover:bg-surface-container-low md:flex-none"
                            @click="prevStep"
                        >
                            <ChevronLeft class="h-4 w-4" />
                            Atrás
                        </button>

                        <!-- NEXT BUTTON (Steps 1, 2) -->
                        <button
                            v-if="step < totalSteps"
                            type="button"
                            class="flex-1 rounded-full bg-primary px-10 py-3.5 font-sans text-[13px] font-bold text-white shadow-xl shadow-primary/20 transition-all hover:bg-[#4b4b22] md:flex-none"
                            @click="nextStep"
                        >
                            Continuar
                        </button>

                        <!-- SUBMIT BUTTON (Step 3) -->
                        <button
                            v-if="step === totalSteps"
                            type="submit"
                            class="flex flex-1 scale-100 transform items-center justify-center gap-3 rounded-full bg-gradient-to-br from-primary to-[#6d6d35] px-10 py-3.5 font-sans text-[13px] font-bold text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.01] hover:from-[#4b4b22] hover:to-[#5c5c2a] active:scale-[0.98] disabled:opacity-60 disabled:shadow-none md:flex-none"
                            :disabled="!canSubmit || form.processing"
                            @click="submit"
                        >
                            <Loader2 v-if="form.processing" class="h-5 w-5 animate-spin" />
                            <span>{{ form.processing ? 'Procesando...' : 'Crear Espacio Académico' }}</span>
                            <ArrowRight v-if="!form.processing" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <CreateCategoryMiniModal :open="showCategoryModal" @close="showCategoryModal = false" @created="onCategoryCreated" />
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
    margin-block: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--md-sys-color-outline-variant, #c9c7b8);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--md-sys-color-outline, #57572a);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-enter-from {
    opacity: 0;
    transform: translateY(10px);
}
.slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
    position: absolute;
}
</style>
