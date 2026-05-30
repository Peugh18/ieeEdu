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
        <div class="max-w-3xl relative z-10 mb-10">
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-3 tracking-tight">
                Centro de <span class="italic font-light">Evaluaciones</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium leading-relaxed">
                Implementa controles de aprendizaje. Las calificaciones formarán parte esencial del progreso académico del estudiante.
            </p>
        </div>

        <div class="bg-surface-container-highest border border-transparent rounded-[2rem] p-8 space-y-8 shadow-sm">
            <h3 class="font-bold text-[18px] text-on-surface font-sans">Nuevo Examen / Evaluación</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-center">
                <div class="md:col-span-2 space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Título</label>
                    <input v-model="quizzes.newQuiz.title" class="w-full rounded-[1.5rem] bg-white px-5 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="Nombre (Ej. Evaluación Final)" />
                </div>
                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Nota min.</label>
                    <input v-model.number="quizzes.newQuiz.minimum_score" type="number" class="w-full rounded-[1.5rem] bg-white px-5 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" placeholder="14" />
                </div>
                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Tiempo (m)</label>
                    <input v-model.number="quizzes.newQuiz.time_limit" type="number" class="w-full rounded-[1.5rem] bg-white px-5 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" placeholder="30" />
                </div>
                <div class="space-y-3">
                    <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Intentos permitidos</label>
                    <input v-model.number="quizzes.newQuiz.max_attempts" type="number" min="1" class="w-full rounded-[1.5rem] bg-white px-5 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent" placeholder="1" />
                </div>
                <div class="pt-8">
                    <button type="button" class="w-full rounded-full bg-gradient-to-br from-primary to-[#707040] px-6 py-4 text-[13px] font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all tracking-wide font-sans" @click="quizzes.createQuiz">Crear Examen</button>
                </div>
            </div>
        </div>

        <div v-if="quizzes.quizzes.length === 0" class="py-20 text-center border-2 border-dashed border-outline-variant/10 rounded-[3rem]">
            <p class="text-on-surface-variant italic font-serif">Aún no has creado exámenes para este curso.</p>
        </div>

        <div v-for="quiz in quizzes.quizzes" :key="quiz.id" class="rounded-[2.5rem] bg-white border border-outline-variant/10 shadow-sm overflow-hidden transition-all group hover:shadow-xl hover:shadow-primary/5 mt-8">
            <div v-if="quizzes.editingQuizId !== quiz.id" class="p-8 flex items-center justify-between cursor-pointer" @click="quizzes.toggleQuiz(quiz.id)">
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-primary/5 rounded-[1.5rem] group-hover:bg-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-on-surface leading-tight">{{ quiz.title }}</h4>
                        <div class="flex items-center gap-4 mt-1 text-xs text-on-surface-variant font-bold uppercase tracking-widest">
                            <span>{{ quiz.questions?.length ?? 0 }} preguntas</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full" />
                            <span>Nota min: {{ quiz.minimum_score }}</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full" />
                            <span>{{ quiz.time_limit }} minutos</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full" />
                            <span>{{ quiz.max_attempts }} intentos</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="text-xs font-bold text-indigo-600 px-4 py-2 hover:bg-indigo-50 rounded-xl transition" @click.stop="quizzes.startEditQuiz(quiz)">Editar</button>
                    <button type="button" class="text-xs font-bold text-red-600 px-4 py-2 hover:bg-red-50 rounded-xl transition" @click.stop="quizzes.deleteQuiz(quiz.id)">Eliminar</button>
                    <div class="p-2 bg-surface-container-low rounded-xl transition group-hover:bg-surface-container-high" :class="{ 'rotate-180': quizzes.openQuizId === quiz.id }">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

            <div v-else class="p-6 bg-surface-container-lowest border-b border-outline-variant/10">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Título</label>
                        <input v-model="quizzes.editQuizData.title" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm focus:outline-none focus:border-primary transition" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Nota min.</label>
                        <input v-model.number="quizzes.editQuizData.minimum_score" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Tiempo (m)</label>
                        <input v-model.number="quizzes.editQuizData.time_limit" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Intentos</label>
                        <input v-model.number="quizzes.editQuizData.max_attempts" type="number" min="1" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <button type="button" class="flex-[1] rounded-xl border border-outline-variant/30 px-2 py-2 text-xs font-bold hover:bg-surface-container transition" @click="quizzes.editingQuizId = null">X</button>
                        <button type="button" class="flex-[3] rounded-xl bg-primary text-white px-3 py-2 text-xs font-bold hover:opacity-90 transition" @click="quizzes.saveEditQuiz">Guardar</button>
                    </div>
                </div>
            </div>

            <div v-show="quizzes.openQuizId === quiz.id" class="px-8 pb-10 border-t border-outline-variant/10 animate-in fade-in duration-500">
                <div class="flex items-center gap-6 border-b border-outline-variant/10 pt-4 mb-6">
                    <button type="button" class="pb-3 text-sm font-bold uppercase tracking-widest transition-all" :class="quizzes.activeQuizTabs[quiz.id] === 'questions' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'" @click="quizzes.activeQuizTabs[quiz.id] = 'questions'">
                        Preguntas
                    </button>
                    <button type="button" class="pb-3 text-sm font-bold uppercase tracking-widest transition-all" :class="quizzes.activeQuizTabs[quiz.id] === 'results' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'" @click="quizzes.activeQuizTabs[quiz.id] = 'results'">
                        Resultados ({{ quiz.attempts?.length ?? 0 }})
                    </button>
                </div>

                <div v-if="quizzes.activeQuizTabs[quiz.id] === 'questions'" class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h5 class="font-bold text-on-surface uppercase tracking-widest text-[10px]">Banco de Preguntas</h5>
                        <button type="button" class="text-sm font-bold text-primary hover:underline" @click="quizzes.startAddQuestion(quiz.id)">+ Agregar Pregunta</button>
                    </div>

                    <div v-if="quizzes.addingQuestionFor === quiz.id" class="p-8 bg-surface-container-lowest rounded-[2rem] border border-outline-variant/20 space-y-6">
                        <input v-model="quizzes.newQuestion.question" class="w-full rounded-xl border border-outline-variant/30 px-5 py-4 text-sm font-bold italic" placeholder="Enunciado de la pregunta..." />

                        <div class="space-y-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest px-1">Opciones de respuesta</p>
                            <div v-for="(ans, idx) in quizzes.newQuestion.answers" :key="idx" class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="ans.is_correct ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-outline-variant'"
                                    @click="quizzes.newQuestion.answers.forEach((a, i) => a.is_correct = (i === idx))"
                                >
                                    <svg v-if="ans.is_correct" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" /></svg>
                                </button>
                                <input v-model="ans.answer_text" class="flex-1 rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm" placeholder="Texto de la opción..." />
                                <button v-if="quizzes.newQuestion.answers.length > 2" type="button" class="text-on-surface-variant hover:text-red-500 transition" @click="quizzes.newQuestion.answers.splice(idx, 1)">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <button type="button" class="text-xs font-bold text-on-surface-variant hover:text-primary transition underline decoration-dotted" @click="quizzes.addAnswerOption">+ Agregar opción</button>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" class="px-6 py-3 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-xl transition" @click="quizzes.addingQuestionFor = null; quizzes.editingQuestionId = null">Cancelar</button>
                            <button type="button" class="px-8 py-3 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:opacity-90 active:scale-95 transition" @click="quizzes.saveQuestion(quiz.id)">Guardar Pregunta</button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(q, qidx) in quiz.questions" :key="q.id" class="p-6 rounded-3xl bg-surface-container-lowest border border-outline-variant/10 flex items-start justify-between gap-4">
                            <div class="space-y-4 min-w-0 flex-1">
                                <p class="text-sm font-bold text-on-surface leading-relaxed"><span class="text-primary tabular-nums mr-2">{{ Number(qidx) + 1 }}.</span> {{ q.question }}</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div v-for="a in q.answers" :key="a.id" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-white/50 border border-outline-variant/5">
                                        <div class="w-2 h-2 rounded-full" :class="a.is_correct ? 'bg-emerald-500 shadow-lg shadow-emerald-500/20' : 'bg-gray-200'" />
                                        <span class="text-xs font-medium" :class="{ 'text-emerald-700 font-bold': a.is_correct }">{{ a.answer_text }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button type="button" title="Editar pregunta" class="p-2 text-on-surface-variant hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition" @click="quizzes.startEditQuestion(quiz.id, q)">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button type="button" title="Eliminar pregunta" class="p-2 text-on-surface-variant hover:text-red-600 hover:bg-red-50 rounded-xl transition" @click="quizzes.deleteQuestion(quiz.id, q.id)">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="quizzes.activeQuizTabs[quiz.id] === 'results'" class="space-y-4">
                    <h5 class="font-bold text-on-surface uppercase tracking-widest text-[10px]">Intentos de Alumnos</h5>
                    <div v-if="!quiz.attempts || quiz.attempts.length === 0" class="py-12 border-2 border-dashed border-outline-variant/10 rounded-3xl text-center">
                        <p class="text-on-surface-variant text-sm font-bold opacity-70">Aún no hay intentos registrados.</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="attempt in quiz.attempts" :key="attempt.id as number" class="flex items-center justify-between p-4 bg-surface-container-low border border-outline-variant/10 rounded-2xl">
                            <div>
                                <p class="text-sm font-bold text-on-surface">
                                    {{ (attempt.user as { name?: string })?.name ?? 'Desconocido' }}
                                    <span class="text-xs text-on-surface-variant font-normal">({{ (attempt.user as { email?: string })?.email }})</span>
                                </p>
                                <p class="text-xs text-on-surface-variant mt-1">{{ attempt.completed_at ? new Date(attempt.completed_at as string).toLocaleString() : 'En curso' }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="px-3 py-1 rounded-xl text-[10px] font-bold uppercase tracking-widest" :class="attempt.status === 'aprobado' ? 'bg-emerald-100 text-emerald-800' : attempt.status === 'reprobado' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'">
                                    {{ attempt.status }}
                                </div>
                                <div class="text-lg font-bold font-mono px-3 py-1 bg-white rounded-xl shadow-sm border border-outline-variant/10">
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
