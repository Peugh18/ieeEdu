<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Award, ArrowRight, ExternalLink } from 'lucide-vue-next';
import type { Certificate } from '@/types/student-dashboard';

defineProps<{
  certificates: Certificate[];
}>();
</script>

<template>
  <div v-if="certificates.length > 0" class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
        <Award class="w-5 h-5 text-[#D4AF37]" />
        Mis certificados
        <span class="text-sm font-normal text-gray-400">({{ certificates.length }})</span>
      </h2>
      <Link :href="route('student.certificates.index')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
        Ver todos <ArrowRight class="w-3 h-3" />
      </Link>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div v-for="cert in certificates" :key="cert.id"
        class="group bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 hover:border-[#D4AF37]/40 hover:shadow-md transition-all">
        <div class="p-3 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
          <Award class="w-6 h-6 text-[#D4AF37]" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-bold text-gray-900 truncate">{{ cert.course_title }}</h4>
          <p class="text-xs text-gray-400 mt-0.5">Emitido el {{ cert.issue_date }}</p>
        </div>
        <a :href="cert.download_url" target="_blank"
          class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-sm flex-shrink-0">
          <ExternalLink class="w-4 h-4" />
        </a>
      </div>
    </div>
  </div>
</template>
