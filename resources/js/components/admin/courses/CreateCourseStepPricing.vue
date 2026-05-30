<script setup lang="ts">
import { PartyPopper } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';

defineProps<{
    form: InertiaForm<CreateCourseForm>;
}>();
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Pricing logic conditional -->
        <template v-if="form.type !== 'masterclass'">
            <div class="space-y-3">
                <label class="block text-[14px] font-bold text-on-surface font-sans ml-1">Inversión Base (S/)</label>
                <input v-model.number="form.price" type="number" min="0" class="w-full rounded-[1.5rem] bg-surface-container-highest px-6 py-5 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant" placeholder="0" />
                <p v-if="form.errors.price" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.price }}</p>
            </div>
            
            <div class="rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm flex flex-col justify-center">
                <label class="flex items-center gap-3 text-[14px] font-bold text-on-surface font-sans mb-5 cursor-pointer w-max">
                    <input type="checkbox" v-model="form.discount_enabled" class="accent-primary w-5 h-5 rounded focus:ring-primary" />
                    <span>Promo Descuento</span>
                </label>
                <div class="flex items-center gap-5">
                    <div class="w-24">
                        <input v-model.number="form.discount" :disabled="!form.discount_enabled" type="number" min="0" max="100" class="w-full rounded-[1.5rem] bg-surface-container-highest px-5 py-4 text-[15px] font-sans text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none border-transparent placeholder:text-outline-variant disabled:opacity-50 disabled:bg-surface-container-low" placeholder="%" />
                    </div>
                    <div class="flex-1">
                        <span class="block text-[12px] text-on-surface-variant font-bold uppercase tracking-widest mb-1">Precio Final</span>
                        <span class="text-2xl font-serif font-bold text-primary">S/ {{ form.sale_price }}</span>
                    </div>
                </div>
                <p v-if="form.errors.discount" class="ml-1 mt-2 text-[13px] font-bold text-red-600">{{ form.errors.discount }}</p>
            </div>
        </template>
        <template v-else>
            <div class="md:col-span-2 rounded-[2rem] bg-surface-container-highest px-8 py-6 flex items-center gap-5 shadow-sm border border-transparent">
                <div class="w-14 h-14 rounded-full bg-surface-container-low border border-white flex items-center justify-center shrink-0">
                    <PartyPopper class="text-primary w-6 h-6" />
                </div>
                <div>
                    <h4 class="font-bold text-[16px] text-on-surface mb-1">Asistencia Gratuita Configurada</h4>
                    <p class="text-[14px] text-on-surface-variant font-medium leading-relaxed">Las sesiones Masterclass no requieren configuración comercial ni fijación de precios.</p>
                </div>
            </div>
        </template>
    </div>
</template>
