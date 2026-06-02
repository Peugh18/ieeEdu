<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Award, Download, ArrowRight, AlertCircle, XCircle, CheckCircle2 } from 'lucide-vue-next';
import type { Course, QuizStats } from '@/types/classroom';

const props = defineProps<{
    course: Course;
    quizStats: QuizStats | null;
    localAllCompleted: boolean;
    localCompletedLength: number;
    allLessonsCount: number;
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const userName = computed(() => currentUser.value?.name || 'Estudiante');

const currentDate = computed(() => {
    if (props.quizStats?.certificate_date) {
        return props.quizStats.certificate_date;
    }
    const d = new Date();
    return d.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
});

// Real Certificate Preview scaling logic
const previewContainer = ref<HTMLElement | null>(null);
const currentWidth = ref(800);
const previewScaleFactor = computed(() => currentWidth.value / 1122); // 1122px standard A4 base width

let resizeObserver: ResizeObserver | null = null;

onMounted(() => {
    if (previewContainer.value) {
        resizeObserver = new ResizeObserver((entries) => {
            for (const entry of entries) {
                currentWidth.value = entry.contentRect.width;
            }
        });
        resizeObserver.observe(previewContainer.value);
    }
});

onUnmounted(() => {
    resizeObserver?.disconnect();
});

const templateImage = computed(() => {
    return props.course.certificate_template?.template_image 
        ? `/storage/${props.course.certificate_template.template_image}` 
        : null;
});

function textStyle(x: number, y: number, size: number) {
    const fontColor = props.course.certificate_template?.font_color || '#57572A';
    const fontFamily = props.course.certificate_template?.font_family || 'serif';
    return {
        position: 'absolute' as const,
        left: `${x * 100}%`,
        top: `${y * 100}%`,
        fontSize: `calc(${size}pt * ${previewScaleFactor.value})`,
        color: fontColor,
        fontFamily: fontFamily,
        transform: 'translate(-50%, -50%)',
        textAlign: 'center' as const,
        whiteSpace: 'nowrap' as const,
        pointerEvents: 'none' as const,
        zIndex: 10,
    };
}
</script>

<template>
    <div class="w-full min-h-full flex flex-col items-center bg-white animate-in fade-in duration-700 py-1">
         <div class="w-full max-w-5xl p-4 md:p-6 bg-white rounded-3xl border border-outline-variant/15 text-center shadow-sm relative overflow-hidden">
              <div class="absolute inset-0 bg-[radial-gradient(#57572a_0.5px,transparent_0.5px)] [background-size:16px_16px] opacity-[0.03] pointer-events-none"></div>
              
              <div class="relative z-10 space-y-5">
                   <!-- State: Passed (Redesigned Elegant Diploma Preview) -->
                   <template v-if="quizStats?.passed">
                       <div class="space-y-4">
                           <span class="inline-flex px-3 py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100 dark:border-emerald-900/30">
                               Acreditación Completada
                           </span>
                           <h2 class="text-xl md:text-2xl font-serif font-bold text-on-background">
                               Felicidades por tu Certificación
                           </h2>
                           <p class="text-xs md:text-sm text-on-surface-variant/70 max-w-lg mx-auto">
                               Has aprobado con éxito la evaluación final. A continuación se muestra una vista previa de tu credencial académica oficial.
                           </p>
                       </div>

                        <!-- Certificate Live Preview (HTML-based to avoid native PDF grey/black borders) -->
                        <div class="relative mx-auto max-w-3xl my-6">
                            <div 
                                ref="previewContainer" 
                                class="relative w-full aspect-[1.414] bg-white rounded-2xl border border-outline-variant/15 shadow-xl overflow-hidden select-none"
                            >
                                <template v-if="templateImage">
                                    <img :src="templateImage" class="w-full h-full object-cover" />
                                    
                                    <!-- Dynamic text positioning -->
                                    <div 
                                        :style="textStyle(
                                            course.certificate_template?.student_name_X ?? 0.5, 
                                            course.certificate_template?.student_name_Y ?? 0.45, 
                                            course.certificate_template?.student_name_font_size ?? 32
                                        )"
                                        class="font-bold uppercase"
                                    >
                                        {{ userName }}
                                    </div>
                                    
                                    <div 
                                        :style="textStyle(
                                            course.certificate_template?.course_title_X ?? 0.5, 
                                            course.certificate_template?.course_title_Y ?? 0.55, 
                                            course.certificate_template?.course_title_font_size ?? 24
                                        )"
                                    >
                                        {{ course.title }}
                                    </div>
                                    
                                    <div 
                                        :style="textStyle(
                                            course.certificate_template?.issue_date_X ?? 0.8, 
                                            course.certificate_template?.issue_date_Y ?? 0.85, 
                                            course.certificate_template?.issue_date_font_size ?? 12
                                        )"
                                    >
                                        Emitido el {{ currentDate }}
                                    </div>
                                    
                                    <div 
                                        :style="textStyle(
                                            course.certificate_template?.certificate_code_X ?? 0.15, 
                                            course.certificate_template?.certificate_code_Y ?? 0.85, 
                                            course.certificate_template?.certificate_code_font_size ?? 8
                                        )"
                                        class="opacity-70"
                                    >
                                        ID: {{ quizStats.certificate_code || 'IEE-VERIF-CODE' }}
                                    </div>
                                </template>
                                
                                <template v-else>
                                    <!-- Default/Fallback elegant design -->
                                    <div 
                                        class="w-full h-full p-8 md:p-12 flex flex-col justify-between text-center border-[8px] md:border-[12px] border-double"
                                        :style="{ 
                                            borderColor: course.certificate_template?.font_color || '#57572A',
                                            color: course.certificate_template?.font_color || '#57572A',
                                            fontFamily: course.certificate_template?.font_family || 'serif'
                                        }"
                                    >
                                        <div class="space-y-1">
                                            <h3 class="text-xs md:text-sm font-black tracking-[0.25em] uppercase">
                                                Instituto de Economía y Empresa
                                            </h3>
                                            <p class="text-[8px] md:text-[9px] tracking-widest uppercase opacity-60">Escuela de Especialización Profesional</p>
                                        </div>
                                        
                                        <div class="space-y-2 py-4">
                                            <h1 class="text-xl md:text-3xl font-bold tracking-widest">CERTIFICADO DE APROBACIÓN</h1>
                                            <p class="text-[10px] md:text-xs italic opacity-85">Se otorga el presente reconocimiento a:</p>
                                            <h2 class="text-lg md:text-2xl font-bold uppercase py-1 border-b border-current/30 inline-block min-w-[200px]">
                                                {{ userName }}
                                            </h2>
                                            <p class="text-[10px] md:text-xs italic opacity-85 mt-2">Por haber aprobado satisfactoriamente el curso de especialización de:</p>
                                            <h3 class="text-xs md:text-sm font-bold uppercase tracking-wide">
                                                {{ course.title }}
                                            </h3>
                                        </div>
                                        
                                        <div class="flex justify-between items-end text-[8px] md:text-[10px] opacity-75 pt-4 border-t border-current/10">
                                            <span>ID: {{ quizStats.certificate_code || 'IEE-VERIF-CODE' }}</span>
                                            <span>Fecha de Emisión: {{ currentDate }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Clean Download Button -->
                        <div class="pt-2">
                            <a 
                                v-if="quizStats.certificate_url" 
                                :href="quizStats.certificate_url" 
                                target="_blank" 
                                class="inline-flex px-8 py-3.5 bg-primary text-white hover:bg-on-background hover:scale-[1.02] active:scale-95 transition-all shadow-md hover:shadow-lg font-bold text-xs uppercase tracking-wider rounded-xl items-center gap-2"
                            >
                                Descargar Certificado Oficial
                                <Download class="w-4 h-4" />
                            </a>
                            <span v-else class="inline-flex px-6 py-3 bg-surface-container text-on-surface-variant/60 font-bold text-xs uppercase tracking-wider rounded-xl border italic">
                                Generando archivo de certificado...
                            </span>
                        </div>

                        <!-- Next Steps & Share (Fills bottom empty space of the card) -->
                        <div class="mt-6 pt-5 border-t border-outline-variant/15 grid grid-cols-1 sm:grid-cols-2 gap-6 text-left max-w-xl mx-auto">
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-on-background uppercase tracking-wider">Comparte tu logro</h4>
                                <p class="text-[11px] text-on-surface-variant/70 leading-relaxed font-medium">¡Comparte tu acreditación oficial en tu perfil de LinkedIn o añade la certificación a tu currículum académico!</p>
                                <a href="https://www.linkedin.com" target="_blank" class="inline-flex mt-2 text-[10px] font-black uppercase tracking-wider text-primary hover:text-on-background items-center gap-1">
                                     Compartir en LinkedIn <ArrowRight class="w-3 h-3" />
                                </a>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-on-background uppercase tracking-wider">¿Qué sigue ahora?</h4>
                                <p class="text-[11px] text-on-surface-variant/70 leading-relaxed font-medium">Sigue ampliando tus horizontes profesionales explorando nuestros otros programas y cursos especializados.</p>
                                <Link :href="route('student.explore.courses')" class="inline-flex mt-2 text-[10px] font-black uppercase tracking-wider text-primary hover:text-on-background items-center gap-1">
                                     Explorar Más Cursos <ArrowRight class="w-3 h-3" />
                                </Link>
                            </div>
                        </div>
                    </template>

                   <!-- State: Max Attempts Exhausted (Failed) -->
                   <template v-else-if="quizStats?.attempts_left === 0">
                       <div class="py-12 space-y-6">
                           <div class="w-16 h-16 rounded-full bg-rose-50 border border-rose-100 mx-auto flex items-center justify-center">
                               <XCircle class="w-8 h-8 text-rose-600" />
                           </div>
                           <div class="space-y-2">
                               <h2 class="text-xl md:text-2xl font-bold text-on-background">Intentos Agotados</h2>
                               <p class="text-sm text-on-surface-variant/70 max-w-md mx-auto leading-relaxed">
                                   Has completado la evaluación final del curso, pero no has alcanzado la nota mínima requerida para aprobar en los intentos permitidos.
                               </p>
                           </div>
                           <div class="pt-4 border-t border-outline-variant/10 text-xs text-on-surface-variant/50 max-w-md mx-auto">
                               Por favor, comunícate con la coordinación académica a través de soporte para habilitar una opción de recuperación.
                           </div>
                       </div>
                   </template>

                   <!-- State: Eligible to Take Exam (All Completed) -->
                   <template v-else-if="localAllCompleted">
                       <div class="py-10 space-y-6">
                           <div class="w-16 h-16 rounded-full bg-primary/5 border border-primary/20 mx-auto flex items-center justify-center">
                               <Award class="w-8 h-8 text-primary" />
                           </div>
                           <div class="space-y-3">
                               <h2 class="text-xl md:text-2xl font-bold text-on-background">Evaluación de Acreditación</h2>
                               <p class="text-sm text-on-surface-variant/70 max-w-md mx-auto leading-relaxed">
                                   ¡Excelente progreso! Has visualizado el 100% del currículo académico. Ahora estás habilitado para rendir la evaluación y certificar tus conocimientos.
                               </p>
                           </div>
                           <div class="pt-4">
                               <Link 
                                   v-if="course.quizzes?.length" 
                                   :href="route('student.exams.take', { quiz: course.quizzes[0].id })" 
                                   class="inline-flex px-8 py-3.5 bg-primary text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-on-background hover:scale-[1.02] active:scale-95 transition-all shadow-md group items-center gap-2"
                               >
                                   Iniciar Examen Final
                                   <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                               </Link>
                               <span v-else class="text-xs text-on-surface-variant/40 italic">La evaluación no está disponible en este momento.</span>
                           </div>
                       </div>
                   </template>

                   <!-- State: Locked (Missing Class Views) -->
                   <template v-else>
                       <div class="py-10 space-y-6">
                           <div class="w-16 h-16 rounded-full bg-orange-50 border border-orange-100 mx-auto flex items-center justify-center">
                               <AlertCircle class="w-8 h-8 text-orange-500" />
                           </div>
                           <div class="space-y-3">
                               <h2 class="text-xl md:text-2xl font-bold text-on-background">Acreditación en Espera</h2>
                               <p class="text-sm text-on-surface-variant/70 max-w-md mx-auto leading-relaxed">
                                   Aún no has completado la visualización de todos los módulos curriculares del curso. Completa el temario restante para desbloquear la evaluación final.
                               </p>
                           </div>
                           
                           <div class="pt-4 flex flex-col items-center gap-2">
                               <div class="px-5 py-3 bg-surface-container-low border border-outline-variant/10 rounded-2xl flex items-center gap-4">
                                   <div class="text-base font-bold text-primary tabular-nums">
                                       {{ localCompletedLength }} / {{ allLessonsCount }} Clases
                                   </div>
                                   <div class="w-32 h-1.5 bg-outline-variant/30 rounded-full overflow-hidden">
                                       <div class="h-full bg-primary rounded-full" :style="{ width: `${(localCompletedLength / allLessonsCount) * 100}%` }"></div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </template>
              </div>
         </div>
    </div>
</template>
