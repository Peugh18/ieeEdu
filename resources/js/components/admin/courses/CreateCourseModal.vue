<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { 
    X, GraduationCap, Video, Users, Star, Info, 
    PartyPopper, UserRound, ImagePlus, BookOpen, 
    Loader2, ArrowRight 
} from 'lucide-vue-next';
import CreateCategoryMiniModal from './CreateCategoryMiniModal.vue';

type CourseType = 'grabado' | 'en vivo' | 'masterclass';

const props = defineProps<{
    open: boolean;
    categories: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'categoryCreated', category: { id: number; name: string }): void;
}>();

const showCategoryModal = ref(false);
const imagePreview = ref<string | null>(null);
const instructorImagePreview = ref<string | null>(null);

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
        form.status = 'BORRADOR';
        form.type = 'grabado';
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
// En Vivo and Masterclass optionally have price but Title, Category, Image always needed.
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
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <!-- WIZARD CONTAINER -->
            <div class="w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl bg-white shadow-2xl border border-outline-variant/10 overflow-hidden">
                
                <!-- HEADER VISUAL -->
                <div class="px-8 py-6 bg-surface-container-low border-b border-outline-variant/10 flex items-start justify-between shrink-0">
                    <div>
                        <h2 class="font-serif text-2xl md:text-3xl text-on-surface">Configuración Inicial del Curso</h2>
                        <p class="mt-1 text-sm text-on-surface-variant font-medium">Asistente de Creación Paso 1: Datos Comerciales y Académicos</p>
                    </div>
                    <button class="rounded-xl p-2 hover:bg-surface-container/50 transition-colors" @click="$emit('close')" aria-label="Cerrar">
                        <X class="w-5 h-5 text-on-surface-variant" />
                    </button>
                </div>

                <!-- SCROLLABLE CONTENT -->
                <div class="p-8 overflow-y-auto flex-1 space-y-10 custom-scrollbar relative">
                    
                    <!-- 1. MODALITY SELECTOR -->
                    <section>
                        <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#57572A] mb-5">
                            <GraduationCap class="w-[18px] h-[18px]" />
                            1. Modalidad del Programa
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Grabado -->
                            <button 
                                type="button" 
                                @click="setModality('grabado')"
                                class="flex flex-col items-start p-5 rounded-2xl border-2 text-left transition-all duration-200 cursor-pointer"
                                :class="form.type === 'grabado' ? 'border-[#57572A] bg-[#57572A]/5 shadow-sm scale-[1.02]' : 'border-outline-variant/20 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                            >
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-4 text-white transition-colors" :class="form.type === 'grabado' ? 'bg-[#57572A]' : 'bg-surface-variant'">
                                    <Video class="w-5 h-5" />
                                </div>
                                <h4 class="font-bold text-[15px] text-on-surface mb-1">Curso Grabado</h4>
                                <p class="text-xs text-on-surface-variant font-medium leading-relaxed">Contenido pregrabado. El alumno avanza a su propio ritmo.</p>
                            </button>

                            <!-- En Vivo -->
                            <button 
                                type="button" 
                                @click="setModality('en vivo')"
                                class="flex flex-col items-start p-5 rounded-2xl border-2 text-left transition-all duration-200 cursor-pointer"
                                :class="form.type === 'en vivo' ? 'border-[#57572A] bg-[#57572A]/5 shadow-sm scale-[1.02]' : 'border-outline-variant/20 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                            >
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-4 text-white transition-colors" :class="form.type === 'en vivo' ? 'bg-[#57572A]' : 'bg-surface-variant'">
                                    <Users class="w-5 h-5" />
                                </div>
                                <h4 class="font-bold text-[15px] text-on-surface mb-1">Curso En Vivo</h4>
                                <p class="text-xs text-on-surface-variant font-medium leading-relaxed">Clases sincrónicas en fechas y horarios programados.</p>
                            </button>

                            <!-- Masterclass -->
                            <button 
                                type="button" 
                                @click="setModality('masterclass')"
                                class="flex flex-col items-start p-5 rounded-2xl border-2 text-left transition-all duration-200 cursor-pointer"
                                :class="form.type === 'masterclass' ? 'border-[#57572A] bg-[#57572A]/5 shadow-sm scale-[1.02]' : 'border-outline-variant/20 bg-white hover:border-[#57572A]/40 hover:bg-surface-container-lowest'"
                            >
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-4 text-white transition-colors" :class="form.type === 'masterclass' ? 'bg-[#57572A]' : 'bg-surface-variant'">
                                    <Star class="w-5 h-5" />
                                </div>
                                <h4 class="font-bold text-[15px] text-on-surface mb-1">Masterclass</h4>
                                <p class="text-xs text-on-surface-variant font-medium leading-relaxed">Sesión magistral gratuita en una fecha específica.</p>
                            </button>
                        </div>
                    </section>

                    <hr class="border-outline-variant/20" />

                    <!-- 2. DATOS COMERCIALES -->
                    <section>
                        <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#57572A] mb-5">
                            <Info class="w-[18px] h-[18px]" />
                            2. Información Comercial
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Data -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Título Oficial del Curso</label>
                                <input v-model="form.title" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none" placeholder="Ej. Alta Especialización en Gestión Pública" />
                                <p v-if="form.errors.title" class="text-xs text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Categoría</label>
                                <div class="flex gap-2">
                                    <select v-model="form.category_id" class="flex-1 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none">
                                        <option value="">Seleccione especialidad</option>
                                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                    <button type="button" class="rounded-2xl border border-outline-variant/30 px-3 hover:bg-surface-container-low transition font-bold text-[#57572A]" @click="showCategoryModal = true">
                                        +
                                    </button>
                                </div>
                                <p v-if="form.errors.category_id" class="text-xs text-red-600">{{ form.errors.category_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Imagen de Portada</label>
                                <div class="flex items-center gap-3">
                                    <label class="flex-1 cursor-pointer rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 hover:bg-surface-container-low transition-all">
                                        <input type="file" accept="image/*" class="hidden" @change="(e) => onPickImage((e.target as HTMLInputElement).files?.[0] ?? null)" />
                                        <span class="text-[13px] font-semibold text-on-surface">Subir banner gráfico</span>
                                        <span class="ml-2 text-[10px] text-on-surface-variant uppercase tracking-widest hidden sm:inline-block">(JPG/PNG/WEBP)</span>
                                    </label>
                                    <div v-if="imagePreview" class="h-11 w-16 overflow-hidden rounded-xl border border-outline-variant/20 shadow-sm flex-shrink-0">
                                        <img :src="imagePreview" alt="Preview" class="h-full w-full object-cover" />
                                    </div>
                                </div>
                                <p v-if="form.errors.image_file" class="text-xs text-red-600">{{ form.errors.image_file }}</p>
                            </div>

                            <!-- Pricing logic conditional -->
                            <template v-if="form.type !== 'masterclass'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Inversión Base (S/)</label>
                                    <input v-model.number="form.price" type="number" min="0" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none" placeholder="0" />
                                    <p v-if="form.errors.price" class="text-xs text-red-600">{{ form.errors.price }}</p>
                                </div>
                                
                                <div class="rounded-2xl border border-outline-variant/20 bg-surface-container-lowest p-4">
                                    <label class="flex items-center gap-2 text-xs uppercase tracking-wider font-bold text-on-surface-variant mb-4 cursor-pointer">
                                        <input type="checkbox" v-model="form.discount_enabled" class="accent-[#57572A] w-4 h-4" />
                                        Aplicar Promoción Especial (%)
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <input v-model.number="form.discount" :disabled="!form.discount_enabled" type="number" min="0" max="100" class="w-24 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[13px] disabled:opacity-50 disabled:bg-surface-container transition-all outline-none focus:border-[#57572A]" placeholder="%" />
                                        <div class="flex-1 flex flex-col justify-center">
                                            <span class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold">Inversión Final Sugerida</span>
                                            <span class="text-lg font-serif font-bold text-[#57572A]">S/ {{ form.sale_price }}</span>
                                        </div>
                                    </div>
                                    <p v-if="form.errors.discount" class="mt-2 text-xs text-red-600">{{ form.errors.discount }}</p>
                                </div>
                            </template>
                            <template v-else>
                                <div class="md:col-span-2 rounded-2xl border border-[#57572A]/20 bg-[#57572A]/5 px-5 py-4 flex items-center gap-4 shadow-sm">
                                    <div class="min-w-[40px] h-[40px] rounded-full bg-[#57572A]/10 flex items-center justify-center">
                                        <PartyPopper class="text-[#57572A] w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[13px] text-on-surface">Asistencia Gratuita Configurada</h4>
                                        <p class="text-xs text-on-surface-variant mt-0.5">Las sesiones Masterclass no requieren configuración comercial ni precios.</p>
                                    </div>
                                </div>
                            </template>

                            <!-- Fechas (Only Live & Masterclass) -->
                            <template v-if="form.type === 'en vivo' || form.type === 'masterclass'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Fecha de Inicio Programada</label>
                                    <input v-model="form.start_date" type="date" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none" />
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Hora Exacta</label>
                                    <input v-model="form.start_time" type="time" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none" />
                                </div>
                            </template>
                        </div>
                    </section>

                    <hr class="border-outline-variant/20" />

                    <!-- 3. PERFIL DOCENTE -->
                    <section>
                        <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#57572A] mb-5">
                            <UserRound class="w-[18px] h-[18px]" />
                            3. Docente Especialista Responsable
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 sm:p-8 rounded-3xl bg-surface-container-lowest border border-outline-variant/15 relative overflow-hidden">
                            <!-- Decor BG -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#F4F4EF] rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                            <!-- Foto y Preview integrados en el layout -->
                            <div class="md:row-span-2 flex flex-col items-center justify-center space-y-4 p-6 border border-dashed border-outline-variant/40 rounded-2xl bg-white relative z-10 shadow-sm">
                                <div class="relative w-28 h-28 rounded-full overflow-hidden bg-surface-container-low shadow-inner border border-outline-variant/20 flex items-center justify-center text-surface-variant transition-transform hover:scale-105">
                                    <img v-if="instructorImagePreview" :src="instructorImagePreview" class="w-full h-full object-cover" />
                                    <ImagePlus v-else class="w-10 h-10" />
                                </div>
                                <label class="cursor-pointer bg-surface-container hover:bg-surface-container-highest px-5 py-2.5 rounded-xl text-[11px] font-bold text-on-surface transition-colors uppercase tracking-widest text-center shadow-sm w-full">
                                    <input type="file" accept="image/*" class="hidden" @change="(e) => onPickInstructorImage((e.target as HTMLInputElement).files?.[0] ?? null)" />
                                    Subir Retrato
                                </label>
                                <p class="text-[10px] text-on-surface-variant text-center leading-tight">Resolución 1:1 (cuadrada)<br>Máximo 2MB Recomendado</p>
                            </div>

                            <div class="space-y-2 relative z-10">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nombre Completo del Profesional</label>
                                <input v-model="form.instructor_name" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-3 text-[13px] focus:border-[#57572A] transition-all outline-none" placeholder="Ej. Dr. Carlos Fernández" />
                            </div>

                            <div class="space-y-2 relative z-10">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Titulación o Cargo Institucional</label>
                                <input v-model="form.instructor_title" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-3 text-[13px] focus:border-[#57572A] transition-all outline-none" placeholder="Ej. Director Nacional de Presupuestos" />
                            </div>

                            <div class="md:col-span-2 space-y-2 relative z-10 mt-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Biografía y Experiencia Comprobada</label>
                                <textarea v-model="form.instructor_bio" rows="4" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-3 text-[13px] focus:border-[#57572A] transition-all outline-none resize-none leading-relaxed" placeholder="Reseña especializada de la trayectoria en el sector público o privado..."></textarea>
                            </div>
                        </div>
                    </section>

                    <hr class="border-outline-variant/20" />

                    <!-- 4. RIGOR ACADÉMICO -->
                    <section class="pb-8">
                        <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#57572A] mb-5">
                            <BookOpen class="w-[18px] h-[18px]" />
                            4. Estructura Académica (General)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-surface-container-lowest p-6 sm:p-8 rounded-3xl border border-outline-variant/15">
                            
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Presentación o Introducción</label>
                                <textarea v-model="form.description" rows="3" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-3 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none leading-relaxed" placeholder="Describe brevemente de qué tratará este programa y su impacto vital..."></textarea>
                                <p v-if="form.errors.description" class="text-xs text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Objetivos del Curso (Recomendado)</label>
                                <textarea v-model="form.objectives" rows="5" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-4 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none leading-relaxed" placeholder="Aplicar normativas vigentes...&#10;Identificar áreas de riesgo financiero..."></textarea>
                                <p class="text-[10px] text-on-surface-variant leading-tight mt-1 flex items-start gap-1">
                                    <Info class="w-3 h-3 flex-shrink-0" />
                                    <span>Pista: Escribe un objetivo por renglón. Se indexarán automáticamente en la vista pública.</span>
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Requisitos Previos (Recomendado)</label>
                                <textarea v-model="form.requirements" rows="5" class="w-full rounded-2xl border border-outline-variant/30 bg-white px-4 py-4 text-[13px] focus:border-[#57572A] focus:ring-2 focus:ring-[#57572A]/15 transition-all outline-none leading-relaxed" placeholder="Conocimientos en finanzas públicas.&#10;Manejo de hoja de cálculo intermedio..."></textarea>
                                <p class="text-[10px] text-on-surface-variant leading-tight mt-1 flex items-start gap-1">
                                    <Info class="w-3 h-3 flex-shrink-0" />
                                    <span>Pista: Escribe un requisito por renglón. Déjalo en blanco si el curso no cuenta con requisitos técnicos.</span>
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- FOOTER ACTIONS -->
                <div class="border-t border-outline-variant/15 bg-surface-container-low px-8 py-5 flex flex-col md:flex-row items-center justify-between shrink-0 gap-4">
                    <div class="hidden md:flex flex-col">
                        <span class="text-xs font-bold text-on-surface">Paso Siguiente: Editor de Sílabo</span>
                        <span class="text-[10px] text-on-surface-variant uppercase tracking-widest">El curso permanecerá en <span class="font-bold">BORRADOR</span></span>
                    </div>
                    <div class="flex gap-3 w-full md:w-auto">
                        <button class="flex-1 md:flex-none rounded-2xl border border-outline-variant/30 bg-white px-6 py-3.5 text-[13px] font-bold text-on-surface hover:bg-surface-container-low transition-colors" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button
                            class="flex-1 md:flex-none rounded-2xl bg-[#57572A] hover:bg-[#4a4a24] px-8 py-3.5 text-[12px] uppercase tracking-widest font-bold text-white shadow-lg shadow-[#57572A]/20 disabled:opacity-50 disabled:shadow-none transition-all flex items-center justify-center gap-2"
                            :disabled="!canSubmit || form.processing"
                            @click="submit"
                        >
                            <Loader2 v-if="form.processing" class="w-[18px] h-[18px] animate-spin" />
                            <span>{{ form.processing ? 'Procesando...' : 'Crear Curso y Continuar' }}</span>
                            <ArrowRight v-if="!form.processing" class="w-[18px] h-[18px]" />
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
</style>
