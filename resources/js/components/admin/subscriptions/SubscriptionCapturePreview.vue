<script setup lang="ts">
import type { Subscription } from '@/types/subscription';
import { CreditCard, Plus, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    subscription: Subscription | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

function formatMoney(n: number) {
    return 'S/ ' + Number(n).toLocaleString('es-PE', { minimumFractionDigits: 2 });
}
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div
                v-if="subscription"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/95 p-8 backdrop-blur-xl"
                @click="emit('close')"
            >
                <button class="absolute right-10 top-10 text-white/50 transition-colors hover:text-white" @click="emit('close')">
                    <XCircle class="h-10 w-10" />
                </button>
                <div class="relative flex h-full w-full max-w-4xl flex-col items-center justify-center gap-6" @click.stop>
                    <template v-if="subscription.payment_capture?.includes('/')">
                        <img
                            :src="subscription.payment_capture"
                            class="max-h-[80vh] max-w-full rounded-2xl border border-white/10 object-contain shadow-2xl"
                        />
                        <a
                            :href="subscription.payment_capture"
                            download
                            class="flex items-center gap-2 rounded-2xl bg-white px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-900 shadow-xl transition-all hover:scale-105"
                        >
                            <Plus class="h-4 w-4" /> Descargar Evidencia Permanente
                        </a>
                    </template>
                    <template v-else>
                        <div class="w-full max-w-md space-y-6 rounded-[3rem] bg-white p-12 text-center shadow-2xl">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-50">
                                <CreditCard class="h-10 w-10 text-amber-500" />
                            </div>
                            <div>
                                <h3 class="font-serif text-2xl text-slate-900">Nota de Pago</h3>
                                <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Activación Administrativa</p>
                            </div>
                            <div class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50 p-6">
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Monto Registrado</span>
                                    <span class="text-lg font-black italic text-primary">{{ formatMoney(subscription.payment_amount || 0) }}</span>
                                </div>
                                <p class="text-sm italic text-slate-600">"{{ subscription.payment_capture }}"</p>
                            </div>
                            <button
                                @click="emit('close')"
                                class="h-14 w-full rounded-2xl bg-[#1a1a1a] text-xs font-black uppercase tracking-widest text-white"
                            >
                                Cerrar Detalle
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
