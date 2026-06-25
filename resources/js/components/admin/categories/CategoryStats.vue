<script setup lang="ts">
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
import { Activity, BookOpen, Layers, Target } from 'lucide-vue-next';

defineProps<{
    total: number;
    visible: number;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <AdminStatsCard label="Infraestructura" sublabel="Total Registradas" :value="total" value-class="text-slate-900">
            <template #icon><Layers class="h-4 w-4 text-primary/40" /></template>
            <template #bg-icon><Layers class="h-full w-full text-primary" /></template>
        </AdminStatsCard>

        <AdminStatsCard label="Visualización" sublabel="En esta vista" :value="visible" value-class="text-emerald-600">
            <template #icon><BookOpen class="h-4 w-4 text-emerald-500/40" /></template>
            <template #bg-icon><BookOpen class="h-full w-full text-emerald-500" /></template>
        </AdminStatsCard>

        <div
            class="group relative overflow-hidden rounded-[2rem] bg-primary p-6 shadow-xl shadow-primary/10 transition-all duration-300 hover:scale-[1.01]"
        >
            <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-outline-variant">Actividad</span>
                    <Activity class="h-4 w-4 text-white/40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase leading-none tracking-widest text-outline-variant">Estado de Red</p>
                    <p class="mt-1 text-2xl font-black italic leading-tight text-white">Canal Optimizando</p>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 h-24 w-24 text-white opacity-[0.08]">
                <Target class="h-full w-full" />
            </div>
        </div>
    </div>
</template>
