<script setup lang="ts">
import { PaymentListItem } from '@/types/admin';
import { AlertCircle, CheckCircle2, Clock, Download, ExternalLink, FileImage, Hash, RefreshCw, Undo2, X, XCircle } from 'lucide-vue-next';

defineProps<{
    payment: PaymentListItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
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
    <Teleport to="body">
        <Transition name="slide">
            <div v-if="payment" class="fixed inset-0 z-50 flex items-center justify-end bg-slate-900/40 backdrop-blur-sm" @click.self="emit('close')">
                <div class="flex h-full w-full max-w-md flex-col overflow-hidden bg-white shadow-2xl">
                    <div class="relative shrink-0 bg-slate-900 p-8 text-white">
                        <div class="absolute right-0 top-0 p-8 opacity-10"><Hash class="h-24 w-24" /></div>
                        <button
                            @click="emit('close')"
                            class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
                        >
                            <X class="h-5 w-5" />
                        </button>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-outline-variant/60">Validación de Pago</span>
                        <h2 class="mt-1 font-serif text-3xl">
                            Transacción <span class="italic text-outline-variant">#{{ payment.id }}</span>
                        </h2>
                    </div>

                    <div class="custom-scrollbar flex-1 space-y-8 overflow-y-auto p-8">
                        <div :class="`flex items-center gap-3 rounded-2xl p-4 ring-1 ring-inset ${statusCfg[payment.status]?.cls}`">
                            <component :is="statusCfg[payment.status]?.icon ?? AlertCircle" class="h-5 w-5" />
                            <span class="text-[10px] font-extrabold uppercase tracking-widest"
                                >Estatus Actual: {{ statusCfg[payment.status]?.label }}</span
                            >
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-4">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                                    <p class="mb-3 text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Estudiante</p>
                                    <div class="flex items-center gap-3">
                                        <div
                                            :class="`flex h-10 w-10 items-center justify-center rounded-xl text-xs font-bold ${aCls(payment.user.id || 0)}`"
                                        >
                                            {{ initials(payment.user.name) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold leading-tight text-slate-900">{{ payment.user.name }}</p>
                                            <p class="text-xs font-medium text-slate-400">{{ payment.user.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                                    <div class="col-span-2 border-b border-slate-200/50 pb-2">
                                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Programa</p>
                                        <p class="font-bold leading-tight text-slate-900">{{ payment.course?.title ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Inversión</p>
                                        <p class="text-lg font-black text-primary">{{ fMoney(payment.amount) }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1 text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Registro</p>
                                        <p class="text-sm font-bold text-slate-600">{{ fDate(payment.created_at) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <p class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Comprobante de Pago</p>
                                <div v-if="payment.comprobante" class="group relative">
                                    <a
                                        :href="payment.comprobante"
                                        target="_blank"
                                        class="block overflow-hidden rounded-[2rem] border border-slate-100 bg-slate-50 shadow-sm"
                                    >
                                        <img
                                            :src="payment.comprobante"
                                            alt="Comprobante"
                                            class="max-h-[400px] w-full object-contain transition-transform duration-700 group-hover:scale-105"
                                        />
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition-colors group-hover:bg-slate-900/10"
                                        >
                                            <ExternalLink class="h-8 w-8 text-white opacity-0 transition-opacity group-hover:opacity-100" />
                                        </div>
                                    </a>
                                    <a
                                        :href="payment.comprobante"
                                        download
                                        class="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-slate-50 text-xs font-bold text-slate-600 transition-all hover:bg-slate-100"
                                    >
                                        <Download class="h-4 w-4" /> Guardar Copia Local
                                    </a>
                                </div>
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center rounded-[2rem] border border-dashed border-slate-100 bg-slate-50 py-12"
                                >
                                    <FileImage class="mb-3 h-10 w-10 text-slate-200" />
                                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Sin Archivo Adjunto</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 border-t border-slate-100 bg-slate-50/50 p-8">
                        <template v-if="payment.status !== 'aprobado'">
                            <button
                                @click="emit('approve', payment)"
                                class="flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-95"
                            >
                                <CheckCircle2 class="h-5 w-5" /> Aprobar y Habilitar Acceso
                            </button>
                            <button
                                v-if="payment.status !== 'rechazado'"
                                @click="emit('reject', payment)"
                                class="flex h-14 w-full items-center justify-center gap-2 rounded-2xl border border-rose-100 bg-white text-sm font-bold text-rose-600 transition-all hover:border-rose-200 hover:bg-rose-50"
                            >
                                <XCircle class="h-5 w-5" /> Rechazar Operación
                            </button>
                        </template>
                        <button
                            v-else
                            @click="emit('revert', payment)"
                            class="flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-amber-500 text-sm font-bold text-white shadow-lg shadow-amber-500/20 transition-all hover:scale-[1.02] active:scale-95"
                        >
                            <Undo2 class="h-5 w-5" /> Revertir Aprobación
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
}
</style>
