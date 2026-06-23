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
            <GraduationCap class="h-4 w-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Salud Académica & Engagement</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <!-- Tasa de Aprobación (donut visual) -->
            <article
                class="flex flex-col items-center gap-6 rounded-[2rem] border border-gray-100 bg-white p-6 text-center shadow-sm md:rounded-[2.5rem] md:p-9"
            >
                <div class="relative h-28 w-28">
                    <svg class="h-full w-full -rotate-90">
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                        <circle
                            cx="56"
                            cy="56"
                            r="48"
                            stroke="currentColor"
                            stroke-width="10"
                            fill="none"
                            :stroke-dasharray="301.6"
                            :stroke-dashoffset="301.6 - (301.6 * stats.approvalRate) / 100"
                            stroke-linecap="round"
                            class="duration-[2s] text-primary transition-all"
                        />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center font-serif text-xl font-black tabular-nums text-gray-900">
                        {{ stats.approvalRate.toFixed(0) }}%
                    </span>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-gray-900">Tasa de Aprobación</h4>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ stats.totalEnrollments }} matrículas totales</p>
                </div>
            </article>

            <!-- Completitud -->
            <article
                class="flex flex-col items-center gap-6 rounded-[2rem] border border-gray-100 bg-white p-6 text-center shadow-sm md:rounded-[2.5rem] md:p-9"
            >
                <div class="relative h-28 w-28">
                    <svg class="h-full w-full -rotate-90">
                        <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                        <circle
                            cx="56"
                            cy="56"
                            r="48"
                            stroke="currentColor"
                            stroke-width="10"
                            fill="none"
                            :stroke-dasharray="301.6"
                            :stroke-dashoffset="301.6 - (301.6 * Number(completionRate)) / 100"
                            stroke-linecap="round"
                            class="duration-[2s] text-emerald-500 transition-all"
                        />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center font-serif text-xl font-black tabular-nums text-gray-900">
                        {{ completionRate }}%
                    </span>
                </div>
                <div>
                    <h4 class="font-serif font-bold text-gray-900">Tasa de Finalización</h4>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-gray-400">{{ stats.completedCourses }} cursos completados</p>
                </div>
            </article>

            <!-- Usuarios vs Premium -->
            <article class="space-y-6 rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm md:rounded-[2.5rem] md:p-9">
                <div class="flex items-center gap-3">
                    <ShieldCheck class="h-5 w-5 text-primary" />
                    <h4 class="font-serif font-bold text-gray-900">Alumnos vs Premium</h4>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
                            <span class="text-sm font-black tabular-nums text-gray-900">{{ stats.totalUsers }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gray-300" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Premium</span>
                            <span class="text-sm font-black tabular-nums text-[#57572A]">{{ stats.premiumUsers }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div
                                class="h-full rounded-full bg-[#57572A]"
                                :style="{ width: (stats.premiumUsers / (stats.totalUsers || 1)) * 100 + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Tasa de Conversión Premium -->
            <article class="flex flex-col justify-between rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm md:rounded-[2.5rem] md:p-9">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="font-serif font-bold leading-tight text-gray-900">Conversión Premium</h4>
                        <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Suscripción Activa</p>
                    </div>
                </div>
                <div class="pt-6">
                    <p class="font-serif text-4xl font-black tabular-nums leading-none text-gray-900">
                        {{ ((stats.premiumUsers / (stats.activeUsers || 1)) * 100).toFixed(1) }}%
                    </p>
                    <p class="mt-3 text-[11px] font-medium text-gray-400">De alumnos activos convertidos</p>
                </div>
            </article>
        </div>
    </section>
</template>
