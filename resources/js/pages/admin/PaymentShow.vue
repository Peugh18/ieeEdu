<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import PaymentShowActions from '@/components/admin/payments/PaymentShowActions.vue';
import PaymentComprobanteCard from '@/components/admin/payments/PaymentComprobanteCard.vue';
import BookOrderShippingForm from '@/components/admin/book-orders/BookOrderShippingForm.vue';
import type { BookOrder } from '@/types/book-order';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import type { SharedData } from '@/types';
import { computed } from 'vue';
import {
    CheckCircle2, XCircle, Clock, RefreshCw,
    User2, BookOpen, DollarSign, Crown,
} from 'lucide-vue-next';

const props = defineProps<{
    payment: {
        id: number;
        status: string;
        amount: number;
        comprobante: string | null;
        created_at: string;
        updated_at: string;
        user: { id: number; name: string; email: string; telefono: string | null };
        course: { id: number; title: string; type: string } | null;
        book: { id: number; title: string; price?: number | string } | null;
        book_order: BookOrder | null;
        subscription_type: string | null;
    };
    shippingStatuses: Record<string, string>;
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

function approve() { router.patch(route('admin.payments.approve', { payment: props.payment.id })); }
function reject() {
    if (!confirm('¿Rechazar este pago?')) return;
    router.patch(route('admin.payments.reject', { payment: props.payment.id }));
}

function revert() {
    if (!confirm('¿Revertir esta aprobación? El estudiante perderá el acceso y el pago volverá a revisión.')) return;
    router.patch(route('admin.payments.revert', { payment: props.payment.id }));
}

const statusCfg: Record<string, { label: string; cls: string; bg: string; icon: typeof Clock }> = {
    pendiente:   { label: 'Pendiente',   cls: 'text-slate-600',    bg: 'bg-slate-100',    icon: Clock },
    en_revision: { label: 'En revisión', cls: 'text-amber-700',    bg: 'bg-amber-50',     icon: RefreshCw },
    aprobado:    { label: 'Aprobado',    cls: 'text-emerald-700',  bg: 'bg-emerald-50',   icon: CheckCircle2 },
    rechazado:   { label: 'Rechazado',   cls: 'text-red-600',      bg: 'bg-red-50',       icon: XCircle },
};

const cfg = computed(() => statusCfg[props.payment.status] ?? { label: props.payment.status, cls: 'text-slate-600', bg: 'bg-slate-100', icon: Clock });

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function formatMoney(n: number) { return 'S/ ' + Number(n).toFixed(2); }
</script>

<template>
    <Head :title="`Pago #${props.payment.id} - Admin IEE`" />

    <AppLayout>
        <div class="px-4 py-8 max-w-7xl mx-auto min-h-screen">
            <AdminPageHeader
                :title="`Pago #${props.payment.id}`"
                :subtitle="`Registrado el ${formatDate(props.payment.created_at)}`"
                back-link="admin.payments.index"
                back-label="Volver a comprobantes"
                compact
            >
                <template #actions>
                    <div
                        class="inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold"
                        :class="[cfg.bg, cfg.cls]"
                    >
                        <component :is="cfg.icon" class="h-5 w-5" />
                        {{ cfg.label }}
                    </div>
                </template>
            </AdminPageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left: Details -->
                <div class="space-y-6 lg:col-span-2">

                    <!-- User card -->
                    <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
                        <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            <User2 class="h-3.5 w-3.5" /> Estudiante
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-sm font-bold text-blue-700">
                                {{ props.payment.user.name.split(' ').slice(0,2).map((w: string) => w[0]).join('').toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-bold text-on-surface">{{ props.payment.user.name }}</p>
                                <p class="text-sm text-on-surface-variant">{{ props.payment.user.email }}</p>
                                <p v-if="props.payment.user.telefono" class="text-xs text-on-surface-variant/70">{{ props.payment.user.telefono }}</p>
                            </div>
                            <Link :href="route('admin.users.show', { user: props.payment.user.id })" class="ml-auto text-xs font-bold text-primary hover:underline">
                                Ver perfil →
                            </Link>
                        </div>
                    </div>

                    <!-- Producto -->
                    <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
                        <template v-if="props.payment.subscription_type">
                            <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                <Crown class="h-3.5 w-3.5 text-primary" /> Membresía Premium
                            </p>
                            <p class="font-bold text-on-surface capitalize">Plan {{ props.payment.subscription_type }}</p>
                        </template>
                        <template v-else-if="props.payment.book">
                            <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                <BookOpen class="h-3.5 w-3.5" /> Libro físico
                            </p>
                            <p class="font-semibold text-on-surface">{{ props.payment.book.title }}</p>
                            <span class="text-xs text-blue-600 border border-blue-200 rounded-full px-2 py-0.5 mt-2 inline-block bg-blue-50">Envío en Perú</span>
                        </template>
                        <template v-else-if="props.payment.course">
                            <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                <BookOpen class="h-3.5 w-3.5" /> Curso
                            </p>
                            <p class="font-semibold text-on-surface">{{ props.payment.course.title }}</p>
                            <span class="text-xs text-on-surface-variant border border-outline-variant/20 rounded-full px-2 py-0.5 mt-1 inline-block">{{ props.payment.course.type }}</span>
                        </template>
                    </div>

                    <BookOrderShippingForm
                        v-if="props.payment.book_order"
                        :order="props.payment.book_order"
                        :shipping-statuses="shippingStatuses"
                    />
                    <div v-else-if="props.payment.book && props.payment.status === 'aprobado'" class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/50 p-4 text-sm text-amber-800">
                        Pedido de envío pendiente de crear. Recarga la página o contacta soporte.
                    </div>
                    <div v-else-if="props.payment.book && props.payment.status !== 'aprobado'" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        Aprueba el pago para habilitar la gestión de envío y dirección.
                    </div>

                    <!-- Payment details -->
                    <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
                        <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            <DollarSign class="h-3.5 w-3.5" /> Detalles del pago
                        </p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-on-surface-variant">Monto</p>
                                <p class="mt-1 text-3xl font-bold text-primary">{{ formatMoney(props.payment.amount) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant">Método</p>
                                <p class="mt-1 font-semibold text-on-surface">Yape / Transferencia (manual)</p>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant">Fecha de registro</p>
                                <p class="mt-1 text-sm text-on-surface">{{ formatDate(props.payment.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant">Última actualización</p>
                                <p class="mt-1 text-sm text-on-surface">{{ formatDate(props.payment.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Comprobante + Actions -->
                <div class="space-y-6">
                    <PaymentShowActions :status="props.payment.status" @approve="approve" @reject="reject" @revert="revert" />
                    <PaymentComprobanteCard :comprobante="props.payment.comprobante" />
                </div>
            </div>
        </div>

        <AdminFlashToast
            :show="!!flash.success"
            variant="success"
            :message="flash.success ?? ''"
        />
    </AppLayout>
</template>
