<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { CheckCircle2, ArrowRight, CreditCard } from 'lucide-vue-next';
import type { SharedData } from '@/types';

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Mis Pagos', href: route('student.payments.index') },
    { title: 'Estado de Membresía', href: '#' },
];

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <Head title="Estado de Membresía - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-slate-50 flex justify-center">
            <div class="w-full max-w-2xl p-4 md:p-12 space-y-8">

                <!-- Flash success -->
                <div v-if="flash.success" class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 flex items-center gap-3">
                    <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
                    <p class="text-sm font-semibold text-emerald-700">{{ flash.success }}</p>
                </div>

                <!-- Status Card -->
                <div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 md:p-16 text-center space-y-8 shadow-sm">
                    <!-- Icon -->
                    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto">
                        <CheckCircle2 class="w-12 h-12 text-primary" />
                    </div>

                    <!-- Content -->
                    <div class="space-y-3">
                        <h1 class="text-3xl md:text-4xl font-serif font-bold italic text-slate-900">
                            ¡Comprobante <span class="text-primary">recibido!</span>
                        </h1>
                        <p class="text-slate-500 text-base max-w-md mx-auto leading-relaxed">
                            Tu comprobante de pago ha sido enviado correctamente. Nuestro equipo lo revisará en menos de
                            <strong class="text-slate-700">24 horas</strong> y te notificaremos por correo al confirmar tu membresía.
                        </p>
                    </div>

                    <!-- Status Badge -->
                    <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-2.5 rounded-full text-sm font-bold uppercase tracking-widest">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        En Revisión
                    </div>

                    <!-- What's next -->
                    <div class="bg-slate-50 rounded-2xl p-6 text-left space-y-3">
                        <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">¿Qué sigue?</p>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-primary/15 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="text-primary text-xs font-black">1</span>
                            </div>
                            <p class="text-sm text-slate-600">Nuestro equipo verifica tu pago (máx. 24 h)</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-primary/15 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="text-primary text-xs font-black">2</span>
                            </div>
                            <p class="text-sm text-slate-600">Recibirás un correo de confirmación con tu acceso activo</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-primary/15 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="text-primary text-xs font-black">3</span>
                            </div>
                            <p class="text-sm text-slate-600">¡Accede a todos los cursos sin restricción!</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <Link
                            :href="route('student.payments.index')"
                            class="inline-flex items-center gap-2 bg-primary text-white font-bold text-xs uppercase tracking-widest px-8 py-3.5 rounded-xl hover:bg-primary/90 transition-colors shadow-lg"
                        >
                            <CreditCard class="w-4 h-4" />
                            Ver mis pagos
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary transition-colors"
                        >
                            Ir al dashboard
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

            </div>
        </div>
        <div class="h-16 lg:hidden"></div>
        <BottomNav active="payments" />
    </AppLayout>
</template>
