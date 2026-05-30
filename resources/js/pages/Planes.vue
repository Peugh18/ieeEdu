<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Navigation from '@/components/landing/Navigation.vue';
import SubscriptionPaymentModal from '@/components/student/SubscriptionPaymentModal.vue';
import type { SharedData } from '@/types';
import { Crown } from 'lucide-vue-next';

const standardFeatures = [
    { text: 'Acceso ilimitado a TODOS los cursos grabados (asíncronos)', icon: 'all_inclusive' },
    { text: 'Acceso a clases en vivo exclusivas', icon: 'videocam' },
    { text: 'Certificaciones incluidas sin costo adicional', icon: 'workspace_premium' },
    { text: 'Acceso a comunidad privada activa', icon: 'forum' },
    { text: 'Actualizaciones constantes de contenido', icon: 'autorenew' },
    { text: 'Contenido orientado al mercado laboral', icon: 'work' }
];

interface PlanConfig {
    id: string;
    name: string;
    badge?: string;
    badgeIcon?: string;
    price: number;
    period: string;
    description: string;
    color: string;
}

interface UserSubscription {
    type: string;
    status: string;
    end_date: string;
}

const props = defineProps<{
    planesConfig?: PlanConfig[];
    userSubscription?: UserSubscription | null;
}>();

const plans = computed(() => {
    return (props.planesConfig || []).map(plan => ({
        ...plan,
        features: standardFeatures
    }));
});

const page = usePage<SharedData>();
const user = computed(() => page.props.auth?.user);

const showModal = ref(false);
const selectedPlan = ref<PlanConfig | null>(null);

function requestPlan(plan: PlanConfig) {
    if (!user.value) {
        router.visit(route('login'), { data: { redirect: '/planes' } });
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
            <!-- Header -->
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

            <!-- Active subscription banner -->
            <div v-if="userSubscription" class="mb-12 bg-primary/5 border border-primary/20 rounded-3xl p-6 flex items-center gap-4 max-w-2xl mx-auto">
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

            <!-- Empty state -->
            <div v-if="!plans.length" class="text-center py-24">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <Crown class="w-8 h-8 text-slate-300" />
                </div>
                <h3 class="text-xl font-bold text-on-background mb-2">Planes no disponibles</h3>
                <p class="text-on-surface-variant">No hay planes configurados en este momento. Contáctanos directamente.</p>
            </div>

            <!-- Pricing Cards -->
            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative z-10">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="bg-surface-container rounded-3xl relative overflow-hidden flex flex-col shadow-sm border border-outline-variant/15 hover:-translate-y-2 hover:shadow-xl hover:border-primary/20 transition-all duration-500 group"
                >
                    <!-- Badge -->
                    <div v-if="plan.badge" class="absolute top-0 right-8 bg-[#D32F2F] text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-b-lg z-10 flex items-center gap-1">
                        <span class="material-symbols-outlined" translate="no" style="font-size: 14px;">{{ plan.badgeIcon }}</span>
                        {{ plan.badge }}
                    </div>

                    <!-- Card Header Gradient -->
                    <div :class="`h-3 w-full bg-gradient-to-r ${plan.color}`"></div>

                    <div class="p-8 lg:p-10 flex flex-col flex-1">
                        <h3 class="text-2xl font-serif font-bold text-on-background mb-2">{{ plan.name }}</h3>
                        <p class="text-sm text-on-surface-variant mb-6 min-h-[40px]">{{ plan.description }}</p>

                        <div class="mb-8 flex items-end gap-2">
                            <span class="text-4xl lg:text-5xl font-bold tracking-tight text-on-background">S/ {{ plan.price }}</span>
                            <span class="text-sm font-bold text-on-surface-variant uppercase tracking-widest pb-1">/ {{ plan.period }}</span>
                        </div>

                        <button
                            @click="requestPlan(plan)"
                            class="w-full relative overflow-hidden rounded-xl bg-primary text-white py-4 font-bold text-xs uppercase tracking-widest hover:shadow-lg transition-all mb-10 group/btn"
                        >
                            <span class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                {{ user ? 'Solicitar plan' : 'Iniciar sesión para comprar' }}
                                <span class="material-symbols-outlined" translate="no" style="font-size: 16px;">arrow_forward</span>
                            </span>
                        </button>

                        <div class="space-y-4 flex-1">
                            <h4 class="text-xs font-bold text-on-background uppercase tracking-widest mb-6 border-b border-outline-variant/20 pb-4">¿Qué incluye?</h4>
                            <ul class="space-y-4">
                                <li v-for="(feature, idx) in plan.features" :key="idx" class="flex items-start gap-3">
                                    <div class="min-w-6 min-h-6 rounded-full bg-surface flex items-center justify-center mt-0.5 shrink-0">
                                        <span class="material-symbols-outlined text-primary" translate="no" style="font-size: 14px; font-weight: bold;">{{ feature.icon }}</span>
                                    </div>
                                    <span class="text-sm text-on-surface-variant leading-relaxed relative top-0.5">{{ feature.text }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Trust Badge -->
            <div class="mt-24 text-center max-w-2xl mx-auto border-t border-outline-variant/20 pt-12">
                <p class="text-sm text-on-surface-variant italic font-serif">
                    Todos nuestros planes incluyen garantía de calidad del Instituto de Especialización Educativa (IEE). Transacción 100% segura.
                </p>
            </div>
        </main>
    </div>

    <!-- Subscription Payment Modal -->
    <SubscriptionPaymentModal
        :show="showModal"
        :plan="selectedPlan"
        @close="showModal = false"
    />
</template>
