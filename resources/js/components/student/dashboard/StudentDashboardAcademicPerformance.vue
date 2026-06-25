<script setup lang="ts">
import type { SummaryStats } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { AlertCircle, BarChart3, CheckCircle2, TrendingUp } from 'lucide-vue-next';

defineProps<{
    stats: SummaryStats;
    animatedStats: SummaryStats;
}>();
</script>

<template>
    <div class="relative overflow-hidden rounded-3xl bg-gray-900 p-8 text-white shadow-2xl">
        <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-[#D4AF37]/10 blur-[60px]"></div>
        <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-blue-500/5 blur-[60px]"></div>

        <div class="relative z-10 space-y-6">
            <div class="flex items-center justify-between">
                <div class="rounded-2xl bg-white/10 p-3">
                    <BarChart3 class="h-5 w-5 text-[#D4AF37]" />
                </div>
                <span
                    class="flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/15 px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-emerald-400"
                >
                    <TrendingUp class="h-3 w-3" /> Rendimiento
                </span>
            </div>

            <div>
                <h3 class="mb-1 text-lg font-bold text-white">Desempeño Académico</h3>
                <p class="text-xs text-white/40">Tu historial de evaluaciones y logros.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/5 p-4 text-center">
                    <p class="text-3xl font-bold text-white">
                        {{ stats.average_score > 0 ? stats.average_score : '--' }}
                    </p>
                    <p class="mt-1 text-[10px] uppercase tracking-widest text-white/40">Prom. Exámenes</p>
                </div>
                <div class="rounded-2xl bg-white/5 p-4 text-center">
                    <p class="text-3xl font-bold text-[#D4AF37]">
                        {{ animatedStats.certificate_count }}
                    </p>
                    <p class="mt-1 text-[10px] uppercase tracking-widest text-white/40">Diplomas</p>
                </div>
            </div>

            <div v-if="stats.average_score > 0" class="space-y-2">
                <div class="flex justify-between text-[10px] uppercase tracking-widest text-white/40">
                    <span>Puntuación promedio</span>
                    <span>{{ stats.average_score }}/20</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-[#D4AF37] to-amber-300 transition-all duration-1000"
                        :style="{ width: (stats.average_score / 20) * 100 + '%' }"
                    ></div>
                </div>
                <p class="flex items-center gap-1 text-[10px] text-emerald-400" v-if="stats.average_score >= 14">
                    <CheckCircle2 class="h-3 w-3" /> Rendimiento aprobatorio
                </p>
                <p class="flex items-center gap-1 text-[10px] text-amber-400" v-else><AlertCircle class="h-3 w-3" /> Sigue esforzándote</p>
            </div>

            <Link
                :href="route('student.certificates.index')"
                class="block w-full rounded-xl bg-white py-3.5 text-center text-xs font-bold uppercase tracking-widest text-gray-900 shadow-xl transition-all hover:bg-[#D4AF37] hover:text-white active:scale-95"
            >
                Ver mis certificados
            </Link>
        </div>
    </div>
</template>
