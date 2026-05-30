<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { BookOpen, CheckCircle2, Award, Calendar, Wifi, Radio, Play, Crown, ChevronRight } from 'lucide-vue-next';
import type { User } from '@/types';
import type { NextLiveClass, SummaryStats } from '@/types/student-dashboard';

const props = defineProps<{
  user: User & { has_subscription?: boolean };
  animatedStats: SummaryStats;
  nextLiveClass: NextLiveClass | null;
}>();

const emit = defineEmits<{
  (e: 'openModal'): void;
}>();

const greeting = computed(() => {
  const h = new Date().getHours();
  if (h < 12) return '¡Buenos días';
  if (h < 18) return '¡Buenas tardes';
  return '¡Buenas noches';
});

const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0, isLive: false, isPast: false });
let countdownInterval: ReturnType<typeof setInterval> | null = null;

function updateCountdown() {
  if (!props.nextLiveClass) return;
  const target = new Date(props.nextLiveClass.start_time).getTime();
  const now = Date.now();
  const diff = target - now;

  if (diff <= 0 && diff > -5400000) {
    countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0, isLive: true, isPast: false };
  } else if (diff <= -5400000) {
    countdown.value = { ...countdown.value, isLive: false, isPast: true };
  } else {
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    countdown.value = { days: d, hours: h, minutes: m, seconds: s, isLive: false, isPast: false };
  }
}

onMounted(() => {
  if (props.nextLiveClass) {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
  }
});

onUnmounted(() => {
  if (countdownInterval) clearInterval(countdownInterval);
});
</script>

