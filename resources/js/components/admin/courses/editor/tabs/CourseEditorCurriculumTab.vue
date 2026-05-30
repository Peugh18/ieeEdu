<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { CourseCurriculumApi } from '@/composables/admin/course-editor/useCourseCurriculum';
import type { CourseLesson } from '@/types/course';

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
        <div class="max-w-3xl relative z-10 mb-10">
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-3 tracking-tight">
                Estructura Curricular <span class="italic font-light">(Sílabo)</span>
            </h2>
            <p class="text-[15px] text-on-surface-variant font-sans font-medium mb-8 leading-relaxed">
                Construye los módulos, clases y agrega material descargable. Un buen sílabo reduce el soporte técnico y mejora la tasa de retención.
            </p>
            <p v-if="isMasterclass" class="text-[13px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-5 py-3 flex items-center gap-3 rounded-[1rem] font-sans">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                NOTA: La condición de Masterclass gratuita limita a la creación obligatoria de 1 única clase en el currículo.
            </p>
        </div>

        <div class="space-y-8 mt-4 relative z-10 w-full bg-surface-container-highest p-8 rounded-[2rem] border border-transparent shadow-sm">
            <div v-if="!isMasterclass">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input v-model="curriculum.newModuleTitle" class="flex-1 rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm placeholder:text-outline-variant" placeholder="Nombre del módulo" />
                    <button type="button" class="rounded-full bg-gradient-to-br from-primary to-[#707040] px-8 py-4 text-[13px] font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all tracking-wide font-sans md:w-auto" @click="curriculum.createModule">Agregar módulo</button>
                </div>
            </div>

            <div class="space-y-5">
                <h3 class="text-[16px] font-bold text-on-surface font-sans">Agregar clase</h3>
                <div v-if="!isMasterclass" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <select v-model="curriculum.newLesson.module_id" class="rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm appearance-none">
                        <option :value="null" disabled>Selecciona un módulo (obligatorio)</option>
                        <option v-for="m in curriculum.modules" :key="m.id" :value="m.id">{{ m.title }}</option>
                    </select>
                    <select v-model="curriculum.newLesson.content_type" class="rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm appearance-none">
                        <option value="video">Video (grabado)</option>
                        <option value="live">En vivo (link + horario)</option>
                        <option value="text">Texto</option>
                    </select>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="rounded-[1.5rem] border border-transparent bg-white shadow-sm px-6 py-4 text-[14px] text-primary font-bold font-sans">
                        Masterclass: 1 clase con link de WhatsApp
                    </div>
                </div>

                <input v-model="curriculum.newLesson.title" class="w-full rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm placeholder:text-outline-variant" placeholder="Título de la clase" />
                <textarea v-model="curriculum.newLesson.description" rows="2" class="w-full rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm placeholder:text-outline-variant resize-none" placeholder="Descripción" />

                <input
                    v-if="!isMasterclass && curriculum.newLesson.content_type === 'video'"
                    v-model="curriculum.newLesson.video_url"
                    class="w-full rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm placeholder:text-outline-variant"
                    placeholder="URL de video (Ej. YouTube, Vimeo)"
                />
                <div v-if="isMasterclass || curriculum.newLesson.content_type === 'live'" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <input
                        v-model="curriculum.newLesson.live_link"
                        class="rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-primary md:col-span-3 focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm placeholder:text-primary/50"
                        :placeholder="isMasterclass ? 'Link de WhatsApp (grupo)' : 'Link Zoom/Meet'"
                    />
                    <input v-if="!isMasterclass" v-model="curriculum.newLesson.start_time" type="datetime-local" class="rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm" />
                    <input v-if="!isMasterclass" v-model="curriculum.newLesson.end_time" type="datetime-local" class="rounded-[1.5rem] bg-white border-transparent px-6 py-4 text-[14px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none shadow-sm" />
                </div>

                <div class="pt-2">
                    <button type="button" class="rounded-full bg-gradient-to-r from-primary to-[#6b6b34] px-8 py-4 text-[14px] font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all tracking-wide disabled:opacity-60 disabled:hover:scale-100 font-sans" :disabled="isMasterclass && curriculum.lessons.length >= 1" @click="curriculum.createLesson">
                        Crear clase
                    </button>
                </div>
            </div>

            <div class="space-y-6 pt-2">
                <div v-for="m in (isMasterclass ? [{ id: -1, title: 'Masterclass' }] : curriculum.modules)" :key="m.id" class="space-y-3">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h4 class="font-bold text-sm text-on-surface">{{ m.title }}</h4>
                        <button v-if="m.id !== -1" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition" @click="curriculum.deleteModule(m.id)">Eliminar módulo</button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="lesson in curriculum.lessonsForModule(m.id)"
                            :key="lesson.id"
                            class="w-full rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm transition"
                        >
                            <div v-if="curriculum.editingLessonId !== lesson.id" class="flex items-start justify-between gap-2 cursor-pointer pb-2" @click="curriculum.toggleLesson(lesson.id)">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-bold text-sm text-on-surface truncate">{{ lesson.title }}</p>
                                        <span :class="['text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider', getLessonStatus(lesson).class]">
                                            {{ getLessonStatus(lesson).label }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wider font-bold">
                                        <span v-if="lesson.content_type === 'video'" class="text-blue-600">Video</span>
                                        <span v-else-if="lesson.content_type === 'live'" class="text-emerald-600">Zoom/Meet</span>
                                        <span v-else class="text-on-surface-variant">{{ lesson.content_type }}</span>
                                        <span v-if="lesson.start_time" class="ml-2 text-on-surface-variant/60">· {{ formatLocalTime(lesson.start_time) }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition px-2 py-1" @click.stop="curriculum.startEditLesson(lesson)">Editar</button>
                                    <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition px-2 py-1" @click.stop="curriculum.deleteLesson(lesson.id)">Eliminar</button>
                                </div>
                            </div>

                            <div v-else class="space-y-4 pb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-xs font-bold text-indigo-600 uppercase">Editando Clase</h5>
                                    <button type="button" class="text-xs font-bold text-on-surface-variant hover:text-on-surface" @click="curriculum.cancelEditLesson">Cancelar</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Título</label>
                                        <input v-model="curriculum.editLessonData.title" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Tipo de Contenido</label>
                                        <select v-model="curriculum.editLessonData.content_type" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition">
                                            <option value="video">Video (grabado)</option>
                                            <option value="live">En vivo (link + horario)</option>
                                            <option value="text">Texto</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Descripción (Opcional)</label>
                                    <textarea v-model="curriculum.editLessonData.description" rows="2" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                </div>

                                <div v-if="curriculum.editLessonData.content_type === 'video'">
                                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">URL de Video</label>
                                    <input v-model="curriculum.editLessonData.video_url" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                </div>

                                <div v-if="curriculum.editLessonData.content_type === 'live'" class="space-y-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Link de la sesión (Zoom/Meet)</label>
                                        <input v-model="curriculum.editLessonData.live_link" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Fecha/Hora Inicio</label>
                                            <input v-model="curriculum.editLessonData.start_time" type="datetime-local" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1 ml-1">Fecha/Hora Fin</label>
                                            <input v-model="curriculum.editLessonData.end_time" type="datetime-local" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary transition" />
                                        </div>
                                    </div>
                                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                        <p class="text-[10px] text-amber-800 leading-tight">
                                            <strong>Nota para Post-En-Vivo:</strong> Si ya pasó la sesión y tienes la grabación, cambia el tipo a <strong>Video</strong> y pega la URL de YouTube/Vimeo.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="button" class="rounded-xl bg-primary px-6 py-2 text-xs font-bold text-white shadow-lg hover:opacity-90 active:scale-95 transition" @click="curriculum.saveEditLesson">Guardar Clase</button>
                                </div>
                            </div>

                            <div v-if="curriculum.openLessonId === lesson.id" class="mt-4 pt-4 border-t border-outline-variant/10">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-bold text-on-surface">Recursos</p>
                                    <button type="button" class="text-sm text-[#4B5320] font-bold hover:underline" @click.stop="curriculum.startAddMaterial(lesson.id)">+ Agregar</button>
                                </div>

                                <div v-if="curriculum.addingMaterialFor === lesson.id" class="mb-3 space-y-2 rounded-xl bg-surface-container-lowest p-3 border border-outline-variant/10">
                                    <input v-model="curriculum.newMaterial.title" class="w-full rounded-xl bg-white border border-outline-variant/20 px-3 py-2 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="Nombre del recurso" />
                                    <input type="file" class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" @change="(e) => { curriculum.newMaterial.file = (e.target as HTMLInputElement).files?.[0] ?? null; }" />
                                    <input v-model="curriculum.newMaterial.external_url" class="w-full rounded-xl bg-white border border-outline-variant/20 px-3 py-2 text-sm focus:outline-none focus:border-primary/50 transition" placeholder="o link externo" />
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" class="rounded-lg border px-3 py-1 text-xs font-bold hover:bg-surface-container-low transition" @click.stop="curriculum.addingMaterialFor = null">Cancelar</button>
                                        <button type="button" class="rounded-lg bg-[#4B5320] px-3 py-1 text-xs font-bold text-white shadow hover:opacity-90 transition" @click.stop="curriculum.createMaterial(lesson.id)">Guardar</button>
                                    </div>
                                </div>

                                <ul class="space-y-3">
                                    <li v-for="mat in (curriculum.materialsByLesson[lesson.id] ?? [])" :key="mat.id" class="flex items-start justify-between gap-3 group">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-semibold text-on-surface truncate">{{ mat.title }}</p>
                                            <p class="text-[11px] text-on-surface-variant truncate mt-0.5">{{ mat.external_url || mat.file_path || mat.kind }}</p>
                                        </div>
                                        <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition pt-0.5" @click.stop="curriculum.deleteMaterial(lesson.id, mat.id)">Eliminar</button>
                                    </li>
                                    <li v-if="!(curriculum.materialsByLesson[lesson.id]?.length)" class="text-xs text-on-surface-variant">Sin recursos aún.</li>
                                </ul>
                            </div>
                        </div>

                        <p v-if="!curriculum.lessonsForModule(m.id).length" class="text-xs text-on-surface-variant">
                            Sin clases todavía.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
