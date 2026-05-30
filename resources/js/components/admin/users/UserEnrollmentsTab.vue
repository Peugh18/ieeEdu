<script setup lang="ts">
import { BookOpen, BookMarked, Calendar, CheckCircle2, Clock } from 'lucide-vue-next';
import type { EnrollmentItem } from '@/types/user-detail';

defineProps<{
    enrollments: EnrollmentItem[];
}>();

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

function enrollmentStatus(e: EnrollmentItem) {
    if (e.completed_at && e.passed) return { label: 'Completado', class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 };
    return { label: 'En progreso', class: 'bg-blue-50 text-blue-700 ring-blue-700/10', icon: Clock };
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h3 class="font-serif text-2xl text-slate-900">Programas de <span class="italic">Estudio</span></h3>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">{{ enrollments.length }} Registros</span>
        </div>
        <div v-if="!enrollments.length" class="bg-white rounded-[2rem] border border-slate-100 p-20 text-center space-y-4">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-200"><BookOpen class="w-10 h-10" /></div>
            <p class="text-slate-400 font-medium">No se han detectado inscripciones activas.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="e in enrollments" :key="e.id" class="group bg-white rounded-[2rem] p-5 border border-slate-100 hover:shadow-xl transition-all duration-500 overflow-hidden relative">
                <div class="flex gap-5 relative z-10">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0">
                        <img v-if="e.course?.image" :src="e.course.image" :alt="e.course?.title" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-slate-200"><BookMarked class="h-8 w-8" /></div>
                    </div>
                    <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">{{ e.course?.type ?? 'N/A' }}</span>
                                <div :class="`px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider ring-1 ring-inset ${enrollmentStatus(e).class}`">{{ enrollmentStatus(e).label }}</div>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 truncate leading-snug group-hover:text-primary transition-colors">{{ e.course?.title ?? 'Curso' }}</h4>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-50 text-xs text-slate-400">
                            <div class="flex items-center gap-1.5"><Calendar class="w-3.5 h-3.5" /><span>{{ formatDate(e.enrolled_at) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
