<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
    Award, Save, Layout, Type, Palette, 
    ChevronLeft, Image as ImageIcon, Crosshair 
} from 'lucide-vue-next';

interface Template {
    id?: number;
    student_name_X?: number;
    student_name_Y?: number;
    student_name_font_size?: number;
    course_title_X?: number;
    course_title_Y?: number;
    course_title_font_size?: number;
    issue_date_X?: number;
    issue_date_Y?: number;
    issue_date_font_size?: number;
    certificate_code_X?: number;
    certificate_code_Y?: number;
    certificate_code_font_size?: number;
    font_color?: string;
    font_family?: string;
    template_image?: string;
}

const props = defineProps<{
    course: { id: number; title: string };
    template: Template;
}>();

const form = useForm({
    student_name_X: Number(props.template?.student_name_X ?? 0.5),
    student_name_Y: Number(props.template?.student_name_Y ?? 0.45),
    student_name_font_size: props.template?.student_name_font_size ?? 32,
    
    course_title_X: Number(props.template?.course_title_X ?? 0.5),
    course_title_Y: Number(props.template?.course_title_Y ?? 0.55),
    course_title_font_size: props.template?.course_title_font_size ?? 24,
    
    issue_date_X: Number(props.template?.issue_date_X ?? 0.8),
    issue_date_Y: Number(props.template?.issue_date_Y ?? 0.85),
    issue_date_font_size: props.template?.issue_date_font_size ?? 12,
    
    certificate_code_X: Number(props.template?.certificate_code_X ?? 0.15),
    certificate_code_Y: Number(props.template?.certificate_code_Y ?? 0.85),
    certificate_code_font_size: props.template?.certificate_code_font_size ?? 8,

    font_color: props.template?.font_color ?? '#57572A',
    font_family: props.template?.font_family ?? 'serif',
    template_image: null as File | null,
});

const imagePreview = ref(props.template?.template_image ? `/storage/${props.template.template_image}` : null);

const handleImageChange = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        form.template_image = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreview.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const showSuccessToast = ref(false);
const showErrorToast = ref(false);

const submit = () => {
    form.post(route('admin.courses.certificate-template.update', { course: props.course.id }), {
        onSuccess: () => {
            showSuccessToast.value = true;
            setTimeout(() => showSuccessToast.value = false, 4000);
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            showErrorToast.value = true;
            setTimeout(() => showErrorToast.value = false, 5000);
        },
        forceFormData: true,
    });
};

const previewContainer = ref<HTMLElement | null>(null);
const currentWidth = ref(1000); // Base reference
const previewScaleFactor = computed(() => currentWidth.value / 1122); // Relative to A4 standard (approx 96dpi width)

let resizeObserver: ResizeObserver | null = null;

onMounted(() => {
    if (previewContainer.value) {
        resizeObserver = new ResizeObserver((entries) => {
            for (let entry of entries) {
                currentWidth.value = entry.contentRect.width;
            }
        });
        resizeObserver.observe(previewContainer.value);
    }
});

onUnmounted(() => {
    resizeObserver?.disconnect();
});

const textStyle = (x: number, y: number, size: number) => ({
    position: 'absolute' as 'absolute',
    left: `${x * 100}%`,
    top: `${y * 100}%`,
    fontSize: `calc(${size}pt * var(--zoom-factor, 1))`,
    color: form.font_color,
    fontFamily: form.font_family,
    transform: 'translate(-50%, -50%)', // Anchor to center
    textAlign: 'center' as 'center',
    whiteSpace: 'nowrap' as 'nowrap',
    pointerEvents: 'none' as 'none',
    zIndex: 10,
    '--zoom-factor': previewScaleFactor.value
});
const updateCoordinate = (key: string, value: string) => {
    (form as any)[key] = parseFloat(value) / 100;
};
</script>

