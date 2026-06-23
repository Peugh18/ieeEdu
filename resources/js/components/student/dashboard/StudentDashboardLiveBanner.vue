<script setup lang="ts">
import type { User } from '@/types';
import type { NextLiveClass, SummaryStats } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { Award, BookOpen, Calendar, CheckCircle2, ChevronRight, Crown, Play, Radio, Wifi } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

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
    <header
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#2C2C15] via-[#3d3d1e] to-[#1a1a0a] p-5 shadow-2xl md:rounded-3xl md:p-12"
    >
        <div
            class="absolute inset-0 opacity-[0.04]"
            style="
                background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);
            "
        ></div>

        <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-[#D4AF37]/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-80 w-80 rounded-full bg-blue-500/5 blur-[80px]"></div>

        <div class="relative z-10 flex flex-col justify-between gap-8 lg:flex-row lg:items-center">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-1.5 backdrop-blur-md">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/70">Instituto de Economía y Empresa</span>
                    </div>
                    <div
                        v-if="user.has_subscription"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white shadow-lg shadow-amber-500/30"
                    >
                        <Crown class="h-3 w-3" />
                        Miembro Premium
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-sm font-medium text-white/50">{{ greeting }},</p>
                    <h1 class="font-serif text-3xl font-bold leading-tight text-white md:text-5xl">
                        {{ user.name.split(' ')[0] }}
                        <span class="text-[#D4AF37]">!</span>
                    </h1>
                    <p class="mt-2 font-serif text-base italic text-white/40">Continúa tu camino hacia la excelencia profesional.</p>
                </div>

                <div class="flex flex-wrap items-center gap-6 pt-2">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-blue-500/20 p-1.5">
                            <BookOpen class="h-4 w-4 text-blue-300" />
                        </div>
                        <div>
                            <p class="text-xl font-bold leading-none text-white">{{ animatedStats.active_courses }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/40">Cursos activos</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-emerald-500/20 p-1.5">
                            <CheckCircle2 class="h-4 w-4 text-emerald-300" />
                        </div>
                        <div>
                            <p class="text-xl font-bold leading-none text-white">{{ animatedStats.completed_courses }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/40">Completados</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-[#D4AF37]/20 p-1.5">
                            <Award class="h-4 w-4 text-[#D4AF37]" />
                        </div>
                        <div>
                            <p class="text-xl font-bold leading-none text-white">{{ animatedStats.certificate_count }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/40">Certificados</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0 lg:w-80">
                <div
                    v-if="nextLiveClass && countdown.isLive"
                    class="cursor-pointer space-y-4 rounded-2xl border border-red-500/30 bg-gradient-to-br from-red-600/90 to-rose-700/90 p-6 shadow-xl shadow-red-900/30 backdrop-blur-md"
                    @click="emit('openModal')"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-white"></span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-white">En Vivo Ahora</span>
                        </div>
                        <div class="rounded-xl bg-white/10 p-2">
                            <Radio class="h-4 w-4 text-white" />
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 text-xs text-white/70">{{ nextLiveClass.course_title }}</p>
                        <p class="text-lg font-bold leading-snug text-white">{{ nextLiveClass.title }}</p>
                    </div>
                    <button
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-white py-3 text-xs font-bold uppercase tracking-widest text-red-700 shadow-lg transition-all hover:bg-red-50 active:scale-95"
                    >
                        <Play class="h-4 w-4 fill-current" />
                        Unirse a la Sesión
                    </button>
                </div>

                <div
                    v-else-if="nextLiveClass && !countdown.isPast"
                    class="cursor-pointer space-y-4 rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur-md transition-colors hover:bg-white/15"
                    @click="emit('openModal')"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-[#D4AF37]" />
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/60">Próxima Sesión en Vivo</span>
                        </div>
                        <div class="rounded-xl bg-[#D4AF37]/10 p-2">
                            <Wifi class="h-4 w-4 text-[#D4AF37]" />
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 text-xs text-white/60">{{ nextLiveClass.course_title }}</p>
                        <p class="text-base font-bold leading-snug text-white">{{ nextLiveClass.title }}</p>
                    </div>
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="rounded-xl bg-white/5 px-1 py-3">
                            <p class="font-mono text-2xl font-bold leading-none text-white">{{ String(countdown.days).padStart(2, '0') }}</p>
                            <p class="mt-1 text-[9px] uppercase text-white/40">Días</p>
                        </div>
                        <div class="rounded-xl bg-white/5 px-1 py-3">
                            <p class="font-mono text-2xl font-bold leading-none text-white">{{ String(countdown.hours).padStart(2, '0') }}</p>
                            <p class="mt-1 text-[9px] uppercase text-white/40">Hrs</p>
                        </div>
                        <div class="rounded-xl bg-white/5 px-1 py-3">
                            <p class="font-mono text-2xl font-bold leading-none text-[#D4AF37]">{{ String(countdown.minutes).padStart(2, '0') }}</p>
                            <p class="mt-1 text-[9px] uppercase text-white/40">Min</p>
                        </div>
                        <div class="rounded-xl bg-white/5 px-1 py-3">
                            <p class="font-mono text-2xl font-bold leading-none text-[#D4AF37]">{{ String(countdown.seconds).padStart(2, '0') }}</p>
                            <p class="mt-1 text-[9px] uppercase text-white/40">Seg</p>
                        </div>
                    </div>
                    <button
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-[#D4AF37]/30 bg-[#D4AF37]/20 py-3 text-xs font-bold uppercase tracking-widest text-[#D4AF37] transition-all hover:bg-[#D4AF37]/30 active:scale-95"
                    >
                        <Calendar class="h-3.5 w-3.5" />
                        Ver detalles
                    </button>
                </div>

                <div v-else class="space-y-3 rounded-2xl border border-dashed border-white/10 bg-white/5 p-6 text-center backdrop-blur-md">
                    <div class="inline-flex rounded-2xl bg-white/5 p-4">
                        <Calendar class="h-8 w-8 text-white/20" />
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/40">Sesiones en Vivo</p>
                        <p class="mt-1 font-serif text-xs text-white/25">No tienes clases próximas programadas.</p>
                    </div>
                    <Link
                        :href="route('student.live-classes.index')"
                        class="inline-flex items-center gap-1.5 text-[10px] uppercase tracking-widest text-[#D4AF37]/60 transition-colors hover:text-[#D4AF37]"
                    >
                        Ver calendario <ChevronRight class="h-3 w-3" />
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>
