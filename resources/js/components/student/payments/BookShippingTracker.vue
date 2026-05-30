<script setup lang="ts">
import type { BookOrderShipping } from '@/types/book-order';

defineProps<{
    shipping: BookOrderShipping | null;
    compact?: boolean;
}>();

const steps = [
    { key: 'awaiting_address', label: 'Confirmando envío' },
    { key: 'preparing', label: 'Preparando pedido' },
    { key: 'shipped', label: 'En camino' },
    { key: 'delivered', label: 'Entregado' },
];

function stepIndex(status: string) {
    const idx = steps.findIndex(s => s.key === status);
    return idx >= 0 ? idx : 0;
}
</script>

<template>
    <div v-if="shipping" :class="compact ? 'space-y-3' : 'rounded-2xl border border-blue-100 bg-blue-50/40 p-4 space-y-4'">
        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Envío del libro</p>

        <div class="flex flex-col gap-2">
            <div v-for="(step, i) in steps" :key="step.key" class="flex items-center gap-2">
                <div
                    class="w-2 h-2 rounded-full shrink-0"
                    :class="i <= stepIndex(shipping.status) ? 'bg-blue-600' : 'bg-slate-200'"
                />
                <span
                    class="text-xs font-medium"
                    :class="i <= stepIndex(shipping.status) ? 'text-slate-800 font-bold' : 'text-slate-400'"
                >
                    {{ step.label }}
                </span>
            </div>
        </div>

        <div v-if="shipping.carrier || shipping.tracking_url || shipping.pickup_location || shipping.student_note" class="text-xs text-slate-600 space-y-1.5 pt-1 border-t border-blue-100/80">
            <p v-if="shipping.carrier"><span class="font-bold">Courier:</span> {{ shipping.carrier }}</p>
            <p v-if="shipping.delivery_mode === 'pickup' && shipping.pickup_location">
                <span class="font-bold">Recojo:</span> {{ shipping.pickup_location }}
            </p>
            <p v-if="shipping.delivery_mode === 'home'"><span class="font-bold">Modalidad:</span> Envío a domicilio</p>
            <p v-if="shipping.student_note">{{ shipping.student_note }}</p>
            <a
                v-if="shipping.tracking_url"
                :href="shipping.tracking_url"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline mt-1"
            >
                Ver seguimiento en courier →
            </a>
        </div>
    </div>
</template>
