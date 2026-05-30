<script setup lang="ts">
import { XCircle, CreditCard, Plus } from 'lucide-vue-next';
import type { Subscription } from '@/types/subscription';

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
            <div v-if="subscription" class="fixed inset-0 z-[100] bg-slate-900/95 backdrop-blur-xl flex items-center justify-center p-8" @click="emit('close')">
                <button class="absolute top-10 right-10 text-white/50 hover:text-white transition-colors" @click="emit('close')">
                    <XCircle class="w-10 h-10" />
                </button>
                <div class="relative max-w-4xl w-full h-full flex flex-col items-center justify-center gap-6" @click.stop>
                    <template v-if="subscription.payment_capture?.includes('/')">
                        <img :src="subscription.payment_capture" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10" />
                        <a :href="subscription.payment_capture" download class="px-8 py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                            <Plus class="w-4 h-4" /> Descargar Evidencia Permanente
                        </a>
                    </template>
                    <template v-else>
                        <div class="bg-white p-12 rounded-[3rem] max-w-md w-full text-center space-y-6 shadow-2xl">
                            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto">
                                <CreditCard class="w-10 h-10 text-amber-500" />
                            </div>
                            <div>
                                <h3 class="font-serif text-2xl text-slate-900">Nota de Pago</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Activación Administrativa</p>
                            </div>
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Monto Registrado</span>
                                    <span class="text-lg font-black text-primary italic">{{ formatMoney(subscription.payment_amount || 0) }}</span>
                                </div>
                                <p class="italic text-slate-600 text-sm">"{{ subscription.payment_capture }}"</p>
                            </div>
                            <button @click="emit('close')" class="w-full h-14 bg-[#1a1a1a] text-white rounded-2xl font-black text-xs uppercase tracking-widest">Cerrar Detalle</button>
                        </div>
                    </template>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
