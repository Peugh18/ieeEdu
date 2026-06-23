import axios from '@/lib/axios';
import type { CourseLesson, CourseModule } from '@/types/course';
import type { MaterialsByLesson, NewLessonDraft, NewMaterialDraft } from '@/types/course-editor';
import { computed, onMounted, ref, type ComputedRef } from 'vue';

function emptyLesson(moduleId: number | null = null): NewLessonDraft {
    return {
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

export function useCourseCurriculum(
    courseId: number,
    initialModules: CourseModule[],
    initialLessons: CourseLesson[],
    isMasterclass: ComputedRef<boolean>,
    onLessonSaved?: () => void,
) {
    const modules = ref<CourseModule[]>([...initialModules]);
    const lessons = ref<CourseLesson[]>([...initialLessons]);
    const newModuleTitle = ref('');
    const newLesson = ref<NewLessonDraft>(emptyLesson());
    const newMaterial = ref<NewMaterialDraft>({ title: '', external_url: '', file: null });
    const addingMaterialFor = ref<number | null>(null);
    const openLessonId = ref<number | null>(null);
    const editingLessonId = ref<number | null>(null);
    const editLessonData = ref<Record<string, unknown>>({
        title: '',
        description: '',
        content_type: 'video',
        video_url: '',
        live_link: '',
        start_time: '',
        end_time: '',
        module_id: null,
    });
    const materialsByLesson = ref<MaterialsByLesson>({});

    const displayModules = computed(() => (isMasterclass.value ? [{ id: -1, title: 'Masterclass', sort_order: 0 }] : modules.value));

    function resetNewLesson(moduleId: number | null = null) {
        newLesson.value = emptyLesson(moduleId);
    }

    async function createModule() {
        const title = newModuleTitle.value.trim();
        if (!title) return;
        const res = await axios.post(route('admin.courses.modules.store', { course: courseId }), { title });
        modules.value.push(res.data);
        newModuleTitle.value = '';
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

        const payload: Record<string, unknown> = {
            module_id: isMasterclass.value ? null : newLesson.value.module_id,
            title: newLesson.value.title.trim(),
            description: newLesson.value.description,
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
        if (!isMasterclass.value && !payload.module_id) {
            alert('Primero selecciona o crea un módulo para esta clase.');
            return;
        }

        try {
            const res = await axios.post(route('admin.courses.lessons.store', { course: courseId }), payload);
            lessons.value.push(res.data);
            resetNewLesson(isMasterclass.value ? null : newLesson.value.module_id);
        } catch (e: unknown) {
            const err = e as { response?: { data?: { message?: string; errors?: { title?: string[] } } } };
            const msg = err?.response?.data?.message ?? err?.response?.data?.errors?.title?.[0] ?? 'No se pudo crear la clase.';
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
        await ensureMaterials(lessonId);
    }

    function startEditLesson(lesson: CourseLesson) {
        editingLessonId.value = lesson.id;
        editLessonData.value = {
            title: lesson.title,
            description: lesson.description || '',
            content_type: lesson.content_type,
            video_url: lesson.video_url || '',
            live_link: lesson.live_link || '',
            start_time: lesson.start_time ? lesson.start_time.substring(0, 16).replace(' ', 'T') : '',
            end_time: lesson.end_time ? lesson.end_time.substring(0, 16).replace(' ', 'T') : '',
            module_id: lesson.module_id,
        };
    }

    async function saveEditLesson() {
        if (!editingLessonId.value) return;
        try {
            const res = await axios.put(route('admin.lessons.update', { lesson: editingLessonId.value }), editLessonData.value);
            const idx = lessons.value.findIndex((l) => l.id === editingLessonId.value);
            if (idx !== -1) lessons.value[idx] = res.data;
            editingLessonId.value = null;
            onLessonSaved?.();
        } catch (e: unknown) {
            const err = e as { response?: { data?: { message?: string } } };
            alert(err?.response?.data?.message ?? 'Error al actualizar la clase');
        }
    }

    function cancelEditLesson() {
        editingLessonId.value = null;
    }

    async function ensureMaterials(lessonId: number) {
        if (materialsByLesson.value[lessonId]) return;
        try {
            const res = await axios.get(route('admin.lessons.materials.index', { lesson: lessonId }));
            materialsByLesson.value[lessonId] = res.data;
        } catch {
            materialsByLesson.value[lessonId] = [];
        }
    }

    function startAddMaterial(lessonId: number) {
        addingMaterialFor.value = lessonId;
        newMaterial.value = { title: '', external_url: '', file: null };
        ensureMaterials(lessonId);
    }

    async function createMaterial(lessonId: number) {
        const payload = new FormData();
        payload.append('title', newMaterial.value.title.trim() || 'Recurso');
        if (newMaterial.value.file) {
            payload.append('file', newMaterial.value.file);
        } else if (newMaterial.value.external_url.trim()) {
            payload.append('kind', 'url');
            payload.append('external_url', newMaterial.value.external_url.trim());
        } else {
            alert('Sube un archivo o agrega un enlace.');
            return;
        }

        try {
            const res = await axios.post(route('admin.lessons.materials.store', { lesson: lessonId }), payload);
            materialsByLesson.value[lessonId] = [...(materialsByLesson.value[lessonId] ?? []), res.data];
            addingMaterialFor.value = null;
            newMaterial.value = { title: '', external_url: '', file: null };
        } catch (e: unknown) {
            const err = e as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
            const errors = err?.response?.data?.errors;
            const msg = errors ? Object.values(errors).flat().join('\n') : (err?.response?.data?.message ?? 'No se pudo guardar el recurso.');
            alert(msg);
        }
    }

    async function deleteMaterial(lessonId: number, materialId: number) {
        if (!confirm('Eliminar recurso?')) return;
        await axios.delete(route('admin.materials.destroy', { material: materialId }));
        materialsByLesson.value[lessonId] = (materialsByLesson.value[lessonId] ?? []).filter((m) => m.id !== materialId);
    }

    function lessonsForModule(moduleId: number) {
        return lessons.value.filter((l) => (isMasterclass.value ? true : l.module_id === moduleId));
    }

    onMounted(() => {
        lessons.value.forEach((l) => ensureMaterials(l.id));
    });

    return {
        modules,
        lessons,
        newModuleTitle,
        newLesson,
        newMaterial,
        addingMaterialFor,
        openLessonId,
        editingLessonId,
        editLessonData,
        materialsByLesson,
        displayModules,
        createModule,
        deleteModule,
        createLesson,
        deleteLesson,
        toggleLesson,
        startEditLesson,
        saveEditLesson,
        cancelEditLesson,
        startAddMaterial,
        createMaterial,
        deleteMaterial,
        lessonsForModule,
    };
}

export type CourseCurriculumApi = ReturnType<typeof useCourseCurriculum>;
