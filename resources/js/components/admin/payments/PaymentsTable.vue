<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    Clock, CheckCircle2, XCircle, RefreshCw, 
    AlertCircle, FileImage, Eye, Activity
} from 'lucide-vue-next';
import { PaymentListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';

defineProps<{
    payments: PaymentListItem[];
    total: number;
    paginationLinks: PaginationLink[];
}>();

const emit = defineEmits<{
    (e: 'view', payment: PaymentListItem): void;
    (e: 'approve', payment: PaymentListItem): void;
    (e: 'reject', payment: PaymentListItem): void;
}>();

// ─── Helpers ─────────────────────────────────────────────────────
import type { Component } from 'vue';

const statusCfg: Record<string, { label: string; cls: string; icon: Component }> = {
    pendiente:   { label: 'Pendiente',   cls: 'bg-blue-50 text-blue-700 ring-blue-700/10',    icon: Clock },
    en_revision: { label: 'En revisión', cls: 'bg-amber-50 text-amber-700 ring-amber-700/10',    icon: RefreshCw },
    aprobado:    { label: 'Aprobado',    cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    rechazado:   { label: 'Rechazado',   cls: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: XCircle },
};
const initials = (n: string) => n.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
const avatarColors = ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'];
const aCls = (id: number) => avatarColors[id % avatarColors.length];
const fDate = (d: string) => new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
const fMoney = (n: number | string) => 'S/ ' + Number(n).toFixed(2);
</script>

<template>
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left min-w-[900px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Programa</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Inversión</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estatus</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Fecha</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!payments.length">
                        <td colspan="5" class="py-24 text-center">
                            <Activity class="w-12 h-12 text-slate-200 mx-auto mb-4" />
                            <p class="text-slate-400 font-medium">No se han detectado transacciones con los criterios seleccionados.</p>
                        </td>
                    </tr>
                    <tr v-for="p in payments" :key="p.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${aCls(p.id)} shadow-sm`">
                                    {{ initials(p.user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ p.user.name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span v-if="p.subscription_type" class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-widest bg-primary/10 text-primary px-2 py-0.5 rounded-full">
                                            Membresía {{ p.subscription_type }}
                                        </span>
                                        <span v-else class="text-[10px] text-slate-400 font-medium uppercase tracking-wider line-clamp-1">{{ p.course?.title ?? 'Pago Directo / Externo' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-base font-black text-slate-900 leading-none">{{ fMoney(p.amount) }}</span>
                                <span v-if="p.comprobante" class="mt-1 flex items-center gap-1 text-[9px] font-bold text-blue-500 uppercase tracking-tighter">
                                    <FileImage class="w-2.5 h-2.5" /> Con Evidencia
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                    :class="statusCfg[p.status]?.cls ?? 'bg-slate-50 text-slate-500'">
                                    <component :is="statusCfg[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                    {{ statusCfg[p.status]?.label ?? p.status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-500">{{ fDate(p.created_at) }}</span>
                                <span class="text-[9px] text-slate-300 font-medium uppercase tracking-widest mt-0.5">ID: #{{ p.id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                <button @click="emit('view', p)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Gestionar">
                                    <Eye class="h-4 w-4" />
                                </button>
                                <template v-if="p.status !== 'aprobado'">
                                    <button @click="emit('approve', p)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 transition-all" title="Aprobar">
                                        <CheckCircle2 class="h-4 w-4" />
                                    </button>
                                    <button v-if="p.status !== 'rechazado'" @click="emit('reject', p)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Rechazar">
                                        <XCircle class="h-4 w-4" />
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="paginationLinks.length > 1" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mostrando {{ payments.length }} de {{ total }} transacciones</p>
        <div class="flex items-center gap-1.5">
            <Link v-for="link in paginationLinks" :key="link.label" :href="link.url || '#'"
                class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                :class="link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600'"
                v-html="link.label" />
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
}
</style>
