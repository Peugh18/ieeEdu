<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import PaymentsList from '@/components/student/payments/PaymentsList.vue';
import PaymentUploadModal from '@/components/student/payments/PaymentUploadModal.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CreditCard, ArrowLeft } from 'lucide-vue-next';
import type { StudentPayment } from '@/types/student-payment';
import type { PaginatedResponse } from '@/types/pagination';

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
        <div class="min-h-screen bg-slate-50 flex justify-center">
            <div class="w-full max-w-7xl p-4 md:p-12 space-y-8">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <h1 class="text-3xl md:text-5xl font-serif font-bold italic text-slate-900">Mis <span class="text-primary">Pagos</span></h1>
                        <p class="text-slate-500 text-sm font-medium">Seguimiento de tus transacciones y comprobantes.</p>
                    </div>
                </div>

                <div v-if="payments.data.length === 0" class="bg-white rounded-[2.5rem] border border-slate-100 p-20 text-center space-y-6">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto">
                        <CreditCard class="w-10 h-10 text-slate-300" />
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-slate-900">Sin pagos registrados</h3>
                        <p class="text-slate-400 text-sm max-w-sm mx-auto">Aún no has realizado ningún pago. Explora nuestros cursos y comienza tu formación.</p>
                    </div>
                    <Link :href="route('student.explore.courses')" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline">
                        <ArrowLeft class="w-4 h-4" /> Explorar cursos
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
