<script setup lang="ts">
import type { BookOrder } from '@/types/book-order';
import { useForm } from '@inertiajs/vue3';
import AppSelect from '@/components/ui/AppSelect.vue';

const props = defineProps<{
    order: BookOrder;
    shippingStatuses: Record<string, string>;
}>();

const form = useForm({
    shipping_status: props.order.shipping_status,
    shipping_address: props.order.shipping_address ?? '',
    district: props.order.district ?? '',
    province: props.order.province ?? '',
    department: props.order.department ?? '',
    shipping_phone: props.order.shipping_phone ?? '',
    delivery_mode: props.order.delivery_mode ?? '',
    pickup_location: props.order.pickup_location ?? '',
    carrier: props.order.carrier ?? '',
    tracking_url: props.order.tracking_url ?? '',
    tracking_code: props.order.tracking_code ?? '',
    student_note: props.order.student_note ?? '',
    admin_notes: props.order.admin_notes ?? '',
});

function submit() {
    form.patch(route('admin.book-orders.update', { bookOrder: props.order.id }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4 rounded-3xl border border-outline-variant/20 bg-surface-container-low p-5">
        <div class="flex items-center justify-between gap-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-primary">Gestión de envío — Perú</p>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-on-primary transition-opacity hover:opacity-90 disabled:opacity-50"
            >
                Guardar envío
            </button>
        </div>

        <div class="grid gap-3 grid-cols-1 md:grid-cols-6">
            <!-- Row 1 -->
            <div class="space-y-1 md:col-span-4">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Dirección completa</label>
                <input
                    v-model="form.shipping_address"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary"
                    placeholder="Calle, N°, urbanización, referencia..."
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Teléfono contacto</label>
                <input 
                    v-model="form.shipping_phone" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                />
            </div>

            <!-- Row 2 -->
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Departamento</label>
                <input 
                    v-model="form.department" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Provincia</label>
                <input 
                    v-model="form.province" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Distrito</label>
                <input 
                    v-model="form.district" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                />
            </div>

            <!-- Row 3 -->
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Estado del envío</label>
                <AppSelect
                    v-model="form.shipping_status"
                    :options="Object.entries(shippingStatuses).map(([value, label]) => ({ value, label }))"
                    class="border-outline-variant/30 bg-surface-container-lowest text-on-surface text-xs font-semibold py-1.5"
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Modalidad</label>
                <AppSelect
                    v-model="form.delivery_mode"
                    :options="[
                        { value: '', label: '— Seleccionar —' },
                        { value: 'home', label: 'Envío a domicilio' },
                        { value: 'pickup', label: 'Recojo en agencia' }
                    ]"
                    class="border-outline-variant/30 bg-surface-container-lowest text-on-surface text-xs py-1.5"
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Courier</label>
                <input 
                    v-model="form.carrier" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                    placeholder="Olva, Shalom, etc." 
                />
            </div>

            <!-- Row 4 -->
            <div class="space-y-1 md:col-span-4">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Link seguimiento courier</label>
                <input
                    v-model="form.tracking_url"
                    type="url"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary"
                    placeholder="https://..."
                />
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Clave / Código recojo</label>
                <input
                    v-model="form.tracking_code"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary"
                    placeholder="Ej. 123456"
                />
            </div>

            <!-- Row 5 (Punto de Recojo) -->
            <div v-if="form.delivery_mode === 'pickup'" class="space-y-1 md:col-span-6">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Punto de recojo</label>
                <input
                    v-model="form.pickup_location"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary"
                    placeholder="Dirección agencia, horario..."
                />
            </div>

            <!-- Row 6 -->
            <div class="space-y-1 md:col-span-3">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Nota visible al alumno</label>
                <input
                    v-model="form.student_note"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary"
                    placeholder="Ej: Llegará en 3-5 días hábiles"
                />
            </div>
            <div class="space-y-1 md:col-span-3">
                <label class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">Notas internas (admin)</label>
                <input 
                    v-model="form.admin_notes" 
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-1.5 text-xs text-on-surface outline-none focus:border-primary" 
                    placeholder="Detalles administrativos..."
                />
            </div>
        </div>
    </form>
</template>
