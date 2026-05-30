<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Crown, AlertCircle, RotateCw, Trash2, Clock, CheckCircle2, XCircle
} from 'lucide-vue-next';
import type { Subscription } from '@/types/subscription';
import type { SubscriptionStatusConfig } from '@/types/subscription';

defineProps<{
    subscriptions: Subscription[];
}>();

const emit = defineEmits<{
    (e: 'preview', sub: Subscription): void;
}>();

const statusCfg: Record<string, SubscriptionStatusConfig> = {
    activa:    { label: 'Activa',    cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    expirada:  { label: 'Expirada',  cls: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: Clock },
    cancelada: { label: 'Cancelada', cls: 'bg-slate-50 text-slate-700 ring-slate-700/10',       icon: XCircle },
};

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatPlanType(type: Subscription['type']) {
    return type.charAt(0).toUpperCase() + type.slice(1);
}

function formatMoney(n: number) {
    return 'S/ ' + Number(n).toLocaleString('es-PE', { minimumFractionDigits: 2 });
}

function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function getACls(id: number) {
    return ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'][id % 5];
}
</script>

<template>
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left min-w-[900px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Cuenta</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Plan Académico</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Inversión Recaudada</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estatus</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Mantenimiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!subscriptions.length">
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-20">
                                <Crown class="w-12 h-12" />
                                <p class="text-sm font-bold uppercase tracking-widest italic">Sin suscripciones activas</p>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="sub in subscriptions" :key="sub.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${getACls(sub.user.id)} shadow-sm shadow-slate-100 shadow-slate-200 group-hover:scale-95 transition-transform duration-500`">
                                    {{ initials(sub.user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ sub.user.name }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wider line-clamp-1">{{ sub.user.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col items-center">
                                <span class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-black tracking-tighter bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200/50">
                                    <Crown class="h-3.5 w-3.5" />
                                    {{ formatPlanType(sub.type) }}
                                </span>
                                <span class="text-[9px] text-slate-300 font-bold uppercase tracking-widest mt-1.5">{{ formatDate(sub.start_date) }} — {{ formatDate(sub.end_date) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-base font-black text-slate-900 tracking-tighter">{{ sub.payment_amount ? formatMoney(sub.payment_amount) : 'S/ 0.00' }}</span>
                                <button v-if="sub.payment_capture" @click="emit('preview', sub)" class="mt-1 flex items-center gap-1.5 text-[9px] font-black uppercase text-blue-500 hover:text-blue-700 transition-colors">
                                    <AlertCircle class="w-2.5 h-2.5" /> Ver Evidencia
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-black uppercase tracking-widest ring-1 ring-inset"
                                    :class="statusCfg[sub.status]?.cls ?? 'bg-slate-50 text-slate-500'">
                                    <component :is="statusCfg[sub.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                    {{ statusCfg[sub.status]?.label ?? sub.status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-all duration-300">
                                <Link :href="route('admin.subscriptions.toggle', { subscription: sub.id })" method="patch" as="button" preserve-scroll
                                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Alternar Estado">
                                    <RotateCw class="w-4 h-4" />
                                </Link>
                                <Link :href="route('admin.subscriptions.destroy', { subscription: sub.id })" method="delete" as="button" preserve-scroll
                                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Remover Acceso">
                                    <Trash2 class="w-4 h-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; line-clamp: 1; }
</style>
