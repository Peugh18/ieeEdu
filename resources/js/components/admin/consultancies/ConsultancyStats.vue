<script setup lang="ts">
import type { ConsultancyStats } from '@/types/consultancy-request';
import { Briefcase, CheckCircle, Clock, Phone } from 'lucide-vue-next';

defineProps<{
    stats: ConsultancyStats;
}>();
</script>

<template>
    <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="flex items-center gap-5 rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10"><Briefcase class="h-6 w-6 text-primary" /></div>
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Total</p>
                <p class="text-3xl font-bold text-on-surface">{{ stats.total }}</p>
            </div>
        </div>
        <div class="flex items-center gap-5 rounded-3xl border border-amber-100 bg-amber-50 p-6 shadow-sm">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100"><Clock class="h-6 w-6 text-amber-600" /></div>
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-widest text-amber-800">Pendientes</p>
                <p class="text-3xl font-bold text-amber-900">{{ stats.pending }}</p>
            </div>
        </div>
        <div class="flex items-center gap-5 rounded-3xl border border-blue-100 bg-blue-50 p-6 shadow-sm">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100"><Phone class="h-6 w-6 text-blue-600" /></div>
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-widest text-blue-800">Contactados</p>
                <p class="text-3xl font-bold text-blue-900">{{ stats.contacted }}</p>
            </div>
        </div>
        <div class="flex items-center gap-5 rounded-3xl border border-green-100 bg-green-50 p-6 shadow-sm">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100"><CheckCircle class="h-6 w-6 text-green-600" /></div>
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-widest text-green-800">Resueltos</p>
                <p class="text-3xl font-bold text-green-900">{{ stats.resolved }}</p>
            </div>
        </div>
    </div>
</template>
