<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Navigation from '@/components/landing/Navigation.vue';
import SubscriptionPaymentModal from '@/components/student/SubscriptionPaymentModal.vue';
import type { SharedData } from '@/types';
import { Crown } from 'lucide-vue-next';

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

        <main class="pt-32 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="inline-flex rounded-full bg-primary/10 text-primary px-4 py-1.5 text-xs font-bold uppercase tracking-[0.1em] mb-6">
                    Suscripciones IEE
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-serif font-bold text-on-background leading-[1.1] tracking-tight mb-8">
                    Desbloquea tu <span class="text-primary italic">potencial ilimitado</span>
                </h1>
                <p class="text-lg text-on-surface-variant max-w-2xl mx-auto">
                    Invierte en tu futuro y obtén acceso total a nuestro catálogo de especialización. Elige el plan que mejor se adapte a tu ritmo de aprendizaje.
                </p>
            </div>

            <div v-if="hasPendingSubscriptionPayment" class="mb-12 bg-amber-50 border border-amber-200 rounded-3xl p-6 flex items-center gap-4 max-w-2xl mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0">
                    <Crown class="w-6 h-6 text-amber-600" />
                </div>
                <div class="flex-1">
                    <p class="font-bold text-on-background">Solicitud de membresía en curso</p>
                    <p class="text-sm text-on-surface-variant">Coordina tu pago por WhatsApp y sube el comprobante en Mis Pagos cuando te lo indiquen.</p>
                    <Link :href="route('student.payments.index')" class="inline-flex mt-3 text-xs font-bold uppercase tracking-wider text-amber-700 hover:text-amber-900">
                        Ir a Mis Pagos →
                    </Link>
                </div>
            </div>

            <div v-else-if="userSubscription" class="mb-12 bg-primary/5 border border-primary/20 rounded-3xl p-6 flex items-center gap-4 max-w-2xl mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                    <Crown class="w-6 h-6 text-primary" />
                </div>
                <div>
                    <p class="font-bold text-on-background">¡Tienes una membresía activa!</p>
                    <p class="text-sm text-on-surface-variant">
                        Plan <span class="capitalize font-semibold text-primary">{{ userSubscription.type }}</span> —
                        Vence el {{ userSubscription.end_date }}
                    </p>
                </div>
            </div>

            <div v-if="!plans.length" class="text-center py-24">
                <h3 class="text-xl font-bold text-on-background mb-2">Planes no disponibles</h3>
                <p class="text-on-surface-variant">No hay planes configurados en este momento. Contáctanos directamente.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative z-10">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="bg-surface-container rounded-3xl relative overflow-hidden flex flex-col shadow-sm border border-outline-variant/15 hover:-translate-y-2 hover:shadow-xl hover:border-primary/20 transition-all duration-500"
                >
                    <div v-if="plan.badge" class="absolute top-0 right-8 bg-[#D32F2F] text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-b-lg z-10">
                        {{ plan.badge }}
                    </div>

                    <div class="h-1 w-full bg-primary"></div>

                    <div class="p-8 lg:p-10 flex flex-col flex-1">
                        <h3 class="text-2xl font-serif font-bold text-on-background mb-2">{{ plan.name }}</h3>
                        <p class="text-sm text-on-surface-variant mb-6 min-h-[40px]">{{ plan.description }}</p>

                        <div class="mb-8 flex items-end gap-2">
                            <span class="text-4xl lg:text-5xl font-bold tracking-tight text-on-background">S/ {{ plan.price }}</span>
                            <span class="text-sm font-bold text-on-surface-variant uppercase tracking-widest pb-1">/ {{ plan.period }}</span>
                        </div>

                        <button
                            @click="requestPlan(plan)"
                            class="w-full rounded-xl bg-primary text-white py-4 font-bold text-xs uppercase tracking-widest hover:shadow-lg transition-all mb-10"
                        >
                            {{ user ? 'Solicitar plan' : 'Iniciar sesión para comprar' }}
                        </button>

                        <div class="space-y-4 flex-1">
                            <h4 class="text-xs font-bold text-on-background uppercase tracking-widest mb-6 border-b border-outline-variant/20 pb-4">¿Qué incluye?</h4>
                            <ul class="space-y-3">
                                <li
                                    v-for="(feature, idx) in plan.features"
                                    :key="idx"
                                    class="text-sm text-on-surface-variant leading-relaxed pl-4 border-l-2 border-primary/30"
                                >
                                    {{ feature }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-24 text-center max-w-2xl mx-auto border-t border-outline-variant/20 pt-12">
                <p class="text-sm text-on-surface-variant italic font-serif">
                    Todos nuestros planes incluyen garantía de calidad del Instituto de Especialización Educativa (IEE). Transacción 100% segura.
                </p>
            </div>
        </main>
    </div>

    <SubscriptionPaymentModal
        :show="showModal"
        :plan="selectedPlan"
        @close="showModal = false"
    />
</template>
