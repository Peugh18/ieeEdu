<script setup lang="ts">
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import BookOrderShippingForm from '@/components/admin/book-orders/BookOrderShippingForm.vue';
import PaymentComprobanteCard from '@/components/admin/payments/PaymentComprobanteCard.vue';
import PaymentShowActions from '@/components/admin/payments/PaymentShowActions.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import type { BookOrder } from '@/types/book-order';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { BookOpen, CheckCircle2, Clock, Crown, DollarSign, RefreshCw, User2, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

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

function approve() {
    router.patch(route('admin.payments.approve', { payment: props.payment.id }));
}
function reject() {
    if (!confirm('¿Rechazar este pago?')) return;
    router.patch(route('admin.payments.reject', { payment: props.payment.id }));
}

function revert() {
    if (!confirm('¿Revertir esta aprobación? El estudiante perderá el acceso y el pago volverá a revisión.')) return;
    router.patch(route('admin.payments.revert', { payment: props.payment.id }));
}

const statusCfg: Record<string, { label: string; cls: string; bg: string; icon: typeof Clock }> = {
    pendiente: { label: 'Pendiente', cls: 'text-slate-600', bg: 'bg-slate-100', icon: Clock },
    en_revision: { label: 'En revisión', cls: 'text-amber-700', bg: 'bg-amber-50', icon: RefreshCw },
    aprobado: { label: 'Aprobado', cls: 'text-emerald-700', bg: 'bg-emerald-50', icon: CheckCircle2 },
    rechazado: { label: 'Rechazado', cls: 'text-red-600', bg: 'bg-red-50', icon: XCircle },
};

const cfg = computed(
    () => statusCfg[props.payment.status] ?? { label: props.payment.status, cls: 'text-slate-600', bg: 'bg-slate-100', icon: Clock },
);

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function formatMoney(n: number) {
    return 'S/ ' + Number(n).toFixed(2);
}
</script>

<template>
    <Head :title="`Pago #${props.payment.id} - Admin IEE`" />

    <AppLayout>
        <div class="mx-auto min-h-screen max-w-7xl px-4 py-8">
            <AdminPageHeader
                :title="`Pago #${props.payment.id}`"
                :subtitle="`Registrado el ${formatDate(props.payment.created_at)}`"
                back-link="admin.payments.index"
                back-label="Volver a comprobantes"
                compact
            >
                <template #actions>
                    <div class="inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold" :class="[cfg.bg, cfg.cls]">
                        <component :is="cfg.icon" class="h-5 w-5" />
                        {{ cfg.label }}
                    </div>
                </template>
            </AdminPageHeader>

            <div class="grid gap-6 lg:grid-cols-3 items-start">
                <!-- Left: Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Top details row (3 columns) -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <!-- User card -->
                        <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-5 shadow-sm flex flex-col justify-between">
                            <div>
                                <p class="mb-3 flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">
                                    <User2 class="h-3.5 w-3.5" /> Estudiante
                                </p>
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-xs font-bold text-primary">
                                        {{
                                            props.payment.user.name
                                                .split(' ')
                                                .slice(0, 2)
                                                .map((w: string) => w[0])
                                                .join('')
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-bold text-on-surface" :title="props.payment.user.name">{{ props.payment.user.name }}</p>
                                        <p class="truncate text-[10px] text-on-surface-variant" :title="props.payment.user.email">{{ props.payment.user.email }}</p>
                                        <p v-if="props.payment.user.telefono" class="mt-0.5 text-[10px] font-medium text-on-surface-variant/75">Telf: {{ props.payment.user.telefono }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 border-t border-outline-variant/10 pt-2 text-right">
                                <Link
                                    :href="route('admin.users.show', { user: props.payment.user.id })"
                                    class="text-[10px] font-black uppercase tracking-wider text-primary hover:underline"
                                >
                                    Ver perfil →
                                </Link>
                            </div>
                        </div>

                        <!-- Producto -->
                        <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-5 shadow-sm flex flex-col justify-between">
                            <div>
                                <template v-if="props.payment.subscription_type">
                                    <p class="mb-3 flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">
                                        <Crown class="h-3.5 w-3.5 text-primary" /> Membresía Premium
                                    </p>
                                    <p class="text-xs font-bold capitalize text-on-surface">Plan {{ props.payment.subscription_type }}</p>
                                </template>
                                <template v-else-if="props.payment.book">
                                    <p class="mb-3 flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">
                                        <BookOpen class="h-3.5 w-3.5" /> Libro físico
                                    </p>
                                    <p class="line-clamp-2 text-xs font-bold text-on-surface" :title="props.payment.book.title">{{ props.payment.book.title }}</p>
                                    <span class="mt-1.5 inline-block rounded-full border border-primary/20 bg-primary/10 px-2 py-0.5 text-[9px] font-bold text-primary"
                                        >Envío en Perú</span
                                    >
                                </template>
                                <template v-else-if="props.payment.course">
                                    <p class="mb-3 flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">
                                        <BookOpen class="h-3.5 w-3.5" /> Curso
                                    </p>
                                    <p class="line-clamp-2 text-xs font-bold text-on-surface" :title="props.payment.course.title">{{ props.payment.course.title }}</p>
                                    <span
                                        class="mt-1.5 inline-block rounded-full border border-outline-variant/20 px-2 py-0.5 text-[9px] font-bold text-on-surface-variant"
                                        >{{ props.payment.course.type }}</span
                                    >
                                </template>
                            </div>
                        </div>

                        <!-- Payment details -->
                        <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-5 shadow-sm flex flex-col justify-between">
                            <div>
                                <p class="mb-3 flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant">
                                    <DollarSign class="h-3.5 w-3.5" /> Inversión
                                </p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xs font-bold text-on-surface">Total:</span>
                                    <span class="text-xl font-black text-primary">{{ formatMoney(props.payment.amount) }}</span>
                                </div>
                                <p class="mt-1 text-[10px] font-medium text-on-surface-variant">Yape / Transferencia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Form (Only if book order exists) -->
                    <BookOrderShippingForm v-if="props.payment.book_order" :order="props.payment.book_order" :shipping-statuses="shippingStatuses" />
                    <div
                        v-else-if="props.payment.book && props.payment.status === 'aprobado'"
                        class="rounded-2xl border border-dashed border-amber-500/30 bg-amber-500/10 p-4 text-xs font-bold text-amber-600 dark:text-amber-400"
                    >
                        Pedido de envío pendiente de crear. Recarga la página o contacta soporte.
                    </div>
                    <div
                        v-else-if="props.payment.book && props.payment.status !== 'aprobado'"
                        class="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low p-4 text-xs font-bold text-on-surface-variant"
                    >
                        Aprueba el pago para habilitar la gestión de envío y dirección.
                    </div>
                </div>

                <!-- Right: Comprobante + Actions (Sticky) -->
                <div class="lg:sticky lg:top-6 lg:self-start space-y-6 w-full">
                    <PaymentShowActions 
                        :status="props.payment.status" 
                        :product-type="
                            props.payment.course ? 'course' : 
                            props.payment.book ? 'book' : 
                            props.payment.subscription_type ? 'membership' : null
                        " 
                        @approve="approve" 
                        @reject="reject" 
                        @revert="revert" 
                    />
                    <PaymentComprobanteCard :comprobante="props.payment.comprobante" />
                </div>
            </div>
        </div>

        <AdminFlashToast :show="!!flash.success" variant="success" :message="flash.success ?? ''" />
    </AppLayout>
</template>
