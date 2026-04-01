<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
    course: any;
    categories: Array<{ id: number; name: string }>;
}>();

const lessons = ref<any[]>(props.course.lessons ?? []);
const modules = ref<any[]>(props.course.modules ?? []);
const openLessonId = ref<number | null>(null);
const materialsByLesson = ref<Record<number, any[]>>({});

const form = useForm({
    title: props.course.title ?? '',
    description: props.course.description ?? '',
    price: Number(props.course.price ?? 0),
    discount_enabled: props.course.sale_price != null && Number(props.course.sale_price) > 0,
    sale_price: Number(props.course.sale_price ?? 0),
    type: props.course.type ?? 'grabado',
    status: props.course.status ?? 'BORRADOR',
    category_id: props.course.category_id ?? '',
    certificate_enabled: props.course.certificate_enabled ?? true,
    image_file: null as File | null,
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
            certificate_enabled: !!data.certificate_enabled,
            _method: 'PUT'
        };
        if (!payload.image_file) {
            delete payload.image_file;
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
                            Aplicar descuento
                        </label>
                        <input
                            v-model.number="form.sale_price"
                            :disabled="!form.discount_enabled"
                            type="number"
                            min="0"
                            class="mt-2 w-full rounded-xl border border-outline-variant/30 px-4 py-2 text-sm disabled:opacity-60"
                            placeholder="Precio final con descuento (S/)"
                        />
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
                </aside>

                <main class="lg:col-span-8 space-y-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <h2 class="font-bold text-base text-on-surface">Módulos, clases y recursos</h2>
                        <p class="text-xs text-on-surface-variant">Masterclass permite solo una clase</p>
                    </div>

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
                </main>
            </div>
        </div>
    </AppLayout>
</template>
