<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { X, Calendar, ExternalLink } from 'lucide-vue-next';
import type { NextLiveClass } from '@/types/student-dashboard';

defineProps<{
  show: boolean;
  nextLiveClass: NextLiveClass | null;
  isLive: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show && nextLiveClass"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="emit('close')">
        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="scale-95 opacity-0 translate-y-4"
          enter-to-class="scale-100 opacity-100 translate-y-0"
        >
          <div v-show="show && nextLiveClass" class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="relative bg-gradient-to-br from-[#2C2C15] to-[#1a1a0a] p-8 text-white overflow-hidden">
              <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E')"></div>
              <div class="absolute top-0 right-0 w-40 h-40 bg-[#D4AF37]/10 rounded-full blur-[60px]"></div>

              <button @click="emit('close')"
                class="absolute top-5 right-5 p-2 bg-white/10 hover:bg-white/20 rounded-xl text-white transition-colors">
                <X class="w-5 h-5" />
              </button>

              <div class="relative z-10">
                <div class="flex items-center gap-2 mb-4">
                  <span v-if="isLive" class="flex items-center gap-1.5 px-3 py-1 bg-red-500/20 border border-red-500/30 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-red-300 uppercase tracking-widest">En Vivo Ahora</span>
                  </span>
                  <span v-else class="flex items-center gap-1.5 px-3 py-1 bg-[#D4AF37]/10 border border-[#D4AF37]/20 rounded-full">
                    <Calendar class="w-3 h-3 text-[#D4AF37]" />
                    <span class="text-[10px] font-bold text-[#D4AF37] uppercase tracking-widest">Sesión Programada</span>
                  </span>
                </div>
                <h4 class="text-xl font-bold leading-snug">{{ nextLiveClass.title }}</h4>
                <p class="text-white/50 text-sm mt-1">{{ nextLiveClass.course_title }}</p>
              </div>
            </div>

            <div class="p-8 space-y-6">
              <div class="bg-gray-50 rounded-2xl p-5 space-y-2 border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Fecha y hora</p>
                <p class="text-sm font-semibold text-gray-800">{{ nextLiveClass.start_time_human }}</p>
              </div>

              <p class="text-sm text-gray-500 leading-relaxed">
                Dirígete a la página del curso para encontrar el enlace directo, materiales de apoyo y el chat de la sesión en vivo.
              </p>

              <div class="flex flex-col gap-3">
                <Link
                  :href="route('student.classroom', { course: nextLiveClass.course_slug })"
                  class="w-full py-4 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 shadow-lg hover:bg-[#4a4a24] active:scale-95 transition-all">
                  Ir al Aula Virtual <ExternalLink class="w-4 h-4" />
                </Link>
                <button @click="emit('close')"
                  class="w-full py-3 text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-600 transition-colors">
                  Cerrar
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
