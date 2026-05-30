<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Sparkles, ArrowRight } from 'lucide-vue-next';
import type { Recommendation } from '@/types/student-dashboard';

defineProps<{
  recommendations: Recommendation[];
}>();
</script>

<template>
  <div v-if="recommendations.length > 0" class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
        <Sparkles class="w-5 h-5 text-primary" />
        Recomendado para ti
      </h2>
      <Link :href="route('student.explore.courses')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
        Explorar catálogo <ArrowRight class="w-3 h-3" />
      </Link>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <article v-for="rec in recommendations" :key="rec.id"
        class="bg-white rounded-2xl border border-gray-100 overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-400 flex flex-col">
        <div class="relative h-36 overflow-hidden bg-gray-100">
          <img v-if="rec.image" :src="rec.image" :alt="rec.title"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent"></div>
          <div class="absolute bottom-3 left-3">
            <span class="px-2.5 py-1 bg-white/90 backdrop-blur-md text-[9px] font-bold text-primary uppercase tracking-widest rounded-full">
              {{ rec.category?.name }}
            </span>
          </div>
        </div>
        <div class="p-5 flex flex-col flex-1">
          <h4 class="text-sm font-bold text-gray-900 mb-4 line-clamp-2 leading-snug group-hover:text-primary transition-colors">
            {{ rec.title }}
          </h4>
          <Link :href="route('cursos.show', { slug: rec.slug, dashboard: true })"
            class="mt-auto inline-flex items-center gap-1.5 text-[10px] font-bold text-primary uppercase tracking-widest hover:gap-2.5 transition-all">
            Ver detalles <ArrowRight class="w-3 h-3" />
          </Link>
        </div>
      </article>
    </div>
  </div>
</template>
