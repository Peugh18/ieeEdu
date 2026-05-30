<script setup lang="ts">
import { DollarSign, Crown, Video, BookOpen } from 'lucide-vue-next';

interface DashboardStats {
    totalIncome: number;
    approvedPayments: number;
    subIncome: number;
    activeSubs: number;
    courseIncome: number;
    publishedCourses: number;
    bookIncome: number;
    bookSalesCount: number;
}

defineProps<{
    stats: DashboardStats;
    subIncomeShare: string | number;
    courseIncomeShare: string | number;
    bookIncomeShare: string | number;
}>();
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <DollarSign class="w-4 h-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Flujos de Ingresos</h2>
        </div>

        <div class="grid gap-4 md:gap-6 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Total -->
            <article class="relative bg-on-background dark:bg-primary rounded-[2.5rem] p-6 md:p-8 overflow-hidden group hover:scale-[1.01] transition-all shadow-2xl sm:col-span-2 xl:col-span-1">
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-primary-fixed">
                            <DollarSign class="w-6 h-6" />
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-white/30 border border-white/10 px-3 py-1 rounded-xl">CAJA REAL</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-2">Facturación Bruta</p>
                        <h3 class="font-serif text-4xl font-black text-primary-fixed tabular-nums leading-none">S/ {{ stats.totalIncome.toLocaleString() }}</h3>
                        <p class="text-[11px] text-white/30 mt-3 font-medium">{{ stats.approvedPayments }} pagos aprobados</p>
                    </div>
                </div>
            </article>

            <!-- Suscripciones -->
            <article class="relative bg-white rounded-[2.5rem] p-6 md:p-8 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                            <Crown class="w-6 h-6" />
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ subIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Membresías</p>
                        <h3 class="font-serif text-4xl font-black text-gray-900 tabular-nums leading-none">S/ {{ stats.subIncome.toLocaleString() }}</h3>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ stats.activeSubs }} activas</p>
                    </div>
                </div>
            </article>

            <!-- Cursos -->
            <article class="relative bg-white rounded-[2.5rem] p-6 md:p-8 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                            <Video class="w-6 h-6" />
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ courseIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Cursos</p>
                        <h3 class="font-serif text-4xl font-black text-gray-900 tabular-nums leading-none">S/ {{ stats.courseIncome.toLocaleString() }}</h3>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ stats.publishedCourses }} publicados</p>
                    </div>
                </div>
            </article>

            <!-- Libros -->
            <article class="relative bg-white rounded-[2.5rem] p-6 md:p-8 border border-blue-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <BookOpen class="w-6 h-6" />
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ bookIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Libros</p>
                        <h3 class="font-serif text-4xl font-black text-gray-900 tabular-nums leading-none">S/ {{ stats.bookIncome.toLocaleString() }}</h3>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ stats.bookSalesCount }} ventas aprobadas</p>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
