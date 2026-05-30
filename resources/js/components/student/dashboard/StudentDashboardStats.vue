<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, ClipboardCheck, Award, ChevronRight } from 'lucide-vue-next';
import type { SummaryStats } from '@/types/student-dashboard';

defineProps<{
  stats: SummaryStats;
}>();
</script>

<template>
  <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <Link :href="route('student.courses.index')"
      class="group bg-white rounded-2xl border border-gray-100 p-4 md:p-6 shadow-sm hover:shadow-lg hover:shadow-blue-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 md:gap-4">
      <div class="p-3 bg-blue-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
        <BookOpen class="w-6 h-6 text-blue-500" />
      </div>
      <div>
        <p class="text-2xl md:text-3xl font-bold text-gray-900 leading-none">{{ stats.active_courses }}</p>
        <p class="text-[10px] md:text-xs text-gray-400 mt-1 font-medium">Cursos activos</p>
      </div>
      <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-blue-400 ml-auto transition-colors" />
    </Link>

    <Link :href="route('student.live-classes.index')"
      class="group bg-white rounded-2xl border border-gray-100 p-4 md:p-6 shadow-sm hover:shadow-lg hover:shadow-amber-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 md:gap-4">
      <div class="p-3 bg-amber-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
        <Calendar class="w-6 h-6 text-amber-500" />
      </div>
      <div>
        <p class="text-2xl md:text-3xl font-bold text-gray-900 leading-none">{{ stats.upcoming_classes }}</p>
        <p class="text-[10px] md:text-xs text-gray-400 mt-1 font-medium">Clases próximas</p>
      </div>
      <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-amber-400 ml-auto transition-colors" />
    </Link>

    <Link :href="route('student.exams.index')"
      class="group bg-white rounded-2xl border border-gray-100 p-4 md:p-6 shadow-sm hover:shadow-lg hover:shadow-purple-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 md:gap-4">
      <div class="p-3 bg-purple-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
        <ClipboardCheck class="w-6 h-6 text-purple-500" />
      </div>
      <div>
        <p class="text-2xl md:text-3xl font-bold text-gray-900 leading-none">{{ stats.available_exams }}</p>
        <p class="text-[10px] md:text-xs text-gray-400 mt-1 font-medium">Evaluaciones</p>
      </div>
      <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-purple-400 ml-auto transition-colors" />
    </Link>

    <Link :href="route('student.certificates.index')"
      class="group bg-gradient-to-br from-primary to-[#3d3d1e] rounded-2xl p-4 md:p-6 shadow-sm hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 md:gap-4">
      <div class="p-3 bg-white/10 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
        <Award class="w-6 h-6 text-[#D4AF37]" />
      </div>
      <div>
        <p class="text-2xl md:text-3xl font-bold text-white leading-none">{{ stats.certificate_count }}</p>
        <p class="text-[10px] md:text-xs text-white/50 mt-1 font-medium">Certificados</p>
      </div>
      <ChevronRight class="w-4 h-4 text-white/20 group-hover:text-[#D4AF37] ml-auto transition-colors" />
    </Link>
  </section>
</template>
