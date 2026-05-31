<script setup lang="ts">
import type { BookOrderShipping } from '@/types/book-order';
import { Truck, MapPin, Clipboard, ExternalLink, Home } from 'lucide-vue-next';

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
    const idx = steps.findIndex(s => s.key === status);
    return idx >= 0 ? idx : 0;
}
</script>

<template>
    <div v-if="shipping" :class="compact ? 'space-y-4' : 'rounded-3xl border border-blue-100/80 bg-blue-50/20 p-6 md:p-8 space-y-8'">
        
        <div class="flex items-center justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Estado de despacho</p>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-[10px] font-semibold text-blue-700 capitalize">
                {{ steps[stepIndex(shipping.status)].label }}
            </span>
        </div>

        <!-- Horizontal Progress Steps -->
        <div class="relative flex items-center justify-between gap-2 max-w-3xl mx-auto pt-2 pb-4">
            <!-- Background Line -->
            <div class="absolute left-[5%] right-[5%] top-[21px] h-0.5 bg-slate-100 dark:bg-slate-800 -z-10"></div>
            <!-- Active Fill Line -->
            <div 
                class="absolute left-[5%] top-[21px] h-0.5 bg-blue-500 -z-10 transition-all duration-500"
                :style="{ width: `${(stepIndex(shipping.status) / (steps.length - 1)) * 90}%` }"
            ></div>

            <div v-for="(step, i) in steps" :key="step.key" class="flex flex-col items-center gap-2 group flex-1">
                <!-- Step Circle -->
                <div
                    class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 border-2 transition-all duration-500 text-[10px] font-bold"
                    :class="[
                        i <= stepIndex(shipping.status) 
                            ? 'bg-blue-500 border-blue-500 text-white shadow-md shadow-blue-500/10' 
                            : 'bg-white border-slate-200 text-slate-400 dark:bg-slate-900 dark:border-slate-800'
                    ]"
                >
                    {{ i + 1 }}
                </div>
                <span
                    class="text-[9px] md:text-[10px] font-black uppercase tracking-wider text-center leading-none mt-1"
                    :class="i <= stepIndex(shipping.status) ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400'"
                >
                    {{ step.label }}
                </span>
            </div>
        </div>

        <!-- Shipping Details Grid -->
        <div v-if="shipping.carrier || shipping.tracking_url || shipping.pickup_location || shipping.student_note" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-4 border-t border-blue-100/50">
            <div v-if="shipping.carrier" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <Truck class="w-4 h-4 text-blue-500" />
                <div>
                    <p class="font-bold text-[8px] uppercase tracking-widest text-slate-400 leading-none mb-0.5">Courier</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ shipping.carrier }}</p>
                </div>
            </div>
            
            <div v-if="shipping.delivery_mode === 'pickup' && shipping.pickup_location" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <MapPin class="w-4 h-4 text-blue-500" />
                <div>
                    <p class="font-bold text-[8px] uppercase tracking-widest text-slate-400 leading-none mb-0.5">Lugar de Recojo</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ shipping.pickup_location }}</p>
                </div>
            </div>

            <div v-if="shipping.delivery_mode === 'home'" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                <Home class="w-4 h-4 text-blue-500" />
                <div>
                    <p class="font-bold text-[8px] uppercase tracking-widest text-slate-400 leading-none mb-0.5">Modalidad</p>
                    <p class="font-bold text-slate-800 dark:text-slate-200">Envío a domicilio</p>
                </div>
            </div>

            <div v-if="shipping.student_note" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400 col-span-full">
                <Clipboard class="w-4 h-4 text-blue-500" />
                <div>
                    <p class="font-bold text-[8px] uppercase tracking-widest text-slate-400 leading-none mb-0.5">Nota de envío</p>
                    <p class="italic text-slate-700 dark:text-slate-300">{{ shipping.student_note }}</p>
                </div>
            </div>

            <div v-if="shipping.tracking_url" class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400 col-span-full pt-1">
                <a
                    :href="shipping.tracking_url"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-500 hover:text-blue-600 hover:underline transition-all"
                >
                    <ExternalLink class="w-3.5 h-3.5" />
                    Realizar seguimiento en courier
                </a>
            </div>
        </div>
    </div>
</template>
