<script setup lang="ts">
import { Wallet, Hash, AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-vue-next';
import type { PaymentItem } from '@/types/user-detail';

const props = defineProps<{
    payments: PaymentItem[];
}>();

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

function formatMoney(n: number | string) {
    return 'S/ ' + Number(n).toFixed(2);
}

const paymentStatusConfig: Record<string, { label: string; class: string; icon: typeof CheckCircle2 }> = {
    aprobado:    { label: 'Aprobado',    class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    en_revision: { label: 'En revisión', class: 'bg-amber-50 text-amber-700 ring-amber-700/10',    icon: Clock },
    pendiente:   { label: 'Pendiente',   class: 'bg-blue-50 text-blue-700 ring-blue-700/10',      icon: AlertCircle },
    rechazado:   { label: 'Rechazado',   class: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: XCircle },
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h3 class="font-serif text-2xl text-slate-900">Historial de <span class="italic">Transacciones</span></h3>
            <span class="px-3 py-1 rounded-full bg-primary/5 text-[10px] font-extrabold uppercase tracking-widest text-primary">{{ payments.length }} Recibos</span>
        </div>
        <div v-if="!payments.length" class="bg-white rounded-[2rem] border border-slate-100 p-20 text-center space-y-4">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-200"><Wallet class="w-10 h-10" /></div>
            <p class="text-slate-400 font-medium">No se registran movimientos financieros locales.</p>
        </div>
        <div v-else class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Detalle</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Monto</th>
                        <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-right">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="p in payments" :key="p.id" class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300"><Hash class="w-5 h-5" /></div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ p.course?.title ?? 'Pago Directo' }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5 uppercase tracking-widest">ID-{{ p.id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center text-sm font-semibold text-slate-600">{{ formatDate(p.created_at) }}</td>
                        <td class="px-6 py-5 text-center text-base font-black text-slate-900">{{ formatMoney(p.amount) }}</td>
                        <td class="px-8 py-5 text-right">
                            <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                :class="paymentStatusConfig[p.status]?.class ?? 'bg-slate-50 text-slate-500'">
                                <component :is="paymentStatusConfig[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                {{ paymentStatusConfig[p.status]?.label ?? p.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
