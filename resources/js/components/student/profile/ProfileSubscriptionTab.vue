<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Crown } from 'lucide-vue-next';

defineProps<{
    subscription: { type: string; end_date: string } | null | undefined;
}>();
</script>

<template>
    <section class="space-y-10 duration-500 animate-in fade-in slide-in-from-bottom-4">
        <div>
            <h2 class="mb-2 font-serif text-3xl font-bold text-on-surface">Suscripción</h2>
            <p class="text-sm text-on-surface-variant">Administra tu membresía actual.</p>
        </div>
        <div
            v-if="subscription"
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 to-black p-8 text-white shadow-2xl lg:p-10"
        >
            <div class="relative z-10 space-y-6">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-amber-400"
                >
                    Suscripción Activa
                </div>
                <h3 class="font-serif text-4xl font-bold capitalize italic text-white sm:text-5xl">Plan {{ subscription.type }}</h3>
                <div class="grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                    <div>
                        <p class="mb-1 text-[10px] font-bold uppercase text-gray-400">Próxima Renovación</p>
                        <p class="font-medium text-amber-400">{{ new Date(subscription.end_date).toLocaleDateString() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="space-y-6 rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-tr from-[#D4AF37] to-[#AA7C11]">
                <Crown class="h-8 w-8 text-white" />
            </div>
            <h3 class="font-serif text-2xl font-bold text-on-surface">No tienes plan activo</h3>
            <Link
                :href="route('planes')"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] px-8 py-3.5 text-xs font-bold uppercase text-white shadow-lg transition-all hover:-translate-y-1"
                >Ver Planes Premium</Link
            >
        </div>
    </section>
</template>
