<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { BookOrder } from '@/types/book-order';

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
    <form @submit.prevent="submit" class="rounded-3xl border border-blue-100 bg-blue-50/30 p-6 space-y-5">
        <div class="flex items-center justify-between gap-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-700">Gestión de envío — Perú</p>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold disabled:opacity-50">Guardar envío</button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2 space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Dirección completa</label>
                <textarea v-model="form.shipping_address" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Calle, N°, urbanización, referencia..." />
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Distrito</label>
                <input v-model="form.district" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Provincia</label>
                <input v-model="form.province" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Departamento</label>
                <input v-model="form.department" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Teléfono contacto</label>
                <input v-model="form.shipping_phone" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Estado del envío</label>
                <select v-model="form.shipping_status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium">
                    <option v-for="(label, key) in shippingStatuses" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Modalidad</label>
                <select v-model="form.delivery_mode" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">— Seleccionar —</option>
                    <option value="home">Envío a domicilio</option>
                    <option value="pickup">Recojo en agencia</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Courier</label>
                <input v-model="form.carrier" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Olva, Shalom, etc." />
            </div>
            <div class="sm:col-span-2 space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Link seguimiento courier</label>
                <input v-model="form.tracking_url" type="url" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="https://..." />
            </div>
            <div v-if="form.delivery_mode === 'pickup'" class="sm:col-span-2 space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Punto de recojo</label>
                <textarea v-model="form.pickup_location" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Dirección agencia, horario..." />
            </div>
            <div class="sm:col-span-2 space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Nota visible al alumno</label>
                <textarea v-model="form.student_note" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Ej: Llegará en 3-5 días hábiles" />
            </div>
            <div class="sm:col-span-2 space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-500">Notas internas (admin)</label>
                <textarea v-model="form.admin_notes" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            </div>
        </div>
    </form>
</template>
