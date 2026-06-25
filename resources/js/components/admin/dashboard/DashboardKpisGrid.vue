<script setup lang="ts">
import { ArrowUpRight, Award, Clock, Crown, MessageCircle, Target, UserCheck, UserX } from 'lucide-vue-next';

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
            <Target class="h-4 w-4 text-primary" />
            <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Indicadores Operativos</h2>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-7">
            <!-- Alumnos activos -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <UserCheck class="h-5 w-5 text-emerald-500" />
                <span class="font-serif text-2xl font-black tabular-nums text-gray-900 md:text-3xl">{{ stats.activeUsers }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Alumnos Activos</span>
            </article>

            <!-- Alumnos inactivos -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <UserX class="h-5 w-5 text-red-400" />
                <span class="font-serif text-2xl font-black tabular-nums text-gray-900 md:text-3xl">{{ stats.inactiveUsers }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Inactivos</span>
            </article>

            <!-- Premium -->
            <article
                class="group flex flex-col gap-2 rounded-2xl bg-primary p-4 shadow-sm transition-all hover:scale-105 hover:shadow-md md:gap-3 md:p-6"
            >
                <Crown class="h-5 w-5 text-primary-fixed" />
                <span class="font-serif text-2xl font-black tabular-nums text-primary-fixed md:text-3xl">{{ stats.premiumUsers }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-primary-fixed/60">Usuarios Premium</span>
            </article>

            <!-- Nuevos este mes -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <ArrowUpRight class="h-5 w-5 text-blue-400" />
                <span class="font-serif text-2xl font-black tabular-nums text-gray-900 md:text-3xl">{{ stats.newUsersThisMonth }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Nuevos este mes</span>
            </article>

            <!-- Pagos pendientes -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-orange-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <Clock class="h-5 w-5 text-orange-400" />
                <span class="font-serif text-2xl font-black tabular-nums text-orange-500 md:text-3xl">{{ stats.pendingPayments }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Pagos Pendientes</span>
            </article>

            <!-- Certificados -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <Award class="h-5 w-5 text-purple-400" />
                <span class="font-serif text-2xl font-black tabular-nums text-gray-900 md:text-3xl">{{ stats.certificatesIssued }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Certificados Emitidos</span>
            </article>

            <!-- WhatsApp Leads -->
            <article
                class="flex flex-col gap-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md md:gap-3 md:p-6"
            >
                <MessageCircle class="h-5 w-5 text-green-500" />
                <span class="font-serif text-2xl font-black tabular-nums text-gray-900 md:text-3xl">{{ stats.whatsappLeadsMonth }}</span>
                <span class="text-[9px] font-black uppercase leading-tight tracking-widest text-gray-400">Leads WhatsApp / mes</span>
            </article>
        </div>
    </section>
</template>
