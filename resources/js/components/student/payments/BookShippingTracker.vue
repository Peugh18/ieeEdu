<script setup lang="ts">
import type { BookOrderShipping } from '@/types/book-order';
import { Clipboard, ExternalLink, Home, MapPin, Truck } from 'lucide-vue-next';

defineProps<{
    shipping: BookOrderShipping | null;
    compact?: boolean;
}>();

const steps = [
    { key: 'awaiting_address', label: 'Confirmación' },
    { key: 'preparing', label: 'Preparación' },
    { key: 'shipped', label: 'En camino' },
    { key: 'delivered', label: 'Entregado' },
];

function stepIndex(status: string) {
    const idx = steps.findIndex((s) => s.key === status);
    return idx >= 0 ? idx : 0;
}
</script>

<template>
    <div v-if="shipping" :class="compact ? 'space-y-4' : 'space-y-8 rounded-3xl border border-blue-100/80 bg-blue-50/20 p-6 md:p-8'">
        <div class="flex items-center justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Estado de despacho</p>
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-semibold capitalize text-blue-700">
                {{ steps[stepIndex(shipping.status)].label }}
            </span>
        </div>

        <!-- Horizontal Progress Steps -->
        <div class="relative mx-auto flex max-w-3xl items-center justify-between gap-2 pb-4 pt-2">
            <!-- Background Line -->
            <div class="absolute left-[5%] right-[5%] top-[21px] -z-10 h-0.5 bg-slate-100 dark:bg-slate-800"></div>
            <!-- Active Fill Line -->
            <div
                class="absolute left-[5%] top-[21px] -z-10 h-0.5 bg-blue-500 transition-all duration-500"
                :style="{ width: `${(stepIndex(shipping.status) / (steps.length - 1)) * 90}%` }"
            ></div>

            <div v-for="(step, i) in steps" :key="step.key" class="group flex flex-1 flex-col items-center gap-2">
                <!-- Step Circle -->
                <div
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 text-[10px] font-bold transition-all duration-500"
                    :class="[
                        i <= stepIndex(shipping.status)
                            ? 'border-blue-500 bg-blue-500 text-white shadow-md shadow-blue-500/10'
                            : 'border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-slate-900',
                    ]"
                >
                    {{ i + 1 }}
                </div>
                <span
                    class="mt-1 text-center text-[9px] font-black uppercase leading-none tracking-wider md:text-[10px]"
                    :class="i <= stepIndex(shipping.status) ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400'"
                >
                    {{ step.label }}
                </span>
            </div>
        </div>

        <!-- Shipping Details Grid -->
        <div
            v-if="shipping.carrier || shipping.tracking_url || shipping.pickup_location || shipping.student_note"
            class="grid grid-cols-1 gap-4 border-t border-blue-100/50 pt-4 sm:grid-cols-2 md:grid-cols-3"
        >
            <div v-if="shipping.carrier" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <Truck class="h-4 w-4 text-blue-500" />
                <div>
                    <p class="mb-0.5 text-[8px] font-bold uppercase leading-none tracking-widest text-slate-400">Courier</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ shipping.carrier }}</p>
                </div>
            </div>

            <div
                v-if="shipping.delivery_mode === 'pickup' && shipping.pickup_location"
                class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400"
            >
                <MapPin class="h-4 w-4 text-blue-500" />
                <div>
                    <p class="mb-0.5 text-[8px] font-bold uppercase leading-none tracking-widest text-slate-400">Lugar de Recojo</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ shipping.pickup_location }}</p>
                </div>
            </div>

            <div v-if="shipping.delivery_mode === 'home'" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <Home class="h-4 w-4 text-blue-500" />
                <div>
                    <p class="mb-0.5 text-[8px] font-bold uppercase leading-none tracking-widest text-slate-400">Modalidad</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">Envío a domicilio</p>
                </div>
            </div>

            <div v-if="shipping.student_note" class="col-span-full flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <Clipboard class="h-4 w-4 text-blue-500" />
                <div>
                    <p class="mb-0.5 text-[8px] font-bold uppercase leading-none tracking-widest text-slate-400">Nota de envío</p>
                    <p class="italic text-slate-700 dark:text-slate-300">{{ shipping.student_note }}</p>
                </div>
            </div>

            <div v-if="shipping.tracking_url" class="col-span-full flex items-center gap-2.5 pt-1 text-xs text-slate-600 dark:text-slate-400">
                <a
                    :href="shipping.tracking_url"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-500 transition-all hover:text-blue-600 hover:underline"
                >
                    <ExternalLink class="h-3.5 w-3.5" />
                    Realizar seguimiento en courier
                </a>
            </div>
        </div>
    </div>
</template>
