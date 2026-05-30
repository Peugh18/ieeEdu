<script setup lang="ts">
import { 
    Target, UserCheck, UserX, Crown, ArrowUpRight, 
    Clock, Award, MessageCircle 
} from 'lucide-vue-next';

interface KpiStats {
    activeUsers: number;
    inactiveUsers: number;
    premiumUsers: number;
    newUsersThisMonth: number;
    pendingPayments: number;
    certificatesIssued: number;
    whatsappLeadsMonth: number;
}

defineProps<{
    stats: KpiStats;
}>();
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <Target class="w-4 h-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Indicadores Operativos</h2>
        </div>

        <div class="grid gap-4 grid-cols-2 md:grid-cols-4 lg:grid-cols-7">
            <!-- Alumnos activos -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <UserCheck class="w-5 h-5 text-emerald-500" />
                <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.activeUsers }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Alumnos Activos</span>
            </article>

            <!-- Alumnos inactivos -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <UserX class="w-5 h-5 text-red-400" />
                <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.inactiveUsers }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Inactivos</span>
            </article>

            <!-- Premium -->
            <article class="bg-primary rounded-2xl p-4 md:p-6 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3 group hover:scale-105">
                <Crown class="w-5 h-5 text-primary-fixed" />
                <span class="text-2xl md:text-3xl font-serif font-black text-primary-fixed tabular-nums">{{ stats.premiumUsers }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-primary-fixed/60 leading-tight">Usuarios Premium</span>
            </article>

            <!-- Nuevos este mes -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <ArrowUpRight class="w-5 h-5 text-blue-400" />
                <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.newUsersThisMonth }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Nuevos este mes</span>
            </article>

            <!-- Pagos pendientes -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-orange-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <Clock class="w-5 h-5 text-orange-400" />
                <span class="text-2xl md:text-3xl font-serif font-black text-orange-500 tabular-nums">{{ stats.pendingPayments }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Pagos Pendientes</span>
            </article>

            <!-- Certificados -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <Award class="w-5 h-5 text-purple-400" />
                <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.certificatesIssued }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Certificados Emitidos</span>
            </article>

            <!-- WhatsApp Leads -->
            <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                <MessageCircle class="w-5 h-5 text-green-500" />
                <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.whatsappLeadsMonth }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Leads WhatsApp / mes</span>
            </article>
        </div>
    </section>
</template>
