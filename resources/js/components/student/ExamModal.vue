<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { 
    Clock, CheckCircle2, Trophy, XCircle, 
    Download, RotateCcw, ArrowRight, ChevronLeft, 
    ChevronRight, X, AlertCircle 
} from 'lucide-vue-next';

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
    time_limit: number;
    max_attempts: number;
    questions: Question[];
}

const props = defineProps<{
    show: boolean;
    quiz: Quiz;
    courseId: number;
}>();

const emit = defineEmits(['close']);

const page = usePage() as any;
const flashResult = computed(() => page.props.flash?.exam_result);
const localResult = ref<any>(null);

const examResult = computed(() => flashResult.value || localResult.value);

// Persistence logic
const storageKey = computed(() => `iie_exam_state_${props.quiz.id}_${page.props.auth?.user?.id}`);

// Exam State
const currentQuestionIndex = ref(0);
const selectedAnswers = ref<Record<number, number>>({});
const isSubmitting = ref(false);
const timeRemaining = ref(props.quiz.time_limit * 60);
const showConfirmModal = ref(false);
let timer: any = null;

const saveState = () => {
    if (examResult.value) return; // Don't save if we already have a result
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
            const elapsedMs = Date.now() - state.timestamp;
            // Only restore if not too old (e.g., 2 hours)
            if (elapsedMs < 2 * 60 * 60 * 1000) {
                selectedAnswers.value = state.answers || {};
                currentQuestionIndex.value = state.index || 0;
                const savedTime = state.time || props.quiz.time_limit * 60;
                const elapsedSecs = Math.floor(elapsedMs / 1000);
                timeRemaining.value = Math.max(0, savedTime - elapsedSecs);
            }
        } catch (e) { console.error('Error loading exam state', e); }
    }
    
    // Also load last result from storage if flash is empty
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

