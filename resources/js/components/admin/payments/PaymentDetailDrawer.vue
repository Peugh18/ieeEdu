<script setup lang="ts">
import { 
    X, Hash, Clock, RefreshCw, CheckCircle2, 
    XCircle, AlertCircle, FileImage, ExternalLink, 
    Download, Check 
} from 'lucide-vue-next';
import { PaymentListItem } from '@/types/admin';

defineProps<{
    payment: PaymentListItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
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
    <Teleport to="body">
        <Transition name="slide">
            <div v-if="payment" class="fixed inset-0 z-50 flex items-center justify-end bg-slate-900/40 backdrop-blur-sm" @click.self="emit('close')">
                <div class="h-full w-full max-w-md bg-white shadow-2xl overflow-hidden flex flex-col">
                    <div class="bg-slate-900 p-8 text-white shrink-0 relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10"><Hash class="w-24 h-24" /></div>
                        <button @click="emit('close')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <X class="h-5 w-5" />
                        </button>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-outline-variant/60">Validación de Pago</span>
                        <h2 class="font-serif text-3xl mt-1">Transacción <span class="italic text-outline-variant">#{{ payment.id }}</span></h2>
                    </div>

                    <div class="p-8 space-y-8 flex-1 overflow-y-auto custom-scrollbar">
                        <div :class="`flex items-center gap-3 p-4 rounded-2xl ring-1 ring-inset ${statusCfg[payment.status]?.cls}`">
                            <component :is="statusCfg[payment.status]?.icon ?? AlertCircle" class="h-5 w-5" />
                            <span class="font-extrabold text-[10px] uppercase tracking-widest">Estatus Actual: {{ statusCfg[payment.status]?.label }}</span>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-4">
                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                    <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-3">Estudiante</p>
                                    <div class="flex items-center gap-3">
                                        <div :class="`h-10 w-10 rounded-xl flex items-center justify-center font-bold text-xs ${aCls(payment.user.id || 0)}`">{{ initials(payment.user.name) }}</div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm leading-tight">{{ payment.user.name }}</p>
                                            <p class="text-xs text-slate-400 font-medium">{{ payment.user.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 grid grid-cols-2 gap-4">
                                    <div class="col-span-2 pb-2 border-b border-slate-200/50">
                                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Programa</p>
                                        <p class="font-bold text-slate-900 leading-tight">{{ payment.course?.title ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Inversión</p>
                                        <p class="text-lg font-black text-primary">{{ fMoney(payment.amount) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Registro</p>
                                        <p class="text-sm font-bold text-slate-600">{{ fDate(payment.created_at) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <p class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Comprobante de Pago</p>
                                <div v-if="payment.comprobante" class="relative group">
                                    <a :href="payment.comprobante" target="_blank" class="block rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm bg-slate-50">
                                        <img :src="payment.comprobante" alt="Comprobante" class="w-full object-contain max-h-[400px] group-hover:scale-105 transition-transform duration-700" />
                                        <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/10 transition-colors flex items-center justify-center">
                                            <ExternalLink class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                        </div>
                                    </a>
                                    <a :href="payment.comprobante" download class="mt-4 w-full h-12 flex items-center justify-center gap-2 rounded-2xl bg-slate-50 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">
                                        <Download class="h-4 w-4" /> Guardar Copia Local
                                    </a>
                                </div>
                                <div v-else class="flex flex-col items-center justify-center py-12 rounded-[2rem] bg-slate-50 border border-slate-100 border-dashed">
                                    <FileImage class="h-10 w-10 text-slate-200 mb-3" />
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sin Archivo Adjunto</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 border-t border-slate-100 bg-slate-50/50 space-y-3">
                        <template v-if="payment.status !== 'aprobado'">
                            <button @click="emit('approve', payment)" class="w-full h-14 rounded-2xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                                <CheckCircle2 class="w-5 h-5" /> Aprobar y Habilitar Acceso
                            </button>
                            <button v-if="payment.status !== 'rechazado'" @click="emit('reject', payment)" class="w-full h-14 rounded-2xl bg-white border border-rose-100 text-sm font-bold text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center justify-center gap-2">
                                <XCircle class="w-5 h-5" /> Rechazar Operación
                            </button>
                        </template>
                        <div v-else class="h-14 flex items-center justify-center gap-2 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-sm">
                            <Check class="h-5 w-5" /> Transacción Finalizada Correctamente
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); opacity: 0; }

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