<template>
    <AppLayout title="Diseñador de Diplomados">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Global Header (Inline) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-6 rounded-[2.5rem] border border-outline-variant/20 shadow-sm">
                <!-- Notifications -->
                <transition 
                    enter-active-class="transition ease-out duration-300 transform" 
                    enter-from-class="opacity-0 translate-y-4" 
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showSuccessToast" class="fixed top-24 right-6 min-w-[300px] z-[100] rounded-2xl bg-[#4B5320] text-white p-4 shadow-2xl border border-white/20 flex items-center gap-3">
                        <Award class="w-8 h-8 text-white/50" />
                        <div>
                            <p class="font-bold text-sm">Cambios guardados</p>
                            <p class="text-[10px] opacity-80">La plantilla del certificado ha sido actualizada.</p>
                        </div>
                    </div>
                </transition>

                <transition 
                    enter-active-class="transition ease-out duration-300 transform" 
                    enter-from-class="opacity-0 translate-y-4" 
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showErrorToast" class="fixed top-24 right-6 min-w-[300px] z-[100] rounded-2xl bg-red-600 text-white p-4 shadow-2xl border border-white/20 flex items-center gap-3">
                        <Crosshair class="w-8 h-8 text-white/50" />
                        <div>
                            <p class="font-bold text-sm">Error al guardar</p>
                            <p class="text-[10px] opacity-80">Revisa que la imagen y los campos sean válidos.</p>
                        </div>
                    </div>
                </transition>

                <div class="flex items-center gap-4">
                    <button @click="router.visit(route('admin.courses.index'))" class="p-3 bg-surface-container-high hover:bg-surface-container-highest rounded-2xl transition-all">
                        <ChevronLeft class="w-5 h-5 text-on-surface" />
                    </button>
                    <div>
                        <h2 class="font-bold text-3xl text-on-surface leading-tight">Configuración de <span class="italic font-serif">Certificado</span></h2>
                        <p class="text-sm text-on-surface-variant font-serif italic">{{ course.title }}</p>
                    </div>
                </div>
                
                <button 
                    @click="submit"
                    :disabled="form.processing"
                    class="flex items-center justify-center gap-3 px-8 py-4 bg-primary text-on-primary rounded-[1.5rem] hover:scale-105 active:scale-95 transition-all shadow-xl shadow-primary/20 font-bold uppercase tracking-widest text-xs min-w-[200px]"
                >
                    <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <Save v-else class="w-5 h-5" />
                    Guardar Cambios
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Controls Sidebar -->
                <div class="space-y-6">
                    <div class="bg-surface-container-lowest rounded-[2.5rem] border border-outline-variant/30 p-8 shadow-sm space-y-8">
                        
                        <!-- Template Image -->
                        <div class="space-y-4">
                            <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary">
                                <ImageIcon class="w-4 h-4" />
                                Imagen de Base (PNG/JPG)
                            </label>
                            <div class="relative group cursor-pointer border-2 border-dashed border-outline-variant/50 rounded-2xl p-6 hover:border-primary/50 transition-all text-center">
                                <input type="file" @change="handleImageChange" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                <div class="space-y-2">
                                    <ImageIcon class="w-8 h-8 text-on-surface-variant/40 mx-auto" />
                                    <p class="text-xs text-on-surface-variant italic">Carga la imagen sin textos para superponerlos dinámicamente.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Typography -->
                        <div class="space-y-6">
                             <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary">
                                <Type class="w-4 h-4" />
                                Estética Global
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-tighter">Color de Fuente</span>
                                    <input v-model="form.font_color" type="color" class="w-full h-10 rounded-lg p-1 bg-surface-container-high border-none cursor-pointer" />
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-tighter">Familia Tipográfica</span>
                                    <select v-model="form.font_family" class="w-full h-10 rounded-lg bg-surface-container-high border-none text-xs font-serif italic">
                                        <option value="serif">Serif (Clásico)</option>
                                        <option value="sans-serif">Sans-Serif (Moderno)</option>
                                        <option value="cursive">Cursive (Elegante)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Placement Controls -->
                        <div class="space-y-8 pt-4 border-t border-outline-variant/30">
                            
                            <!-- Student Name -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Nombre del Alumno</span>
                                    <div class="flex items-center gap-1">
                                        <Type class="w-3 h-3 text-on-surface-variant" />
                                        <input v-model.number="form.student_name_font_size" type="number" class="w-10 text-[10px] bg-transparent border-none p-0 focus:ring-0 font-bold" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <span class="text-[9px] text-on-surface-variant/60 uppercase">X (Horizontal %)</span>
                                        <input 
                                            :value="form.student_name_X * 100" 
                                            @input="updateCoordinate('student_name_X', ($event.target as HTMLInputElement).value)"
                                            type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                        />
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[9px] text-on-surface-variant/60 uppercase">Y (Vertical %)</span>
                                        <input 
                                            :value="form.student_name_Y * 100" 
                                            @input="updateCoordinate('student_name_Y', ($event.target as HTMLInputElement).value)"
                                            type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Course Title -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Título del Curso</span>
                                    <input v-model.number="form.course_title_font_size" type="number" class="w-10 text-[10px] bg-transparent border-none p-0 focus:ring-0 font-bold text-right" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input 
                                        :value="form.course_title_X * 100" 
                                        @input="updateCoordinate('course_title_X', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                    <input 
                                        :value="form.course_title_Y * 100" 
                                        @input="updateCoordinate('course_title_Y', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                </div>
                            </div>

                            <!-- Issue Date -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Fecha de Emisión</span>
                                    <input v-model.number="form.issue_date_font_size" type="number" class="w-10 text-[10px] bg-transparent border-none p-0 focus:ring-0 font-bold text-right" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input 
                                        :value="form.issue_date_X * 100" 
                                        @input="updateCoordinate('issue_date_X', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                    <input 
                                        :value="form.issue_date_Y * 100" 
                                        @input="updateCoordinate('issue_date_Y', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                </div>
                            </div>

                             <!-- Validation Code -->
                             <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">ID de Verificación</span>
                                    <input v-model.number="form.certificate_code_font_size" type="number" class="w-10 text-[10px] bg-transparent border-none p-0 focus:ring-0 font-bold text-right" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input 
                                        :value="form.certificate_code_X * 100" 
                                        @input="updateCoordinate('certificate_code_X', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                    <input 
                                        :value="form.certificate_code_Y * 100" 
                                        @input="updateCoordinate('certificate_code_Y', ($event.target as HTMLInputElement).value)"
                                        type="range" min="0" max="100" step="0.01" class="w-full accent-primary" 
                                    />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Live Preview Canvas -->
                <div class="lg:col-span-2">
                    <div class="sticky top-10 space-y-6">
                        <header class="flex items-center justify-between text-xs font-bold uppercase tracking-widest text-on-surface-variant/40">
                            <span class="flex items-center gap-2"><Layout class="w-4 h-4" /> Previsualización a Escala</span>
                            <span class="bg-surface-container-high px-3 py-1 rounded-full italic font-serif">Modo: Desarrollo Académico</span>
                        </header>

                        <div ref="previewContainer" class="relative bg-surface-container-high rounded-[3rem] border border-outline-variant/40 shadow-2xl overflow-hidden aspect-[1.414]">
                            <!-- The Background Image -->
                            <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-surface-container-low text-on-surface-variant/20 italic font-serif gap-4">
                                <Award class="w-20 h-20 opacity-10" />
                                <p>Carga una plantilla para comenzar a diseñar</p>
                            </div>

                            <!-- Overlay Elements (Simulated Positions) -->
                            <div :style="textStyle(form.student_name_X, form.student_name_Y, form.student_name_font_size)">
                                JUAN PÉREZ ACADÉMICO
                            </div>
                            <div :style="textStyle(form.course_title_X, form.course_title_Y, form.course_title_font_size)">
                                {{ course.title }}
                            </div>
                            <div :style="textStyle(form.issue_date_X, form.issue_date_Y, form.issue_date_font_size)">
                                Emitido el 01/01/2026
                            </div>
                            <div :style="textStyle(form.certificate_code_X, form.certificate_code_Y, form.certificate_code_font_size)" class="opacity-70">
                                ID: IEE-SAMPLE-VERIF-2026
                            </div>

                            <!-- Indicator Overlay if editing active? No, just the preview -->
                        </div>

                        <footer class="p-6 bg-primary/5 rounded-3xl border border-primary/10 flex items-start gap-4">
                            <Crosshair class="w-5 h-5 text-primary mt-1" />
                            <p class="text-xs text-on-surface-variant/70 leading-relaxed italic font-serif">
                                <strong class="text-primary uppercase tracking-widest font-sans inline-block mb-1">Nota Académica:</strong><br>
                                Ajuste los controles laterales para posicionar los textos sobre su plantilla. 
                                Los valores se calculan en base a una hoja A4 en horizontal (297mm x 210mm).
                                Use colores de contraste que respeten la autoridad institucional de su diseño.
                            </p>
                        </footer>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Specific serif fonts when selected */
.serif { font-family: 'Playfair Display', serif; }
</style>
