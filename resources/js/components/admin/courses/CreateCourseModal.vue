<script setup lang="ts">
import { watch } from 'vue';
import { X, ChevronLeft, Loader2, ArrowRight } from 'lucide-vue-next';
import CreateCategoryMiniModal from './CreateCategoryMiniModal.vue';
import CreateCourseStepBasic from './CreateCourseStepBasic.vue';
import CreateCourseStepPricing from './CreateCourseStepPricing.vue';
import CreateCourseStepInstructor from './CreateCourseStepInstructor.vue';
import { useCreateCourse } from '@/composables/admin/courses/useCreateCourse';

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
    { immediate: true }
);
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-surface-tint/60 backdrop-blur-md">
            <!-- WIZARD CONTAINER -->
            <div class="w-full max-w-4xl max-h-[95vh] flex flex-col rounded-[2.5rem] bg-white shadow-2xl shadow-surface-tint/10 border border-outline-variant/10 overflow-hidden relative">
                
                <!-- BACKGROUND BLUR -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/10 to-transparent rounded-full blur-3xl -mt-20 -mr-20 pointer-events-none"></div>

                <!-- HEADER VISUAL AND STEPPER -->
                <div class="px-8 sm:px-10 pt-8 pb-6 border-b border-outline-variant/10 flex flex-col gap-6 shrink-0 bg-white/70 backdrop-blur-md relative z-10">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="font-serif text-3xl text-on-surface font-bold tracking-tight mb-2">Crear <span class="italic font-light">Programa</span></h2>
                            <p class="text-[14px] text-on-surface-variant font-medium">Define los datos principales para abrir un nuevo espacio en el campus.</p>
                        </div>
                        <button class="rounded-full p-2.5 bg-surface-container-lowest border border-outline-variant/10 shadow-sm hover:bg-surface-container-low transition-colors" @click="emit('close')" aria-label="Cerrar">
                            <X class="w-5 h-5 text-on-surface-variant" />
                        </button>
                    </div>

                    <!-- STEP INDICATOR -->
                    <div class="flex items-center gap-3">
                        <div class="h-2 flex-1 rounded-full overflow-hidden bg-surface-container">
                            <div class="h-full bg-primary rounded-full transition-all duration-500 ease-out" :style="{ width: `${(step / totalSteps) * 100}%` }"></div>
                        </div>
                        <span class="text-[12px] font-bold text-primary uppercase tracking-widest whitespace-nowrap">Paso {{ step }} de {{ totalSteps }}</span>
                    </div>
                </div>

                <!-- SCROLLABLE CONTENT -->
                <div class="p-8 sm:p-10 overflow-x-hidden overflow-y-auto flex-1 custom-scrollbar relative z-10 bg-white">
                    
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
                            <CreateCourseStepPricing
                                :form="form"
                            />
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
                                <h3 class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-widest text-on-surface-variant mb-6">
                                    4. Detalles Académicos
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-surface-container-highest p-8 sm:p-10 rounded-[2.5rem] border border-transparent">
                                    
                                    <div class="md:col-span-2 space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Sinopsis</label>
                                        <textarea v-model="form.description" rows="3" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Descripción atractiva..."></textarea>
                                        <p v-if="form.errors.description" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.description }}</p>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Metas (Un objetivo por línea)</label>
                                        <textarea v-model="form.objectives" rows="5" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Identificar riesgos financieros..."></textarea>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Requisitos Previos</label>
                                        <textarea v-model="form.requirements" rows="5" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Conocimientos de Excel..."></textarea>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </transition>
                </div>

                <!-- FOOTER ACTIONS -->
                <div class="bg-surface-container-lowest px-8 sm:px-10 py-6 flex flex-col md:flex-row items-center justify-between shrink-0 gap-6 border-t border-outline-variant/10 z-20">
                    <div class="hidden md:flex flex-col">
                        <span class="text-[14px] font-bold text-on-surface mb-0.5">
                            {{ step === 1 ? 'Configuración Comercial' : step === 2 ? 'Perfil Docente' : 'Rigores Académicos' }}
                        </span>
                        <span class="text-[11px] text-on-surface-variant font-medium tracking-wide">
                            {{ step === 1 ? 'Paso inicial para crear tu espacio.' : step === 2 ? 'Humaniza tu propuesta educativa.' : 'Listos para crear el borrador seguro.' }}
                        </span>
                    </div>
                    
                    <div class="flex gap-4 w-full md:w-auto">
                        <!-- CANCEL BUTTON (Only Step 1) -->
                        <button 
                            v-if="step === 1"
                            type="button"
                            class="flex-1 md:flex-none rounded-full border border-outline-variant/10 bg-surface-container-lowest px-8 py-3.5 text-[13px] font-bold text-on-surface hover:bg-surface-container-low shadow-sm transition-all" 
                            @click="emit('close')"
                        >
                            Cancelar
                        </button>
                        
                        <!-- BACK BUTTON (Steps 2, 3) -->
                        <button 
                            v-if="step > 1"
                            type="button"
                            class="flex-1 md:flex-none flex items-center justify-center gap-2 rounded-full border border-outline-variant/10 bg-surface-container-lowest px-8 py-3.5 text-[13px] font-bold text-on-surface hover:bg-surface-container-low shadow-sm transition-all" 
                            @click="prevStep"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            Atrás
                        </button>
                        
                        <!-- NEXT BUTTON (Steps 1, 2) -->
                        <button
                            v-if="step < totalSteps"
                            type="button"
                            class="flex-1 md:flex-none rounded-full bg-primary hover:bg-[#4b4b22] px-10 py-3.5 text-[13px] font-bold text-white shadow-xl shadow-primary/20 transition-all font-sans"
                            @click="nextStep"
                        >
                            Continuar
                        </button>

                        <!-- SUBMIT BUTTON (Step 3) -->
                        <button
                            v-if="step === totalSteps"
                            type="submit"
                            class="flex-1 md:flex-none rounded-full bg-gradient-to-br from-primary to-[#6d6d35] hover:from-[#4b4b22] hover:to-[#5c5c2a] px-10 py-3.5 text-[13px] font-bold text-white shadow-xl shadow-primary/20 disabled:opacity-60 disabled:shadow-none transition-all flex items-center justify-center gap-3 transform scale-100 hover:scale-[1.01] active:scale-[0.98] font-sans"
                            :disabled="!canSubmit || form.processing"
                            @click="submit"
                        >
                            <Loader2 v-if="form.processing" class="w-5 h-5 animate-spin" />
                            <span>{{ form.processing ? 'Procesando...' : 'Crear Espacio Académico' }}</span>
                            <ArrowRight v-if="!form.processing" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <CreateCategoryMiniModal
        :open="showCategoryModal"
        @close="showCategoryModal = false"
        @created="onCategoryCreated"
    />
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
    background-color: var(--md-sys-color-outline-variant, #C9C7B8);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--md-sys-color-outline, #57572A);
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
