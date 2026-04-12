<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { 
    X, GraduationCap, Video, Users, Star, Info, 
    PartyPopper, UserRound, ImagePlus, ChevronLeft,
    Loader2, ArrowRight 
} from 'lucide-vue-next';
import CreateCategoryMiniModal from './CreateCategoryMiniModal.vue';

type CourseType = 'grabado' | 'en vivo' | 'masterclass';

const props = defineProps<{
    open: boolean;
    categories: Array<{ id: number; name: string }>;
    duplicateData?: Record<string, any> | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'categoryCreated', category: { id: number; name: string }): void;
}>();

const showCategoryModal = ref(false);
const imagePreview = ref<string | null>(null);
const instructorImagePreview = ref<string | null>(null);

const step = ref(1);
const totalSteps = 3;

function nextStep() {
    if (step.value < totalSteps) step.value++;
}
function prevStep() {
    if (step.value > 1) step.value--;
}

const form = useForm({
    title: '',
    description: '',
    price: 0,
    discount_enabled: false,
    discount: 0,
    sale_price: 0,
    type: 'grabado' as CourseType,
    status: 'BORRADOR',
    category_id: '' as string | number,
    image_file: null as File | null,
    instructor_name: '',
    instructor_title: '',
    instructor_bio: '',
    instructor_image_file: null as File | null,
    
    // Rigor & Modality logic
    start_date: '',
    start_time: '',
    whatsapp_link: '',
    objectives: '',
    requirements: '',
});

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;
        form.reset();
        form.clearErrors();
        imagePreview.value = null;
        instructorImagePreview.value = null;
        showCategoryModal.value = false;
        step.value = 1;

        if (props.duplicateData) {
            form.title = props.duplicateData.title + ' (Copia)';
            form.description = props.duplicateData.description || '';
            form.price = props.duplicateData.price || 0;
            form.discount_enabled = Boolean(props.duplicateData.discount_enabled);
            form.discount = props.duplicateData.discount || 0;
            form.sale_price = props.duplicateData.sale_price || 0;
            form.type = props.duplicateData.type === 'evento' ? 'masterclass' : (props.duplicateData.type || 'grabado');
            form.category_id = props.duplicateData.category_id || (props.duplicateData.category?.id || '');
            form.instructor_name = props.duplicateData.instructor_name || '';
            form.instructor_title = props.duplicateData.instructor_title || '';
            form.instructor_bio = props.duplicateData.instructor_bio || '';
            form.start_date = props.duplicateData.start_date || '';
            form.start_time = props.duplicateData.start_time || '';
            form.whatsapp_link = props.duplicateData.whatsapp_link || '';
            form.objectives = props.duplicateData.objectives || '';
            form.requirements = props.duplicateData.requirements || '';
            form.status = 'BORRADOR';
        } else {
            form.status = 'BORRADOR';
            form.type = 'grabado';
        }
    },
);

// Switch modality logic
function setModality(t: CourseType) {
    form.type = t;
    if (t === 'masterclass') {
        form.price = 0;
        form.discount_enabled = false;
        form.discount = 0;
        form.sale_price = 0;
    }
}

watch([() => form.price, () => form.discount], ([newPrice, newDiscount]) => {
    if (form.discount_enabled && (newDiscount as number) > 0) {
        form.sale_price = Number(((newPrice as number) - ((newPrice as number) * ((newDiscount as number) / 100))).toFixed(2));
    } else {
        form.sale_price = 0;
    }
});

watch(() => form.discount_enabled, (enabled: boolean) => {
    if (enabled && form.discount > 0) {
        form.sale_price = Number((form.price - (form.price * (form.discount / 100))).toFixed(2));
    } else {
        form.sale_price = 0;
    }
});

// Require specific fields depending on the modality.
const canSubmit = computed(() => {
    const isFree = form.type === 'masterclass';
    const hasValidPrice = isFree || Number(form.price) >= 0;
    return !!form.title.trim() && !!form.description.trim() && hasValidPrice && !!form.category_id && !!form.image_file;
});

function onPickImage(file: File | null) {
    form.image_file = file;
    if (!file) {
        imagePreview.value = null;
        return;
    }
    imagePreview.value = URL.createObjectURL(file);
}

function onPickInstructorImage(file: File | null) {
    form.instructor_image_file = file;
    if (!file) {
        instructorImagePreview.value = null;
        return;
    }
    instructorImagePreview.value = URL.createObjectURL(file);
}

function submit() {
    form.status = 'BORRADOR';
    form.transform((data) => {
        const payload: any = {
            ...data,
            sale_price: data.discount_enabled ? data.sale_price : '',
            discount: data.discount_enabled ? data.discount : '',
        };
        // Transform the DB payload type backwards for compatibility "evento"
        if (payload.type === 'masterclass') {
            payload.type = 'evento';
            payload.price = 0;
        }
        return payload;
    }).post(route('admin.courses.store'), {
        forceFormData: true,
        onSuccess: () => {
            emit('close');
        },
    });
}

