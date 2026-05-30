<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Crown, Zap, Lock } from 'lucide-vue-next';
import type { User } from '@/types';

defineProps<{
  user: User & { has_subscription?: boolean };
}>();
</script>

<template>
  <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-bold text-gray-900">Estado de Membresía</h3>
      <div v-if="user.has_subscription"
        class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-100 rounded-full">
        <Crown class="w-3 h-3 text-amber-500" />
        <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Premium</span>
      </div>
      <div v-else class="px-3 py-1 bg-gray-50 border border-gray-100 rounded-full">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Básica</span>
      </div>
    </div>

    <div v-if="user.has_subscription" class="space-y-3">
      <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl border border-amber-100">
        <Zap class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-bold text-amber-900">Acceso ilimitado activo</p>
          <p class="text-xs text-amber-700/70 mt-0.5">
            Tienes acceso a todos los cursos del plan de suscripción. Si la suscripción se cancela, solo conservarás tus cursos comprados individualmente.
          </p>
        </div>
      </div>
      <Link :href="route('student.explore.courses')"
        class="block w-full py-3 text-center text-xs font-bold text-primary uppercase tracking-widest border border-primary/20 rounded-xl hover:bg-primary/5 transition-colors">
        Explorar más cursos
      </Link>
    </div>

    <div v-else class="space-y-3">
      <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
        <Lock class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-bold text-gray-700">Plan estándar</p>
          <p class="text-xs text-gray-500 mt-0.5">
            Tienes acceso a los cursos que compraste individualmente. Actualiza al plan premium y desbloquea todo el catálogo.
          </p>
        </div>
      </div>
      <Link :href="route('planes')"
        class="flex items-center justify-center gap-2 w-full py-3 text-xs font-bold text-white bg-gradient-to-r from-primary to-[#D4AF37] uppercase tracking-widest rounded-xl hover:shadow-lg hover:shadow-primary/20 active:scale-95 transition-all">
        <Crown class="w-3.5 h-3.5" />
        Ver planes
      </Link>
    </div>
  </div>
</template>
