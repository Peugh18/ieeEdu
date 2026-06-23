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
            <div class="mx-auto max-w-[1440px] space-y-6 p-4 pb-24 md:space-y-12 md:p-12">
                <!-- Premium Header -->
                <header
                    class="relative mb-4 overflow-hidden rounded-2xl bg-gradient-to-br from-on-background to-[#2D302B] p-6 shadow-2xl shadow-on-background/20 md:mb-12 md:rounded-[3rem] md:p-16"
                >
                    <!-- Decor -->
                    <div class="absolute right-0 top-0 -mr-48 -mt-48 h-96 w-96 rounded-full bg-primary/10 blur-[100px]"></div>
                    <div class="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-[#D4AF37]/5 blur-[80px]"></div>

                    <div class="relative z-10 max-w-4xl space-y-6">
                        <div
                            class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 shadow-inner backdrop-blur-xl"
                        >
                            <div class="h-2 w-2 animate-pulse rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-white/90">Transacciones</span>
                        </div>

                        <h1 class="font-serif text-2xl font-bold leading-tight tracking-tight text-white md:text-6xl">
                            Historial de <span class="italic text-[#D4AF37]">Pagos</span>
                        </h1>

                        <p class="hidden max-w-2xl font-serif text-lg italic leading-relaxed text-background/70 md:block md:text-xl">
                            Consulte el estado de sus inscripciones, comprobantes adjuntos y el seguimiento del despacho de sus libros físicos.
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
