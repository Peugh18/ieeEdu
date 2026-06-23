<script setup lang="ts">
import type { User } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Crown, Lock, Zap } from 'lucide-vue-next';

defineProps<{
    user: User & { has_subscription?: boolean };
}>();
</script>

<template>
    <div class="space-y-4 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Estado de Membresía</h3>
            <div v-if="user.has_subscription" class="flex items-center gap-1.5 rounded-full border border-amber-100 bg-amber-50 px-3 py-1">
                <Crown class="h-3 w-3 text-amber-500" />
                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Premium</span>
            </div>
            <div v-else class="rounded-full border border-gray-100 bg-gray-50 px-3 py-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Básica</span>
            </div>
        </div>

        <div v-if="user.has_subscription" class="space-y-3">
            <div class="flex items-start gap-3 rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50 to-yellow-50 p-4">
                <Zap class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" />
                <div>
                    <p class="text-sm font-bold text-amber-900">Acceso ilimitado activo</p>
                    <p class="mt-0.5 text-xs text-amber-700/70">
                        Tienes acceso a todos los cursos del plan de suscripción. Si la suscripción se cancela, solo conservarás tus cursos comprados
                        individualmente.
                    </p>
                </div>
            </div>
            <Link
                :href="route('student.explore.courses')"
                class="block w-full rounded-xl border border-primary/20 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary transition-colors hover:bg-primary/5"
            >
                Explorar más cursos
            </Link>
        </div>

        <div v-else class="space-y-3">
            <div class="flex items-start gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <Lock class="mt-0.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                <div>
                    <p class="text-sm font-bold text-gray-700">Plan estándar</p>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Tienes acceso a los cursos que compraste individualmente. Actualiza al plan premium y desbloquea todo el catálogo.
                    </p>
                </div>
            </div>
            <Link
                :href="route('planes')"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-[#D4AF37] py-3 text-xs font-bold uppercase tracking-widest text-white transition-all hover:shadow-lg hover:shadow-primary/20 active:scale-95"
            >
                <Crown class="h-3.5 w-3.5" />
                Ver planes
            </Link>
        </div>
    </div>
</template>
