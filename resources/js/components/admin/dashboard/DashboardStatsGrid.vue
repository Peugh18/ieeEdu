<script setup lang="ts">
import { DollarSign, Crown, Video } from 'lucide-vue-next';

interface DashboardStats {
    totalIncome: number;
    approvedPayments: number;
    subIncome: number;
    activeSubs: number;
    courseIncome: number;
    publishedCourses: number;
}

defineProps<{
    stats: DashboardStats;
    subIncomeShare: string | number;
    courseIncomeShare: string | number;
}>();
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <DollarSign class="w-4 h-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Flujos de Ingresos</h2>
        </div>

        <div class="grid gap-4 md:gap-6 grid-cols-1 lg:grid-cols-3">
            <!-- Total -->
            <article class="relative bg-on-background dark:bg-primary rounded-[2.5rem] p-6 md:p-9 overflow-hidden group hover:scale-[1.01] transition-all shadow-2xl">
                <div class="relative z-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-primary-fixed">
                            <DollarSign class="w-6 h-6" />
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-white/30 border border-white/10 px-3 py-1 rounded-xl">CAJA REAL</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-2">Facturación Bruta</p>
                        <h3 class="font-serif text-5xl font-black text-primary-fixed tabular-nums leading-none">S/ {{ stats.totalIncome.toLocaleString() }}</h3>
                        <p class="text-[11px] text-white/30 mt-3 font-medium">{{ stats.approvedPayments }} pagos aprobados en total</p>
                    </div>
                </div>
                <div class="absolute -bottom-8 -right-8 w-44 h-44 text-white/5"><DollarSign class="w-full h-full" /></div>
            </article>

            <!-- Suscripciones -->
            <article class="relative bg-white rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                <div class="relative z-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                            <Crown class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[9px] font-black uppercase tracking-widest text-primary bg-primary/5 px-3 py-1 rounded-xl border border-primary/10">PREMIUM</span>
                            <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ subIncomeShare }}% del total</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Membresías</p>
                        <h3 class="font-serif text-5xl font-black text-gray-900 tabular-nums leading-none">S/ {{ stats.subIncome.toLocaleString() }}</h3>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="w-full h-1.5 rounded-full bg-gray-100">
                                 <div class="h-full bg-primary rounded-full transition-all duration-[2000ms]" :style="{width: subIncomeShare + '%'}"></div>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ stats.activeSubs }} suscripciones activas</p>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-32 h-32 text-primary/5"><Crown class="w-full h-full" /></div>
            </article>

            <!-- Cursos -->
            <article class="relative bg-white rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                <div class="relative z-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                            <Video class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[9px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-xl border border-amber-100">CURSOS</span>
                            <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ courseIncomeShare }}% del total</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Venta Directa</p>
                        <h3 class="font-serif text-5xl font-black text-gray-900 tabular-nums leading-none">S/ {{ stats.courseIncome.toLocaleString() }}</h3>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="w-full h-1.5 rounded-full bg-gray-100">
                                 <div class="h-full bg-amber-400 rounded-full transition-all duration-[2000ms]" :style="{width: courseIncomeShare + '%'}"></div>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ stats.publishedCourses }} cursos publicados</p>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-32 h-32 text-amber-500/5"><Video class="w-full h-full" /></div>
            </article>
        </div>
    </section>
</template>
