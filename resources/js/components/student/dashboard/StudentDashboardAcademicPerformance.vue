<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BarChart3, TrendingUp, CheckCircle2, AlertCircle } from 'lucide-vue-next';
import type { SummaryStats } from '@/types/student-dashboard';

defineProps<{
  stats: SummaryStats;
  animatedStats: SummaryStats;
}>();
</script>

<template>
  <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
    <div class="absolute -top-16 -right-16 w-48 h-48 bg-[#D4AF37]/10 rounded-full blur-[60px]"></div>
    <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-blue-500/5 rounded-full blur-[60px]"></div>

    <div class="relative z-10 space-y-6">
      <div class="flex items-center justify-between">
        <div class="p-3 bg-white/10 rounded-2xl">
          <BarChart3 class="w-5 h-5 text-[#D4AF37]" />
        </div>
        <span class="px-3 py-1 bg-emerald-500/15 text-emerald-400 text-[9px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20 flex items-center gap-1.5">
          <TrendingUp class="w-3 h-3" /> Rendimiento
        </span>
      </div>

      <div>
        <h3 class="text-lg font-bold text-white mb-1">Desempeño Académico</h3>
        <p class="text-white/40 text-xs">Tu historial de evaluaciones y logros.</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="bg-white/5 rounded-2xl p-4 text-center">
          <p class="text-3xl font-bold text-white">
            {{ stats.average_score > 0 ? stats.average_score : '--' }}
          </p>
          <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Prom. Exámenes</p>
        </div>
        <div class="bg-white/5 rounded-2xl p-4 text-center">
          <p class="text-3xl font-bold text-[#D4AF37]">
            {{ animatedStats.certificate_count }}
          </p>
          <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Diplomas</p>
        </div>
      </div>

      <div v-if="stats.average_score > 0" class="space-y-2">
        <div class="flex justify-between text-[10px] text-white/40 uppercase tracking-widest">
          <span>Puntuación promedio</span>
          <span>{{ stats.average_score }}/20</span>
        </div>
        <div class="h-2 bg-white/10 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-[#D4AF37] to-amber-300 rounded-full transition-all duration-1000"
            :style="{ width: (stats.average_score / 20 * 100) + '%' }"></div>
        </div>
        <p class="text-[10px] text-emerald-400 flex items-center gap-1" v-if="stats.average_score >= 14">
          <CheckCircle2 class="w-3 h-3" /> Rendimiento aprobatorio
        </p>
        <p class="text-[10px] text-amber-400 flex items-center gap-1" v-else>
          <AlertCircle class="w-3 h-3" /> Sigue esforzándote
        </p>
      </div>

      <Link :href="route('student.certificates.index')"
        class="block w-full py-3.5 bg-white text-gray-900 text-xs text-center font-bold uppercase tracking-widest rounded-xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-xl active:scale-95">
        Ver mis certificados
      </Link>
    </div>
  </div>
</template>
