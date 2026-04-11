<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage, Link as InertiaLink } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Trophy, XCircle, Download, RotateCcw, ArrowRight, Clock, CheckCircle2 } from 'lucide-vue-next';

interface Answer {
    id: number;
    answer_text: string;
}

interface Question {
    id: number;
    question: string;
    answers: Answer[];
}

interface Quiz {
    id: number;
    title: string;
    course?: { title: string };
    time_limit: number;
    max_attempts: number;
    questions: Question[];
}

const props = defineProps<{
    quiz: Quiz;
    current_attempt: number;
}>();

const page = usePage() as any;
const flashResult = computed(() => page.props.flash?.exam_result);
const localResult = ref<any>(null);

const examResult = computed(() => flashResult.value || localResult.value);
const showResultModal = computed(() => !!examResult.value);

// Persistence logic
const storageKey = computed(() => `iie_exam_state_${props.quiz.id}_${page.props.auth?.user?.id}`);

// Reactive State
const currentQuestionIndex = ref(0);
const selectedAnswers = ref<Record<number, number>>({});
const isSubmitting = ref(false);
const timeRemaining = ref(props.quiz.time_limit * 60);
const showConfirmModal = ref(false); 
let timer: any = null;

const saveState = () => {
    if (examResult.value) return; 
    const state = {
        answers: selectedAnswers.value,
        index: currentQuestionIndex.value,
        time: timeRemaining.value,
        timestamp: Date.now()
    };
    localStorage.setItem(storageKey.value, JSON.stringify(state));
};

const loadState = () => {
    const saved = localStorage.getItem(storageKey.value);
    if (saved) {
        try {
            const state = JSON.parse(saved);
            if (Date.now() - state.timestamp < 2 * 60 * 60 * 1000) {
                selectedAnswers.value = state.answers || {};
                currentQuestionIndex.value = state.index || 0;
                timeRemaining.value = state.time || props.quiz.time_limit * 60;
            }
        } catch (e) { }
    }
    const savedResult = localStorage.getItem(`${storageKey.value}_result`);
    if (savedResult) {
        try { localResult.value = JSON.parse(savedResult); } catch (e) {}
    }
};

const clearState = () => {
    localStorage.removeItem(storageKey.value);
    localStorage.removeItem(`${storageKey.value}_result`);
    localResult.value = null;
};

// Modal State
const showModal = ref(false);
const modalType = ref<'submit' | 'timeup'>('submit');
const modalTitle = ref('');
const modalMessage = ref('');
const onConfirmModal = ref<(() => void) | null>(null);

