<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CreditCard, Calendar, ArrowRight, Upload, Package } from 'lucide-vue-next';
import PaymentStatusBadge from './PaymentStatusBadge.vue';
import BookShippingTracker from './BookShippingTracker.vue';
import type { StudentPayment } from '@/types/student-payment';

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
        label: o.shipping_status === 'awaiting_address' ? 'Confirmando envío'
            : o.shipping_status === 'preparing' ? 'Preparando pedido'
            : o.shipping_status === 'shipped' ? 'En camino'
            : o.shipping_status === 'delivered' ? 'Entregado' : o.shipping_status,
        tracking_url: o.tracking_url,
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
        <div v-for="p in payments" :key="p.id" class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" :class="p.book ? 'bg-blue-50 text-blue-600' : p.subscription_type ? 'bg-primary/10 text-primary' : 'bg-slate-50 text-slate-400'">
                            <Package v-if="p.book" class="w-5 h-5" />
                            <CreditCard v-else class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-900">{{ productLabel(p) }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">
                                {{ p.book ? 'Libro físico' : p.course ? 'Curso' : p.subscription_type ? 'Membresía' : 'Pago' }} · #{{ p.id }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><Calendar class="w-3 h-3" /> {{ formatDate(p.created_at) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="text-xl font-black text-slate-900 tabular-nums">{{ formatMoney(p.amount) }}</p>
                        <PaymentStatusBadge :status="p.status" />
                    </div>
                </div>

                <div v-if="p.book && p.status === 'aprobado'" class="mb-4">
                    <BookShippingTracker :shipping="shippingFromOrder(p)" />
                    <p v-if="p.book_order?.shipping_address" class="text-xs text-slate-500 mt-3 pl-1">
                        <span class="font-bold text-slate-700">Dirección:</span>
                        {{ p.book_order.shipping_address }}<template v-if="p.book_order.district">, {{ p.book_order.district }}</template><template v-if="p.book_order.province">, {{ p.book_order.province }}</template><template v-if="p.book_order.department"> — {{ p.book_order.department }}</template>
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-50">
                    <Link v-if="p.status === 'aprobado' && p.course" :href="route('student.classroom', { course: p.course.slug })" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"><ArrowRight class="w-3.5 h-3.5" /> Ir al curso</Link>
                    <Link v-else-if="p.status === 'aprobado' && p.subscription_type" :href="route('student.courses.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"><ArrowRight class="w-3.5 h-3.5" /> Ver cursos</Link>
                    <button v-else-if="p.status === 'pendiente'" @click="emit('upload', p)" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-primary px-4 py-2 rounded-xl hover:bg-primary/90 transition-colors"><Upload class="w-3.5 h-3.5" /> Subir comprobante</button>
                    <span v-else-if="p.status === 'en_revision'" class="text-xs text-amber-600 font-bold">Comprobante en revisión</span>
                </div>
            </div>
        </div>
    </div>
</template>