<template>
  <header class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#2C2C15] via-[#3d3d1e] to-[#1a1a0a] p-5 md:p-12 shadow-2xl">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

    <div class="absolute -top-20 -right-20 w-80 h-80 bg-[#D4AF37]/10 rounded-full blur-[80px]"></div>
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-500/5 rounded-full blur-[80px]"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
      <div class="space-y-4">
        <div class="flex items-center gap-3 flex-wrap">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/10 rounded-full px-4 py-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.2em]">Instituto de Economía y Empresa</span>
          </div>
          <div v-if="user.has_subscription"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-amber-600 text-white text-[10px] font-bold uppercase tracking-[0.2em] px-4 py-1.5 rounded-full shadow-lg shadow-amber-500/30">
            <Crown class="w-3 h-3" />
            Miembro Premium
          </div>
        </div>

        <div>
          <p class="text-white/50 text-sm font-medium mb-1">{{ greeting }},</p>
          <h1 class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight">
            {{ user.name.split(' ')[0] }}
            <span class="text-[#D4AF37]">!</span>
          </h1>
          <p class="text-white/40 font-serif text-base mt-2 italic">
            Continúa tu camino hacia la excelencia profesional.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-6 pt-2">
          <div class="flex items-center gap-2">
            <div class="p-1.5 bg-blue-500/20 rounded-lg">
              <BookOpen class="w-4 h-4 text-blue-300" />
            </div>
            <div>
              <p class="text-xl font-bold text-white leading-none">{{ animatedStats.active_courses }}</p>
              <p class="text-[10px] text-white/40 uppercase tracking-widest">Cursos activos</p>
            </div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div class="flex items-center gap-2">
            <div class="p-1.5 bg-emerald-500/20 rounded-lg">
              <CheckCircle2 class="w-4 h-4 text-emerald-300" />
            </div>
            <div>
              <p class="text-xl font-bold text-white leading-none">{{ animatedStats.completed_courses }}</p>
              <p class="text-[10px] text-white/40 uppercase tracking-widest">Completados</p>
            </div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div class="flex items-center gap-2">
            <div class="p-1.5 bg-[#D4AF37]/20 rounded-lg">
              <Award class="w-4 h-4 text-[#D4AF37]" />
            </div>
            <div>
              <p class="text-xl font-bold text-white leading-none">{{ animatedStats.certificate_count }}</p>
              <p class="text-[10px] text-white/40 uppercase tracking-widest">Certificados</p>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:w-80 flex-shrink-0">
        <div v-if="nextLiveClass && countdown.isLive"
          class="bg-gradient-to-br from-red-600/90 to-rose-700/90 backdrop-blur-md border border-red-500/30 rounded-2xl p-6 space-y-4 shadow-xl shadow-red-900/30 cursor-pointer"
          @click="emit('openModal')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
              <span class="text-[10px] font-bold text-white uppercase tracking-[0.3em]">En Vivo Ahora</span>
            </div>
            <div class="p-2 bg-white/10 rounded-xl">
              <Radio class="w-4 h-4 text-white" />
            </div>
          </div>
          <div>
            <p class="text-white/70 text-xs mb-1">{{ nextLiveClass.course_title }}</p>
            <p class="text-white font-bold text-lg leading-snug">{{ nextLiveClass.title }}</p>
          </div>
          <button class="w-full py-3 bg-white text-red-700 text-xs font-bold uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 hover:bg-red-50 active:scale-95 transition-all shadow-lg">
            <Play class="w-4 h-4 fill-current" />
            Unirse a la Sesión
          </button>
        </div>

        <div v-else-if="nextLiveClass && !countdown.isPast"
          class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-6 space-y-4 cursor-pointer hover:bg-white/15 transition-colors"
          @click="emit('openModal')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <Calendar class="w-4 h-4 text-[#D4AF37]" />
              <span class="text-[10px] font-bold text-white/60 uppercase tracking-[0.2em]">Próxima Sesión en Vivo</span>
            </div>
            <div class="p-2 bg-[#D4AF37]/10 rounded-xl">
              <Wifi class="w-4 h-4 text-[#D4AF37]" />
            </div>
          </div>
          <div>
            <p class="text-white/60 text-xs mb-1">{{ nextLiveClass.course_title }}</p>
            <p class="text-white font-bold text-base leading-snug">{{ nextLiveClass.title }}</p>
          </div>
          <div class="grid grid-cols-4 gap-2 text-center">
            <div class="bg-white/5 rounded-xl py-3 px-1">
              <p class="text-2xl font-mono font-bold text-white leading-none">{{ String(countdown.days).padStart(2, '0') }}</p>
              <p class="text-[9px] text-white/40 uppercase mt-1">Días</p>
            </div>
            <div class="bg-white/5 rounded-xl py-3 px-1">
              <p class="text-2xl font-mono font-bold text-white leading-none">{{ String(countdown.hours).padStart(2, '0') }}</p>
              <p class="text-[9px] text-white/40 uppercase mt-1">Hrs</p>
            </div>
            <div class="bg-white/5 rounded-xl py-3 px-1">
              <p class="text-2xl font-mono font-bold text-[#D4AF37] leading-none">{{ String(countdown.minutes).padStart(2, '0') }}</p>
              <p class="text-[9px] text-white/40 uppercase mt-1">Min</p>
            </div>
            <div class="bg-white/5 rounded-xl py-3 px-1">
              <p class="text-2xl font-mono font-bold text-[#D4AF37] leading-none">{{ String(countdown.seconds).padStart(2, '0') }}</p>
              <p class="text-[9px] text-white/40 uppercase mt-1">Seg</p>
            </div>
          </div>
          <button class="w-full py-3 bg-[#D4AF37]/20 border border-[#D4AF37]/30 text-[#D4AF37] text-xs font-bold uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 hover:bg-[#D4AF37]/30 active:scale-95 transition-all">
            <Calendar class="w-3.5 h-3.5" />
            Ver detalles
          </button>
        </div>

        <div v-else
          class="bg-white/5 backdrop-blur-md border border-dashed border-white/10 rounded-2xl p-6 text-center space-y-3">
          <div class="inline-flex p-4 bg-white/5 rounded-2xl">
            <Calendar class="w-8 h-8 text-white/20" />
          </div>
          <div>
            <p class="text-white/40 text-xs font-bold uppercase tracking-widest">Sesiones en Vivo</p>
            <p class="text-white/25 text-xs mt-1 font-serif">No tienes clases próximas programadas.</p>
          </div>
          <Link :href="route('student.live-classes.index')" class="inline-flex items-center gap-1.5 text-[10px] text-[#D4AF37]/60 uppercase tracking-widest hover:text-[#D4AF37] transition-colors">
            Ver calendario <ChevronRight class="w-3 h-3" />
          </Link>
        </div>
      </div>
    </div>
  </header>
</template>
