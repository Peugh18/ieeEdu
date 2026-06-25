<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import { useExamSession } from '@/composables/student/useExamSession';
import AppLayout from '@/layouts/AppLayout.vue';
import { SharedData } from '@/types';
import { Quiz } from '@/types/exam';
import { Head, Link as InertiaLink, router, usePage } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, Clock, Download, RotateCcw, Trophy, XCircle } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps<{
    quiz: Quiz;
    current_attempt: number;
}>();

const page = usePage<SharedData>();

const {
    currentQuestionIndex,
    selectedAnswers,
    isSubmitting,
    timeRemaining,
    examResult,
    formattedTime,
    loadState,
    clearState,
    startTimer,
    selectAnswer,
    nextQuestion,
    prevQuestion,
    submitExam,
} = useExamSession(props.quiz);

const showResultModal = computed(() => !!examResult.value);

// Modal UI State
const showModal = ref(false);
const modalType = ref<'submit' | 'timeup'>('submit');
const modalTitle = ref('');
const modalMessage = ref('');
const onConfirmModal = ref<(() => void) | null>(null);

const triggerTimeUp = () => {
    modalType.value = 'timeup';
    modalTitle.value = '¡Tiempo Agotado!';
    modalMessage.value = 'El tiempo para realizar esta evaluación ha finalizado. Tus respuestas actuales serán enviadas automáticamente.';
    onConfirmModal.value = () => {
        showModal.value = false;
        submitExam();
    };
    showModal.value = true;
};

const triggerSubmitConfirm = () => {
    if (isSubmitting.value) return;

    const totalQuestions = props.quiz.questions.length;
    const answeredCount = Object.keys(selectedAnswers.value).length;

    modalType.value = 'submit';
    if (answeredCount < totalQuestions) {
        modalTitle.value = 'Examen Incompleto';
        modalMessage.value = `¡Atención! Solo has respondido ${answeredCount} de ${totalQuestions} preguntas. ¿Seguro que quieres entregar sin completar todo?`;
    } else {
        modalTitle.value = 'Confirmar Entrega';
        modalMessage.value = '¡Excelente! Has respondido todas las preguntas. ¿Estás seguro de enviar tus respuestas para su calificación final?';
    }

    onConfirmModal.value = () => {
        showModal.value = false;
        submitExam();
    };
    showModal.value = true;
};

// Error handling logic
const showLocalError = ref(false);
const localErrorMessage = ref('');

watch(
    () => page.props.flash?.error,
    (newVal) => {
        if (newVal) {
            localErrorMessage.value = newVal;
            showLocalError.value = true;
        }
    },
    { immediate: true },
);