function onCategoryCreated(category: { id: number; name: string }) {
    emit('categoryCreated', category);
    form.category_id = category.id;
}
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-surface-tint/60 backdrop-blur-md">
            <!-- WIZARD CONTAINER -->
            <div class="w-full max-w-4xl max-h-[95vh] flex flex-col rounded-[2.5rem] bg-white shadow-2xl shadow-surface-tint/10 border border-outline-variant/10 overflow-hidden relative">
                
                <!-- BACKGROUND BLUR (SaaS magic) -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#57572A]/10 to-transparent rounded-full blur-3xl -mt-20 -mr-20 pointer-events-none"></div>

                <!-- HEADER VISUAL AND STEPPER -->
                <div class="px-8 sm:px-10 pt-8 pb-6 border-b border-outline-variant/10 flex flex-col gap-6 shrink-0 bg-white/70 backdrop-blur-md relative z-10">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="font-serif text-3xl text-on-surface font-bold tracking-tight mb-2">Crear <span class="italic font-light">Programa</span></h2>
                            <p class="text-[14px] text-on-surface-variant font-medium">Define los datos principales para abrir un nuevo espacio en el campus.</p>
                        </div>
                        <button class="rounded-full p-2.5 bg-surface-container-lowest border border-outline-variant/10 shadow-sm hover:bg-surface-container-low transition-colors" @click="$emit('close')" aria-label="Cerrar">
                            <X class="w-5 h-5 text-on-surface-variant" />
                        </button>
                    </div>

                    <!-- STEP INDICATOR -->
                    <div class="flex items-center gap-3">
                        <div class="h-2 flex-1 rounded-full overflow-hidden bg-surface-container">
                            <div class="h-full bg-[#57572A] rounded-full transition-all duration-500 ease-out" :style="{ width: `${(step / totalSteps) * 100}%` }"></div>
                        </div>
                        <span class="text-[12px] font-bold text-[#57572A] uppercase tracking-widest whitespace-nowrap">Paso {{ step }} de {{ totalSteps }}</span>
                    </div>
                </div>

                <!-- SCROLLABLE CONTENT -->
                <div class="p-8 sm:p-10 overflow-x-hidden overflow-y-auto flex-1 custom-scrollbar relative z-10 bg-white">
                    
                    <!-- ========================= -->
                    <!-- STEP 1: MODALIDAD & DATOS COMERCIALES -->
                    <!-- ========================= -->
                    <transition name="slide">
                        <div v-show="step === 1" class="space-y-12">
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
                                        :class="form.type === 'grabado' ? 'border-[#57572A] bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                                    >
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'grabado' ? 'bg-[#57572A] shadow-lg shadow-[#57572A]/30' : 'bg-surface-variant'">
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
                                        :class="form.type === 'en vivo' ? 'border-[#57572A] bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                                    >
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'en vivo' ? 'bg-[#57572A] shadow-lg shadow-[#57572A]/30' : 'bg-surface-variant'">
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
                                        :class="form.type === 'masterclass' ? 'border-[#57572A] bg-surface-container-highest shadow-md scale-[1.02]' : 'border-outline-variant/15 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                                    >
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-5 text-white transition-colors" :class="form.type === 'masterclass' ? 'bg-[#57572A] shadow-lg shadow-[#57572A]/30' : 'bg-surface-variant'">
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
                                        <input v-model="form.title" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Ej. Alta Especialización en Finanzas Públicas" />
                                        <p v-if="form.errors.title" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.title }}</p>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Especialidad</label>
                                        <div class="flex gap-2">
                                            <select v-model="form.category_id" class="flex-1 rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent appearance-none">
                                                <option value="">Seleccione Categoría</option>
                                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                            </select>
                                            <button type="button" class="rounded-[1.5rem] bg-surface-container-highest px-6 hover:bg-surface-container-high transition-colors font-bold text-[#57572A] flex items-center justify-center shadow-sm" @click="showCategoryModal = true">
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
                                                <span class="text-[13px] font-bold text-[#57572A]">Elegir Imagen</span>
                                                <span class="block text-[11px] text-on-surface-variant font-medium mt-0.5">JPG/PNG/WEBP</span>
                                            </label>
                                        </div>
                                        <p v-if="form.errors.image_file" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.image_file }}</p>
                                    </div>

                                    <!-- Pricing logic conditional -->
                                    <template v-if="form.type !== 'masterclass'">
                                        <div class="space-y-3">
                                            <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Inversión Base (S/)</label>
                                            <input v-model.number="form.price" type="number" min="0" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="0" />
                                            <p v-if="form.errors.price" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.price }}</p>
                                        </div>
                                        
                                        <div class="rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm flex flex-col justify-center">
                                            <label class="flex items-center gap-3 text-[14px] font-bold text-on-surface font-sans mb-5 cursor-pointer w-max">
                                                <input type="checkbox" v-model="form.discount_enabled" class="accent-[#57572A] w-5 h-5 rounded focus:ring-[#57572A]" />
                                                <span>Promo Descuento</span>
                                            </label>
                                            <div class="flex items-center gap-5">
                                                <div class="w-24">
                                                    <input v-model.number="form.discount" :disabled="!form.discount_enabled" type="number" min="0" max="100" class="w-full rounded-[1.5rem] bg-surface-container-highest px-5 py-4 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent placeholder:text-outline-variant disabled:opacity-50 disabled:bg-surface-container-low" placeholder="%" />
                                                </div>
                                                <div class="flex-1">
                                                    <span class="block text-[12px] text-on-surface-variant font-bold uppercase tracking-widest mb-1">Precio Final</span>
                                                    <span class="text-2xl font-serif font-bold text-[#57572A]">S/ {{ form.sale_price }}</span>
                                                </div>
                                            </div>
                                            <p v-if="form.errors.discount" class="ml-1 mt-2 text-[13px] font-bold text-red-600">{{ form.errors.discount }}</p>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="md:col-span-2 rounded-[2rem] bg-surface-container-highest px-8 py-6 flex items-center gap-5 shadow-sm border border-transparent">
                                            <div class="w-14 h-14 rounded-full bg-surface-container-low border border-white flex items-center justify-center shrink-0">
                                                <PartyPopper class="text-[#57572A] w-6 h-6" />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-[16px] text-on-surface mb-1">Asistencia Gratuita Configurada</h4>
                                                <p class="text-[14px] text-on-surface-variant font-medium leading-relaxed">Las sesiones Masterclass no requieren configuración comercial ni fijación de precios.</p>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Fechas (Only Live & Masterclass) -->
                                    <template v-if="form.type === 'en vivo' || form.type === 'masterclass'">
                                        <div class="space-y-3">
                                            <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Fecha de Lanzamiento</label>
                                            <input v-model="form.start_date" type="date" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent" />
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Hora Programada (Local)</label>
                                            <input v-model="form.start_time" type="time" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent" />
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </div>
                    </transition>

                    <!-- ========================= -->
                    <!-- STEP 2: DOCENTE -->
                    <!-- ========================= -->
                    <transition name="slide">
                        <div v-show="step === 2" class="space-y-12">
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
                                            <label class="cursor-pointer flex items-center justify-center w-full px-6 py-3.5 bg-white rounded-full text-[12px] font-bold uppercase tracking-wider text-[#57572A] border border-outline-variant/20 hover:bg-[#57572A]/5 hover:border-[#57572A]/20 shadow-sm transition-all">
                                                <input type="file" accept="image/*" class="hidden" @change="(e) => onPickInstructorImage((e.target as HTMLInputElement).files?.[0] ?? null)" />
                                                Subir Foto
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-3 relative z-10">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Nombre Evidenciado</label>
                                        <input v-model="form.instructor_name" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Mgtr. Carlos Fernández" />
                                    </div>

                                    <div class="space-y-3 relative z-10">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Titulación Oficial</label>
                                        <input v-model="form.instructor_title" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Gerente de Finanzas" />
                                    </div>

                                    <div class="md:col-span-2 space-y-3 relative z-10 mt-2">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Biografía Breve</label>
                                        <textarea v-model="form.instructor_bio" rows="3" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent resize-none leading-relaxed placeholder:text-outline-variant" placeholder="Biografía profesional..."></textarea>
                                    </div>
                                </div>
                            </section>
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
                                        <textarea v-model="form.description" rows="3" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Descripción atractiva..."></textarea>
                                        <p v-if="form.errors.description" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.description }}</p>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Metas (Un objetivo por línea)</label>
                                        <textarea v-model="form.objectives" rows="5" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Identificar riesgos financieros..."></textarea>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Requisitos Previos</label>
                                        <textarea v-model="form.requirements" rows="5" class="w-full rounded-[1.5rem] bg-white px-6 py-5 text-[14px] focus:ring-2 focus:ring-[#57572A]/20 transition-all outline-none border-transparent leading-relaxed placeholder:text-outline-variant" placeholder="Conocimientos de Excel..."></textarea>
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
                            @click="$emit('close')"
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
                            class="flex-1 md:flex-none rounded-full bg-[#57572A] hover:bg-[#4b4b22] px-10 py-3.5 text-[13px] font-bold text-white shadow-xl shadow-[#57572A]/20 transition-all font-sans"
                            @click="nextStep"
                        >
                            Continuar
                        </button>

                        <!-- SUBMIT BUTTON (Step 3) -->
                        <button
                            v-if="step === totalSteps"
                            type="submit"
                            class="flex-1 md:flex-none rounded-full bg-gradient-to-br from-[#57572A] to-[#6d6d35] hover:from-[#4b4b22] hover:to-[#5c5c2a] px-10 py-3.5 text-[13px] font-bold text-white shadow-xl shadow-[#57572A]/20 disabled:opacity-60 disabled:shadow-none transition-all flex items-center justify-center gap-3 transform hover:scale-[1.01] active:scale-[0.99] font-sans"
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
/* Scrollbar that blends beautifully with the design system */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
    margin-block: 8px; /* Adds space top/bottom padding pseudo */
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--md-sys-color-outline-variant, #C9C7B8);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--md-sys-color-outline, #57572A);
}

/* Stepper transitions */
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
