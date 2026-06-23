<script setup lang="ts">
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
import { Activity, CreditCard, Crown, Sparkles, Users } from 'lucide-vue-next';

defineProps<{
    activeCount: number;
    totalCount: number;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <AdminStatsCard label="Canal Premium" sublabel="Total Activas" :value="activeCount" value-class="text-slate-900">
            <template #icon><Crown class="h-4 w-4 text-amber-500/40" /></template>
            <template #bg-icon><Crown class="h-full w-full text-amber-500" /></template>
        </AdminStatsCard>

        <AdminStatsCard label="Participación" sublabel="Alcance Global" :value="totalCount" value-class="text-slate-900">
            <template #icon><Users class="h-4 w-4 text-slate-300" /></template>
        </AdminStatsCard>

        <div class="group relative overflow-hidden rounded-[2rem] bg-[#1a1a1a] p-6 shadow-xl transition-all duration-300 hover:scale-[1.01]">
            <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-outline-variant/60">Monitoreo</span>
                    <Activity class="h-4 w-4 text-white/40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase leading-none tracking-widest text-outline-variant/40">Análisis de Retención</p>
                    <p class="mt-1 text-2xl font-black italic tracking-tight text-white">Ecosistema Activo</p>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 h-24 w-24 text-white opacity-[0.08]">
                <Sparkles class="h-full w-full" />
            </div>
        </div>

        <div
            class="group relative overflow-hidden rounded-[2rem] bg-emerald-600 p-6 shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:scale-[1.01]"
        >
            <div class="relative z-10 flex h-full flex-col justify-between space-y-4 text-white">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-60">Matrícula</span>
                    <CreditCard class="h-4 w-4 opacity-40" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase leading-none tracking-widest opacity-60">Ingresos Proyectados</p>
                    <p class="mt-1 text-2xl font-black uppercase italic tracking-tighter">Validación 24/7</p>
                </div>
            </div>
        </div>
    </div>
</template>
