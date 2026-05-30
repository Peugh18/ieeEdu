<script setup lang="ts">
import { GraduationCap, ShieldCheck } from 'lucide-vue-next';

interface AcademicStats {
    approvalRate: number;
    totalEnrollments: number;
    completedCourses: number;
    totalUsers: number;
    activeUsers: number;
    premiumUsers: number;
}

defineProps<{
    stats: AcademicStats;
    completionRate: string | number;
}>();
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <GraduationCap class="w-4 h-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Salud Académica & Engagement</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

            <!-- Tasa de Aprobación (donut visual) -->
            <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm flex flex-col items-center gap-6 text-center">
                <div class="relative w-28 h-28">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="10" fill="none"
                            :stroke-dasharray="301.6"
                            :stroke-dashoffset="301.6 - (301.6 * stats.approvalRate / 100)"
                             stroke-linecap="round" class="text-primary transition-all duration-[2s]" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center font-serif font-black text-xl text-gray-900 tabular-nums">
                        {{ stats.approvalRate.toFixed(0) }}%
                    </span>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-gray-900">Tasa de Aprobación</h4>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ stats.totalEnrollments }} matrículas totales</p>
                </div>
            </article>

            <!-- Completitud -->
            <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm flex flex-col items-center gap-6 text-center">
                <div class="relative w-28 h-28">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="10" fill="none"
                            :stroke-dasharray="301.6"
                            :stroke-dashoffset="301.6 - (301.6 * Number(completionRate) / 100)"
                             stroke-linecap="round" class="text-emerald-500 transition-all duration-[2s]" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center font-serif font-black text-xl text-gray-900 tabular-nums">
                        {{ completionRate }}%
                    </span>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-gray-900">Tasa de Finalización</h4>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ stats.completedCourses }} cursos completados</p>
                </div>
            </article>

            <!-- Usuarios vs Premium -->
            <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3">
                    <ShieldCheck class="w-5 h-5 text-primary" />
                    <h4 class="font-serif font-bold text-gray-900">Alumnos vs Premium</h4>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
                            <span class="text-sm font-black text-gray-900 tabular-nums">{{ stats.totalUsers }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gray-300 rounded-full" style="width:100%"></div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Premium</span>
                            <span class="text-sm font-black text-[#57572A] tabular-nums">{{ stats.premiumUsers }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#57572A] rounded-full" :style="{width: ((stats.premiumUsers / (stats.totalUsers || 1)) * 100) + '%'}"></div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Tasa de Conversión Premium -->
            <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-serif font-bold text-gray-900 leading-tight">Conversión Premium</h4>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">Suscripción Activa</p>
                    </div>
                </div>
                <div class="pt-6">
                    <p class="text-4xl font-serif font-black text-gray-900 tabular-nums leading-none">
                        {{ ((stats.premiumUsers / (stats.activeUsers || 1)) * 100).toFixed(1) }}%
                    </p>
                    <p class="text-[11px] text-gray-400 mt-3 font-medium">De alumnos activos convertidos</p>
                </div>
            </article>

        </div>
    </section>
</template>
