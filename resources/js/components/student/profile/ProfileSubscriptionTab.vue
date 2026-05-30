<script setup lang="ts">
import { Crown } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

defineProps<{
    subscription: { type: string; end_date: string } | null | undefined;
}>();
</script>

<template>
    <section class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div>
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Suscripción</h2>
            <p class="text-on-surface-variant text-sm">Administra tu membresía actual.</p>
        </div>
        <div v-if="subscription" class="bg-gradient-to-br from-gray-900 to-black text-white p-8 lg:p-10 rounded-3xl relative overflow-hidden shadow-2xl">
            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest text-amber-400">Suscripción Activa</div>
                <h3 class="text-4xl sm:text-5xl font-serif font-bold italic text-white capitalize">Plan {{ subscription.type }}</h3>
                <div class="grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Próxima Renovación</p>
                        <p class="font-medium text-amber-400">{{ new Date(subscription.end_date).toLocaleDateString() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 text-center space-y-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-[#D4AF37] to-[#AA7C11] rounded-full mx-auto flex items-center justify-center"><Crown class="w-8 h-8 text-white" /></div>
            <h3 class="text-2xl font-serif font-bold text-on-surface">No tienes plan activo</h3>
            <Link :href="route('planes')" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] text-white px-8 py-3.5 rounded-xl text-xs font-bold uppercase shadow-lg hover:-translate-y-1 transition-all">Ver Planes Premium</Link>
        </div>
    </section>
</template>
