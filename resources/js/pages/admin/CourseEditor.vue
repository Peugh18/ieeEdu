<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    course: any;
    categories: Array<{ id: number; name: string }>;
}>();

const lessons = ref<any[]>(props.course.lessons ?? []);
const modules = ref<any[]>(props.course.modules ?? []);
const openLessonId = ref<number | null>(null);
const openQuizId = ref<number | null>(null);
const isExamView = ref(false);
const quizzes = ref<any[]>(props.course.quizzes ?? []);
const materialsByLesson = ref<Record<number, any[]>>({});

const form = useForm({
    title: props.course.title ?? '',
    description: props.course.description ?? '',
    price: Number(props.course.price ?? 0),
    discount_enabled: props.course.discount != null && Number(props.course.discount) > 0,
    discount: Number(props.course.discount ?? 0),
    sale_price: Number(props.course.sale_price ?? 0),
    type: props.course.type ?? 'grabado',
    status: props.course.status ?? 'BORRADOR',
    category_id: props.course.category_id ?? '',
    certificate_enabled: props.course.certificate_enabled ?? true,
    image_file: null as File | null,
    instructor_name: props.course.instructor_name ?? '',
    instructor_title: props.course.instructor_title ?? '',
    instructor_bio: props.course.instructor_bio ?? '',
    instructor_image_file: null as File | null,
    start_date: props.course.start_date ?? '',
    start_time: props.course.start_time ?? '',
    class_hours: props.course.class_hours ?? '',
    whatsapp_link: props.course.whatsapp_link ?? '',
    objectives: props.course.objectives ?? '',
    requirements: props.course.requirements ?? '',
});

watch([() => form.price, () => form.discount], ([newPrice, newDiscount]) => {
    if (form.discount_enabled && (newDiscount as number) > 0) {
        form.sale_price = Number(((newPrice as number) - ((newPrice as number) * ((newDiscount as number) / 100))).toFixed(2));
    } else {
        form.sale_price = 0;
    }
});

watch(() => form.discount_enabled, (enabled: boolean) => {
    if (enabled && form.discount > 0) {
        form.sale_price = Number((form.price - (form.price * (form.discount / 100))).toFixed(2));
    } else {
        form.sale_price = 0;
    }
});

const newModuleTitle = ref('');
const newLesson = ref({
    module_id: null as number | null,
    title: '',
    description: '',
    content_type: 'video' as 'video' | 'live' | 'event' | 'text',
    video_url: '',
    live_link: '',
    start_time: '',
    end_time: '',
});

const newMaterial = ref({
    title: '',
    external_url: '',
    file: null as File | null,
});
const addingMaterialFor = ref<number | null>(null);

const showSuccessNotification = ref(false);
const showErrorNotification = ref(false);
const showPublishSuccessNotification = ref(false);

const isMasterclass = computed(() => form.type === 'masterclass' || form.type === 'evento');
const canPublish = computed(() => {
    if (lessons.value.length < 1) return false;
    if (isMasterclass.value && lessons.value.length !== 1) return false;
    return true;
});

function resetNewLesson(moduleId: number | null = null) {
    newLesson.value = {
        module_id: moduleId,
        title: '',
        description: '',
        content_type: 'video',
        video_url: '',
        live_link: '',
        start_time: '',
        end_time: '',
    };
}

function saveCourse() {
    form.transform((data) => {
        const payload: any = {
            ...data,
            sale_price: data.discount_enabled ? data.sale_price : '',
            discount: data.discount_enabled ? data.discount : '',
            certificate_enabled: !!data.certificate_enabled,
            _method: 'PUT'
        };
        if (!payload.image_file) {
            delete payload.image_file;
        }
        if (!payload.instructor_image_file) {
            delete payload.instructor_image_file;
        }
        return payload;
    }).post(route('admin.courses.update', props.course.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showSuccessNotification.value = true;
            setTimeout(() => {
                showSuccessNotification.value = false;
            }, 3000);
        },
        onError: () => {
            showErrorNotification.value = true;
            setTimeout(() => {
                showErrorNotification.value = false;
            }, 4000);
        }
    });
}

function publishCourse() {
    if (!canPublish.value) {
        alert('Debes tener al menos 1 clase. En masterclass solo se permite 1.');
        return;
    }
    router.patch(route('admin.courses.publish', props.course.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showPublishSuccessNotification.value = true;
            setTimeout(() => { showPublishSuccessNotification.value = false; }, 3000);
        },
        onError: (errors: any) => {
            if (errors.course) alert(errors.course);
            else {
                showErrorNotification.value = true;
                setTimeout(() => { showErrorNotification.value = false; }, 4000);
            }
        }
    });
}

function hideCourse() {
    router.patch(route('admin.courses.hide', props.course.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
             showSuccessNotification.value = true;
             setTimeout(() => { showSuccessNotification.value = false; }, 3000);
        }
    });
}

