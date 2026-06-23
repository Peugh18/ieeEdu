<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseQuizzesApi } from '@/composables/admin/course-editor/useCourseQuizzes';

defineProps<{
    show: boolean;
    quizzes: CourseQuizzesApi;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 mb-10 max-w-3xl">
            <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-on-surface">
                Centro de <span class="font-light italic">Evaluaciones</span>
            </h2>
            <p class="font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                Implementa controles de aprendizaje. Las calificaciones formarán parte esencial del progreso académico del estudiante.
            </p>
        </div>

        <div class="space-y-8 rounded-[2rem] border border-transparent bg-surface-container-highest p-8 shadow-sm">
            <h3 class="font-sans text-[18px] font-bold text-on-surface">Nuevo Examen / Evaluación</h3>
            <div class="grid grid-cols-1 items-center gap-6 md:grid-cols-6">
                <div class="space-y-3 md:col-span-2">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Título</label>
                    <input
                        v-model="quizzes.newQuiz.title"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-5 py-4 font-sans text-[14px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Nombre (Ej. Evaluación Final)"
                    />
                </div>
                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Nota min.</label>
                    <input
                        v-model.number="quizzes.newQuiz.minimum_score"
                        type="number"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-5 py-4 font-sans text-[14px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        placeholder="14"
                    />
                </div>
                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Tiempo (m)</label>
                    <input
                        v-model.number="quizzes.newQuiz.time_limit"
                        type="number"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-5 py-4 font-sans text-[14px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        placeholder="30"
                    />
                </div>
                <div class="space-y-3">
                    <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Intentos permitidos</label>
                    <input
                        v-model.number="quizzes.newQuiz.max_attempts"
                        type="number"
                        min="1"
                        class="w-full rounded-[1.5rem] border-transparent bg-white px-5 py-4 font-sans text-[14px] text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                        placeholder="1"
                    />
                </div>
                <div class="pt-8">
                    <button
                        type="button"
                        class="w-full rounded-full bg-gradient-to-br from-primary to-[#707040] px-6 py-4 font-sans text-[13px] font-bold tracking-wide text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                        @click="quizzes.createQuiz"
                    >
                        Crear Examen
                    </button>
                </div>
            </div>
        </div>

        <div v-if="quizzes.quizzes.length === 0" class="rounded-[3rem] border-2 border-dashed border-outline-variant/10 py-20 text-center">
            <p class="font-serif italic text-on-surface-variant">Aún no has creado exámenes para este curso.</p>
        </div>

        <div
            v-for="quiz in quizzes.quizzes"
            :key="quiz.id"
            class="group mt-8 overflow-hidden rounded-[2.5rem] border border-outline-variant/10 bg-white shadow-sm transition-all hover:shadow-xl hover:shadow-primary/5"
        >
            <div
                v-if="quizzes.editingQuizId !== quiz.id"
                class="flex cursor-pointer items-center justify-between p-8"
                @click="quizzes.toggleQuiz(quiz.id)"
            >
                <div class="flex items-center gap-6">
                    <div class="rounded-[1.5rem] bg-primary/5 p-4 transition-colors group-hover:bg-primary/10">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold leading-tight text-on-surface">{{ quiz.title }}</h4>
                        <div class="mt-1 flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                            <span>{{ quiz.questions?.length ?? 0 }} preguntas</span>
                            <span class="h-1 w-1 rounded-full bg-outline-variant" />
                            <span>Nota min: {{ quiz.minimum_score }}</span>
                            <span class="h-1 w-1 rounded-full bg-outline-variant" />
                            <span>{{ quiz.time_limit }} minutos</span>
                            <span class="h-1 w-1 rounded-full bg-outline-variant" />
                            <span>{{ quiz.max_attempts }} intentos</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="rounded-xl px-4 py-2 text-xs font-bold text-indigo-600 transition hover:bg-indigo-50"
                        @click.stop="quizzes.startEditQuiz(quiz)"
                    >
                        Editar
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50"
                        @click.stop="quizzes.deleteQuiz(quiz.id)"
                    >
                        Eliminar
                    </button>
                    <div
                        class="rounded-xl bg-surface-container-low p-2 transition group-hover:bg-surface-container-high"
                        :class="{ 'rotate-180': quizzes.openQuizId === quiz.id }"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div v-else class="border-b border-outline-variant/10 bg-surface-container-lowest p-6">
                <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-6">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Título</label>
                        <input
                            v-model="quizzes.editQuizData.title"
                            class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm transition focus:border-primary focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Nota min.</label>
                        <input
                            v-model.number="quizzes.editQuizData.minimum_score"
                            type="number"
                            class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Tiempo (m)</label>
                        <input
                            v-model.number="quizzes.editQuizData.time_limit"
                            type="number"
                            class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Intentos</label>
                        <input
                            v-model.number="quizzes.editQuizData.max_attempts"
                            type="number"
                            min="1"
                            class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm"
                        />
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <button
                            type="button"
                            class="flex-[1] rounded-xl border border-outline-variant/30 px-2 py-2 text-xs font-bold transition hover:bg-surface-container"
                            @click="quizzes.editingQuizId = null"
                        >
                            X
                        </button>
                        <button
                            type="button"
                            class="flex-[3] rounded-xl bg-primary px-3 py-2 text-xs font-bold text-white transition hover:opacity-90"
                            @click="quizzes.saveEditQuiz"
                        >
                            Guardar
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="quizzes.openQuizId === quiz.id" class="border-t border-outline-variant/10 px-8 pb-10 duration-500 animate-in fade-in">
                <div class="mb-6 flex items-center gap-6 border-b border-outline-variant/10 pt-4">
                    <button
                        type="button"
                        class="pb-3 text-sm font-bold uppercase tracking-widest transition-all"
                        :class="
                            quizzes.activeQuizTabs[quiz.id] === 'questions'
                                ? 'border-b-2 border-primary text-primary'
                                : 'text-on-surface-variant hover:text-on-surface'
                        "
                        @click="quizzes.activeQuizTabs[quiz.id] = 'questions'"
                    >
                        Preguntas
                    </button>
                    <button
                        type="button"
                        class="pb-3 text-sm font-bold uppercase tracking-widest transition-all"
                        :class="
                            quizzes.activeQuizTabs[quiz.id] === 'results'
                                ? 'border-b-2 border-primary text-primary'
                                : 'text-on-surface-variant hover:text-on-surface'
                        "
                        @click="quizzes.activeQuizTabs[quiz.id] = 'results'"
                    >
                        Resultados ({{ quiz.attempts?.length ?? 0 }})
                    </button>
                </div>

                <div v-if="quizzes.activeQuizTabs[quiz.id] === 'questions'" class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h5 class="text-[10px] font-bold uppercase tracking-widest text-on-surface">Banco de Preguntas</h5>
                        <button type="button" class="text-sm font-bold text-primary hover:underline" @click="quizzes.startAddQuestion(quiz.id)">
                            + Agregar Pregunta
                        </button>
                    </div>

                    <div
                        v-if="quizzes.addingQuestionFor === quiz.id"
                        class="space-y-6 rounded-[2rem] border border-outline-variant/20 bg-surface-container-lowest p-8"
                    >
                        <input
                            v-model="quizzes.newQuestion.question"
                            class="w-full rounded-xl border border-outline-variant/30 px-5 py-4 text-sm font-bold italic"
                            placeholder="Enunciado de la pregunta..."
                        />

                        <div class="space-y-4">
                            <p class="px-1 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Opciones de respuesta</p>
                            <div v-for="(ans, idx) in quizzes.newQuestion.answers" :key="idx" class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="flex h-6 w-6 items-center justify-center rounded-full border-2 transition-all"
                                    :class="ans.is_correct ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-outline-variant'"
                                    @click="quizzes.newQuestion.answers.forEach((a, i) => (a.is_correct = i === idx))"
                                >
                                    <svg v-if="ans.is_correct" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        />
                                    </svg>
                                </button>
                                <input
                                    v-model="ans.answer_text"
                                    class="flex-1 rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm"
                                    placeholder="Texto de la opción..."
                                />
                                <button
                                    v-if="quizzes.newQuestion.answers.length > 2"
                                    type="button"
                                    class="text-on-surface-variant transition hover:text-red-500"
                                    @click="quizzes.newQuestion.answers.splice(idx, 1)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <button
                                type="button"
                                class="text-xs font-bold text-on-surface-variant underline decoration-dotted transition hover:text-primary"
                                @click="quizzes.addAnswerOption"
                            >
                                + Agregar opción
                            </button>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                type="button"
                                class="rounded-xl px-6 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-high"
                                @click="
                                    quizzes.addingQuestionFor = null;
                                    quizzes.editingQuestionId = null;
                                "
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                class="rounded-xl bg-primary px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:opacity-90 active:scale-95"
                                @click="quizzes.saveQuestion(quiz.id)"
                            >
                                Guardar Pregunta
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(q, qidx) in quiz.questions"
                            :key="q.id"
                            class="flex items-start justify-between gap-4 rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6"
                        >
                            <div class="min-w-0 flex-1 space-y-4">
                                <p class="text-sm font-bold leading-relaxed text-on-surface">
                                    <span class="mr-2 tabular-nums text-primary">{{ Number(qidx) + 1 }}.</span> {{ q.question }}
                                </p>
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                    <div
                                        v-for="a in q.answers"
                                        :key="a.id"
                                        class="flex items-center gap-3 rounded-xl border border-outline-variant/5 bg-white/50 px-3 py-2"
                                    >
                                        <div
                                            class="h-2 w-2 rounded-full"
                                            :class="a.is_correct ? 'bg-emerald-500 shadow-lg shadow-emerald-500/20' : 'bg-gray-200'"
                                        />
                                        <span class="text-xs font-medium" :class="{ 'font-bold text-emerald-700': a.is_correct }">{{
                                            a.answer_text
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button
                                    type="button"
                                    title="Editar pregunta"
                                    class="rounded-xl p-2 text-on-surface-variant transition hover:bg-emerald-50 hover:text-emerald-600"
                                    @click="quizzes.startEditQuestion(quiz.id, q)"
                                >
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    title="Eliminar pregunta"
                                    class="rounded-xl p-2 text-on-surface-variant transition hover:bg-red-50 hover:text-red-600"
                                    @click="quizzes.deleteQuestion(quiz.id, q.id)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="quizzes.activeQuizTabs[quiz.id] === 'results'" class="space-y-4">
                    <h5 class="text-[10px] font-bold uppercase tracking-widest text-on-surface">Intentos de Alumnos</h5>
                    <div
                        v-if="!quiz.attempts || quiz.attempts.length === 0"
                        class="rounded-3xl border-2 border-dashed border-outline-variant/10 py-12 text-center"
                    >
                        <p class="text-sm font-bold text-on-surface-variant opacity-70">Aún no hay intentos registrados.</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="attempt in quiz.attempts"
                            :key="attempt.id"
                            class="flex items-center justify-between rounded-2xl border border-outline-variant/10 bg-surface-container-low p-4"
                        >
                            <div>
                                <p class="text-sm font-bold text-on-surface">
                                    {{ attempt.user?.name ?? 'Desconocido' }}
                                    <span class="text-xs font-normal text-on-surface-variant" v-if="attempt.user?.email"
                                        >({{ attempt.user.email }})</span
                                    >
                                </p>
                                <p class="mt-1 text-xs text-on-surface-variant">
                                    {{ attempt.completed_at ? new Date(attempt.completed_at).toLocaleString() : 'En curso' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="rounded-xl px-3 py-1 text-[10px] font-bold uppercase tracking-widest"
                                    :class="
                                        attempt.status === 'aprobado'
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : attempt.status === 'reprobado'
                                              ? 'bg-red-100 text-red-800'
                                              : 'bg-blue-100 text-blue-800'
                                    "
                                >
                                    {{ attempt.status }}
                                </div>
                                <div class="rounded-xl border border-outline-variant/10 bg-white px-3 py-1 font-mono text-lg font-bold shadow-sm">
                                    {{ attempt.score !== null ? attempt.score : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
