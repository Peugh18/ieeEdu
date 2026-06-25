<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';

interface EnrolledStudent {
    id: number;
    user?: { name?: string; email?: string };
    has_certificate: boolean;
    certificate: { id: number; code: string; issue_date: string } | null;
}

defineProps<{
    show: boolean;
    filteredStudents: EnrolledStudent[];
}>();

const studentFilter = defineModel<'all' | 'certified' | 'pending'>('filter', { required: true });
</script>

<template>
    <CourseEditorTabPanel :show="show">
        <div class="relative z-10 max-w-4xl">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="mb-2 font-serif text-3xl font-bold tracking-tight text-on-surface">
                        Alumnos <span class="font-light italic">Inscritos</span>
                    </h2>
                    <p class="font-sans text-[15px] font-medium leading-relaxed text-on-surface-variant">
                        Listado de alumnos matriculados y su estado de certificación.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="whitespace-nowrap text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Filtrar por</label>
                    <select
                        v-model="studentFilter"
                        class="cursor-pointer rounded-xl border border-outline-variant/20 bg-surface-container-low px-4 py-2.5 text-sm font-bold text-on-surface outline-none transition-all focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="all">Todos los alumnos</option>
                        <option value="certified">Certificado Obtenido</option>
                        <option value="pending">Falta Obtener</option>
                    </select>
                </div>
            </div>

            <div
                v-if="filteredStudents.length === 0"
                class="rounded-3xl border-2 border-dashed border-outline-variant/20 bg-surface-container-lowest py-16 text-center"
            >
                <svg class="mx-auto mb-4 h-12 w-12 text-outline-variant/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
                <p class="text-sm font-bold text-on-surface-variant opacity-80">No hay alumnos que coincidan con este filtro.</p>
            </div>
            <div v-else class="space-y-4">
                <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="flex flex-col justify-between gap-4 rounded-3xl border border-outline-variant/10 bg-surface-container-low p-6 transition-all hover:shadow-md sm:flex-row sm:items-center"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-sm font-bold text-primary">
                            {{
                                student.user?.name
                                    ?.split(' ')
                                    .slice(0, 2)
                                    .map((n: string) => n[0])
                                    .join('')
                                    .toUpperCase() || 'U'
                            }}
                        </div>
                        <div>
                            <p class="text-[15px] font-bold text-on-surface">{{ student.user?.name ?? 'Desconocido' }}</p>
                            <p class="text-xs font-medium text-on-surface-variant">{{ student.user?.email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 sm:gap-8">
                        <div v-if="student.has_certificate && student.certificate" class="flex items-center gap-6 sm:gap-8">
                            <div class="text-left sm:text-right">
                                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Código</p>
                                <p
                                    class="inline-block rounded-lg border border-outline-variant/20 bg-white px-2 py-1 font-mono text-sm font-bold text-on-surface"
                                >
                                    {{ student.certificate.code }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Emisión</p>
                                <p class="text-sm font-bold text-on-surface">
                                    {{
                                        new Date(student.certificate.issue_date).toLocaleDateString('es-PE', {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric',
                                        })
                                    }}
                                </p>
                            </div>
                            <a
                                :href="'/admin/certificates/' + student.certificate.id + '/download?action=stream'"
                                target="_blank"
                                rel="noopener"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-white text-primary transition-colors hover:bg-primary/5"
                                title="Descargar PDF"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    />
                                </svg>
                            </a>
                        </div>
                        <div v-else class="text-left sm:text-right">
                            <p
                                class="inline-flex items-center gap-1.5 rounded-xl border border-amber-100 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-600"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                Falta Obtener
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