async function createModule() {
    const title = newModuleTitle.value.trim();
    if (!title) return;
    const res = await axios.post(route('admin.courses.modules.store', { course: props.course.id }), { title });
    modules.value.push(res.data);
    newModuleTitle.value = '';
    // UX: al crear un módulo, seleccionarlo automáticamente para la próxima clase
    newLesson.value.module_id = res.data.id;
}

async function deleteModule(moduleId: number) {
    if (!confirm('Eliminar módulo?')) return;
    await axios.delete(route('admin.modules.destroy', { module: moduleId }));
    modules.value = modules.value.filter((m) => m.id !== moduleId);
    lessons.value = lessons.value.filter((l) => l.module_id !== moduleId);
}

async function createLesson() {
    if (isMasterclass.value && lessons.value.length >= 1) {
        alert('Masterclass solo permite una clase.');
        return;
    }

    const payload: any = {
        module_id: isMasterclass.value ? null : newLesson.value.module_id,
        title: newLesson.value.title.trim(),
        description: newLesson.value.description,
        // Masterclass: usamos "live" como link (WhatsApp)
        content_type: isMasterclass.value ? 'live' : newLesson.value.content_type,
    };

    if (!payload.title) {
        alert('Ingresa el título de la clase.');
        return;
    }
    if (payload.content_type === 'video') {
        payload.video_url = newLesson.value.video_url;
        if (!payload.video_url) {
            alert('Clase grabada requiere video (URL).');
            return;
        }
    }
    if (payload.content_type === 'live') {
        payload.live_link = newLesson.value.live_link;
        payload.start_time = newLesson.value.start_time || null;
        payload.end_time = newLesson.value.end_time || null;
        if (!payload.live_link) {
            alert(isMasterclass.value ? 'Masterclass requiere link de WhatsApp.' : 'Clase en vivo requiere link.');
            return;
        }
    }

    // Validación UX antes de llamar backend
    if (!isMasterclass.value && !payload.module_id) {
        alert('Primero selecciona o crea un módulo para esta clase.');
        return;
    }

    try {
        const res = await axios.post(route('admin.courses.lessons.store', { course: props.course.id }), payload);
        lessons.value.push(res.data);
        resetNewLesson(isMasterclass.value ? null : newLesson.value.module_id);
    } catch (e: any) {
        const msg =
            e?.response?.data?.message ??
            e?.response?.data?.errors?.title?.[0] ??
            'No se pudo crear la clase.';
        alert(msg);
    }
}

async function deleteLesson(lessonId: number) {
    if (!confirm('Eliminar clase?')) return;
    await axios.delete(route('admin.lessons.destroy', { lesson: lessonId }));
    lessons.value = lessons.value.filter((l) => l.id !== lessonId);
    delete materialsByLesson.value[lessonId];
    if (openLessonId.value === lessonId) openLessonId.value = null;
}

async function toggleLesson(lessonId: number) {
    if (openLessonId.value === lessonId) {
        openLessonId.value = null;
        return;
    }
    openLessonId.value = lessonId;
    if (!materialsByLesson.value[lessonId]) {
        const res = await axios.get(route('admin.lessons.materials.index', { lesson: lessonId }));
        materialsByLesson.value[lessonId] = res.data;
    }
}

function startAddMaterial(lessonId: number) {
    addingMaterialFor.value = lessonId;
    newMaterial.value = { title: '', external_url: '', file: null };
}

async function createMaterial(lessonId: number) {
    const payload = new FormData();
    payload.append('title', newMaterial.value.title.trim() || 'Recurso');
    if (newMaterial.value.file) {
        payload.append('kind', 'other');
        payload.append('file', newMaterial.value.file);
    } else if (newMaterial.value.external_url.trim()) {
        payload.append('kind', 'url');
        payload.append('external_url', newMaterial.value.external_url.trim());
    } else {
        alert('Sube un archivo o agrega un enlace.');
        return;
    }

    const res = await axios.post(route('admin.lessons.materials.store', { lesson: lessonId }), payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });
    materialsByLesson.value[lessonId] = [...(materialsByLesson.value[lessonId] ?? []), res.data];
    addingMaterialFor.value = null;
}

async function deleteMaterial(lessonId: number, materialId: number) {
    if (!confirm('Eliminar recurso?')) return;
    await axios.delete(route('admin.materials.destroy', { material: materialId }));
    materialsByLesson.value[lessonId] = (materialsByLesson.value[lessonId] ?? []).filter((m) => m.id !== materialId);
}
// ─── Quiz Management ─────────────────────────────────────────────
const newQuiz = ref({
    title: '',
    time_limit: 30,
    max_attempts: 1,
    minimum_score: 14,
});

const editingQuizId = ref<number | null>(null);
const editQuizData = ref({ title: '', minimum_score: 14, time_limit: 30, max_attempts: 1 });

