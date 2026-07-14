<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import PaymentsList from '@/components/student/payments/PaymentsList.vue';
import PaymentUploadModal from '@/components/student/payments/PaymentUploadModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginatedResponse } from '@/types/pagination';
import type { StudentPayment } from '@/types/student-payment';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    payments: PaginatedResponse<StudentPayment>;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Mis Pagos', href: route('student.payments.index') },
];

const showModal = ref(false);
const selectedPayment = ref<StudentPayment | null>(null);

function openUpload(payment: StudentPayment) {
    selectedPayment.value = payment;
    showModal.value = true;
}
</script>

<template>
    <Head title="Mis Pagos - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 md:space-y-8 md:px-8 md:py-8 pb-24">
                <!-- Clean Modern Header -->
                <header class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-start">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                <CreditCard class="h-4 w-4 text-primary" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Transacciones</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-background md:text-3xl">Historial de Pagos</h1>
                        <p class="mt-1 text-sm text-on-surface-variant/70">
                            Consulta el estado de tus inscripciones, comprobantes adjuntos y seguimiento de libros.
                        </p>
                    </div>
                </header>

                <div v-if="payments.data.length === 0" class="space-y-6 rounded-[2.5rem] border border-outline-variant/20 bg-white p-20 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-50">
                        <CreditCard class="h-10 w-10 text-slate-300" />
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-slate-900">Sin pagos registrados</h3>
                        <p class="mx-auto max-w-sm text-sm text-slate-400">
                            Aún no has realizado ningún pago. Explora nuestros cursos y comience su formación.
                        </p>
                    </div>
                    <Link
                        :href="route('student.explore.courses')"
                        class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline"
                    >
                        <ArrowLeft class="h-4 w-4" /> Explorar cursos
                    </Link>
                </div>

                <PaymentsList v-else :payments="payments.data" @upload="openUpload" />
            </div>
        </div>
        <div class="h-16 lg:hidden"></div>
        <BottomNav active="payments" />

        <PaymentUploadModal :show="showModal" :payment-id="selectedPayment?.id ?? null" @close="showModal = false" />
    </AppLayout>
</template>
