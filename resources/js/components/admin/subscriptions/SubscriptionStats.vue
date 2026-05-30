<script setup lang="ts">
import { Crown, Users, Activity, Sparkles, CreditCard } from 'lucide-vue-next';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';

defineProps<{
    activeCount: number;
    totalCount: number;
}>();
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <AdminStatsCard label="Canal Premium" sublabel="Total Activas" :value="activeCount" value-class="text-slate-900">
            <template #icon><Crown class="h-4 w-4 text-amber-500/40" /></template>
            <template #bg-icon><Crown class="w-full h-full text-amber-500" /></template>
        </AdminStatsCard>

        <AdminStatsCard label="Participación" sublabel="Alcance Global" :value="totalCount" value-class="text-slate-900">
            <template #icon><Users class="h-4 w-4 text-slate-300" /></template>
        </AdminStatsCard>

        <div class="group relative overflow-hidden rounded-[2rem] bg-[#1a1a1a] p-6 shadow-xl transition-all duration-300 hover:scale-[1.01]">
            <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-outline-variant/60">Monitoreo</span>
                    <Activity class="h-4 w-4 text-white/40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold text-outline-variant/40 uppercase tracking-widest leading-none">Análisis de Retención</p>
                    <p class="text-2xl font-black text-white mt-1 italic tracking-tight">Ecosistema Activo</p>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-[0.08] text-white">
                <Sparkles class="w-full h-full" />
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-[2rem] bg-emerald-600 p-6 shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:scale-[1.01]">
            <div class="relative z-10 flex flex-col justify-between h-full space-y-4 text-white">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-60">Matrícula</span>
                    <CreditCard class="h-4 w-4 opacity-40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest leading-none opacity-60">Ingresos Proyectados</p>
                    <p class="text-2xl font-black mt-1 uppercase tracking-tighter italic">Validación 24/7</p>
                </div>
            </div>
        </div>
    </div>
</template>
