<script setup lang="ts">
import { PaymentListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Link } from '@inertiajs/vue3';
import { Activity, AlertCircle, CheckCircle2, Clock, Eye, FileImage, RefreshCw, Undo2, XCircle } from 'lucide-vue-next';

defineProps<{
    payments: PaymentListItem[];
    total: number;
    paginationLinks: PaginationLink[];
}>();

const emit = defineEmits<{
    (e: 'view', payment: PaymentListItem): void;
    (e: 'approve', payment: PaymentListItem): void;
    (e: 'reject', payment: PaymentListItem): void;
    (e: 'revert', payment: PaymentListItem): void;
}>();

// ─── Helpers ─────────────────────────────────────────────────────
import type { Component } from 'vue';

const statusCfg: Record<string, { label: string; cls: string; icon: Component }> = {
    pendiente: { label: 'Pendiente', cls: 'bg-blue-50 text-blue-700 ring-blue-700/10', icon: Clock },
    en_revision: { label: 'En revisión', cls: 'bg-amber-50 text-amber-700 ring-amber-700/10', icon: RefreshCw },
    aprobado: { label: 'Aprobado', cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    rechazado: { label: 'Rechazado', cls: 'bg-rose-50 text-rose-700 ring-rose-700/10', icon: XCircle },
};
const initials = (n: string) =>
    n
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
const avatarColors = [
    'bg-indigo-50 text-indigo-700',
    'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700',
    'bg-amber-50 text-amber-700',
    'bg-rose-50 text-rose-700',
];
const aCls = (id: number) => avatarColors[id % avatarColors.length];
const fDate = (d: string) => new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
const fMoney = (n: number | string) => 'S/ ' + Number(n).toFixed(2);
</script>

<template>
    <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-sm">
        <div class="custom-scrollbar overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Programa</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Inversión</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estatus</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Fecha</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-if="!payments.length">
                        <td colspan="5" class="py-24 text-center">
                            <Activity class="mx-auto mb-4 h-12 w-12 text-slate-200" />
                            <p class="font-medium text-slate-400">No se han detectado transacciones con los criterios seleccionados.</p>
                        </td>
                    </tr>
                    <tr v-for="p in payments" :key="p.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div
                                    :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${aCls(p.id)} shadow-sm`"
                                >
                                    {{ initials(p.user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary">
                                        {{ p.user.name }}
                                    </p>
                                    <div class="mt-0.5 flex items-center gap-1.5">
                                        <span
                                            v-if="p.subscription_type"
                                            class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-widest text-primary"
                                        >
                                            Membresía {{ p.subscription_type }}
                                        </span>
                                        <span
                                            v-else-if="p.book"
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-widest text-blue-700"
                                        >
                                            Libro · {{ p.book.title }}
                                        </span>
                                        <span v-else class="line-clamp-1 text-[10px] font-medium uppercase tracking-wider text-slate-400">{{
                                            p.course?.title ?? 'Pago Directo / Externo'
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-base font-black leading-none text-slate-900">{{ fMoney(p.amount) }}</span>
                                <span
                                    v-if="p.comprobante"
                                    class="mt-1 flex items-center gap-1 text-[9px] font-bold uppercase tracking-tighter text-blue-500"
                                >
                                    <FileImage class="h-2.5 w-2.5" /> Con Evidencia
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                    :class="statusCfg[p.status]?.cls ?? 'bg-slate-50 text-slate-500'"
                                >
                                    <component :is="statusCfg[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                    {{ statusCfg[p.status]?.label ?? p.status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-500">{{ fDate(p.created_at) }}</span>
                                <span class="mt-0.5 text-[9px] font-medium uppercase tracking-widest text-slate-300">ID: #{{ p.id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-opacity group-hover:opacity-100">
                                <button
                                    @click="emit('view', p)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-primary hover:bg-slate-50 hover:text-primary"
                                    title="Gestionar"
                                >
                                    <Eye class="h-4 w-4" />
                                </button>
                                <template v-if="p.status !== 'aprobado'">
                                    <button
                                        @click="emit('approve', p)"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-600"
                                        title="Aprobar"
                                    >
                                        <CheckCircle2 class="h-4 w-4" />
                                    </button>
                                    <button
                                        v-if="p.status !== 'rechazado'"
                                        @click="emit('reject', p)"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-rose-500 hover:bg-rose-50 hover:text-rose-600"
                                        title="Rechazar"
                                    >
                                        <XCircle class="h-4 w-4" />
                                    </button>
                                </template>
                                <button
                                    v-else
                                    @click="emit('revert', p)"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-amber-500 hover:bg-amber-50 hover:text-amber-600"
                                    title="Revertir aprobación"
                                >
                                    <Undo2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="paginationLinks.length > 1" class="flex flex-col items-center justify-between gap-4 px-2 md:flex-row">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Mostrando {{ payments.length }} de {{ total }} transacciones</p>
        <div class="flex items-center gap-1.5">
            <Link
                v-for="link in paginationLinks"
                :key="link.label"
                :href="link.url || '#'"
                class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                :class="
                    link.active
                        ? 'bg-slate-900 text-white shadow-lg'
                        : 'border border-slate-100 bg-white text-slate-400 hover:border-slate-300 hover:text-slate-600'
                "
                v-html="link.label"
            />
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
