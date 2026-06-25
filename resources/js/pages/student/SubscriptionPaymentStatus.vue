<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, CreditCard } from 'lucide-vue-next';
import { computed } from 'vue';

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
        <div class="flex min-h-screen justify-center bg-slate-50">
            <div class="w-full max-w-2xl space-y-8 p-4 md:p-12">
                <!-- Flash success -->
                <div v-if="flash.success" class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4">
                    <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-600" />
                    <p class="text-sm font-semibold text-emerald-700">{{ flash.success }}</p>
                </div>

                <!-- Status Card -->
                <div class="space-y-8 rounded-[2.5rem] border border-slate-100 bg-white p-10 text-center shadow-sm md:p-16">
                    <!-- Icon -->
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-primary/10">
                        <CheckCircle2 class="h-12 w-12 text-primary" />
                    </div>

                    <!-- Content -->
                    <div class="space-y-3">
                        <h1 class="font-serif text-3xl font-bold italic text-slate-900 md:text-4xl">
                            ¡Comprobante <span class="text-primary">recibido!</span>
                        </h1>
                        <p class="mx-auto max-w-md text-base leading-relaxed text-slate-500">
                            Tu comprobante de pago ha sido enviado correctamente. Nuestro equipo lo revisará en menos de
                            <strong class="text-slate-700">24 horas</strong> y te notificaremos por correo al confirmar tu membresía.
                        </p>
                    </div>

                    <!-- Status Badge -->
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-amber-700"
                    >
                        <span class="h-2 w-2 animate-pulse rounded-full bg-amber-400"></span>
                        En Revisión
                    </div>

                    <!-- What's next -->
                    <div class="space-y-3 rounded-2xl bg-slate-50 p-6 text-left">
                        <p class="mb-4 text-xs font-extrabold uppercase tracking-widest text-slate-400">¿Qué sigue?</p>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/15">
                                <span class="text-xs font-black text-primary">1</span>
                            </div>
                            <p class="text-sm text-slate-600">Nuestro equipo verifica tu pago (máx. 24 h)</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/15">
                                <span class="text-xs font-black text-primary">2</span>
                            </div>
                            <p class="text-sm text-slate-600">Recibirás un correo de confirmación con tu acceso activo</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/15">
                                <span class="text-xs font-black text-primary">3</span>
                            </div>
                            <p class="text-sm text-slate-600">¡Accede a todos los cursos sin restricción!</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col items-center justify-center gap-4 pt-2 sm:flex-row">
                        <Link
                            :href="route('student.payments.index')"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3.5 text-xs font-bold uppercase tracking-widest text-white shadow-lg transition-colors hover:bg-primary/90"
                        >
                            <CreditCard class="h-4 w-4" />
                            Ver mis pagos
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition-colors hover:text-primary"
                        >
                            Ir al dashboard
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-16 lg:hidden"></div>
        <BottomNav active="payments" />
    </AppLayout>
</template>
