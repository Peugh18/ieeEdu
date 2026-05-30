<script setup lang="ts">
import { Briefcase, Clock, Phone, CheckCircle } from 'lucide-vue-next';
import type { ConsultancyStats } from '@/types/consultancy-request';

defineProps<{
    stats: ConsultancyStats;
}>();
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center"><Briefcase class="w-6 h-6 text-primary" /></div>
            <div><p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-on-surface">{{ stats.total }}</p></div>
        </div>
        <div class="bg-amber-50 rounded-3xl p-6 shadow-sm border border-amber-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center"><Clock class="w-6 h-6 text-amber-600" /></div>
            <div><p class="text-xs font-bold text-amber-800 uppercase tracking-widest mb-1">Pendientes</p><p class="text-3xl font-bold text-amber-900">{{ stats.pending }}</p></div>
        </div>
        <div class="bg-blue-50 rounded-3xl p-6 shadow-sm border border-blue-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center"><Phone class="w-6 h-6 text-blue-600" /></div>
            <div><p class="text-xs font-bold text-blue-800 uppercase tracking-widest mb-1">Contactados</p><p class="text-3xl font-bold text-blue-900">{{ stats.contacted }}</p></div>
        </div>
        <div class="bg-green-50 rounded-3xl p-6 shadow-sm border border-green-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center"><CheckCircle class="w-6 h-6 text-green-600" /></div>
            <div><p class="text-xs font-bold text-green-800 uppercase tracking-widest mb-1">Resueltos</p><p class="text-3xl font-bold text-green-900">{{ stats.resolved }}</p></div>
        </div>
    </div>
</template>