const formattedTime = computed(() => {
    const mins = Math.floor(timeRemaining.value / 60);
    const secs = timeRemaining.value % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

const startTimer = () => {
    if (timer) clearInterval(timer);
    timer = setInterval(() => {
        if (timeRemaining.value > 0) {
            timeRemaining.value--;
            // Save state every 5 seconds to reduce perf impact or every second for accuracy
            if (timeRemaining.value % 5 === 0) saveState();
        } else {
            clearInterval(timer);
            submitExam();
        }
    }, 1000);
};

const stopTimer = () => {
    if (timer) clearInterval(timer);
};

const selectAnswer = (questionId: number, answerId: number) => {
    if (examResult.value) return;
    selectedAnswers.value[questionId] = answerId;
    saveState();
};

const submitExam = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    stopTimer();
    
    router.post(route('student.exams.submit', { quiz: props.quiz.id }), {
        answers: selectedAnswers.value
    }, {
        onSuccess: () => {
            isSubmitting.value = false;
            showConfirmModal.value = false;
            // Persist the result so F5 doesn't clear it
            if (flashResult.value) {
                localStorage.setItem(`${storageKey.value}_result`, JSON.stringify(flashResult.value));
            }
            // Clear the ongoing attempt state but keep the result URL/Score
            localStorage.removeItem(storageKey.value);
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const resetExam = () => {
    clearState();
    currentQuestionIndex.value = 0;
    selectedAnswers.value = {};
    timeRemaining.value = props.quiz.time_limit * 60;
    router.reload({ 
        only: ['flash', 'quiz'],
        onSuccess: () => {
            startTimer();
        }
    });
};

watch(() => props.show, (newVal) => {
    if (newVal && props.quiz?.questions) {
        loadState();
        if (!examResult.value) {
            startTimer();
        }
    } else {
        stopTimer();
    }
});

onMounted(() => {
    if (props.show && props.quiz?.questions) {
        loadState();
        if (!examResult.value) startTimer();
    }
});

onUnmounted(() => stopTimer());

const nextQuestion = () => {
    if (currentQuestionIndex.value < (props.quiz?.questions?.length || 0) - 1) {
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
</script>

<template>
    <div v-if="show && quiz?.questions" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-300">
        
        <!-- Main Exam Container -->
        <div v-if="!examResult" class="bg-white w-full max-w-4xl h-[95vh] md:h-[90vh] rounded-2xl md:rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
            <!-- Header -->
            <div class="px-4 py-3 md:px-8 md:py-6 bg-surface-container-lowest border-b border-outline-variant/30 flex justify-between items-center shrink-0 gap-2">
                <div class="flex items-center gap-2 md:gap-4 min-w-0">
                    <div class="w-9 h-9 md:w-12 md:h-12 bg-primary/10 rounded-xl md:rounded-2xl flex items-center justify-center shrink-0">
                        <Clock class="w-4 h-4 md:w-6 md:h-6 text-primary" :class="timeRemaining < 60 ? 'animate-pulse text-red-600' : ''" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm md:text-xl font-serif font-bold italic leading-none truncate">{{ quiz.title }}</h2>
                        <p class="text-[8px] md:text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mt-0.5 md:mt-1 hidden sm:block">Evaluación Final</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-6 shrink-0">
                    <div class="flex flex-col items-end">
                        <span class="text-[8px] md:text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5 md:mb-1 hidden sm:block">Tiempo</span>
                        <span class="text-lg md:text-2xl font-mono font-bold tracking-tighter tabular-nums" :class="timeRemaining < 60 ? 'text-red-600' : 'text-primary'">
                            {{ formattedTime }}
                        </span>
                    </div>
                    <button @click="$emit('close')" class="p-2 md:p-3 hover:bg-surface-container rounded-xl md:rounded-2xl transition-colors">
                        <X class="w-5 h-5 md:w-6 md:h-6 text-on-surface-variant" />
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="h-1.5 w-full bg-surface-container-high shrink-0 overflow-hidden">
                <div 
                    class="h-full bg-primary transition-all duration-500"
                    :style="{ width: `${((currentQuestionIndex + 1) / (quiz.questions?.length || 1)) * 100}%` }"
                ></div>
            </div>

            <!-- Question Content -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-12 custom-scrollbar bg-surface-container-lowest">
                <div v-if="quiz.questions?.length > 0" class="max-w-2xl mx-auto space-y-5 md:space-y-8 animate-in fade-in slide-in-from-right-4 duration-300" :key="currentQuestionIndex">
                    <div class="space-y-2 md:space-y-4">
                        <span class="text-[10px] md:text-xs font-bold text-primary uppercase tracking-[0.2em] italic">Pregunta {{ currentQuestionIndex + 1 }} de {{ quiz.questions?.length }}</span>
                        <h3 class="text-xl md:text-3xl font-bold text-on-surface leading-snug font-serif italic text-pretty">
                            {{ quiz.questions[currentQuestionIndex].question }}
                        </h3>
                    </div>

                    <div class="grid gap-3 md:gap-4">
                        <button
                            v-for="(ans, i) in quiz.questions[currentQuestionIndex].answers"
                            :key="ans.id"
                            @click="selectAnswer(quiz.questions[currentQuestionIndex].id, ans.id)"
                            class="w-full flex items-center p-4 md:p-6 rounded-xl md:rounded-[2rem] border-2 transition-all group text-left shadow-sm relative overflow-hidden active:scale-[0.98]"
                            :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id 
                            ? 'border-primary bg-primary/5' 
                            : 'border-outline-variant/30 hover:border-primary/40 bg-white'"
                        >
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl md:rounded-2xl border-2 flex items-center justify-center mr-3 md:mr-5 transition-colors shrink-0" 
                                 :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'border-primary bg-primary text-white' : 'border-outline-variant/40 group-hover:border-primary/50 text-transparent'">
                                <CheckCircle2 class="w-4 h-4 md:w-5 md:h-5" />
                            </div>
                            <span class="text-sm md:text-lg text-on-surface font-medium leading-relaxed group-hover:text-primary transition-colors pr-2 md:pr-4" :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'text-primary' : ''">
                                {{ ans.answer_text }}
                            </span>
                        </button>
                    </div>
                </div>
                <!-- Empty State -->
                <div v-else class="h-full flex flex-col items-center justify-center text-center p-6 md:p-12">
                     <AlertCircle class="w-12 h-12 md:w-16 md:h-16 text-on-surface-variant/20 mb-4" />
                     <p class="text-base md:text-xl font-serif italic text-on-surface-variant">Este examen aún no tiene preguntas configuradas.</p>
                </div>
            </div>

            <!-- Footer Navigation -->
            <div class="px-4 py-3 md:px-8 md:py-6 bg-surface-container-low border-t border-outline-variant/30 shrink-0 flex items-center justify-between gap-2">
                <button 
                    @click="prevQuestion"
                    :disabled="currentQuestionIndex === 0"
                    class="flex items-center gap-1.5 md:gap-2 px-3 py-2.5 md:px-6 md:py-3 text-on-surface-variant font-bold text-[10px] md:text-xs uppercase tracking-widest hover:text-primary transition-all disabled:opacity-30"
                >
                    <ChevronLeft class="w-4 h-4" /> <span class="hidden sm:inline">Anterior</span>
                </button>

                <div class="flex items-center gap-2 md:gap-4">
                    <button 
                        v-if="currentQuestionIndex < (quiz.questions?.length || 0) - 1"
                        @click="nextQuestion"
                        class="px-5 py-3 md:px-8 md:py-4 bg-primary text-on-primary font-bold text-[10px] md:text-xs uppercase tracking-widest rounded-xl md:rounded-2xl shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all flex items-center gap-2 md:gap-3 active:scale-95"
                    >
                        Siguiente <ChevronRight class="w-4 h-4" />
                    </button>

                    <button 
                        v-else-if="quiz.questions?.length > 0"
                        @click="showConfirmModal = true"
                        class="px-5 py-3 md:px-10 md:py-4 bg-emerald-600 text-white font-bold text-[10px] md:text-xs uppercase tracking-widest rounded-xl md:rounded-2xl shadow-xl shadow-emerald-900/10 hover:bg-emerald-700 transition-all active:scale-95 flex items-center gap-2 md:gap-3"
                    >
                        Finalizar <ArrowRight class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- RESULTS VIEW -->
        <div v-else class="bg-white rounded-2xl md:rounded-[4rem] shadow-2xl max-w-xl w-full p-6 md:p-12 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-500 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="w-20 h-20 md:w-32 md:h-32 mx-auto rounded-2xl md:rounded-[2.5rem] flex items-center justify-center mb-6 md:mb-10 shadow-inner" :class="examResult.status === 'aprobado' ? 'bg-emerald-50' : 'bg-red-50'">
                    <Trophy v-if="examResult.status === 'aprobado'" class="w-10 h-10 md:w-16 md:h-16 text-emerald-600 animate-bounce" />
                    <XCircle v-else class="w-10 h-10 md:w-16 md:h-16 text-red-600 animate-pulse" />
                </div>

                <h3 class="text-2xl md:text-4xl font-serif font-bold italic text-on-surface mb-2 leading-tight">
                    {{ examResult.status === 'aprobado' ? '¡Excelente Trabajo!' : 'Sigue intentándolo' }}
                </h3>
                <p class="text-sm md:text-lg font-serif italic text-on-surface-variant mb-6 md:mb-10 uppercase tracking-widest font-bold">Nota Final: <span class="text-primary text-2xl md:text-3xl">{{ examResult.score }}</span>/20</p>
                
                <p class="text-sm md:text-xl font-serif italic text-on-surface-variant mb-8 md:mb-12 leading-relaxed">
                    {{ examResult.status === 'aprobado' 
                        ? 'Has superado satisfactoriamente los estándares académicos requeridos para esta certificación.' 
                        : 'No has alcanzado el puntaje mínimo de ' + examResult.passing_score + '. Revisa los materiales y vuelve a intentarlo.' }}
                </p>

                <div class="flex flex-col gap-3 md:gap-4">
                    <template v-if="examResult.status === 'aprobado'">
                        <a 
                            v-if="examResult.certificate_url"
                            :href="examResult.certificate_url" 
                            target="_blank"
                            class="w-full py-4 md:py-5 bg-emerald-600 text-white font-bold rounded-xl md:rounded-2xl shadow-xl shadow-emerald-900/20 hover:bg-emerald-700 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] md:text-xs active:scale-95"
                        >
                            <Download class="w-4 h-4" /> Descargar Certificado
                        </a>
                        <button 
                            @click="$emit('close')" 
                            class="w-full py-4 md:py-5 bg-primary text-white font-bold rounded-xl md:rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all uppercase tracking-widest text-[10px] md:text-xs active:scale-95"
                        >
                            Volver al curso
                        </button>
                    </template>
                    <template v-else>
                        <button 
                            @click="clearState(); router.visit(route('student.exams.index'));"
                            class="w-full py-4 md:py-5 bg-primary text-white font-bold rounded-xl md:rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] md:text-xs active:scale-95"
                        >
                            <RotateCcw class="w-4 h-4" /> Volver al panel
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Confirm Submit Mini Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white rounded-2xl md:rounded-[3rem] shadow-2xl max-w-sm w-full p-6 md:p-10 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-200">
                <div class="w-14 h-14 md:w-20 md:h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4 md:mb-6">
                    <AlertCircle class="w-7 h-7 md:w-10 md:h-10 text-primary" />
                </div>
                <h3 class="text-xl md:text-2xl font-serif font-bold italic text-on-surface mb-2 md:mb-3">¿Confirmar entrega?</h3>
                <p class="text-on-surface-variant leading-relaxed font-serif italic text-sm md:text-base mb-6 md:mb-8">
                    Has respondido {{ Object.keys(selectedAnswers).length }} de {{ quiz.questions?.length }} preguntas. Una vez enviado, no podrás cambiar tus respuestas.
                </p>
                
                <div class="flex flex-col gap-3">
                    <button 
                        @click="submitExam"
                        :disabled="isSubmitting"
                        class="w-full py-3.5 md:py-4 bg-primary text-white font-bold rounded-xl md:rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all uppercase tracking-widest text-[10px] active:scale-95"
                    >
                        {{ isSubmitting ? 'Calificando...' : 'Sí, calificar ahora' }}
                    </button>
                    <button 
                        @click="showConfirmModal = false"
                        class="w-full py-3.5 md:py-4 text-on-surface-variant hover:text-primary font-bold rounded-xl md:rounded-2xl transition-all uppercase tracking-widest text-[10px]"
                    >
                        Continuar respondiendo
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.1);
    border-radius: 10px;
}
</style>
