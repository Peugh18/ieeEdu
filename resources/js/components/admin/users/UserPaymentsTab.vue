<script setup lang="ts">
import type { PaymentItem } from '@/types/user-detail';
import { AlertCircle, CheckCircle2, Clock, Hash, Wallet, XCircle } from 'lucide-vue-next';

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
    aprobado: { label: 'Aprobado', class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    en_revision: { label: 'En revisión', class: 'bg-amber-50 text-amber-700 ring-amber-700/10', icon: Clock },
    pendiente: { label: 'Pendiente', class: 'bg-blue-50 text-blue-700 ring-blue-700/10', icon: AlertCircle },
    rechazado: { label: 'Rechazado', class: 'bg-rose-50 text-rose-700 ring-rose-700/10', icon: XCircle },
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h3 class="font-serif text-2xl text-slate-900">Historial de <span class="italic">Transacciones</span></h3>
            <span class="rounded-full bg-primary/5 px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-primary"
                >{{ payments.length }} Recibos</span
            >
        </div>
        <div v-if="!payments.length" class="space-y-4 rounded-[2rem] border border-slate-100 bg-white p-20 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-200"><Wallet class="h-10 w-10" /></div>
            <p class="font-medium text-slate-400">No se registran movimientos financieros locales.</p>
        </div>
        <div v-else class="overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Detalle</th>
                        <th class="px-6 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Fecha</th>
                        <th class="px-6 py-4 text-center text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Monto</th>
                        <th class="px-8 py-4 text-right text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="p in payments" :key="p.id" class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-300">
                                    <Hash class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold leading-tight text-slate-900">{{ p.course?.title ?? 'Pago Directo' }}</p>
                                    <p class="mt-0.5 text-[10px] uppercase tracking-widest text-slate-400">ID-{{ p.id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center text-sm font-semibold text-slate-600">{{ formatDate(p.created_at) }}</td>
                        <td class="px-6 py-5 text-center text-base font-black text-slate-900">{{ formatMoney(p.amount) }}</td>
                        <td class="px-8 py-5 text-right">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                :class="paymentStatusConfig[p.status]?.class ?? 'bg-slate-50 text-slate-500'"
                            >
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
