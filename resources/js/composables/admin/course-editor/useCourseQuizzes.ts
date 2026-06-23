import axios from '@/lib/axios';
import type { CourseEditorQuiz } from '@/types/course-editor';
import { ref } from 'vue';

export function useCourseQuizzes(courseId: number, initialQuizzes: CourseEditorQuiz[] = []) {
    const quizzes = ref<CourseEditorQuiz[]>([...initialQuizzes]);
    const openQuizId = ref<number | null>(null);
    const editingQuizId = ref<number | null>(null);
    const editQuizData = ref({ title: '', minimum_score: 14, time_limit: 30, max_attempts: 1 });
    const newQuiz = ref({ title: '', time_limit: 30, max_attempts: 1, minimum_score: 14 });
    const addingQuestionFor = ref<number | null>(null);
    const editingQuestionId = ref<number | null>(null);
    const newQuestion = ref({
        question: '',
        type: 'multiple_choice' as const,
        points: 1,
        answers: [
            { answer_text: '', is_correct: true },
            { answer_text: '', is_correct: false },
        ],
    });
    const activeQuizTabs = ref<Record<number, 'questions' | 'results'>>({});

    function startEditQuiz(quiz: CourseEditorQuiz) {
        editingQuizId.value = quiz.id;
        openQuizId.value = quiz.id;
        editQuizData.value = {
            title: quiz.title,
            minimum_score: quiz.minimum_score ?? 14,
            time_limit: quiz.time_limit ?? 30,
            max_attempts: quiz.max_attempts ?? 1,
        };
    }

    async function saveEditQuiz() {
        if (!editQuizData.value.title.trim() || !editingQuizId.value) return;
        try {
            const res = await axios.put(route('admin.quizzes.update', { quiz: editingQuizId.value }), editQuizData.value);
            const idx = quizzes.value.findIndex((q) => q.id === editingQuizId.value);
            if (idx !== -1) quizzes.value[idx] = { ...quizzes.value[idx], ...res.data };
            editingQuizId.value = null;
        } catch {
            alert('Error al actualizar');
        }
    }

    async function createQuiz() {
        if (!newQuiz.value.title.trim()) return;
        try {
            const res = await axios.post(route('admin.courses.quizzes.store', { course: courseId }), newQuiz.value);
            quizzes.value.push({ ...res.data, questions: [] });
            newQuiz.value = { title: '', time_limit: 30, max_attempts: 1, minimum_score: 14 };
        } catch {
            alert('Error al crear el examen');
        }
    }

    async function deleteQuiz(id: number) {
        if (!confirm('Eliminar examen y todas sus preguntas?')) return;
        await axios.delete(route('admin.quizzes.destroy', { quiz: id }));
        quizzes.value = quizzes.value.filter((q) => q.id !== id);
    }

    function startAddQuestion(quizId: number) {
        addingQuestionFor.value = quizId;
        editingQuestionId.value = null;
        newQuestion.value = {
            question: '',
            type: 'multiple_choice',
            points: 1,
            answers: [
                { answer_text: '', is_correct: true },
                { answer_text: '', is_correct: false },
            ],
        };
    }

    function startEditQuestion(
        quizId: number,
        q: {
            id: number;
            question: string;
            type: 'multiple_choice' | 'true_false';
            points: number;
            answers: Array<{ id?: number; answer_text: string; is_correct: boolean }>;
        },
    ) {
        addingQuestionFor.value = quizId;
        editingQuestionId.value = q.id;
        newQuestion.value = {
            question: q.question,
            type: q.type,
            points: q.points,
            answers: q.answers.map((a) => ({ ...a })),
        };
    }

    function addAnswerOption() {
        newQuestion.value.answers.push({ answer_text: '', is_correct: false });
    }

    async function saveQuestion(quizId: number) {
        if (!newQuestion.value.question.trim()) return;
        if (newQuestion.value.answers.filter((a) => a.is_correct).length === 0) {
            alert('Selecciona al menos una respuesta correcta.');
            return;
        }

        try {
            if (editingQuestionId.value) {
                const res = await axios.put(route('admin.questions.update', { question: editingQuestionId.value }), newQuestion.value);
                const qIdx = quizzes.value.findIndex((q) => q.id === quizId);
                if (qIdx !== -1) {
                    const qqIdx = quizzes.value[qIdx].questions.findIndex((qq) => qq.id === editingQuestionId.value);
                    if (qqIdx !== -1) quizzes.value[qIdx].questions[qqIdx] = res.data;
                }
            } else {
                const res = await axios.post(route('admin.questions.store'), {
                    quiz_id: quizId,
                    ...newQuestion.value,
                });
                const qIdx = quizzes.value.findIndex((q) => q.id === quizId);
                if (qIdx !== -1) quizzes.value[qIdx].questions.push(res.data);
            }
            addingQuestionFor.value = null;
            editingQuestionId.value = null;
            newQuestion.value = {
                question: '',
                type: 'multiple_choice',
                points: 1,
                answers: [
                    { answer_text: '', is_correct: true },
                    { answer_text: '', is_correct: false },
                ],
            };
        } catch {
            alert('Error al guardar la pregunta');
        }
    }

    async function deleteQuestion(quizId: number, questionId: number) {
        if (!confirm('Eliminar pregunta?')) return;
        await axios.delete(route('admin.questions.destroy', { question: questionId }));
        const qIdx = quizzes.value.findIndex((q) => q.id === quizId);
        if (qIdx !== -1) {
            quizzes.value[qIdx].questions = quizzes.value[qIdx].questions.filter((q) => q.id !== questionId);
        }
    }

    function toggleQuiz(id: number) {
        if (openQuizId.value === id) {
            openQuizId.value = null;
        } else {
            openQuizId.value = id;
            if (!activeQuizTabs.value[id]) activeQuizTabs.value[id] = 'questions';
        }
    }

    return {
        quizzes,
        openQuizId,
        editingQuizId,
        editQuizData,
        newQuiz,
        addingQuestionFor,
        editingQuestionId,
        newQuestion,
        activeQuizTabs,
        startEditQuiz,
        saveEditQuiz,
        createQuiz,
        deleteQuiz,
        startAddQuestion,
        startEditQuestion,
        addAnswerOption,
        saveQuestion,
        deleteQuestion,
        toggleQuiz,
    };
}

export type CourseQuizzesApi = ReturnType<typeof useCourseQuizzes>;
