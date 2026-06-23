<script setup lang="ts">
import type { Certificate } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Award, ExternalLink } from 'lucide-vue-next';

defineProps<{
    certificates: Certificate[];
}>();
</script>

<template>
    <div v-if="certificates.length > 0" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                <Award class="h-5 w-5 text-[#D4AF37]" />
                Mis certificados
                <span class="text-sm font-normal text-gray-400">({{ certificates.length }})</span>
            </h2>
            <Link
                :href="route('student.certificates.index')"
                class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-widest text-primary hover:underline"
            >
                Ver todos <ArrowRight class="h-3 w-3" />
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div
                v-for="cert in certificates"
                :key="cert.id"
                class="group flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 transition-all hover:border-[#D4AF37]/40 hover:shadow-md"
            >
                <div class="flex-shrink-0 rounded-2xl bg-gradient-to-br from-amber-50 to-yellow-50 p-3 transition-transform group-hover:scale-110">
                    <Award class="h-6 w-6 text-[#D4AF37]" />
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="truncate text-sm font-bold text-gray-900">{{ cert.course_title }}</h4>
                    <p class="mt-0.5 text-xs text-gray-400">Emitido el {{ cert.issue_date }}</p>
                </div>
                <a
                    :href="cert.download_url"
                    target="_blank"
                    class="flex-shrink-0 rounded-xl bg-gray-50 p-2.5 text-gray-400 shadow-sm transition-all hover:bg-[#D4AF37] hover:text-white"
                >
                    <ExternalLink class="h-4 w-4" />
                </a>
            </div>
        </div>
    </div>
</template>
