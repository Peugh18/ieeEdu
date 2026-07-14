<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseCurriculumApi } from '@/composables/admin/course-editor/useCourseCurriculum';
import type { CourseLesson } from '@/types/course';
import AppSelect from '@/components/ui/AppSelect.vue';

defineProps<{
    show: boolean;
    isMasterclass: boolean;
    curriculum: CourseCurriculumApi;
    getLessonStatus: (lesson: CourseLesson) => { label: string; class: string };
    formatLocalTime: (iso: string) => string;
}>();
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 mb-10 max-w-3xl">
            <h2 class="mb-3 font-serif text-3xl font-bold tracking-tight text-on-surface">
                Estructura Curricular <span class="font-light italic">(Sílabo)</span>
            </h2>
            <p class="mb-8 font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                Construye los módulos, clases y agrega material descargable. Un buen sílabo reduce el soporte técnico y mejora la tasa de retención.
            </p>
            <p
                v-if="isMasterclass"
                class="flex items-center gap-3 rounded-[1rem] border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 px-5 py-3 font-sans text-[13px] font-bold text-amber-800 dark:text-amber-200"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    />
                </svg>
                NOTA: La condición de Masterclass gratuita limita a la creación obligatoria de 1 única clase en el currículo.
            </p>
        </div>

        <div class="relative z-10 mt-4 w-full space-y-8 rounded-[2rem] border border-transparent bg-surface-container-highest p-8 shadow-sm">
            <div v-if="!isMasterclass">
                <div class="flex flex-col gap-4 sm:flex-row">
                    <input
                        v-model="curriculum.newModuleTitle"
                        class="flex-1 rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                        placeholder="Nombre del módulo"
                    />
                    <button
                        type="button"
                        class="rounded-full bg-gradient-to-br from-primary to-[#707040] px-8 py-4 font-sans text-[13px] font-bold tracking-wide text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98] md:w-auto"
                        @click="curriculum.createModule"
                    >
                        Agregar módulo
                    </button>
                </div>
            </div>

            <div class="space-y-5">
                <h3 class="font-sans text-[16px] font-bold text-on-surface">Agregar clase</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <AppSelect
                        v-if="!isMasterclass"
                        v-model="curriculum.newLesson.module_id"
                        :options="[
                            { value: null, label: 'Selecciona un módulo (obligatorio)' },
                            ...curriculum.modules.map(m => ({ value: m.id, label: m.title }))
                        ]"
                        class="bg-white border-0 shadow-sm text-[14px]"
                    />
                    <div v-else class="rounded-[1.5rem] border border-transparent bg-white px-6 py-4 font-sans text-[14px] font-bold text-primary shadow-sm">
                        Masterclass (Clase Única)
                    </div>
                    <AppSelect
                        v-model="curriculum.newLesson.content_type"
                        :options="[
                            { value: 'video', label: 'Video (grabado)' },
                            { value: 'live', label: 'En vivo (link + horario)' },
                            { value: 'text', label: 'Texto' }
                        ]"
                        class="bg-white border-0 shadow-sm text-[14px]"
                    />
                </div>

                <input
                    v-model="curriculum.newLesson.title"
                    class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                    placeholder="Título de la clase"
                />
                <textarea
                    v-model="curriculum.newLesson.description"
                    rows="2"
                    class="w-full resize-none rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                    placeholder="Descripción"
                />

                <input
                    v-if="curriculum.newLesson.content_type === 'video'"
                    v-model="curriculum.newLesson.video_url"
                    class="w-full rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                    placeholder="URL de video (Ej. YouTube, Vimeo)"
                />
                <div v-if="curriculum.newLesson.content_type === 'live'" class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <input
                        v-model="curriculum.newLesson.live_link"
                        class="rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-primary shadow-sm outline-none transition-all placeholder:text-primary/50 focus:ring-2 focus:ring-primary/20 md:col-span-1"
                        placeholder="Link Zoom/Meet"
                    />
                    <input
                        v-model="curriculum.newLesson.start_time"
                        type="datetime-local"
                        class="rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all focus:ring-2 focus:ring-primary/20"
                    />
                    <input
                        v-model="curriculum.newLesson.end_time"
                        type="datetime-local"
                        class="rounded-[1.5rem] border-transparent bg-white px-6 py-4 font-sans text-[14px] text-on-surface shadow-sm outline-none transition-all focus:ring-2 focus:ring-primary/20"
                    />
                </div>

                <div class="pt-2">
                    <button
                        type="button"
                        class="rounded-full bg-gradient-to-r from-primary to-[#6b6b34] px-8 py-4 font-sans text-[14px] font-bold tracking-wide text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60 disabled:hover:scale-100"
                        :disabled="isMasterclass && curriculum.lessons.length >= 1"
                        @click="curriculum.createLesson"
                    >
                        Crear clase
                    </button>
                </div>
            </div>

            <div class="space-y-6 pt-2">
                <div v-for="m in isMasterclass ? [{ id: -1, title: 'Masterclass' }] : curriculum.modules" :key="m.id" class="space-y-3">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h4 class="text-sm font-bold text-on-surface">{{ m.title }}</h4>
                        <button
                            v-if="m.id !== -1"
                            type="button"
                            class="text-xs font-bold text-red-600 transition hover:text-red-800"
                            @click="curriculum.deleteModule(m.id)"
                        >
                            Eliminar módulo
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="lesson in curriculum.lessonsForModule(m.id)"
                            :key="lesson.id"
                            class="w-full rounded-2xl border border-outline-variant/10 bg-white p-4 shadow-sm transition"
                        >
                            <div
                                v-if="curriculum.editingLessonId !== lesson.id"
                                class="flex cursor-pointer items-start justify-between gap-2 pb-2"
                                @click="curriculum.toggleLesson(lesson.id)"
                            >
                                <div class="min-w-0">
                                    <div class="mb-1 flex items-center gap-2">
                                        <p class="truncate text-sm font-bold text-on-surface">{{ lesson.title }}</p>
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider',
                                                getLessonStatus(lesson).class,
                                            ]"
                                        >
                                            {{ getLessonStatus(lesson).label }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
                                        <span v-if="lesson.content_type === 'video'" class="text-blue-600">Video</span>
                                        <span v-else-if="lesson.content_type === 'live'" class="text-emerald-600">Zoom/Meet</span>
                                        <span v-else class="text-on-surface-variant">{{ lesson.content_type }}</span>
                                        <span v-if="lesson.start_time" class="ml-2 text-on-surface-variant/60"
                                            >· {{ formatLocalTime(lesson.start_time) }}</span
                                        >
                                    </p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        class="px-2 py-1 text-xs font-bold text-indigo-600 transition hover:text-indigo-800"
                                        @click.stop="curriculum.startEditLesson(lesson)"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        type="button"
                                        class="px-2 py-1 text-xs font-bold text-red-600 transition hover:text-red-800"
                                        @click.stop="curriculum.deleteLesson(lesson.id)"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>

                            <div v-else class="space-y-4 pb-4">
                                <div class="mb-2 flex items-center justify-between">
                                    <h5 class="text-xs font-bold uppercase text-indigo-600">Editando Clase</h5>
                                    <button
                                        type="button"
                                        class="text-xs font-bold text-on-surface-variant hover:text-on-surface"
                                        @click="curriculum.cancelEditLesson"
                                    >
                                        Cancelar
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant">Título</label>
                                        <input
                                            v-model="curriculum.editLessonData.title"
                                            class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant"
                                            >Tipo de Contenido</label
                                        >
                                        <AppSelect
                                            v-model="curriculum.editLessonData.content_type"
                                            :options="[
                                                { value: 'video', label: 'Video (grabado)' },
                                                { value: 'live', label: 'En vivo (link + horario)' },
                                                { value: 'text', label: 'Texto' }
                                            ]"
                                            class="border-outline-variant/30 text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant"
                                        >Descripción (Opcional)</label
                                    >
                                    <textarea
                                        v-model="curriculum.editLessonData.description"
                                        rows="2"
                                        class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                    />
                                </div>

                                <div v-if="curriculum.editLessonData.content_type === 'video'">
                                    <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant">URL de Video</label>
                                    <input
                                        v-model="curriculum.editLessonData.video_url"
                                        class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                    />
                                </div>

                                <div v-if="curriculum.editLessonData.content_type === 'live'" class="space-y-3">
                                    <div>
                                        <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant"
                                            >Link de la sesión (Zoom/Meet)</label
                                        >
                                        <input
                                            v-model="curriculum.editLessonData.live_link"
                                            class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                        />
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant"
                                                >Fecha/Hora Inicio</label
                                            >
                                            <input
                                                v-model="curriculum.editLessonData.start_time"
                                                type="datetime-local"
                                                class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                            />
                                        </div>
                                        <div>
                                            <label class="mb-1 ml-1 block text-[10px] font-bold uppercase text-on-surface-variant"
                                                >Fecha/Hora Fin</label
                                            >
                                            <input
                                                v-model="curriculum.editLessonData.end_time"
                                                type="datetime-local"
                                                class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm transition focus:border-primary focus:outline-none"
                                            />
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 p-3">
                                        <p class="text-[10px] leading-tight text-amber-800 dark:text-amber-200">
                                            <strong>Nota para Post-En-Vivo:</strong> Si ya pasó la sesión y tienes la grabación, cambia el tipo a
                                            <strong>Video</strong> y pega la URL de YouTube/Vimeo.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button
                                        type="button"
                                        class="rounded-xl bg-primary px-6 py-2 text-xs font-bold text-white shadow-lg transition hover:opacity-90 active:scale-95"
                                        @click="curriculum.saveEditLesson"
                                    >
                                        Guardar Clase
                                    </button>
                                </div>
                            </div>

                            <div v-if="curriculum.openLessonId === lesson.id" class="mt-4 border-t border-outline-variant/10 pt-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-sm font-bold text-on-surface">Recursos</p>
                                    <button
                                        type="button"
                                        class="text-sm font-bold text-[#4B5320] hover:underline"
                                        @click.stop="curriculum.startAddMaterial(lesson.id)"
                                    >
                                        + Agregar
                                    </button>
                                </div>

                                <div
                                    v-if="curriculum.addingMaterialFor === lesson.id"
                                    class="mb-3 space-y-2 rounded-xl border border-outline-variant/10 bg-surface-container-lowest p-3"
                                >
                                    <input
                                        v-model="curriculum.newMaterial.title"
                                        class="w-full rounded-xl border border-outline-variant/20 bg-white px-3 py-2 text-sm transition focus:border-primary/50 focus:outline-none"
                                        placeholder="Nombre del recurso"
                                    />
                                    <input
                                        type="file"
                                        class="w-full text-sm text-on-surface-variant transition file:mr-3 file:rounded-full file:border-0 file:bg-primary/10 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/20"
                                        @change="
                                            (e) => {
                                                curriculum.newMaterial.file = (e.target as HTMLInputElement).files?.[0] ?? null;
                                            }
                                        "
                                    />
                                    <input
                                        v-model="curriculum.newMaterial.external_url"
                                        class="w-full rounded-xl border border-outline-variant/20 bg-white px-3 py-2 text-sm transition focus:border-primary/50 focus:outline-none"
                                        placeholder="o link externo"
                                    />
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button
                                            type="button"
                                            class="rounded-lg border px-3 py-1 text-xs font-bold transition hover:bg-surface-container-low"
                                            @click.stop="curriculum.addingMaterialFor = null"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg bg-[#4B5320] px-3 py-1 text-xs font-bold text-white shadow transition hover:opacity-90"
                                            @click.stop="curriculum.createMaterial(lesson.id)"
                                        >
                                            Guardar
                                        </button>
                                    </div>
                                </div>

                                <ul class="space-y-3">
                                    <li
                                        v-for="mat in curriculum.materialsByLesson[lesson.id] ?? []"
                                        :key="mat.id"
                                        class="group flex items-start justify-between gap-3"
                                    >
                                        <div class="min-w-0">
                                            <p class="truncate text-[13px] font-semibold text-on-surface">{{ mat.title }}</p>
                                            <p class="mt-0.5 truncate text-[11px] text-on-surface-variant">
                                                {{ mat.external_url || mat.file_path || mat.kind }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            class="pt-0.5 text-xs font-bold text-red-600 transition hover:text-red-800"
                                            @click.stop="curriculum.deleteMaterial(lesson.id, mat.id)"
                                        >
                                            Eliminar
                                        </button>
                                    </li>
                                    <li v-if="!curriculum.materialsByLesson[lesson.id]?.length" class="text-xs text-on-surface-variant">
                                        Sin recursos aún.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <p v-if="!curriculum.lessonsForModule(m.id).length" class="text-xs text-on-surface-variant">Sin clases todavía.</p>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
