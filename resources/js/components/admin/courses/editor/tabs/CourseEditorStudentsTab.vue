<script setup lang="ts">
import CourseEditorTabPanel from '@/components/admin/courses/editor/CourseEditorTabPanel.vue';
import type { ComputedRef } from 'vue';

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
        <div class="max-w-4xl relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-serif font-bold text-on-surface mb-2 tracking-tight">
                        Alumnos <span class="italic font-light">Inscritos</span>
                    </h2>
                    <p class="text-[15px] text-on-surface-variant font-sans font-medium leading-relaxed">
                        Listado de alumnos matriculados y su estado de certificación.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Filtrar por</label>
                    <select v-model="studentFilter" class="rounded-xl border border-outline-variant/20 bg-surface-container-low px-4 py-2.5 text-sm font-bold text-on-surface outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                        <option value="all">Todos los alumnos</option>
                        <option value="certified">Certificado Obtenido</option>
                        <option value="pending">Falta Obtener</option>
                    </select>
                </div>
            </div>

            <div v-if="filteredStudents.length === 0" class="py-16 border-2 border-dashed border-outline-variant/20 rounded-3xl text-center bg-surface-container-lowest">
                <svg class="w-12 h-12 text-outline-variant/50 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="text-on-surface-variant text-sm font-bold opacity-80">No hay alumnos que coincidan con este filtro.</p>
            </div>
            <div v-else class="space-y-4">
                <div v-for="student in filteredStudents" :key="student.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-6 bg-surface-container-low border border-outline-variant/10 rounded-3xl gap-4 hover:shadow-md transition-all">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl flex items-center justify-center font-bold text-sm bg-primary/10 text-primary">
                            {{ student.user?.name?.split(' ').slice(0, 2).map((n: string) => n[0]).join('').toUpperCase() || 'U' }}
                        </div>
                        <div>
                            <p class="font-bold text-on-surface text-[15px]">{{ student.user?.name ?? 'Desconocido' }}</p>
                            <p class="text-xs text-on-surface-variant font-medium">{{ student.user?.email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 sm:gap-8">
                        <div v-if="student.has_certificate && student.certificate" class="flex items-center gap-6 sm:gap-8">
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Código</p>
                                <p class="text-sm font-mono font-bold text-on-surface bg-white px-2 py-1 rounded-lg border border-outline-variant/20 inline-block">{{ student.certificate.code }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Emisión</p>
                                <p class="text-sm font-bold text-on-surface">{{ new Date(student.certificate.issue_date).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' }) }}</p>
                            </div>
                            <a :href="'/admin/certificates/' + student.certificate.id + '/download?action=stream'" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-outline-variant/20 text-primary hover:bg-primary/5 transition-colors" title="Descargar PDF">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            </a>
                        </div>
                        <div v-else class="text-left sm:text-right">
                            <p class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-xl inline-flex items-center gap-1.5 border border-amber-100">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Falta Obtener
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CourseEditorTabPanel>
</template>
