import { SharedData } from '@/types';
import { ExamResult, Quiz } from '@/types/exam';
import { router, usePage } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';

export function useExamSession(quiz: Quiz) {
    const page = usePage<SharedData>();

    const storageKey = computed(() => {
        const userId = page.props.auth?.user?.id || 0;
        return `iie_exam_state_${quiz.id}_${userId}`;
    });

    const flashResult = computed<ExamResult | null>(() => page.props.flash?.exam_result || null);
    const localResult = ref<ExamResult | null>(null);
    const examResult = computed(() => flashResult.value || localResult.value);

    const currentQuestionIndex = ref(0);
    const selectedAnswers = ref<Record<number, number>>({});
    const isSubmitting = ref(false);
    const timeRemaining = ref(quiz.time_limit * 60);
    let timer: ReturnType<typeof setInterval> | null = null;

    const saveState = () => {
        if (examResult.value) return;
        const state = {
            answers: selectedAnswers.value,
            index: currentQuestionIndex.value,
            time: timeRemaining.value,
            timestamp: Date.now(),
        };
        localStorage.setItem(storageKey.value, JSON.stringify(state));
    };

    const loadState = () => {
        const saved = localStorage.getItem(storageKey.value);
        if (saved) {
            try {
                const state = JSON.parse(saved);
                const elapsedMs = Date.now() - state.timestamp;
                if (elapsedMs < 2 * 60 * 60 * 1000) {
                    selectedAnswers.value = state.answers || {};
                    currentQuestionIndex.value = state.index || 0;
                    const savedTime = state.time ?? quiz.time_limit * 60;
                    const elapsedSecs = Math.floor(elapsedMs / 1000);
                    timeRemaining.value = Math.max(0, savedTime - elapsedSecs);
                }
            } catch (_e) {
                console.error('Error loading exam state', _e);
            }
        }

        const savedResult = localStorage.getItem(`${storageKey.value}_result`);
        if (savedResult) {
            try {
                localResult.value = JSON.parse(savedResult);
            } catch (_e) {}
        }
    };

    const clearState = () => {
        localStorage.removeItem(storageKey.value);
        localStorage.removeItem(`${storageKey.value}_result`);
        localResult.value = null;
    };

    const selectAnswer = (questionId: number, answerId: number) => {
        if (examResult.value) return;
        selectedAnswers.value[questionId] = answerId;
        saveState();
    };

    const nextQuestion = () => {
        if (currentQuestionIndex.value < quiz.questions.length - 1) {
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

    const formattedTime = computed(() => {
        const mins = Math.floor(timeRemaining.value / 60);
        const secs = timeRemaining.value % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    });

    const startTimer = (onTimeUp?: () => void) => {
        if (timer) clearInterval(timer);
        timer = setInterval(() => {
            if (timeRemaining.value > 0) {
                timeRemaining.value--;
                if (timeRemaining.value % 5 === 0) saveState();
            } else {
                clearInterval(timer!);
                if (onTimeUp) {
                    onTimeUp();
                } else {
                    submitExam();
                }
            }
        }, 1000);
    };

    const stopTimer = () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    };

    const submitExam = (options?: { onSuccess?: (result: ExamResult | null) => void; onError?: () => void }) => {
        if (isSubmitting.value) return;
        isSubmitting.value = true;
        stopTimer();

        router.post(
            route('student.exams.submit', { quiz: quiz.id }),
            {
                answers: selectedAnswers.value,
            },
            {
                onSuccess: () => {
                    isSubmitting.value = false;
                    if (flashResult.value) {
                        localStorage.setItem(`${storageKey.value}_result`, JSON.stringify(flashResult.value));
                    }
                    localStorage.removeItem(storageKey.value);
                    if (options?.onSuccess) {
                        options.onSuccess(flashResult.value);
                    }
                },
                onError: () => {
                    isSubmitting.value = false;
                    if (options?.onError) {
                        options.onError();
                    }
                },
            },
        );
    };

    onUnmounted(() => {
        stopTimer();
    });

    return {
        currentQuestionIndex,
        selectedAnswers,
        isSubmitting,
        timeRemaining,
        examResult,
        formattedTime,
        saveState,
        loadState,
        clearState,
        startTimer,
        stopTimer,
        selectAnswer,
        nextQuestion,
        prevQuestion,
        submitExam,
    };
}