const clearFlashErrorAndRedirect = () => {
    showLocalError.value = false;
    clearState();
    if (props.quiz.course?.slug) {
        router.visit(route('student.classroom', { course: props.quiz.course.slug }));
    } else {
        router.visit(route('student.exams.index'));
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Exámenes', href: '/student/exams' },
    { title: props.quiz.title, href: '#' },
];

onMounted(() => {
    loadState();
    if (props.quiz.questions.length > 0 && !examResult.value) {
        startTimer(() => triggerTimeUp());
    }
});
</script>

<template>
    <Head :title="`${quiz.title} - Evaluación`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-[calc(100svh-4rem)] flex-col items-center justify-center overflow-y-auto bg-surface-container-low p-6 md:p-12">
            <div
                class="relative flex max-h-full w-full max-w-4xl flex-col overflow-hidden rounded-[3rem] border border-outline-variant/20 bg-white shadow-xl"
            >
                <!-- Header -->
                <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/30 bg-surface-container-lowest p-8">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-primary">{{ quiz.course?.title || 'Evaluación' }}</p>
                        <h2 class="mt-1 font-serif text-2xl font-bold italic">{{ quiz.title }}</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="hidden items-center justify-center rounded-full bg-indigo-50 px-5 py-2 text-xs font-bold text-indigo-700 sm:flex">
                            Intento {{ current_attempt }} de {{ quiz.max_attempts }}
                        </div>
                        <div
                            class="flex items-center gap-2 rounded-full bg-red-50 px-5 py-2 font-bold text-red-600"
                            :class="timeRemaining < 60 ? 'animate-pulse' : ''"
                        >
                            <Clock class="h-4 w-4" />
                            <span class="font-mono text-lg tabular-nums tracking-wider">{{ formattedTime }}</span>
                        </div>
                        <div class="hidden rounded-full bg-primary/10 px-5 py-2.5 text-xs font-bold tabular-nums text-primary sm:block">
                            Pregunta {{ currentQuestionIndex + 1 }} de {{ quiz.questions.length }}
                        </div>
                    </div>
                </div>

                <!-- Content container -->
                <div class="custom-scrollbar flex-1 overflow-y-auto p-8 md:p-12" v-if="quiz.questions.length > 0">
                    <div class="mx-auto max-w-2xl space-y-8 duration-300 animate-in fade-in slide-in-from-right-4" :key="currentQuestionIndex">
                        <h3 class="font-serif text-2xl font-bold italic leading-snug text-on-surface">
                            {{ quiz.questions[currentQuestionIndex].question }}
                        </h3>

                        <div class="space-y-4">
                            <button
                                v-for="ans in quiz.questions[currentQuestionIndex].answers"
                                :key="ans.id"
                                @click="selectAnswer(quiz.questions[currentQuestionIndex].id, ans.id)"
                                class="group flex w-full items-center rounded-3xl border-2 p-6 text-left shadow-sm transition-all"
                                :class="
                                    selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id
                                        ? 'border-primary bg-primary/5'
                                        : 'border-outline-variant/30 bg-white hover:border-primary/40'
                                "
                            >
                                <div
                                    class="mr-5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border-2 transition-colors"
                                    :class="
                                        selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id
                                            ? 'border-primary bg-primary text-white'
                                            : 'border-outline-variant/40 text-transparent group-hover:border-primary/50'
                                    "
                                >
                                    <CheckCircle2 class="h-5 w-5" />
                                </div>
                                <span
                                    class="pr-4 text-lg font-medium leading-relaxed text-on-surface transition-colors group-hover:text-primary"
                                    :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'text-primary' : ''"
                                >
                                    {{ ans.answer_text }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-1 items-center justify-center p-12 text-center" v-else>
                    <p class="font-serif text-xl italic text-on-surface-variant">Este examen aún no tiene preguntas configuradas por el docente.</p>
                </div>

                <!-- Footer nav -->
                <div
                    class="flex shrink-0 items-center border-t border-outline-variant/30 bg-surface-container-lowest p-6"
                    :class="currentQuestionIndex > 0 ? 'justify-between' : 'justify-end'"
                >
                    <button
                        v-if="currentQuestionIndex > 0"
                        @click="prevQuestion"
                        class="rounded-2xl px-8 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant transition-colors hover:bg-surface-container hover:text-primary"
                    >
                        Anterior
                    </button>

                    <button
                        v-if="currentQuestionIndex < quiz.questions.length - 1"
                        @click="nextQuestion"
                        class="rounded-3xl bg-primary px-10 py-4 text-xs font-bold uppercase tracking-widest text-on-primary shadow-xl shadow-primary/20 transition-all hover:scale-105"
                    >
                        Siguiente Pregunta
                    </button>

                    <button
                        v-if="currentQuestionIndex === quiz.questions.length - 1 && quiz.questions.length > 0"
                        @click="triggerSubmitConfirm"
                        :disabled="isSubmitting"
                        class="rounded-3xl bg-emerald-600 px-12 py-4 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-emerald-900/20 transition-all hover:scale-105 hover:bg-emerald-700 disabled:opacity-50"
                    >
                        {{ isSubmitting ? 'Calificando...' : 'Finalizar y Entregar' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirm Submission Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm duration-200 animate-in fade-in"
        >
            <div
                class="w-full max-w-md rounded-[3rem] border border-outline-variant/10 bg-white p-10 text-center shadow-2xl duration-200 animate-in zoom-in-95"
            >
                <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-primary/10">
                    <Clock v-if="modalType === 'timeup'" class="h-12 w-12 animate-pulse text-primary" />
                    <CheckCircle2 v-else class="h-12 w-12 text-primary" />
                </div>
                <h3 class="mb-4 font-serif text-3xl font-bold italic text-on-surface">{{ modalTitle }}</h3>
                <p class="mb-10 font-serif text-lg italic leading-relaxed text-on-surface-variant">{{ modalMessage }}</p>

                <div class="flex flex-col gap-4">
                    <button
                        @click="onConfirmModal && onConfirmModal()"
                        class="w-full rounded-2xl bg-primary py-5 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition-all hover:opacity-90 active:scale-95"
                    >
                        {{ modalType === 'timeup' ? 'Aceptar y Enviar' : 'Sí, calificar ahora' }}
                    </button>
                    <button
                        v-if="modalType === 'submit'"
                        @click="showModal = false"
                        class="w-full rounded-2xl py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant transition-all hover:bg-surface-container-low hover:text-primary"
                    >
                        Volver al examen
                    </button>
                </div>
            </div>
        </div>

        <!-- FINAL RESULTS MODAL (Success / Failure) -->
        <div
            v-if="showResultModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 p-4 backdrop-blur-md duration-500 animate-in fade-in"
        >
            <div
                class="duration-400 relative w-full max-w-xl overflow-hidden rounded-[4rem] border border-outline-variant/10 bg-white p-12 text-center shadow-[0_30px_90px_rgba(0,0,0,0.3)] animate-in zoom-in-95"
            >
                <!-- Abstract Design Bgs -->
                <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>

                <div class="relative z-10">
                    <div
                        class="mx-auto mb-10 flex h-32 w-32 items-center justify-center rounded-[2.5rem] shadow-inner"
                        :class="examResult.status === 'aprobado' ? 'bg-emerald-50' : 'bg-red-50'"
                    >
                        <Trophy v-if="examResult.status === 'aprobado'" class="h-16 w-16 animate-bounce text-emerald-600" />
                        <XCircle v-else class="h-16 w-16 animate-pulse text-red-600" />
                    </div>

                    <h3 class="mb-4 font-serif text-4xl font-bold italic leading-tight text-on-surface lg:text-5xl">
                        {{ examResult.status === 'aprobado' ? '¡Magnífica Calificación!' : 'Sigue intentándolo' }}
                    </h3>

                    <p class="mb-12 font-serif text-xl italic text-on-surface-variant">
                        Has obtenido un puntaje de <span class="text-3xl font-bold tabular-nums text-primary">{{ examResult.score }}</span> sobre 20.
                        {{
                            examResult.status === 'aprobado'
                                ? 'Has superado los estándares académicos necesarios.'
                                : 'No has alcanzado el puntaje mínimo de ' + examResult.passing_score + ' para la acreditación.'
                        }}
                    </p>

                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <template v-if="examResult.status === 'aprobado'">
                            <a
                                v-if="examResult.certificate_url"
                                :href="examResult.certificate_url"
                                target="_blank"
                                class="flex w-full items-center justify-center gap-3 rounded-2xl bg-emerald-600 px-10 py-5 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-emerald-900/20 transition-all hover:bg-emerald-700 sm:w-auto"
                            >
                                <Download class="h-4 w-4" /> Mi Certificado
                            </a>
                            <InertiaLink
                                :href="route('student.courses.index')"
                                class="flex w-full items-center justify-center gap-3 rounded-2xl bg-primary px-10 py-5 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition-all hover:opacity-90 sm:w-auto"
                            >
                                Finalizar <ArrowRight class="h-4 w-4" />
                            </InertiaLink>
                        </template>
                        <template v-else>
                            <button
                                @click="
                                    clearState();
                                    quiz.course?.slug
                                        ? router.visit(route('student.classroom', { course: quiz.course.slug }))
                                        : router.visit(route('student.exams.index'));
                                "
                                class="flex w-full items-center justify-center gap-3 rounded-2xl bg-primary px-10 py-5 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition-all hover:opacity-90 sm:w-auto"
                            >
                                <RotateCcw class="h-4 w-4" /> Volver al curso
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div
            v-if="showLocalError"
            class="fixed inset-0 z-[120] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm duration-200 animate-in fade-in"
        >
            <div
                class="w-full max-w-md rounded-[3rem] border border-outline-variant/10 bg-white p-10 text-center shadow-2xl duration-200 animate-in zoom-in-95"
            >
                <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-red-100">
                    <XCircle class="h-12 w-12 animate-pulse text-red-600" />
                </div>
                <h3 class="mb-4 font-serif text-3xl font-bold italic text-on-surface">Error</h3>
                <p class="mb-10 font-serif text-lg italic leading-relaxed text-on-surface-variant">{{ localErrorMessage }}</p>

                <div class="flex flex-col gap-4">
                    <button
                        @click="clearFlashErrorAndRedirect"
                        class="w-full rounded-2xl bg-primary py-5 text-xs font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition-all hover:opacity-90 active:scale-95"
                    >
                        Volver al curso
                    </button>
                </div>
            </div>
        </div>
        <BottomNav />
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.15);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.25);
}
</style>
