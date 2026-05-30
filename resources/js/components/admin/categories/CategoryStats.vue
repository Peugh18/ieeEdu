<script setup lang="ts">
import { Layers, BookOpen, Activity, Target } from 'lucide-vue-next';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';

defineProps<{
    total: number;
    visible: number;
}>();
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <AdminStatsCard label="Infraestructura" sublabel="Total Registradas" :value="total" value-class="text-slate-900">
            <template #icon><Layers class="h-4 w-4 text-primary/40" /></template>
            <template #bg-icon><Layers class="w-full h-full text-primary" /></template>
        </AdminStatsCard>

        <AdminStatsCard label="Visualización" sublabel="En esta vista" :value="visible" value-class="text-emerald-600">
            <template #icon><BookOpen class="h-4 w-4 text-emerald-500/40" /></template>
            <template #bg-icon><BookOpen class="w-full h-full text-emerald-500" /></template>
        </AdminStatsCard>

        <div class="group relative overflow-hidden rounded-[2rem] bg-primary p-6 shadow-xl shadow-primary/10 transition-all duration-300 hover:scale-[1.01]">
            <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-outline-variant">Actividad</span>
                    <Activity class="h-4 w-4 text-white/40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold text-outline-variant uppercase tracking-widest leading-none">Estado de Red</p>
                    <p class="text-2xl font-black text-white mt-1 italic leading-tight">Canal Optimizando</p>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-[0.08] text-white">
                <Target class="w-full h-full" />
            </div>
        </div>
    </div>
</template>
