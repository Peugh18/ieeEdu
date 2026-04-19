<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';

const standardFeatures = [
    { text: 'Acceso ilimitado a TODOS los cursos grabados (asíncronos)', icon: 'all_inclusive' },
    { text: 'Acceso a clases en vivo exclusivas', icon: 'videocam' },
    { text: 'Certificaciones incluidas sin costo adicional', icon: 'workspace_premium' },
    { text: 'Acceso a comunidad privada activa', icon: 'forum' },
    { text: 'Actualizaciones constantes de contenido', icon: 'autorenew' },
    { text: 'Contenido orientado al mercado laboral', icon: 'work' }
];

const plans = [
    {
        id: 'trimestral',
        name: 'Plan Trimestral',
        badge: 'Ideal para empezar',
        badgeIcon: 'rocket_launch',
        price: 350,
        period: '3 meses',
        description: 'Comienza tu desarrollo profesional ahora.',
        color: 'from-amber-400 to-amber-600',
        features: standardFeatures
    },
    {
        id: 'semestral',
        name: 'Plan Semestral',
        badge: 'Más popular',
        badgeIcon: 'local_fire_department',
        price: 600,
        period: '6 meses',
        description: 'Mejor relación calidad/precio.',
        color: 'from-blue-500 to-indigo-700',
        features: standardFeatures
    },
    {
        id: 'anual',
        name: 'Plan Anual',
        badge: 'Máximo ahorro',
        badgeIcon: 'savings',
        price: 990,
        period: '12 meses',
        description: 'Acceso total sin límites todo el año.',
        color: 'from-green-500 to-emerald-700',
        features: standardFeatures
    }
];

import { usePage } from '@inertiajs/vue3';

const page = usePage();

const buyPlan = (plan: any) => {
    const user = (page.props as any).auth?.user;
    const name = user ? user.name : '';

    const text = `Hola 👋, ${name ? 'soy ' + name : 'qué tal'} y estoy interesado en el plan:

📦 *${plan.name}*

Quiero acceso completo a todos los cursos.

¿Me pueden brindar más información para continuar con mi inscripción?`;
    const url = `https://wa.me/51934057867?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};
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

            <!-- Pricing Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative z-10">
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
                            @click="buyPlan(plan)"
                            class="w-full relative overflow-hidden rounded-xl bg-primary text-white py-4 font-bold text-xs uppercase tracking-widest hover:shadow-lg transition-all mb-10 group/btn"
                        >
                            <span class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                Comprar por WhatsApp
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
</template>