function startEditQuiz(quiz: any) {
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
        const idx = quizzes.value.findIndex(q => q.id === editingQuizId.value);
        if (idx !== -1) quizzes.value[idx] = { ...quizzes.value[idx], ...res.data };
        editingQuizId.value = null;
    } catch (e) { alert('Error al actualizar'); }
}

async function createQuiz() {
    if (!newQuiz.value.title.trim()) return;
    try {
        const res = await axios.post(route('admin.courses.quizzes.store', { course: props.course.id }), newQuiz.value);
        quizzes.value.push({ ...res.data, questions: [] });
        newQuiz.value = { title: '', time_limit: 30, max_attempts: 1, minimum_score: 14 };
    } catch (e) { alert('Error al crear el examen'); }
}

async function deleteQuiz(id: number) {
    if (!confirm('Eliminar examen y todas sus preguntas?')) return;
    await axios.delete(route('admin.quizzes.destroy', { quiz: id }));
    quizzes.value = quizzes.value.filter(q => q.id !== id);
}

const addingQuestionFor = ref<number | null>(null);
const editingQuestionId = ref<number | null>(null);
const newQuestion = ref({
    question: '',
    type: 'multiple_choice',
    points: 1,
    answers: [
        { answer_text: '', is_correct: true },
        { answer_text: '', is_correct: false },
    ]
});

function startAddQuestion(quizId: number) {
    addingQuestionFor.value = quizId;
    editingQuestionId.value = null;
    newQuestion.value = { question: '', type: 'multiple_choice', points: 1, answers: [{ answer_text: '', is_correct: true }, { answer_text: '', is_correct: false }] };
}

function startEditQuestion(quizId: number, q: any) {
    addingQuestionFor.value = quizId;
    editingQuestionId.value = q.id;
    newQuestion.value = {
        question: q.question,
        type: q.type,
        points: q.points,
        answers: q.answers.map((a: any) => ({ ...a }))
    };
}

function addAnswerOption() {
    newQuestion.value.answers.push({ answer_text: '', is_correct: false });
}

async function saveQuestion(quizId: number) {
    if (!newQuestion.value.question.trim()) return;
    if (newQuestion.value.answers.filter(a => a.is_correct).length === 0) {
        alert('Selecciona al menos una respuesta correcta.');
        return;
    }
    
    try {
        if (editingQuestionId.value) {
            const res = await axios.put(route('admin.questions.update', { question: editingQuestionId.value }), newQuestion.value);
            const qIdx = quizzes.value.findIndex(q => q.id === quizId);
            if (qIdx !== -1) {
                const qqIdx = quizzes.value[qIdx].questions.findIndex((qq: any) => qq.id === editingQuestionId.value);
                if (qqIdx !== -1) quizzes.value[qIdx].questions[qqIdx] = res.data;
            }
        } else {
            const res = await axios.post(route('admin.questions.store'), { 
                quiz_id: quizId,
                ...newQuestion.value 
            });
            const qIdx = quizzes.value.findIndex(q => q.id === quizId);
            if (qIdx !== -1) quizzes.value[qIdx].questions.push(res.data);
        }
        addingQuestionFor.value = null;
        editingQuestionId.value = null;
        newQuestion.value = { question: '', type: 'multiple_choice', points: 1, answers: [{ answer_text: '', is_correct: true }, { answer_text: '', is_correct: false }] };
    } catch (e) { alert('Error al guardar la pregunta'); }
}

async function deleteQuestion(quizId: number, questionId: number) {
    if (!confirm('Eliminar pregunta?')) return;
    await axios.delete(route('admin.questions.destroy', { question: questionId }));
    const qIdx = quizzes.value.findIndex(q => q.id === quizId);
    if (qIdx !== -1) {
        quizzes.value[qIdx].questions = quizzes.value[qIdx].questions.filter((q: any) => q.id !== questionId);
    }
}
const activeQuizTabs = ref<Record<number, 'questions' | 'results'>>({});
async function toggleQuiz(id: number) {
    if (openQuizId.value === id) {
        openQuizId.value = null;
    } else {
        openQuizId.value = id;
        if (!activeQuizTabs.value[id]) {
            activeQuizTabs.value[id] = 'questions';
        }
    }
}
</script>

