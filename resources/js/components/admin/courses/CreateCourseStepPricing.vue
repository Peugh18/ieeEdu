<script setup lang="ts">
import type { CreateCourseForm } from '@/composables/admin/courses/useCreateCourse';
import type { InertiaForm } from '@inertiajs/vue3';
import { PartyPopper } from 'lucide-vue-next';

defineProps<{
    form: InertiaForm<CreateCourseForm>;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Pricing logic conditional -->
        <template v-if="form.type !== 'masterclass'">
            <div class="space-y-3">
                <label class="ml-1 block font-sans text-[14px] font-bold text-on-surface">Inversión Base (S/)</label>
                <input
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-6 py-5 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20"
                    placeholder="0"
                />
                <p v-if="form.errors.price" class="ml-1 mt-1 text-[13px] font-bold text-red-600">{{ form.errors.price }}</p>
            </div>

            <div class="flex flex-col justify-center rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
                <label class="mb-5 flex w-max cursor-pointer items-center gap-3 font-sans text-[14px] font-bold text-on-surface">
                    <input type="checkbox" v-model="form.discount_enabled" class="h-5 w-5 rounded accent-primary focus:ring-primary" />
                    <span>Promo Descuento</span>
                </label>
                <div class="flex items-center gap-5">
                    <div class="w-24">
                        <input
                            v-model.number="form.discount"
                            :disabled="!form.discount_enabled"
                            type="number"
                            min="0"
                            max="100"
                            class="w-full rounded-[1.5rem] border-transparent bg-surface-container-highest px-5 py-4 font-sans text-[15px] text-on-surface outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/20 disabled:bg-surface-container-low disabled:opacity-50"
                            placeholder="%"
                        />
                    </div>
                    <div class="flex-1">
                        <span class="mb-1 block text-[12px] font-bold uppercase tracking-widest text-on-surface-variant">Precio Final</span>
                        <span class="font-serif text-2xl font-bold text-primary">S/ {{ form.sale_price }}</span>
                    </div>
                </div>
                <p v-if="form.errors.discount" class="ml-1 mt-2 text-[13px] font-bold text-red-600">{{ form.errors.discount }}</p>
            </div>
        </template>
        <template v-else>
            <div
                class="flex items-center gap-5 rounded-[2rem] border border-transparent bg-surface-container-highest px-8 py-6 shadow-sm md:col-span-2"
            >
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-white bg-surface-container-low">
                    <PartyPopper class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <h4 class="mb-1 text-[16px] font-bold text-on-surface">Asistencia Gratuita Configurada</h4>
                    <p class="text-[14px] font-medium leading-relaxed text-on-surface-variant">
                        Las sesiones Masterclass no requieren configuración comercial ni fijación de precios.
                    </p>
                </div>
            </div>
        </template>
    </div>
</template>
