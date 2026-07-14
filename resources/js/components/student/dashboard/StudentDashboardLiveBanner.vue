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
        class="relative overflow-hidden rounded-3xl border border-primary/20 bg-gradient-to-br from-primary/15 via-primary/5 to-transparent p-5 shadow-sm md:p-6 dark:from-primary/20 dark:via-primary/10"
    >
        <div class="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/10 bg-primary/5 px-4 py-1.5">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-primary"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Instituto de Economía y Empresa</span>
                    </div>
                    <div
                        v-if="user.has_subscription"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white shadow-sm"
                    >
                        <Crown class="h-3 w-3" />
                        Miembro Premium
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-sm font-medium text-on-surface-variant/70">{{ greeting }},</p>
                    <h1 class="text-3xl font-bold leading-tight text-on-background md:text-4xl">
                        {{ user.name.split(' ')[0] }}
                        <span class="text-primary">!</span>
                    </h1>
                    <p class="mt-2 text-sm text-on-surface-variant">Continúa tu camino hacia la excelencia profesional.</p>
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-1">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-blue-50 p-1.5 text-blue-600">
                            <BookOpen class="h-3 w-3" />
                        </div>
                        <div>
                            <p class="text-base font-bold leading-none text-on-surface">{{ animatedStats.active_courses }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-on-surface-variant">Cursos activos</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-emerald-50 p-1.5 text-emerald-600">
                            <CheckCircle2 class="h-3 w-3" />
                        </div>
                        <div>
                            <p class="text-base font-bold leading-none text-on-surface">{{ animatedStats.completed_courses }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-on-surface-variant">Completados</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-1.5 text-primary">
                            <Award class="h-3 w-3" />
                        </div>
                        <div>
                            <p class="text-base font-bold leading-none text-on-surface">{{ animatedStats.certificate_count }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-on-surface-variant">Certificados</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0 lg:w-72">
                <div
                    v-if="nextLiveClass && countdown.isLive"
                    class="cursor-pointer space-y-3 rounded-2xl border border-red-500/30 bg-red-50/50 p-5 shadow-sm transition-colors hover:bg-red-50"
                    @click="emit('openModal')"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-red-600"></span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-red-600">En Vivo</span>
                        </div>
                        <div class="rounded-lg bg-red-100 p-1.5">
                            <Radio class="h-3.5 w-3.5 text-red-600" />
                        </div>
                    </div>
                    <div>
                        <p class="mb-0.5 text-[11px] text-red-600/70">{{ nextLiveClass.course_title }}</p>
                        <p class="text-base font-bold leading-snug text-red-900">{{ nextLiveClass.title }}</p>
                    </div>
                    <button
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 py-2.5 text-[11px] font-bold uppercase tracking-widest text-white shadow-sm transition-all hover:bg-red-700 active:scale-95"
                    >
                        <Play class="h-3.5 w-3.5 fill-current" />
                        Unirse a la Sesión
                    </button>
                </div>

                <div
                    v-else-if="nextLiveClass && !countdown.isPast"
                    class="cursor-pointer space-y-3 rounded-2xl border border-outline-variant/20 bg-background p-5 shadow-sm transition-colors hover:bg-surface-container-low"
                    @click="emit('openModal')"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Calendar class="h-3.5 w-3.5 text-primary" />
                            <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant/60">Próxima Sesión</span>
                        </div>
                        <div class="rounded-lg bg-primary/10 p-1.5">
                            <Wifi class="h-3.5 w-3.5 text-primary" />
                        </div>
                    </div>
                    <div>
                        <p class="mb-0.5 text-[11px] text-on-surface-variant/60">{{ nextLiveClass.course_title }}</p>
                        <p class="text-sm font-bold leading-snug text-on-surface">{{ nextLiveClass.title }}</p>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 text-center">
                        <div class="rounded-lg bg-surface-container px-1 py-2">
                            <p class="font-mono text-lg font-bold leading-none text-on-surface">{{ String(countdown.days).padStart(2, '0') }}</p>
                            <p class="mt-0.5 text-[8px] uppercase text-on-surface-variant">Días</p>
                        </div>
                        <div class="rounded-lg bg-surface-container px-1 py-2">
                            <p class="font-mono text-lg font-bold leading-none text-on-surface">{{ String(countdown.hours).padStart(2, '0') }}</p>
                            <p class="mt-0.5 text-[8px] uppercase text-on-surface-variant">Hrs</p>
                        </div>
                        <div class="rounded-lg bg-surface-container px-1 py-2">
                            <p class="font-mono text-lg font-bold leading-none text-primary">{{ String(countdown.minutes).padStart(2, '0') }}</p>
                            <p class="mt-0.5 text-[8px] uppercase text-on-surface-variant">Min</p>
                        </div>
                        <div class="rounded-lg bg-surface-container px-1 py-2">
                            <p class="font-mono text-lg font-bold leading-none text-primary">{{ String(countdown.seconds).padStart(2, '0') }}</p>
                            <p class="mt-0.5 text-[8px] uppercase text-on-surface-variant">Seg</p>
                        </div>
                    </div>
                    <button
                        class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-primary/20 bg-primary/10 py-2.5 text-[11px] font-bold uppercase tracking-widest text-primary transition-all hover:bg-primary/20 active:scale-95"
                    >
                        <Calendar class="h-3 w-3" />
                        Ver detalles
                    </button>
                </div>

                <div v-else class="space-y-3 rounded-2xl border border-dashed border-outline-variant/30 bg-background p-6 text-center shadow-sm">
                    <div class="inline-flex rounded-2xl bg-surface-container p-4">
                        <Calendar class="h-8 w-8 text-on-surface-variant/40" />
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Sesiones en Vivo</p>
                        <p class="mt-1 text-xs text-on-surface-variant/60">No tienes clases próximas programadas.</p>
                    </div>
                    <Link
                        :href="route('student.live-classes.index')"
                        class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-primary transition-colors hover:text-primary/80"
                    >
                        Ver calendario <ChevronRight class="h-3 w-3" />
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>
