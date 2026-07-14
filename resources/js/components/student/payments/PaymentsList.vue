<script setup lang="ts">
import type { StudentPayment } from '@/types/student-payment';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Calendar, CreditCard, Package, Upload } from 'lucide-vue-next';
import BookShippingTracker from './BookShippingTracker.vue';
import PaymentStatusBadge from './PaymentStatusBadge.vue';

defineProps<{
    payments: StudentPayment[];
}>();

const emit = defineEmits<{
    (e: 'upload', payment: StudentPayment): void;
}>();

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatMoney(n: number) {
    return 'S/ ' + Number(n).toFixed(2);
}

function productLabel(p: StudentPayment) {
    if (p.subscription_type) return 'Membresía ' + p.subscription_type.charAt(0).toUpperCase() + p.subscription_type.slice(1);
    if (p.book) return p.book.title;
    if (p.course) return p.course.title;
    return 'Pago Directo';
}

function shippingFromOrder(p: StudentPayment) {
    const o = p.book_order;
    if (!o) return null;
    return {
        status: o.shipping_status,
        label:
            o.shipping_status === 'awaiting_address'
                ? 'Confirmando envío'
                : o.shipping_status === 'preparing'
                  ? 'Preparando pedido'
                  : o.shipping_status === 'shipped'
                    ? 'En camino'
                    : o.shipping_status === 'delivered'
                      ? 'Entregado'
                      : o.shipping_status,
        tracking_url: o.tracking_url,
        tracking_code: o.tracking_code,
        delivery_mode: o.delivery_mode,
        pickup_location: o.pickup_location,
        student_note: o.student_note,
        carrier: o.carrier,
        shipping_address: o.shipping_address,
        district: o.district,
        province: o.province,
        department: o.department,
    };
}
</script>

<template>
    <div class="space-y-4">
        <div v-for="p in payments" :key="p.id" class="overflow-hidden rounded-3xl border border-outline-variant/20 bg-surface-container-lowest shadow-sm">
            <div class="p-6 md:p-8">
                <div class="mb-4 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                            :class="
                                p.book
                                    ? 'bg-blue-50 text-blue-600'
                                    : p.subscription_type
                                      ? 'bg-primary/10 text-primary'
                                      : 'bg-slate-50 text-slate-400'
                            "
                        >
                            <Package v-if="p.book" class="h-5 w-5" />
                            <CreditCard v-else class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-900">{{ productLabel(p) }}</p>
                            <p class="mt-0.5 text-[10px] uppercase tracking-widest text-slate-400">
                                {{ p.book ? 'Libro físico' : p.course ? 'Curso' : p.subscription_type ? 'Membresía' : 'Pago' }} · #{{ p.id }}
                            </p>
                            <p class="mt-1 flex items-center gap-1 text-xs text-slate-500">
                                <Calendar class="h-3 w-3" /> {{ formatDate(p.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="text-xl font-black tabular-nums text-slate-900">{{ formatMoney(p.amount) }}</p>
                        <PaymentStatusBadge :status="p.status" />
                    </div>
                </div>

                <div v-if="p.book && p.status === 'aprobado'" class="mb-4">
                    <BookShippingTracker :shipping="shippingFromOrder(p)" />
                    <p v-if="p.book_order?.shipping_address" class="mt-3 pl-1 text-xs text-slate-500">
                        <span class="font-bold text-slate-700">Dirección:</span>
                        {{ p.book_order.shipping_address }}<template v-if="p.book_order.district">, {{ p.book_order.district }}</template
                        ><template v-if="p.book_order.province">, {{ p.book_order.province }}</template
                        ><template v-if="p.book_order.department"> — {{ p.book_order.department }}</template>
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-slate-50 pt-2">
                    <Link
                        v-if="p.status === 'aprobado' && p.course"
                        :href="route('student.classroom', { course: p.course.slug })"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"
                        ><ArrowRight class="h-3.5 w-3.5" /> Ir al curso</Link
                    >
                    <Link
                        v-else-if="p.status === 'aprobado' && p.subscription_type"
                        :href="route('student.courses.index')"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"
                        ><ArrowRight class="h-3.5 w-3.5" /> Ver cursos</Link
                    >
                    <button
                        v-else-if="p.status === 'pendiente'"
                        @click="emit('upload', p)"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-primary/90"
                    >
                        <Upload class="h-3.5 w-3.5" /> Subir comprobante
                    </button>
                    <span v-else-if="p.status === 'en_revision'" class="text-xs font-bold text-amber-600">Comprobante en revisión</span>
                    <div v-else-if="p.status === 'rechazado'" class="flex flex-col gap-2 pt-1">
                        <p class="text-xs font-semibold text-rose-600">El comprobante fue rechazado por el administrador. Por favor, sube uno válido.</p>
                        <button
                            @click="emit('upload', p)"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-rose-700 w-fit"
                        >
                            <Upload class="h-3.5 w-3.5" /> Corregir comprobante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
