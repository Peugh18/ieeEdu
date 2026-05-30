<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Flame, Play, ArrowRight, GraduationCap, Sparkles } from 'lucide-vue-next';
import type { ContinueLearning, SummaryStats } from '@/types/student-dashboard';

const props = defineProps<{
  continueLearning: ContinueLearning | null;
  stats: SummaryStats;
}>();

function progressCircle(pct: number, r = 28) {
  const circumference = 2 * Math.PI * r;
  const offset = circumference - (pct / 100) * circumference;
  return { circumference, offset };
}

const ring = computed(() => {
  const pct = props.continueLearning?.progress ?? 0;
  return progressCircle(pct);
});

const motivationalMessage = computed(() => {
  const pct = props.continueLearning?.progress ?? 0;
  if (pct === 0) return '¡Comienza hoy tu aprendizaje!';
  if (pct < 25) return 'Excelente inicio, sigue adelante';
  if (pct < 50) return 'Vas por buen camino, continúa';
  if (pct < 75) return '¡Más de la mitad! Eres imparable';
  if (pct < 100) return '¡Casi lo logras! Un esfuerzo más';
  return '¡Curso completado! Descarga tu certificado';
});
</script>

<template>
  <div v-if="continueLearning">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
        <Flame class="w-5 h-5 text-orange-500" />
        Continuar aprendiendo
      </h2>
      <Link :href="route('student.courses.index')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
        Todos mis cursos <ArrowRight class="w-3 h-3" />
      </Link>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group">
      <div class="flex flex-col md:flex-row">
        <div class="relative md:w-64 h-52 md:h-auto overflow-hidden flex-shrink-0 bg-gray-900">
          <img
            v-if="continueLearning.image"
            :src="continueLearning.image"
            :alt="continueLearning.course_title"
            class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-gray-900/60 md:from-transparent md:to-gray-900/20 to-transparent"></div>

          <div class="absolute bottom-4 left-4 md:bottom-auto md:top-1/2 md:-right-10 md:left-auto md:-translate-y-1/2 z-10">
            <div class="relative w-20 h-20 flex items-center justify-center">
              <svg class="w-20 h-20 -rotate-90" viewBox="0 0 72 72">
                <circle cx="36" cy="36" r="28" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="4" />
                <circle cx="36" cy="36" r="28" fill="none" stroke="#D4AF37" stroke-width="4"
                  stroke-linecap="round"
                  :stroke-dasharray="ring.circumference"
                  :stroke-dashoffset="ring.offset"
                  style="transition: stroke-dashoffset 1.5s ease"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-bold text-white">{{ continueLearning.progress }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex-1 p-8 flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 border border-orange-100 rounded-full">
                <Flame class="w-3 h-3 text-orange-500" />
                <span class="text-[10px] font-bold text-orange-600 uppercase tracking-widest">En progreso</span>
              </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors">
              {{ continueLearning.course_title }}
            </h3>
            <p class="text-sm text-gray-500 flex items-center gap-2">
              <Play class="w-3.5 h-3.5 text-[#D4AF37] fill-current flex-shrink-0" />
              Última lección: <span class="font-semibold text-gray-700">{{ continueLearning.lesson_title }}</span>
            </p>
          </div>

          <div class="space-y-4 mt-6">
            <div class="space-y-1.5">
              <div class="flex justify-between items-center">
                <p class="text-xs text-gray-400 font-medium">{{ motivationalMessage }}</p>
                <span class="text-xs font-bold text-primary">{{ continueLearning.progress }}%</span>
              </div>
              <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gradient-to-r from-primary to-[#D4AF37] rounded-full transition-all duration-1000"
                  :style="{ width: continueLearning.progress + '%' }"
                ></div>
              </div>
            </div>

            <Link
              :href="route('student.classroom', { course: continueLearning.course_slug })"
              class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary hover:bg-[#4a4a24] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 active:scale-95 group/btn"
            >
              <Play class="w-4 h-4 fill-current" />
              Continuar curso
              <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else-if="stats.active_courses === 0"
    class="bg-gradient-to-br from-primary/5 to-[#D4AF37]/5 border-2 border-dashed border-primary/15 rounded-3xl p-12 text-center space-y-6">
    <div class="inline-flex p-6 bg-white rounded-3xl shadow-sm mb-2 ring-8 ring-primary/5">
      <GraduationCap class="w-12 h-12 text-primary" />
    </div>
    <div class="space-y-3">
      <h2 class="text-2xl font-bold text-gray-900">Empieza tu camino académico</h2>
      <p class="text-gray-500 max-w-lg mx-auto text-sm leading-relaxed">
        Aún no tienes cursos en progreso. Explora nuestro catálogo y comienza a especializarte con los mejores expertos del sector.
      </p>
    </div>
    <Link :href="route('student.explore.courses')"
      class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-xl hover:shadow-primary/30 active:scale-95 transition-all">
      <Sparkles class="w-4 h-4" />
      Explorar cursos
    </Link>
  </div>
</template>
