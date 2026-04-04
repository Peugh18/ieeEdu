<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { BookOpen, CheckCircle2, Clock } from 'lucide-vue-next';

const props = defineProps<{
    quiz: any;
    current_attempt: number;
}>();

const currentQuestionIndex = ref(0);
const selectedAnswers = ref<Record<number, number>>({});
const isSubmitting = ref(false);

const timeRemaining = ref(props.quiz.time_limit ? props.quiz.time_limit * 60 : 30 * 60);
let timer: any = null;

const showModal = ref(false);
const modalTitle = ref('');
const modalMessage = ref('');
const modalType = ref<'submit' | 'timeup'>('submit');
const onConfirmModal = ref<(() => void) | null>(null);

const performSubmit = () => {
    isSubmitting.value = true;
    if (timer) clearInterval(timer);
    router.post(route('student.exams.submit', props.quiz.id), {
        answers: selectedAnswers.value
    });
};

onMounted(() => {
    timer = setInterval(() => {
        if (timeRemaining.value > 0) {
            timeRemaining.value--;
        } else {
            clearInterval(timer);
            modalType.value = 'timeup';
            modalTitle.value = '¡El tiempo ha terminado!';
            modalMessage.value = 'Se acabó el tiempo asignado. Tus respuestas guardadas se enviarán automáticamente para su calificación.';
            onConfirmModal.value = () => {
                showModal.value = false;
                performSubmit();
            };
            showModal.value = true;
            
            // Automaically submit after 4 seconds if they are AFK
            setTimeout(() => {
                if(showModal.value && modalType.value === 'timeup') {
                    performSubmit();
                }
            }, 4000);
        }
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const formattedTime = computed(() => {
    const min = Math.floor(timeRemaining.value / 60);
    const sec = timeRemaining.value % 60;
    return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Exámenes', href: '/student/exams' },
    { title: props.quiz.title, href: '#' },
];

const selectAnswer = (questionId: number, answerId: number) => {
    selectedAnswers.value[questionId] = answerId;
};

const nextQuestion = () => {
    if (currentQuestionIndex.value < props.quiz.questions.length - 1) {
        currentQuestionIndex.value++;
    }
};

const prevQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
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
                        <div class="hidden sm:flex px-5 py-2 bg-indigo-50 rounded-full font-bold text-indigo-700 items-center justify-center">
                            Intento {{ current_attempt }} de {{ quiz.max_attempts }}
                        </div>
                        <div class="px-5 py-2 bg-red-50 rounded-full font-bold text-red-600 flex items-center gap-2" :class="timeRemaining < 60 ? 'animate-pulse' : ''">
                            <Clock class="w-4 h-4" />
                            <span class="tabular-nums font-mono text-lg tracking-wider">{{ formattedTime }}</span>
                        </div>
                        <div class="hidden sm:block px-5 py-2.5 bg-primary/10 rounded-full font-bold text-primary tabular-nums">
                            Pregunta {{ currentQuestionIndex + 1 }} de {{ quiz.questions.length }}
                        </div>
                    </div>
                </div>

                <!-- Content container -->
                <div class="flex-1 overflow-y-auto p-8 md:p-12 custom-scrollbar" v-if="quiz.questions.length > 0">
                    <div class="max-w-2xl mx-auto space-y-8 animate-in fade-in slide-in-from-right-4 duration-300" :key="currentQuestionIndex">
                        <h3 class="text-2xl font-bold text-on-surface leading-snug">
                            {{ quiz.questions[currentQuestionIndex].question }}
                        </h3>

                        <div class="space-y-4">
                            <button
                                v-for="(ans, i) in quiz.questions[currentQuestionIndex].answers"
                                :key="ans.id"
                                @click="selectAnswer(quiz.questions[currentQuestionIndex].id, ans.id)"
                                class="w-full flex items-center p-5 rounded-2xl border-2 transition-alltext-left group"
                                :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id 
                                ? 'border-primary bg-primary/5 shadow-sm' 
                                : 'border-outline-variant/30 hover:border-primary/40 bg-white'"
                            >
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center mr-4 transition-colors shrink-0" 
                                     :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'border-primary bg-primary text-white' : 'border-outline-variant/40 group-hover:border-primary/50 text-transparent'">
                                    <CheckCircle2 class="w-4 h-4" />
                                </div>
                                <span class="text-lg text-on-surface font-medium leading-relaxed group-hover:text-primary transition-colors text-left" :class="selectedAnswers[quiz.questions[currentQuestionIndex].id] === ans.id ? 'text-primary' : ''">
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
                        class="px-10 py-4 bg-primary text-on-primary font-bold text-xs uppercase tracking-widest rounded-2xl shadow-lg hover:scale-105 transition-all"
                    >
                        Siguiente Pregunta
                    </button>

                    <button 
                        v-if="currentQuestionIndex === quiz.questions.length - 1 && quiz.questions.length > 0"
                        @click="triggerSubmitConfirm"
                        :disabled="isSubmitting"
                        class="px-12 py-4 bg-emerald-600 text-white font-bold text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-900/20 hover:bg-emerald-700 hover:scale-105 transition-all disabled:opacity-50"
                    >
                        {{ isSubmitting ? 'Enviando...' : 'Finalizar y Calificar' }}
                    </button>
                </div>
            </div>

        </div>

        <!-- Custom Modal Overlay -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full p-8 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-200">
                <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <Clock v-if="modalType === 'timeup'" class="w-10 h-10 text-primary animate-pulse" />
                    <CheckCircle2 v-else class="w-10 h-10 text-primary" />
                </div>
                <h3 class="text-2xl font-serif font-bold text-on-surface mb-3">{{ modalTitle }}</h3>
                <p class="text-on-surface-variant leading-relaxed text-sm mb-8">{{ modalMessage }}</p>
                
                <div class="flex flex-col gap-3">
                    <button 
                        @click="onConfirmModal && onConfirmModal()"
                        class="w-full py-4 bg-primary text-white font-bold rounded-2xl shadow-lg hover:opacity-90 active:scale-95 transition"
                    >
                        {{ modalType === 'timeup' ? 'Aceptar y Enviar' : 'Sí, calificar ahora' }}
                    </button>
                    <button 
                        v-if="modalType === 'submit'"
                        @click="showModal = false"
                        class="w-full py-4 text-on-surface-variant hover:text-primary font-bold rounded-2xl hover:bg-surface-container-low transition"
                    >
                        Volver al examen
                    </button>
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