<template>
    <Head title="Editor de Curso" />
    <AppLayout>
        <!-- Notificación de éxito flotante -->
        <transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
            enter-to-class="opacity-100 translate-y-0 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showSuccessNotification" class="fixed bottom-6 right-6 z-50 rounded-xl bg-[#4B5320] px-6 py-4 shadow-xl border border-white/20 flex items-center gap-3 text-white">
                <svg class="h-6 w-6 text-emerald-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="font-bold text-sm">¡Cambios guardados!</h4>
                    <p class="text-xs text-white/80">El curso se ha actualizado correctamente.</p>
                </div>
            </div>
            
            <div v-else-if="showPublishSuccessNotification" class="fixed bottom-6 right-6 z-50 rounded-xl bg-emerald-700 px-6 py-4 shadow-xl border border-white/20 flex items-center gap-3 text-white">
                <svg class="h-6 w-6 text-emerald-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <h4 class="font-bold text-sm">¡Curso Publicado!</h4>
                    <p class="text-xs text-white/80">El curso ahora está visible para los alumnos.</p>
                </div>
            </div>

            <div v-else-if="showErrorNotification" class="fixed bottom-6 right-6 z-50 rounded-xl bg-red-600 px-6 py-4 shadow-xl border border-white/20 flex items-center gap-3 text-white">
                <svg class="h-6 w-6 text-red-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="font-bold text-sm">Error al guardar</h4>
                    <p class="text-xs text-white/80">Revisa los campos marcados en rojo.</p>
                </div>
            </div>
        </transition>

        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-on-surface">Editor del curso</h1>
                    <p class="text-sm text-on-surface-variant">
                        Estado: <span class="font-bold text-on-surface">{{ form.status }}</span> · Clases: {{ lessons.length }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="rounded-xl border border-outline-variant/30 px-4 py-2 text-sm font-semibold" @click="hideCourse">Ocultar</button>
                    <button class="rounded-xl flex items-center justify-center gap-2 bg-primary px-4 py-2 text-sm font-semibold text-white disabled:opacity-60 disabled:cursor-not-allowed transition" :disabled="form.processing" @click="saveCourse">
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Guardar cambios
                    </button>
                    <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="!canPublish" @click="publishCourse">Publicar</button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <aside class="lg:col-span-4 rounded-2xl border border-outline-variant/10 bg-white p-5 space-y-3">
                    <h2 class="font-semibold">Información del curso</h2>
                    <div>
                        <input v-model="form.title" class="w-full rounded-xl border px-4 py-3 text-sm" :class="form.errors.title ? 'border-red-500' : 'border-outline-variant/30'" placeholder="Nombre del curso" />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                    </div>
                    <div>
                        <textarea v-model="form.description" rows="4" class="w-full rounded-xl border px-4 py-3 text-sm" :class="form.errors.description ? 'border-red-500' : 'border-outline-variant/30'" placeholder="Descripción" />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input v-model.number="form.price" type="number" min="0" class="w-full rounded-xl border px-4 py-3 text-sm" :class="form.errors.price ? 'border-red-500' : 'border-outline-variant/30'" placeholder="Precio" />
                            <p v-if="form.errors.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</p>
                        </div>
                        <select v-model="form.type" class="rounded-xl border border-outline-variant/30 px-4 py-3 text-sm">
                            <option value="grabado">Grabado</option>
                            <option value="en vivo">En vivo</option>
                            <option value="masterclass">Masterclass</option>
                            <option value="evento">Evento/Charla</option>
                        </select>
                    </div>
                    <div v-if="isMasterclass" class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-3">
                        <label class="flex items-center gap-2 text-sm font-semibold">
                            <input type="checkbox" v-model="form.certificate_enabled" />
                            Certificado habilitado (opcional)
                        </label>
                        <p class="mt-1 text-xs text-on-surface-variant">Para masterclass/evento puedes activarlo o desactivarlo.</p>
                    </div>
                    <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-3">
                        <label class="flex items-center gap-2 text-sm font-semibold">
                            <input type="checkbox" v-model="form.discount_enabled" />
                            Aplicar descuento (%)
                        </label>
                        <div class="flex gap-2 mt-2">
                            <input
                                v-model.number="form.discount"
                                :disabled="!form.discount_enabled"
                                type="number"
                                min="0"
                                max="100"
                                class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm disabled:opacity-60"
                                placeholder="Porcentaje (Ej. 20)"
                            />
                            <div class="w-full flex flex-col justify-center pl-2">
                                <span class="text-xs text-on-surface-variant font-bold">Precio final calculdo: <br/> S/ {{ form.sale_price }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <select v-model="form.status" class="rounded-xl border border-outline-variant/30 px-4 py-3 text-sm">
                            <option value="BORRADOR">Borrador</option>
                            <option value="PUBLICADO">Publicado</option>
                            <option value="OCULTO">Oculto</option>
                        </select>
                        <div>
                            <select v-model="form.category_id" class="w-full rounded-xl border px-4 py-3 text-sm" :class="form.errors.category_id ? 'border-red-500' : 'border-outline-variant/30'">
                                <option value="">Sin categoría</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-600">{{ form.errors.category_id }}</p>
                        </div>
                    </div>
                    <div>
                        <input type="file" accept="image/*" class="w-full text-sm" @change="(e) => { form.image_file = (e.target as HTMLInputElement).files?.[0] ?? null; }" />
                        <p v-if="form.errors.image_file" class="mt-1 text-xs text-red-600">{{ form.errors.image_file }}</p>
                    </div>
                    <img v-if="course.image" :src="course.image" class="h-16 w-16 rounded-xl object-cover border border-outline-variant/20" />

                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Detalles específicos</h3>
                    
                    <div v-if="form.type === 'en vivo' || form.type === 'masterclass' || form.type === 'evento'" class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Fecha de inicio</label>
                            <input v-model="form.start_date" type="date" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Hora</label>
                            <input v-model="form.start_time" type="time" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" />
                        </div>
                    </div>

                    <div v-if="form.type === 'grabado'">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Horas de clase (Duración)</label>
                        <input v-model.number="form.class_hours" type="number" min="0" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="Ej. 12" />
                    </div>

                    <div v-if="form.type === 'masterclass' || form.type === 'evento'">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Link Grupo de WhatsApp</label>
                        <input v-model="form.whatsapp_link" type="url" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="https://chat.whatsapp.com/..." />
                    </div>

                    <div v-if="form.type !== 'masterclass' && form.type !== 'evento'">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Objetivos del curso</label>
                        <textarea v-model="form.objectives" rows="3" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="¿Qué aprenderá el alumno? (Puedes separar por saltos de línea)"></textarea>
                    </div>

                    <div v-if="form.type !== 'masterclass' && form.type !== 'evento'">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Requisitos</label>
                        <textarea v-model="form.requirements" rows="3" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="¿Qué necesita saber el alumno antes?"></textarea>
                    </div>

                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Instructor (Opcional)</h3>
                    <div>
                        <input v-model="form.instructor_name" class="w-full rounded-xl border px-4 py-3 text-sm border-outline-variant/30" placeholder="Nombre (Ej. Mg. Juan Pérez)" />
                    </div>
                    <div>
                        <input v-model="form.instructor_title" class="w-full rounded-xl border px-4 py-3 text-sm border-outline-variant/30" placeholder="Cargo/Título (Ej. Ex-Ministro de Minería)" />
                    </div>
                    <div>
                        <textarea v-model="form.instructor_bio" rows="3" class="w-full rounded-xl border px-4 py-3 text-sm border-outline-variant/30" placeholder="Pequeña reseña o biografía del docente..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1 ml-1">Foto de Perfil</label>
                        <input type="file" accept="image/*" class="w-full text-sm" @change="(e) => { form.instructor_image_file = (e.target as HTMLInputElement).files?.[0] ?? null; }" />
                    </div>
                    <img v-if="course.instructor_image" :src="course.instructor_image" class="h-16 w-16 rounded-full object-cover border border-outline-variant/20" />

                    <hr class="border-outline-variant/20 my-4" />
                    <div class="space-y-4">
                         <h3 class="font-bold text-sm text-primary uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Certificación
                         </h3>
                         <div class="p-4 rounded-2xl bg-primary/5 border border-primary/10 space-y-3">
                             <p class="text-[11px] text-on-surface-variant font-medium italic leading-relaxed">
                                Personaliza el diseño del diploma que recibirán los alumnos al completar este curso.
                             </p>
                             <Link 
                                :href="route('admin.courses.certificate-template.edit', { course: props.course.id })"
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all"
                             >
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Diseñar Diploma
                             </Link>
                         </div>
                    </div>

                </aside>

                <main class="lg:col-span-8 space-y-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-2 bg-surface-container-low p-1 rounded-2xl border border-outline-variant/10">
                            <button 
                                @click="isExamView = false"
                                class="px-5 py-2 text-sm font-bold rounded-xl transition"
                                :class="!isExamView ? 'bg-white shadow text-primary font-bold' : 'text-on-surface-variant hover:text-primary'"
                            >
                                Contenido
                            </button>
                            <button 
                                @click="isExamView = true"
                                class="px-5 py-2 text-sm font-bold rounded-xl transition"
                                :class="isExamView ? 'bg-white shadow text-primary font-bold' : 'text-on-surface-variant hover:text-primary'"
                            >
                                Exámenes
                            </button>
                        </div>
                        <p v-if="!isExamView" class="text-xs text-on-surface-variant">Masterclass permite solo una clase</p>
                    </div>

                    <div v-if="!isExamView" class="space-y-6">
                        <div v-if="!isMasterclass">
                            <div class="flex gap-2">
                                <input v-model="newModuleTitle" class="flex-1 rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="Nombre del módulo" />
                                <button class="rounded-xl bg-[#4B5320] px-5 py-3 text-sm font-semibold text-white shadow hover:opacity-90 transition" @click="createModule">Agregar módulo</button>
                            </div>
                        </div>

                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-on-surface">Agregar clase</h3>
                        <div v-if="!isMasterclass" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <select v-model="newLesson.module_id" class="rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition">
                                <option :value="null" disabled>Selecciona un módulo (obligatorio)</option>
                                <option v-for="m in modules" :key="m.id" :value="m.id">{{ m.title }}</option>
                            </select>
                            <select v-model="newLesson.content_type" class="rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition">
                                <option value="video">Video (grabado)</option>
                                <option value="live">En vivo (link + horario)</option>
                                <option value="text">Texto</option>
                            </select>
                        </div>
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low px-4 py-3 text-sm text-on-surface-variant font-medium">
                                Masterclass: 1 clase con link de WhatsApp
                            </div>
                        </div>

                        <input v-model="newLesson.title" class="w-full rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="Título de la clase" />
                        <textarea v-model="newLesson.description" rows="2" class="w-full rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="Descripción"></textarea>

                        <input
                            v-if="!isMasterclass && newLesson.content_type === 'video'"
                            v-model="newLesson.video_url"
                            class="w-full rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary/50 transition"
                            placeholder="URL de video"
                        />
                        <div v-if="isMasterclass || newLesson.content_type === 'live'" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input
                                v-model="newLesson.live_link"
                                class="rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm md:col-span-3 focus:outline-none focus:border-primary/50 transition"
                                :placeholder="isMasterclass ? 'Link de WhatsApp (grupo)' : 'Link Zoom/Meet'"
                            />
                            <input v-if="!isMasterclass" v-model="newLesson.start_time" class="rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none" placeholder="Inicio (opcional) YYYY-MM-DD HH:MM" />
                            <input v-if="!isMasterclass" v-model="newLesson.end_time" class="rounded-xl bg-white border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none" placeholder="Fin (opcional) YYYY-MM-DD HH:MM" />
                        </div>

                        <div>
                            <button class="rounded-xl bg-[#4B5320] px-5 py-2.5 text-sm font-semibold text-white shadow hover:opacity-90 transition disabled:opacity-60" :disabled="isMasterclass && lessons.length >= 1" @click="createLesson">
                                Crear clase
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6 pt-2">
                        <div v-for="m in (isMasterclass ? [{ id: -1, title: 'Masterclass' }] : modules)" :key="m.id" class="space-y-3">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <h4 class="font-bold text-sm text-on-surface">{{ m.title }}</h4>
                                <button v-if="m.id !== -1" class="text-xs font-bold text-red-600 hover:text-red-800 transition" @click="deleteModule(m.id)">Eliminar módulo</button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="lesson in lessons.filter((l) => isMasterclass ? true : l.module_id === m.id)"
                                    :key="lesson.id"
                                    class="w-full rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm transition"
                                >
                                    <div class="flex items-start justify-between gap-2 cursor-pointer pb-2" @click="toggleLesson(lesson.id)">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-on-surface truncate">{{ lesson.title }}</p>
                                            <p class="text-xs text-on-surface-variant mt-0.5">{{ lesson.content_type }}</p>
                                        </div>
                                        <button class="text-xs font-bold text-red-600 hover:text-red-800 transition pt-0.5" @click.stop="deleteLesson(lesson.id)">Eliminar</button>
                                    </div>

                                    <div v-if="openLessonId === lesson.id || true" class="mt-4 pt-4 border-t border-outline-variant/10">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-sm font-bold text-on-surface">Recursos</p>
                                            <button class="text-sm text-[#4B5320] font-bold hover:underline" @click.stop="startAddMaterial(lesson.id)">+ Agregar</button>
                                        </div>

                                        <div v-if="addingMaterialFor === lesson.id" class="mb-3 space-y-2 rounded-xl bg-surface-container-lowest p-3 border border-outline-variant/10">
                                            <input v-model="newMaterial.title" class="w-full rounded-xl bg-white border border-outline-variant/20 px-3 py-2 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="Nombre del recurso" />
                                            <input type="file" class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" @change="(e) => { newMaterial.file = (e.target as HTMLInputElement).files?.[0] ?? null; }" />
                                            <input v-model="newMaterial.external_url" class="w-full rounded-xl bg-white border border-outline-variant/20 px-3 py-2 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="o link externo" />
                                            <div class="flex justify-end gap-2 pt-1">
                                                <button class="rounded-lg border px-3 py-1 text-xs font-bold hover:bg-surface-container-low transition" @click.stop="addingMaterialFor = null">Cancelar</button>
                                                <button class="rounded-lg bg-[#4B5320] px-3 py-1 text-xs font-bold text-white shadow hover:opacity-90 transition" @click.stop="createMaterial(lesson.id)">Guardar</button>
                                            </div>
                                        </div>

                                        <ul class="space-y-3">
                                            <li
                                                v-for="mat in (materialsByLesson[lesson.id] ?? [])"
                                                :key="mat.id"
                                                class="flex items-start justify-between gap-3 group"
                                            >
                                                <div class="min-w-0">
                                                    <p class="text-[13px] font-semibold text-on-surface truncate">{{ mat.title }}</p>
                                                    <p class="text-[11px] text-on-surface-variant truncate mt-0.5">{{ mat.external_url || mat.file_path || mat.kind }}</p>
                                                </div>
                                                <button class="text-xs font-bold text-red-600 hover:text-red-800 transition pt-0.5" @click.stop="deleteMaterial(lesson.id, mat.id)">Eliminar</button>
                                            </li>
                                            <li v-if="!(materialsByLesson[lesson.id]?.length)" class="text-xs text-on-surface-variant">Sin recursos aún.</li>
                                        </ul>
                                    </div>
                                </div>

                                <p v-if="!lessons.filter((l) => isMasterclass ? true : l.module_id === m.id).length" class="text-xs text-on-surface-variant">
                                    Sin clases todavía.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam View section -->
                <div v-else class="space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="bg-surface-container-low border border-outline-variant/20 rounded-[2rem] p-8 space-y-6">
                            <h3 class="font-bold text-lg">Nuevo Examen / Evaluación</h3>
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
                                <div class="md:col-span-2">
                                     <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Título</label>
                                     <input v-model="newQuiz.title" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm focus:outline-none focus:border-primary transition" placeholder="Nombre (Ej. Evaluación Final)" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Nota min.</label>
                                    <input v-model.number="newQuiz.minimum_score" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="14" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Tiempo (m)</label>
                                    <input v-model.number="newQuiz.time_limit" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="30" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Intentos permitidos</label>
                                    <input v-model.number="newQuiz.max_attempts" type="number" min="1" class="w-full rounded-xl border border-outline-variant/30 px-4 py-3 text-sm" placeholder="1" />
                                </div>
                                <div class="pt-4">
                                    <button @click="createQuiz" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-xl hover:opacity-90 transition">Crear Examen</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="quizzes.length === 0" class="py-20 text-center border-2 border-dashed border-outline-variant/10 rounded-[3rem]">
                             <p class="text-on-surface-variant italic font-serif">Aún no has creado exámenes para este curso.</p>
                        </div>

                        <div v-for="quiz in quizzes" :key="quiz.id" class="rounded-[2.5rem] bg-white border border-outline-variant/10 shadow-sm overflow-hidden transition-all group hover:shadow-xl hover:shadow-primary/5">
                            
                            <!-- Header Vista Normal -->
                            <div v-if="editingQuizId !== quiz.id" class="p-8 flex items-center justify-between cursor-pointer" @click="toggleQuiz(quiz.id)">
                                <div class="flex items-center gap-6">
                                    <div class="p-4 bg-primary/5 rounded-[1.5rem] group-hover:bg-primary/10 transition-colors">
                                         <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg text-on-surface leading-tight">{{ quiz.title }}</h4>
                                        <div class="flex items-center gap-4 mt-1 text-xs text-on-surface-variant font-bold uppercase tracking-widest">
                                            <span>{{ quiz.questions?.length ?? 0 }} preguntas</span>
                                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                                            <span>Nota min: {{ quiz.minimum_score }}</span>
                                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                                            <span>{{ quiz.time_limit }} minutos</span>
                                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                                            <span>{{ quiz.max_attempts }} intentos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button class="text-xs font-bold text-indigo-600 px-4 py-2 hover:bg-indigo-50 rounded-xl transition" @click.stop="startEditQuiz(quiz)">Editar</button>
                                    <button class="text-xs font-bold text-red-600 px-4 py-2 hover:bg-red-50 rounded-xl transition" @click.stop="deleteQuiz(quiz.id)">Eliminar</button>
                                    <div class="p-2 bg-surface-container-low rounded-xl transition group-hover:bg-surface-container-high" :class="{ 'rotate-180': openQuizId === quiz.id }">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Header Vista Edición -->
                            <div v-else class="p-6 bg-surface-container-lowest border-b border-outline-variant/10">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Título</label>
                                        <input v-model="editQuizData.title" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Nota min.</label>
                                        <input v-model.number="editQuizData.minimum_score" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Tiempo (m)</label>
                                        <input v-model.number="editQuizData.time_limit" type="number" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Intentos</label>
                                        <input v-model.number="editQuizData.max_attempts" type="number" min="1" class="w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm" />
                                    </div>
                                    <div class="flex items-center gap-2 mt-4">
                                        <button @click="editingQuizId = null" class="flex-[1] rounded-xl border border-outline-variant/30 px-2 py-2 text-xs font-bold hover:bg-surface-container transition">X</button>
                                        <button @click="saveEditQuiz" class="flex-[3] rounded-xl bg-primary text-white px-3 py-2 text-xs font-bold hover:opacity-90 transition">Guardar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Content (Questions) -->
                            <div v-show="openQuizId === quiz.id" class="px-8 pb-10 border-t border-outline-variant/10 animate-in fade-in duration-500">
                                
                                <!-- Tabs -->
                                <div class="flex items-center gap-6 border-b border-outline-variant/10 pt-4 mb-6">
                                    <button @click="activeQuizTabs[quiz.id] = 'questions'" class="pb-3 text-sm font-bold uppercase tracking-widest transition-all" :class="activeQuizTabs[quiz.id] === 'questions' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'">
                                        Preguntas
                                    </button>
                                    <button @click="activeQuizTabs[quiz.id] = 'results'" class="pb-3 text-sm font-bold uppercase tracking-widest transition-all" :class="activeQuizTabs[quiz.id] === 'results' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'">
                                        Resultados ({{ quiz.attempts?.length ?? 0 }})
                                    </button>
                                </div>

                                <div v-if="activeQuizTabs[quiz.id] === 'questions'" class="space-y-8">
                                    <div class="flex items-center justify-between">
                                        <h5 class="font-bold text-on-surface uppercase tracking-widest text-[10px]">Banco de Preguntas</h5>
                                        <button @click="startAddQuestion(quiz.id)" class="text-sm font-bold text-primary hover:underline">+ Agregar Pregunta</button>
                                    </div>

                                    <!-- Add Question Form -->
                                    <div v-if="addingQuestionFor === quiz.id" class="p-8 bg-surface-container-lowest rounded-[2rem] border border-outline-variant/20 space-y-6">
                                        <input v-model="newQuestion.question" class="w-full rounded-xl border border-outline-variant/30 px-5 py-4 text-sm font-bold italic" placeholder="Enunciado de la pregunta..." />
                                        
                                        <div class="space-y-4">
                                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest px-1">Opciones de respuesta</p>
                                            <div v-for="(ans, idx) in newQuestion.answers" :key="idx" class="flex items-center gap-3">
                                                <button 
                                                    @click="newQuestion.answers.forEach((a, i) => a.is_correct = (i === idx))"
                                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                    :class="ans.is_correct ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-outline-variant'"
                                                >
                                                    <svg v-if="ans.is_correct" class="w-3 h-3 " fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                                </button>
                                                <input v-model="ans.answer_text" class="flex-1 rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm" placeholder="Texto de la opción..." />
                                                <button v-if="newQuestion.answers.length > 2" @click="newQuestion.answers.splice(idx, 1)" class="text-on-surface-variant hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            <button @click="addAnswerOption" class="text-xs font-bold text-on-surface-variant hover:text-primary transition underline decoration-dotted">+ Agregar opción</button>
                                        </div>

                                        <div class="flex justify-end gap-3 pt-4">
                                            <button @click="addingQuestionFor = null; editingQuestionId = null;" class="px-6 py-3 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-xl transition">Cancelar</button>
                                            <button @click="saveQuestion(quiz.id)" class="px-8 py-3 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:opacity-90 active:scale-95 transition">Guardar Pregunta</button>
                                        </div>
                                    </div>

                                    <!-- Questions List -->
                                    <div class="space-y-4">
                                        <div v-for="(q, qidx) in quiz.questions" :key="q.id" class="p-6 rounded-3xl bg-surface-container-lowest border border-outline-variant/10 flex items-start justify-between gap-4">
                                            <div class="space-y-4 min-w-0 flex-1">
                                                <p class="text-sm font-bold text-on-surface leading-relaxed"><span class="text-primary tabular-nums mr-2">{{ Number(qidx) + 1 }}.</span> {{ q.question }}</p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <div v-for="a in q.answers" :key="a.id" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-white/50 border border-outline-variant/5">
                                                         <div class="w-2 h-2 rounded-full" :class="a.is_correct ? 'bg-emerald-500 shadow-lg shadow-emerald-500/20' : 'bg-gray-200'"></div>
                                                         <span class="text-xs font-medium" :class="{ 'text-emerald-700 font-bold': a.is_correct }">{{ a.answer_text }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <button @click="startEditQuestion(quiz.id, q)" title="Editar pregunta" class="p-2 text-on-surface-variant hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <button @click="deleteQuestion(quiz.id, q.id)" title="Eliminar pregunta" class="p-2 text-on-surface-variant hover:text-red-600 hover:bg-red-50 rounded-xl transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <div v-else-if="activeQuizTabs[quiz.id] === 'results'" class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h5 class="font-bold text-on-surface uppercase tracking-widest text-[10px]">Intentos de Alumnos</h5>
                                    </div>
                                    <div v-if="!quiz.attempts || quiz.attempts.length === 0" class="py-12 border-2 border-dashed border-outline-variant/10 rounded-3xl text-center">
                                        <p class="text-on-surface-variant text-sm font-bold opacity-70">Aún no hay intentos registrados.</p>
                                    </div>
                                    <div v-else class="space-y-3">
                                        <div v-for="attempt in quiz.attempts" :key="attempt.id" class="flex items-center justify-between p-4 bg-surface-container-low border border-outline-variant/10 rounded-2xl">
                                            <div>
                                                <p class="text-sm font-bold text-on-surface">{{ attempt.user?.name ?? 'Desconocido' }} <span class="text-xs text-on-surface-variant font-normal">({{ attempt.user?.email }})</span></p>
                                                <p class="text-xs text-on-surface-variant mt-1">{{ attempt.completed_at ? new Date(attempt.completed_at).toLocaleString() : 'En curso' }}</p>
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
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
