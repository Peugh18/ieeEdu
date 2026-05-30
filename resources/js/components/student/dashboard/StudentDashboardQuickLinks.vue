<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Sparkles, ClipboardCheck, Calendar, BookOpen, Star } from 'lucide-vue-next';
import type { SummaryStats } from '@/types/student-dashboard';

defineProps<{
  stats: SummaryStats;
}>();
</script>

<template>
  <div class="space-y-4">
    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
      <Sparkles class="w-5 h-5 text-primary" />
      Accesos rápidos
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <Link :href="route('student.exams.index')"
        class="group bg-white rounded-2xl border border-gray-100 p-5 flex flex-col items-center text-center hover:border-purple-200 hover:shadow-md transition-all duration-300">
        <div class="p-3 bg-purple-50 rounded-2xl group-hover:scale-110 transition-transform mb-3">
          <ClipboardCheck class="w-6 h-6 text-purple-500" />
        </div>
        <h4 class="text-xs font-bold text-gray-800">Mis evaluaciones</h4>
        <p class="text-[10px] text-gray-400 mt-1">{{ stats.available_exams }} disponibles</p>
      </Link>

      <Link :href="route('student.live-classes.index')"
        class="group bg-white rounded-2xl border border-gray-100 p-5 flex flex-col items-center text-center hover:border-amber-200 hover:shadow-md transition-all duration-300">
        <div class="p-3 bg-amber-50 rounded-2xl group-hover:scale-110 transition-transform mb-3">
          <Calendar class="w-6 h-6 text-amber-500" />
        </div>
        <h4 class="text-xs font-bold text-gray-800">Clases en vivo</h4>
        <p class="text-[10px] text-gray-400 mt-1">{{ stats.upcoming_classes }} próximas</p>
      </Link>

      <Link :href="route('student.explore.publications')"
        class="group bg-white rounded-2xl border border-gray-100 p-5 flex flex-col items-center text-center hover:border-blue-200 hover:shadow-md transition-all duration-300">
        <div class="p-3 bg-blue-50 rounded-2xl group-hover:scale-110 transition-transform mb-3">
          <BookOpen class="w-6 h-6 text-blue-500" />
        </div>
        <h4 class="text-xs font-bold text-gray-800">Publicaciones</h4>
        <p class="text-[10px] text-gray-400 mt-1">Libros y artículos</p>
      </Link>

      <Link :href="route('student.explore.masterclasses')"
        class="group bg-white rounded-2xl border border-gray-100 p-5 flex flex-col items-center text-center hover:border-rose-200 hover:shadow-md transition-all duration-300">
        <div class="p-3 bg-rose-50 rounded-2xl group-hover:scale-110 transition-transform mb-3">
          <Star class="w-6 h-6 text-rose-500" />
        </div>
        <h4 class="text-xs font-bold text-gray-800">Masterclasses</h4>
        <p class="text-[10px] text-gray-400 mt-1">Sesiones especiales</p>
      </Link>
    </div>
  </div>
</template>