const formattedTime = computed(() => {
    const mins = Math.floor(timeRemaining.value / 60);
    const secs = timeRemaining.value % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

onMounted(() => {
    loadState();
    if (props.quiz.questions.length > 0 && !examResult.value) {
        timer = setInterval(() => {
            if (timeRemaining.value > 0) {
                timeRemaining.value--;
                if (timeRemaining.value % 5 === 0) saveState();
            } else {
                clearInterval(timer);
                triggerTimeUp();
            }
        }, 1000);
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const triggerTimeUp = () => {
    modalType.value = 'timeup';
    modalTitle.value = '¡Tiempo Agotado!';
    modalMessage.value = 'El tiempo para realizar esta evaluación ha finalizado. Tus respuestas actuales serán enviadas automáticamente.';
    onConfirmModal.value = () => {
        showModal.value = false;
        performSubmit();
    };
    showModal.value = true;
};

const performSubmit = () => {
    isSubmitting.value = true;
    if (timer) clearInterval(timer);
    router.post(route('student.exams.submit', { quiz: props.quiz.id }), {
        answers: selectedAnswers.value
    }, {
        onSuccess: () => {
            isSubmitting.value = false;
            if (flashResult.value) {
                localStorage.setItem(`${storageKey.value}_result`, JSON.stringify(flashResult.value));
            }
            localStorage.removeItem(storageKey.value);
        },
        onError: () => {
             isSubmitting.value = false;
        }
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Exámenes', href: '/student/exams' },
    { title: props.quiz.title, href: '#' },
];

const selectAnswer = (questionId: number, answerId: number) => {
    if (examResult.value) return;
    selectedAnswers.value[questionId] = answerId;
    saveState();
};

const nextQuestion = () => {
    if (currentQuestionIndex.value < props.quiz.questions.length - 1) {
        currentQuestionIndex.value++;
        saveState();
    }
};

const prevQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
        saveState();
    }
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
        performSubmit();
    };
    showModal.value = true;
};
</script>

<template>
    <Head :title="`${quiz.title} - Evaluación`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-[calc(100svh-4rem)] bg-surface-container-low flex flex-col items-center justify-center p-6 md:p-12 overflow-y-auto">
            
            <div class="w-full max-w-4xl max-h-full flex flex-col bg-white rounded-[3rem] shadow-xl border border-outline-variant/20 overflow-hidden relative">
                <!-- Header -->
                <div class="p-8 border-b border-outline-variant/30 flex justify-between items-center bg-surface-container-lowest shrink-0">
                    <div>
                        <p class="text-xs font-bold text-primary uppercase tracking-widest">{{ quiz.course?.title || 'Evaluación' }}</p>
                        <h2 class="text-2xl font-serif font-bold italic mt-1">{{ quiz.title }}</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex px-5 py-2 bg-indigo-50 rounded-full font-bold text-indigo-700 items-center justify-center text-xs">
                            Intento {{ current_attempt }} de {{ quiz.max_attempts }}
                        </div>
                        <div class="px-5 py-2 bg-red-50 rounded-full font-bold text-red-600 flex items-center gap-2" :class="timeRemaining < 60 ? 'animate-pulse' : ''">
                            <Clock class="w-4 h-4" />
                            <span class="tabular-nums font-mono text-lg tracking-wider">{{ formattedTime }}</span>
                        </div>
                        <div class="hidden sm:block px-5 py-2.5 bg-primary/10 rounded-full font-bold text-primary tabular-nums text-xs">
                            Pregunta {{ currentQuestionIndex + 1 }} de {{ quiz.questions.length }}
                        </div>
                    </div>
                </div>

                <!-- Content container -->
                <div class="flex-1 overflow-y-auto p-8 md:p-12 custom-scrollbar" v-if="quiz.questions.length > 0">
                    <div class="max-w-2xl mx-auto space-y-8 animate-in fade-in slide-in-from-right-4 duration-300" :key="currentQuestionIndex">
                        <h3 class="text-2xl font-bold text-on-surface leading-snug font-serif italic">
                            {{ quiz.questions[currentQuestionIndex].question }}
                        </h3>

                        <div class="space-y-4">
                            <button
                                v-for="(ans, i) in quiz.questions[currentQuestionIndex].answers"
                                :key="ans.id"
                                @click="selectAnswer(quiz.questions[currentQuestionIndex].id, ans.id)"
                                class="w-full flex items-center p-6 rounded-3xl border-2 transition-all group text-left shadow-sm"
                                :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id 
                                ? 'border-primary bg-primary/5' 
                                : 'border-outline-variant/30 hover:border-primary/40 bg-white'"
                            >
                                <div class="w-10 h-10 rounded-2xl border-2 flex items-center justify-center mr-5 transition-colors shrink-0" 
                                     :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'border-primary bg-primary text-white' : 'border-outline-variant/40 group-hover:border-primary/50 text-transparent'">
                                    <CheckCircle2 class="w-5 h-5" />
                                </div>
                                <span class="text-lg text-on-surface font-medium leading-relaxed group-hover:text-primary transition-colors pr-4" :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'text-primary' : ''">
                                    {{ ans.answer_text }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 flex items-center justify-center p-12 text-center" v-else>
                     <p class="text-on-surface-variant font-serif italic text-xl">Este examen aún no tiene preguntas configuradas por el docente.</p>
                </div>

                <!-- Footer nav -->
                <div class="p-6 border-t border-outline-variant/30 bg-surface-container-lowest shrink-0 flex items-center" :class="currentQuestionIndex > 0 ? 'justify-between' : 'justify-end'">
                    <button 
                        v-if="currentQuestionIndex > 0"
                        @click="prevQuestion"
                        class="px-8 py-4 text-on-surface-variant font-bold text-xs uppercase tracking-widest hover:text-primary transition-colors rounded-2xl hover:bg-surface-container"
                    >
                        Anterior
                    </button>
                    
                    <button 
                        v-if="currentQuestionIndex < quiz.questions.length - 1"
                        @click="nextQuestion"
                        class="px-10 py-4 bg-primary text-on-primary font-bold text-xs uppercase tracking-widest rounded-3xl shadow-xl shadow-primary/20 hover:scale-105 transition-all"
                    >
                        Siguiente Pregunta
                    </button>

                    <button 
                        v-if="currentQuestionIndex === quiz.questions.length - 1 && quiz.questions.length > 0"
                        @click="triggerSubmitConfirm"
                        :disabled="isSubmitting"
                        class="px-12 py-4 bg-emerald-600 text-white font-bold text-xs uppercase tracking-widest rounded-3xl shadow-xl shadow-emerald-900/20 hover:bg-emerald-700 hover:scale-105 transition-all disabled:opacity-50"
                    >
                        {{ isSubmitting ? 'Calificando...' : 'Finalizar y Entregar' }}
                    </button>
                </div>
            </div>

        </div>

        <!-- Confirm Submission Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white rounded-[3rem] shadow-2xl max-w-md w-full p-10 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-200">
                <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-8">
                    <Clock v-if="modalType === 'timeup'" class="w-12 h-12 text-primary animate-pulse" />
                    <CheckCircle2 v-else class="w-12 h-12 text-primary" />
                </div>
                <h3 class="text-3xl font-serif font-bold italic text-on-surface mb-4">{{ modalTitle }}</h3>
                <p class="text-on-surface-variant leading-relaxed font-serif italic text-lg mb-10">{{ modalMessage }}</p>
                
                <div class="flex flex-col gap-4">
                    <button 
                        @click="onConfirmModal && onConfirmModal()"
                        class="w-full py-5 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 active:scale-95 transition-all uppercase tracking-widest text-xs"
                    >
                        {{ modalType === 'timeup' ? 'Aceptar y Enviar' : 'Sí, calificar ahora' }}
                    </button>
                    <button 
                        v-if="modalType === 'submit'"
                        @click="showModal = false"
                        class="w-full py-5 text-on-surface-variant hover:text-primary font-bold rounded-2xl hover:bg-surface-container-low transition-all uppercase tracking-widest text-xs"
                    >
                        Volver al examen
                    </button>
                </div>
            </div>
        </div>

        <!-- FINAL RESULTS MODAL (Success / Failure) -->
        <div v-if="showResultModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md animate-in fade-in duration-500">
            <div class="bg-white rounded-[4rem] shadow-[0_30px_90px_rgba(0,0,0,0.3)] max-w-xl w-full p-12 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-400 relative overflow-hidden">
                <!-- Abstract Design Bgs -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="w-32 h-32 mx-auto rounded-[2.5rem] flex items-center justify-center mb-10 shadow-inner" :class="examResult.status === 'aprobado' ? 'bg-emerald-50' : 'bg-red-50'">
                        <Trophy v-if="examResult.status === 'aprobado'" class="w-16 h-16 text-emerald-600 animate-bounce" />
                        <XCircle v-else class="w-16 h-16 text-red-600 animate-pulse" />
                    </div>

                    <h3 class="text-4xl lg:text-5xl font-serif font-bold italic text-on-surface mb-4 leading-tight">
                        {{ examResult.status === 'aprobado' ? '¡Magnífica Calificación!' : 'Sigue intentándolo' }}
                    </h3>
                    
                    <p class="text-xl font-serif italic text-on-surface-variant mb-12">
                        Has obtenido un puntaje de <span class="font-bold text-primary text-3xl tabular-nums">{{ examResult.score }}</span> sobre 20. 
                        {{ examResult.status === 'aprobado' ? 'Has superado los estándares académicos necesarios.' : 'No has alcanzado el puntaje mínimo de ' + examResult.passing_score + ' para la acreditación.' }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                        <template v-if="examResult.status === 'aprobado'">
                            <a 
                                v-if="examResult.certificate_url"
                                :href="examResult.certificate_url" 
                                target="_blank"
                                class="w-full sm:w-auto px-10 py-5 bg-emerald-600 text-white font-bold rounded-2xl shadow-xl shadow-emerald-900/20 hover:bg-emerald-700 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-xs"
                            >
                                <Download class="w-4 h-4" /> Mi Certificado
                            </a>
                            <InertiaLink 
                                :href="route('student.courses.index')" 
                                class="w-full sm:w-auto px-10 py-5 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-xs"
                            >
                                Finalizar <ArrowRight class="w-4 h-4" />
                            </InertiaLink>
                        </template>
                        <template v-else>
                            <button 
                                @click="clearState(); router.visit(route('student.exams.index'));"
                                class="w-full sm:w-auto px-10 py-5 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-xs"
                            >
                                <RotateCcw class="w-4 h-4" /> Volver al listado
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
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
