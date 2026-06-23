<script setup lang="ts">
import type { EnrollmentItem } from '@/types/user-detail';
import { BookMarked, BookOpen, Calendar, CheckCircle2, Clock } from 'lucide-vue-next';

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
            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-500"
                >{{ enrollments.length }} Registros</span
            >
        </div>
        <div v-if="!enrollments.length" class="space-y-4 rounded-[2rem] border border-slate-100 bg-white p-20 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-200">
                <BookOpen class="h-10 w-10" />
            </div>
            <p class="font-medium text-slate-400">No se han detectado inscripciones activas.</p>
        </div>
        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div
                v-for="e in enrollments"
                :key="e.id"
                class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-5 transition-all duration-500 hover:shadow-xl"
            >
                <div class="relative z-10 flex gap-5">
                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                        <img v-if="e.course?.image" :src="e.course.image" :alt="e.course?.title" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-slate-200"><BookMarked class="h-8 w-8" /></div>
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col justify-between py-1">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">{{ e.course?.type ?? 'N/A' }}</span>
                                <div
                                    :class="`rounded-lg px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider ring-1 ring-inset ${enrollmentStatus(e).class}`"
                                >
                                    {{ enrollmentStatus(e).label }}
                                </div>
                            </div>
                            <h4 class="truncate text-base font-bold leading-snug text-slate-900 transition-colors group-hover:text-primary">
                                {{ e.course?.title ?? 'Curso' }}
                            </h4>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-50 pt-2 text-xs text-slate-400">
                            <div class="flex items-center gap-1.5">
                                <Calendar class="h-3.5 w-3.5" /><span>{{ formatDate(e.enrolled_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
