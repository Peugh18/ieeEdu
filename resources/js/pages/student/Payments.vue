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
        <div class="min-h-screen bg-background">
            <div class="p-4 md:p-12 max-w-[1440px] mx-auto space-y-6 md:space-y-12 pb-24">
                
                <!-- Premium Header -->
                <header class="relative overflow-hidden bg-gradient-to-br from-on-background to-[#2D302B] rounded-2xl md:rounded-[3rem] p-6 md:p-16 mb-4 md:mb-12 shadow-2xl shadow-on-background/20">
                    <!-- Decor -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#D4AF37]/5 rounded-full blur-[80px] -ml-32 -mb-32"></div>

                    <div class="relative z-10 space-y-6 max-w-4xl">
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 shadow-inner">
                            <div class="w-2 h-2 rounded-full bg-[#D4AF37] animate-pulse"></div>
                            <span class="text-[10px] font-bold text-white/90 uppercase tracking-[0.25em]">Transacciones</span>
                        </div>
                        
                        <h1 class="text-2xl md:text-6xl font-serif font-bold text-white leading-tight tracking-tight">
                            Historial de <span class="italic text-[#D4AF37]">Pagos</span>
                        </h1>
                        
                        <p class="hidden md:block text-background/70 font-serif text-lg md:text-xl italic max-w-2xl leading-relaxed">
                            Consulte el estado de sus inscripciones, comprobantes adjuntos y el seguimiento del despacho de sus libros físicos.
                        </p>
                    </div>
                </header>

                <div v-if="payments.data.length === 0" class="bg-white rounded-[2.5rem] border border-outline-variant/20 p-20 text-center space-y-6">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto">
                        <CreditCard class="w-10 h-10 text-slate-300" />
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-slate-900">Sin pagos registrados</h3>
                        <p class="text-slate-400 text-sm max-w-sm mx-auto">Aún no has realizado ningún pago. Explora nuestros cursos y comience su formación.</p>
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
