<script setup lang="ts">
import { BookOpen, Crown, DollarSign, Video } from 'lucide-vue-next';

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
            <DollarSign class="h-4 w-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Flujos de Ingresos</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
            <!-- Total -->
            <article
                class="group relative overflow-hidden rounded-[2.5rem] bg-on-background p-6 shadow-2xl transition-all hover:scale-[1.01] dark:bg-primary sm:col-span-2 md:p-8 xl:col-span-1"
            >
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-primary-fixed">
                            <DollarSign class="h-6 w-6" />
                        </div>
                        <span class="rounded-xl border border-white/10 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-white/30"
                            >CAJA REAL</span
                        >
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-white/30">Facturación Bruta</p>
                        <h3 class="font-serif text-4xl font-black tabular-nums leading-none text-primary-fixed">
                            S/ {{ stats.totalIncome.toLocaleString() }}
                        </h3>
                        <p class="mt-3 text-[11px] font-medium text-white/30">{{ stats.approvedPayments }} pagos aprobados</p>
                    </div>
                </div>
            </article>

            <!-- Suscripciones -->
            <article
                class="group relative overflow-hidden rounded-[2.5rem] border border-gray-100 bg-white p-6 shadow-sm transition-all hover:scale-[1.01] hover:shadow-xl md:p-8"
            >
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <Crown class="h-6 w-6" />
                        </div>
                        <span class="text-[10px] font-bold tabular-nums text-gray-400">{{ subIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Membresías</p>
                        <h3 class="font-serif text-4xl font-black tabular-nums leading-none text-gray-900">
                            S/ {{ stats.subIncome.toLocaleString() }}
                        </h3>
                        <p class="mt-2 text-[11px] font-medium text-gray-400">{{ stats.activeSubs }} activas</p>
                    </div>
                </div>
            </article>

            <!-- Cursos -->
            <article
                class="group relative overflow-hidden rounded-[2.5rem] border border-gray-100 bg-white p-6 shadow-sm transition-all hover:scale-[1.01] hover:shadow-xl md:p-8"
            >
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                            <Video class="h-6 w-6" />
                        </div>
                        <span class="text-[10px] font-bold tabular-nums text-gray-400">{{ courseIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Cursos</p>
                        <h3 class="font-serif text-4xl font-black tabular-nums leading-none text-gray-900">
                            S/ {{ stats.courseIncome.toLocaleString() }}
                        </h3>
                        <p class="mt-2 text-[11px] font-medium text-gray-400">{{ stats.publishedCourses }} publicados</p>
                    </div>
                </div>
            </article>

            <!-- Libros -->
            <article
                class="group relative overflow-hidden rounded-[2.5rem] border border-blue-100 bg-white p-6 shadow-sm transition-all hover:scale-[1.01] hover:shadow-xl md:p-8"
            >
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                            <BookOpen class="h-6 w-6" />
                        </div>
                        <span class="text-[10px] font-bold tabular-nums text-gray-400">{{ bookIncomeShare }}%</span>
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Libros</p>
                        <h3 class="font-serif text-4xl font-black tabular-nums leading-none text-gray-900">
                            S/ {{ stats.bookIncome.toLocaleString() }}
                        </h3>
                        <p class="mt-2 text-[11px] font-medium text-gray-400">{{ stats.bookSalesCount }} ventas aprobadas</p>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
