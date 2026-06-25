<script setup lang="ts">
import type { Subscription, SubscriptionStatusConfig } from '@/types/subscription';
import { Link } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle2, Clock, Crown, RotateCw, Trash2, XCircle } from 'lucide-vue-next';

defineProps<{
    subscriptions: Subscription[];
}>();

const emit = defineEmits<{
    (e: 'preview', sub: Subscription): void;
}>();

const statusCfg: Record<string, SubscriptionStatusConfig> = {
    activa: { label: 'Activa', cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    expirada: { label: 'Expirada', cls: 'bg-rose-50 text-rose-700 ring-rose-700/10', icon: Clock },
    cancelada: { label: 'Cancelada', cls: 'bg-slate-50 text-slate-700 ring-slate-700/10', icon: XCircle },
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
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

function getACls(id: number) {
    return [
        'bg-indigo-50 text-indigo-700',
        'bg-blue-50 text-blue-700',
        'bg-emerald-50 text-emerald-700',
        'bg-amber-50 text-amber-700',
        'bg-rose-50 text-rose-700',
    ][id % 5];
}
</script>

<template>
    <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-sm">
        <div class="custom-scrollbar overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Cuenta</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Plan Académico</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">
                            Inversión Recaudada
                        </th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estatus</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Mantenimiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!subscriptions.length">
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-20">
                                <Crown class="h-12 w-12" />
                                <p class="text-sm font-bold uppercase italic tracking-widest">Sin suscripciones activas</p>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="sub in subscriptions" :key="sub.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div
                                    :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${getACls(sub.user.id)} shadow-sm shadow-slate-100 shadow-slate-200 transition-transform duration-500 group-hover:scale-95`"
                                >
                                    {{ initials(sub.user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary">
                                        {{ sub.user.name }}
                                    </p>
                                    <p class="mt-0.5 line-clamp-1 text-[10px] font-medium uppercase tracking-wider text-slate-400">
                                        {{ sub.user.email }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col items-center">
                                <span
                                    class="inline-flex items-center gap-2 rounded-xl bg-amber-50 px-4 py-2 text-xs font-black tracking-tighter text-amber-700 ring-1 ring-inset ring-amber-200/50"
                                >
                                    <Crown class="h-3.5 w-3.5" />
                                    {{ formatPlanType(sub.type) }}
                                </span>
                                <span class="mt-1.5 text-[9px] font-bold uppercase tracking-widest text-slate-300"
                                    >{{ formatDate(sub.start_date) }} — {{ formatDate(sub.end_date) }}</span
                                >
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-base font-black tracking-tighter text-slate-900">{{ formatMoney(sub.payment_amount ?? 0) }}</span>
                                <span
                                    v-if="sub.payment_status && sub.payment_status !== 'aprobado'"
                                    class="mt-1 text-[9px] font-bold uppercase tracking-widest text-amber-600"
                                >
                                    Pago {{ sub.payment_status === 'en_revision' ? 'en revisión' : 'pendiente' }}
                                </span>
                                <button
                                    v-if="sub.payment_capture"
                                    @click="emit('preview', sub)"
                                    class="mt-1 flex items-center gap-1.5 text-[9px] font-black uppercase text-blue-500 transition-colors hover:text-blue-700"
                                >
                                    <AlertCircle class="h-2.5 w-2.5" /> Ver Evidencia
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-black uppercase tracking-widest ring-1 ring-inset"
                                    :class="statusCfg[sub.status]?.cls ?? 'bg-slate-50 text-slate-500'"
                                >
                                    <component :is="statusCfg[sub.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                    {{ statusCfg[sub.status]?.label ?? sub.status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-all duration-300 group-hover:opacity-100">
                                <Link
                                    :href="route('admin.subscriptions.toggle', { subscription: sub.id })"
                                    method="patch"
                                    as="button"
                                    preserve-scroll
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-primary hover:bg-slate-50 hover:text-primary"
                                    title="Alternar Estado"
                                >
                                    <RotateCw class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="route('admin.subscriptions.destroy', { subscription: sub.id })"
                                    method="delete"
                                    as="button"
                                    preserve-scroll
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-rose-500 hover:bg-rose-50 hover:text-rose-600"
                                    title="Remover Acceso"
                                >
                                    <Trash2 class="h-4 w-4" />
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
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    line-clamp: 1;
}
</style>
