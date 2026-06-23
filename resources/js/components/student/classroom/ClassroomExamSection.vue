<script setup lang="ts">
import type { Course, QuizStats } from '@/types/classroom';
import { Link, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Award, Download, XCircle } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

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
    return props.course.certificate_template?.template_image ? `/storage/${props.course.certificate_template.template_image}` : null;
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
    <div class="flex min-h-full w-full flex-col items-center bg-white py-1 duration-700 animate-in fade-in">
        <div class="relative w-full max-w-5xl overflow-hidden rounded-3xl border border-outline-variant/15 bg-white p-4 text-center shadow-sm md:p-6">
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(#57572a_0.5px,transparent_0.5px)] opacity-[0.03] [background-size:16px_16px]"
            ></div>

            <div class="relative z-10 space-y-5">
                <!-- State: Passed (Redesigned Elegant Diploma Preview) -->
                <template v-if="quizStats?.passed">
                    <div class="space-y-4">
                        <span
                            class="inline-flex rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:border-emerald-900/30 dark:bg-emerald-950/20 dark:text-emerald-300"
                        >
                            Acreditación Completada
                        </span>
                        <h2 class="font-serif text-xl font-bold text-on-background md:text-2xl">Felicidades por tu Certificación</h2>
                        <p class="mx-auto max-w-lg text-xs text-on-surface-variant/70 md:text-sm">
                            Has aprobado con éxito la evaluación final. A continuación se muestra una vista previa de tu credencial académica oficial.
                        </p>
                    </div>

                    <!-- Certificate Live Preview (HTML-based to avoid native PDF grey/black borders) -->
                    <div class="relative mx-auto my-6 max-w-3xl">
                        <div
                            ref="previewContainer"
                            class="relative aspect-[1.414] w-full select-none overflow-hidden rounded-2xl border border-outline-variant/15 bg-white shadow-xl"
                        >
                            <template v-if="templateImage">
                                <img :src="templateImage" class="h-full w-full object-cover" />

                                <!-- Dynamic text positioning -->
                                <div
                                    :style="
                                        textStyle(
                                            course.certificate_template?.student_name_X ?? 0.5,
                                            course.certificate_template?.student_name_Y ?? 0.45,
                                            course.certificate_template?.student_name_font_size ?? 32,
                                        )
                                    "
                                    class="font-bold uppercase"
                                >
                                    {{ userName }}
                                </div>

                                <div
                                    :style="
                                        textStyle(
                                            course.certificate_template?.course_title_X ?? 0.5,
                                            course.certificate_template?.course_title_Y ?? 0.55,
                                            course.certificate_template?.course_title_font_size ?? 24,
                                        )
                                    "
                                >
                                    {{ course.title }}
                                </div>

                                <div
                                    :style="
                                        textStyle(
                                            course.certificate_template?.issue_date_X ?? 0.8,
                                            course.certificate_template?.issue_date_Y ?? 0.85,
                                            course.certificate_template?.issue_date_font_size ?? 12,
                                        )
                                    "
                                >
                                    Emitido el {{ currentDate }}
                                </div>

                                <div
                                    :style="
                                        textStyle(
                                            course.certificate_template?.certificate_code_X ?? 0.15,
                                            course.certificate_template?.certificate_code_Y ?? 0.85,
                                            course.certificate_template?.certificate_code_font_size ?? 8,
                                        )
                                    "
                                    class="opacity-70"
                                >
                                    ID: {{ quizStats.certificate_code || 'IEE-VERIF-CODE' }}
                                </div>
                            </template>

                            <template v-else>
                                <!-- Default/Fallback elegant design -->
                                <div
                                    class="flex h-full w-full flex-col justify-between border-[8px] border-double p-8 text-center md:border-[12px] md:p-12"
                                    :style="{
                                        borderColor: course.certificate_template?.font_color || '#57572A',
                                        color: course.certificate_template?.font_color || '#57572A',
                                        fontFamily: course.certificate_template?.font_family || 'serif',
                                    }"
                                >
                                    <div class="space-y-1">
                                        <h3 class="text-xs font-black uppercase tracking-[0.25em] md:text-sm">Instituto de Economía y Empresa</h3>
                                        <p class="text-[8px] uppercase tracking-widest opacity-60 md:text-[9px]">
                                            Escuela de Especialización Profesional
                                        </p>
                                    </div>

                                    <div class="space-y-2 py-4">
                                        <h1 class="text-xl font-bold tracking-widest md:text-3xl">CERTIFICADO DE APROBACIÓN</h1>
                                        <p class="text-[10px] italic opacity-85 md:text-xs">Se otorga el presente reconocimiento a:</p>
                                        <h2
                                            class="border-current/30 inline-block min-w-[200px] border-b py-1 text-lg font-bold uppercase md:text-2xl"
                                        >
                                            {{ userName }}
                                        </h2>
                                        <p class="mt-2 text-[10px] italic opacity-85 md:text-xs">
                                            Por haber aprobado satisfactoriamente el curso de especialización de:
                                        </p>
                                        <h3 class="text-xs font-bold uppercase tracking-wide md:text-sm">
                                            {{ course.title }}
                                        </h3>
                                    </div>

                                    <div class="border-current/10 flex items-end justify-between border-t pt-4 text-[8px] opacity-75 md:text-[10px]">
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
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-md transition-all hover:scale-[1.02] hover:bg-on-background hover:shadow-lg active:scale-95"
                        >
                            Descargar Certificado Oficial
                            <Download class="h-4 w-4" />
                        </a>
                        <span
                            v-else
                            class="inline-flex rounded-xl border bg-surface-container px-6 py-3 text-xs font-bold uppercase italic tracking-wider text-on-surface-variant/60"
                        >
                            Generando archivo de certificado...
                        </span>
                    </div>

                    <!-- Next Steps & Share (Fills bottom empty space of the card) -->
                    <div class="mx-auto mt-6 grid max-w-xl grid-cols-1 gap-6 border-t border-outline-variant/15 pt-5 text-left sm:grid-cols-2">
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-on-background">Comparte tu logro</h4>
                            <p class="text-[11px] font-medium leading-relaxed text-on-surface-variant/70">
                                ¡Comparte tu acreditación oficial en tu perfil de LinkedIn o añade la certificación a tu currículum académico!
                            </p>
                            <a
                                href="https://www.linkedin.com"
                                target="_blank"
                                class="mt-2 inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-primary hover:text-on-background"
                            >
                                Compartir en LinkedIn <ArrowRight class="h-3 w-3" />
                            </a>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-on-background">¿Qué sigue ahora?</h4>
                            <p class="text-[11px] font-medium leading-relaxed text-on-surface-variant/70">
                                Sigue ampliando tus horizontes profesionales explorando nuestros otros programas y cursos especializados.
                            </p>
                            <Link
                                :href="route('student.explore.courses')"
                                class="mt-2 inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-primary hover:text-on-background"
                            >
                                Explorar Más Cursos <ArrowRight class="h-3 w-3" />
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- State: Max Attempts Exhausted (Failed) -->
                <template v-else-if="quizStats?.attempts_left === 0">
                    <div class="space-y-6 py-12">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-rose-100 bg-rose-50">
                            <XCircle class="h-8 w-8 text-rose-600" />
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-xl font-bold text-on-background md:text-2xl">Intentos Agotados</h2>
                            <p class="mx-auto max-w-md text-sm leading-relaxed text-on-surface-variant/70">
                                Has completado la evaluación final del curso, pero no has alcanzado la nota mínima requerida para aprobar en los
                                intentos permitidos.
                            </p>
                        </div>
                        <div class="mx-auto max-w-md border-t border-outline-variant/10 pt-4 text-xs text-on-surface-variant/50">
                            Por favor, comunícate con la coordinación académica a través de soporte para habilitar una opción de recuperación.
                        </div>
                    </div>
                </template>

                <!-- State: Eligible to Take Exam (All Completed) -->
                <template v-else-if="localAllCompleted">
                    <div class="space-y-6 py-10">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-primary/20 bg-primary/5">
                            <Award class="h-8 w-8 text-primary" />
                        </div>
                        <div class="space-y-3">
                            <h2 class="text-xl font-bold text-on-background md:text-2xl">Evaluación de Acreditación</h2>
                            <p class="mx-auto max-w-md text-sm leading-relaxed text-on-surface-variant/70">
                                ¡Excelente progreso! Has visualizado el 100% del currículo académico. Ahora estás habilitado para rendir la evaluación
                                y certificar tus conocimientos.
                            </p>
                        </div>
                        <div class="pt-4">
                            <Link
                                v-if="course.quizzes?.length"
                                :href="route('student.exams.take', { quiz: course.quizzes[0].id })"
                                class="group inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-md transition-all hover:scale-[1.02] hover:bg-on-background active:scale-95"
                            >
                                Iniciar Examen Final
                                <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            </Link>
                            <span v-else class="text-xs italic text-on-surface-variant/40">La evaluación no está disponible en este momento.</span>
                        </div>
                    </div>
                </template>

                <!-- State: Locked (Missing Class Views) -->
                <template v-else>
                    <div class="space-y-6 py-10">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-orange-100 bg-orange-50">
                            <AlertCircle class="h-8 w-8 text-orange-500" />
                        </div>
                        <div class="space-y-3">
                            <h2 class="text-xl font-bold text-on-background md:text-2xl">Acreditación en Espera</h2>
                            <p class="mx-auto max-w-md text-sm leading-relaxed text-on-surface-variant/70">
                                Aún no has completado la visualización de todos los módulos curriculares del curso. Completa el temario restante para
                                desbloquear la evaluación final.
                            </p>
                        </div>

                        <div class="flex flex-col items-center gap-2 pt-4">
                            <div class="flex items-center gap-4 rounded-2xl border border-outline-variant/10 bg-surface-container-low px-5 py-3">
                                <div class="text-base font-bold tabular-nums text-primary">
                                    {{ localCompletedLength }} / {{ allLessonsCount }} Clases
                                </div>
                                <div class="h-1.5 w-32 overflow-hidden rounded-full bg-outline-variant/30">
                                    <div
                                        class="h-full rounded-full bg-primary"
                                        :style="{ width: `${(localCompletedLength / allLessonsCount) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
