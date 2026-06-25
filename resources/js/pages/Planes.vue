<script setup lang="ts">
import Navigation from '@/components/landing/Navigation.vue';
import SubscriptionPaymentModal from '@/components/student/SubscriptionPaymentModal.vue';
import type { SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Crown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface PlanConfig {
    id: string;
    name: string;
    badge?: string;
    price: number;
    period: string;
    description: string;
    features: string[];
}

interface UserSubscription {
    type: string;
    status: string;
    end_date: string;
}

const props = defineProps<{
    planesConfig?: PlanConfig[];
    userSubscription?: UserSubscription | null;
    hasPendingSubscriptionPayment?: boolean;
}>();

const plans = computed(() => props.planesConfig ?? []);

const page = usePage<SharedData>();
const user = computed(() => page.props.auth?.user);

const showModal = ref(false);
const selectedPlan = ref<PlanConfig | null>(null);

function requestPlan(plan: PlanConfig) {
    if (!user.value) {
        router.visit(route('login'), { data: { redirect: '/planes' } });
        return;
    }
    if (props.userSubscription) {
        alert('Ya tienes una membresía Premium activa.');
        return;
    }
    if (props.hasPendingSubscriptionPayment) {
        router.visit(route('student.payments.index'));
        return;
    }
    selectedPlan.value = plan;
    showModal.value = true;
}
</script>

<template>
    <Head title="Planes de Acceso Total - IEE" />

    <div class="min-h-screen bg-surface font-sans">
        <Navigation />

        <main class="mx-auto max-w-7xl px-4 pb-24 pt-32 sm:px-6 lg:px-8">
            <div class="mx-auto mb-20 max-w-3xl text-center">
                <span class="mb-6 inline-flex rounded-full bg-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.1em] text-primary">
                    Suscripciones IEE
                </span>
                <h1 class="mb-8 font-serif text-4xl font-bold leading-[1.1] tracking-tight text-on-background sm:text-5xl lg:text-[56px]">
                    Desbloquea tu <span class="italic text-primary">potencial ilimitado</span>
                </h1>
                <p class="mx-auto max-w-2xl text-lg text-on-surface-variant">
                    Invierte en tu futuro y obtén acceso total a nuestro catálogo de especialización. Elige el plan que mejor se adapte a tu ritmo de
                    aprendizaje.
                </p>
            </div>

            <div
                v-if="hasPendingSubscriptionPayment"
                class="mx-auto mb-12 flex max-w-2xl items-center gap-4 rounded-3xl border border-amber-200 bg-amber-50 p-6"
            >
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
                    <Crown class="h-6 w-6 text-amber-600" />
                </div>
                <div class="flex-1">
                    <p class="font-bold text-on-background">Solicitud de membresía en curso</p>
                    <p class="text-sm text-on-surface-variant">
                        Coordina tu pago por WhatsApp y sube el comprobante en Mis Pagos cuando te lo indiquen.
                    </p>
                    <Link
                        :href="route('student.payments.index')"
                        class="mt-3 inline-flex text-xs font-bold uppercase tracking-wider text-amber-700 hover:text-amber-900"
                    >
                        Ir a Mis Pagos →
                    </Link>
                </div>
            </div>

            <div
                v-else-if="userSubscription"
                class="mx-auto mb-12 flex max-w-2xl items-center gap-4 rounded-3xl border border-primary/20 bg-primary/5 p-6"
            >
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10">
                    <Crown class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <p class="font-bold text-on-background">¡Tienes una membresía activa!</p>
                    <p class="text-sm text-on-surface-variant">
                        Plan <span class="font-semibold capitalize text-primary">{{ userSubscription.type }}</span> — Vence el
                        {{ userSubscription.end_date }}
                    </p>
                </div>
            </div>

            <div v-if="!plans.length" class="py-24 text-center">
                <h3 class="mb-2 text-xl font-bold text-on-background">Planes no disponibles</h3>
                <p class="text-on-surface-variant">No hay planes configurados en este momento. Contáctanos directamente.</p>
            </div>

            <div v-else class="relative z-10 grid grid-cols-1 gap-8 md:grid-cols-3 lg:gap-12">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="relative flex flex-col overflow-hidden rounded-3xl border border-outline-variant/15 bg-surface-container shadow-sm transition-all duration-500 hover:-translate-y-2 hover:border-primary/20 hover:shadow-xl"
                >
                    <div
                        v-if="plan.badge"
                        class="absolute right-8 top-0 z-10 rounded-b-lg bg-[#D32F2F] px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white"
                    >
                        {{ plan.badge }}
                    </div>

                    <div class="h-1 w-full bg-primary"></div>

                    <div class="flex flex-1 flex-col p-8 lg:p-10">
                        <h3 class="mb-2 font-serif text-2xl font-bold text-on-background">{{ plan.name }}</h3>
                        <p class="mb-6 min-h-[40px] text-sm text-on-surface-variant">{{ plan.description }}</p>

                        <div class="mb-8 flex items-end gap-2">
                            <span class="text-4xl font-bold tracking-tight text-on-background lg:text-5xl">S/ {{ plan.price }}</span>
                            <span class="pb-1 text-sm font-bold uppercase tracking-widest text-on-surface-variant">/ {{ plan.period }}</span>
                        </div>

                        <button
                            @click="requestPlan(plan)"
                            class="mb-10 w-full rounded-xl bg-primary py-4 text-xs font-bold uppercase tracking-widest text-white transition-all hover:shadow-lg"
                        >
                            {{ user ? 'Solicitar plan' : 'Iniciar sesión para comprar' }}
                        </button>

                        <div class="flex-1 space-y-4">
                            <h4 class="mb-6 border-b border-outline-variant/20 pb-4 text-xs font-bold uppercase tracking-widest text-on-background">
                                ¿Qué incluye?
                            </h4>
                            <ul class="space-y-3">
                                <li
                                    v-for="(feature, idx) in plan.features"
                                    :key="idx"
                                    class="border-l-2 border-primary/30 pl-4 text-sm leading-relaxed text-on-surface-variant"
                                >
                                    {{ feature }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-24 max-w-2xl border-t border-outline-variant/20 pt-12 text-center">
                <p class="font-serif text-sm italic text-on-surface-variant">
                    Todos nuestros planes incluyen garantía de calidad del Instituto de Especialización Educativa (IEE). Transacción 100% segura.
                </p>
            </div>
        </main>
    </div>

    <SubscriptionPaymentModal :show="showModal" :plan="selectedPlan" @close="showModal = false" />
</template>
