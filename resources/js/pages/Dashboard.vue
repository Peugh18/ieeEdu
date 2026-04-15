<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type SharedData, type User } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen, Calendar, ClipboardCheck, Award, Clock, ArrowRight,
    Play, CheckCircle2, ChevronRight, BarChart3, TrendingUp, X,
    ExternalLink, Crown, Flame, Zap, Star, GraduationCap, Wifi,
    Radio, Timer, Lock, Sparkles, ChevronUp, AlertCircle
} from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted } from 'vue';

interface SummaryStats {
    active_courses: number;
    completed_courses: number;
    upcoming_classes: number;
    available_exams: number;
    average_score: number;
    certificate_count: number;
}

interface ContinueLearning {
    course_title: string;
    course_slug: string;
    lesson_title: string;
    lesson_id: number;
    progress: number;
    image: string;
}

interface Recommendation {
    id: number;
    title: string;
    slug: string;
    image: string;
    category: { name: string };
}

interface Certificate {
    id: number;
    course_title: string;
    issue_date: string;
    code: string;
    download_url: string;
}

interface NextLiveClass {
    id: number;
    title: string;
    course_title: string;
    start_time: string;
    start_time_human: string;
    course_slug: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mi Aula Virtual', href: '/dashboard' },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User & { has_subscription?: boolean };

const props = defineProps<{
    stats: SummaryStats;
    continueLearning: ContinueLearning | null;
    recommendations: Recommendation[];
    certificates: Certificate[];
    nextLiveClass: NextLiveClass | null;
}>();

// ─── Live Modal ───────────────────────────────────────────────────────────────
const showLiveModal = ref(false);

// ─── Live Countdown Timer ─────────────────────────────────────────────────────
const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0, isLive: false, isPast: false });
let countdownInterval: ReturnType<typeof setInterval> | null = null;

function updateCountdown() {
    if (!props.nextLiveClass) return;
    const target = new Date(props.nextLiveClass.start_time).getTime();
    const now = Date.now();
    const diff = target - now;

    if (diff <= 0 && diff > -5400000) { // within 90 min window = is live
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

// ─── Stats counter animation ──────────────────────────────────────────────────
const animatedStats = ref({
    active_courses: 0,
    completed_courses: 0,
    upcoming_classes: 0,
    available_exams: 0,
    average_score: 0,
    certificate_count: 0,
});

function animateNumber(key: keyof typeof animatedStats.value, target: number, duration = 1200) {
    const start = Date.now();
    const startVal = 0;
    const tick = () => {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        animatedStats.value[key] = Math.round(startVal + (target - startVal) * ease) as any;
        if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

// ─── Greeting based on time ───────────────────────────────────────────────────
const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return '¡Buenos días';
    if (h < 18) return '¡Buenas tardes';
    return '¡Buenas noches';
});

// ─── Progress ring calculation ────────────────────────────────────────────────
function progressCircle(pct: number, r = 28) {
    const circumference = 2 * Math.PI * r;
    const offset = circumference - (pct / 100) * circumference;
    return { circumference, offset };
}

const ring = computed(() => {
    const pct = props.continueLearning?.progress ?? 0;
    return progressCircle(pct);
});

// ─── Motivational message based on progress ───────────────────────────────────
const motivationalMessage = computed(() => {
    const pct = props.continueLearning?.progress ?? 0;
    if (pct === 0) return '¡Comienza hoy tu aprendizaje!';
    if (pct < 25) return 'Excelente inicio, sigue adelante';
    if (pct < 50) return 'Vas por buen camino, continúa';
    if (pct < 75) return '¡Más de la mitad! Eres imparable';
    if (pct < 100) return '¡Casi lo logras! Un esfuerzo más';
    return '¡Curso completado! Descarga tu certificado';
});

onMounted(() => {
    // Animate stats
    setTimeout(() => {
        animateNumber('active_courses', props.stats.active_courses);
        animateNumber('completed_courses', props.stats.completed_courses, 1400);
        animateNumber('upcoming_classes', props.stats.upcoming_classes, 1100);
        animateNumber('available_exams', props.stats.available_exams, 1300);
        animateNumber('average_score', props.stats.average_score, 1600);
        animateNumber('certificate_count', props.stats.certificate_count, 1500);
    }, 300);

    // Live countdown
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
    <Head title="Aula Virtual - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50/30 dark:from-[#141410] dark:via-on-background dark:to-on-background pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

                <!-- ══════════ 1. HERO HEADER ══════════ -->
                <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#2C2C15] via-[#3d3d1e] to-[#1a1a0a] p-8 md:p-12 shadow-2xl">
                    <!-- Grid pattern background -->
                    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

                    <!-- Glow orbs -->
                    <div class="absolute -top-20 -right-20 w-80 h-80 bg-[#D4AF37]/10 rounded-full blur-[80px]"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-500/5 rounded-full blur-[80px]"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <!-- Left: Greeting -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/10 rounded-full px-4 py-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-white/70 uppercase tracking-[0.2em]">Instituto de Economía y Empresa</span>
                                </div>
                                <!-- Subscription badge -->
                                <div v-if="user.has_subscription"
                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-amber-600 text-white text-[10px] font-bold uppercase tracking-[0.2em] px-4 py-1.5 rounded-full shadow-lg shadow-amber-500/30">
                                    <Crown class="w-3 h-3" />
                                    Miembro Premium
                                </div>
                            </div>

                            <div>
                                <p class="text-white/50 text-sm font-medium mb-1">{{ greeting }},</p>
                                <h1 class="text-4xl md:text-5xl font-serif font-bold text-white leading-tight">
                                    {{ user.name.split(' ')[0] }}
                                    <span class="text-[#D4AF37]">!</span>
                                </h1>
                                <p class="text-white/40 font-serif text-base mt-2 italic">
                                    Continúa tu camino hacia la excelencia profesional.
                                </p>
                            </div>

                            <!-- Mini stats row -->
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

                        <!-- Right: Live Class Widget -->
                        <div class="lg:w-80 flex-shrink-0">
                            <!-- LIVE NOW -->
                            <div v-if="nextLiveClass && countdown.isLive"
                                class="bg-gradient-to-br from-red-600/90 to-rose-700/90 backdrop-blur-md border border-red-500/30 rounded-2xl p-6 space-y-4 shadow-xl shadow-red-900/30 cursor-pointer"
                                @click="showLiveModal = true">
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

                            <!-- UPCOMING LIVE CLASS with countdown -->
                            <div v-else-if="nextLiveClass && !countdown.isPast"
                                class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-6 space-y-4 cursor-pointer hover:bg-white/15 transition-colors"
                                @click="showLiveModal = true">
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
                                <!-- Countdown -->
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

                            <!-- NO LIVE CLASSES -->
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

                <!-- ══════════ 2. STATS STRIP ══════════ -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Active Courses -->
                    <Link :href="route('student.courses.index')"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:shadow-blue-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">
                        <div class="p-3 bg-blue-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
                            <BookOpen class="w-6 h-6 text-blue-500" />
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900 leading-none">{{ animatedStats.active_courses }}</p>
                            <p class="text-xs text-gray-400 mt-1 font-medium">Cursos activos</p>
                        </div>
                        <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-blue-400 ml-auto transition-colors" />
                    </Link>

                    <!-- Upcoming Classes -->
                    <Link :href="route('student.live-classes.index')"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:shadow-amber-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">
                        <div class="p-3 bg-amber-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
                            <Calendar class="w-6 h-6 text-amber-500" />
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900 leading-none">{{ animatedStats.upcoming_classes }}</p>
                            <p class="text-xs text-gray-400 mt-1 font-medium">Clases próximas</p>
                        </div>
                        <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-amber-400 ml-auto transition-colors" />
                    </Link>

                    <!-- Available Exams -->
                    <Link :href="route('student.exams.index')"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:shadow-purple-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">
                        <div class="p-3 bg-purple-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
                            <ClipboardCheck class="w-6 h-6 text-purple-500" />
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900 leading-none">{{ animatedStats.available_exams }}</p>
                            <p class="text-xs text-gray-400 mt-1 font-medium">Evaluaciones</p>
                        </div>
                        <ChevronRight class="w-4 h-4 text-gray-200 group-hover:text-purple-400 ml-auto transition-colors" />
                    </Link>

                    <!-- Certificates + Score -->
                    <Link :href="route('student.certificates.index')"
                        class="group bg-gradient-to-br from-primary to-[#3d3d1e] rounded-2xl p-6 shadow-sm hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">
                        <div class="p-3 bg-white/10 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
                            <Award class="w-6 h-6 text-[#D4AF37]" />
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-white leading-none">{{ animatedStats.certificate_count }}</p>
                            <p class="text-xs text-white/50 mt-1 font-medium">Certificados</p>
                        </div>
                        <ChevronRight class="w-4 h-4 text-white/20 group-hover:text-[#D4AF37] ml-auto transition-colors" />
                    </Link>
                </section>

                <!-- ══════════ 3. MAIN CONTENT GRID ══════════ -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ── LEFT COLUMN (2/3) ── -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- CONTINUE LEARNING CARD -->
                        <div v-if="continueLearning">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <Flame class="w-5 h-5 text-orange-500" />
                                    Continuar aprendiendo
                                </h2>
                                <Link :href="route('student.courses.index')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
                                    Todos mis cursos <ArrowRight class="w-3 h-3" />
                                </Link>
                            </div>

                            <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group">
                                <div class="flex flex-col md:flex-row">
                                    <!-- Course Image + progress ring -->
                                    <div class="relative md:w-64 h-52 md:h-auto overflow-hidden flex-shrink-0 bg-gray-900">
                                        <img
                                            v-if="continueLearning.image"
                                            :src="continueLearning.image"
                                            :alt="continueLearning.course_title"
                                            class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700"
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/60 md:from-transparent md:to-gray-900/20 to-transparent"></div>

                                        <!-- Floating progress ring -->
                                        <div class="absolute bottom-4 left-4 md:bottom-auto md:top-1/2 md:-right-10 md:left-auto md:-translate-y-1/2 z-10">
                                            <div class="relative w-20 h-20 flex items-center justify-center">
                                                <svg class="w-20 h-20 -rotate-90" viewBox="0 0 72 72">
                                                    <circle cx="36" cy="36" r="28" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="4" />
                                                    <circle cx="36" cy="36" r="28" fill="none" stroke="#D4AF37" stroke-width="4"
                                                        stroke-linecap="round"
                                                        :stroke-dasharray="ring.circumference"
                                                        :stroke-dashoffset="ring.offset"
                                                        style="transition: stroke-dashoffset 1.5s ease"
                                                    />
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <span class="text-sm font-bold text-white">{{ continueLearning.progress }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 p-8 flex flex-col justify-between">
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 border border-orange-100 rounded-full">
                                                    <Flame class="w-3 h-3 text-orange-500" />
                                                    <span class="text-[10px] font-bold text-orange-600 uppercase tracking-widest">En progreso</span>
                                                </span>
                                            </div>
                                            <h3 class="text-2xl font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors">
                                                {{ continueLearning.course_title }}
                                            </h3>
                                            <p class="text-sm text-gray-500 flex items-center gap-2">
                                                <Play class="w-3.5 h-3.5 text-[#D4AF37] fill-current flex-shrink-0" />
                                                Última lección: <span class="font-semibold text-gray-700">{{ continueLearning.lesson_title }}</span>
                                            </p>
                                        </div>

                                        <div class="space-y-4 mt-6">
                                            <!-- Progress bar -->
                                            <div class="space-y-1.5">
                                                <div class="flex justify-between items-center">
                                                    <p class="text-xs text-gray-400 font-medium">{{ motivationalMessage }}</p>
                                                    <span class="text-xs font-bold text-primary">{{ continueLearning.progress }}%</span>
                                                </div>
                                                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                                    <div
                                                        class="h-full bg-gradient-to-r from-primary to-[#D4AF37] rounded-full transition-all duration-1000"
                                                        :style="{ width: continueLearning.progress + '%' }"
                                                    ></div>
                                                </div>
                                            </div>

                                            <Link
                                                :href="route('student.classroom', { course: continueLearning.course_slug })"
                                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary hover:bg-[#4a4a24] text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 active:scale-95 group/btn"
                                            >
                                                <Play class="w-4 h-4 fill-current" />
                                                Continuar curso
                                                <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EMPTY STATE (no courses) -->
                        <div v-else-if="stats.active_courses === 0"
                            class="bg-gradient-to-br from-primary/5 to-[#D4AF37]/5 border-2 border-dashed border-primary/15 rounded-3xl p-12 text-center space-y-6">
                            <div class="inline-flex p-6 bg-white rounded-3xl shadow-sm mb-2 ring-8 ring-primary/5">
                                <GraduationCap class="w-12 h-12 text-primary" />
                            </div>
                            <div class="space-y-3">
                                <h2 class="text-2xl font-bold text-gray-900">Empieza tu camino académico</h2>
                                <p class="text-gray-500 max-w-lg mx-auto text-sm leading-relaxed">
                                    Aún no tienes cursos en progreso. Explora nuestro catálogo y comienza a especializarte con los mejores expertos del sector.
                                </p>
                            </div>
                            <Link :href="route('student.explore.courses')"
                                class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-xl hover:shadow-primary/30 active:scale-95 transition-all">
                                <Sparkles class="w-4 h-4" />
                                Explorar cursos
                            </Link>
                        </div>

                        <!-- CERTIFICATES SECTION -->
                        <div v-if="certificates.length > 0" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <Award class="w-5 h-5 text-[#D4AF37]" />
                                    Mis certificados
                                    <span class="text-sm font-normal text-gray-400">({{ certificates.length }})</span>
                                </h2>
                                <Link :href="route('student.certificates.index')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
                                    Ver todos <ArrowRight class="w-3 h-3" />
                                </Link>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-for="cert in certificates" :key="cert.id"
                                    class="group bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 hover:border-[#D4AF37]/40 hover:shadow-md transition-all">
                                    <div class="p-3 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl group-hover:scale-110 transition-transform flex-shrink-0">
                                        <Award class="w-6 h-6 text-[#D4AF37]" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 truncate">{{ cert.course_title }}</h4>
                                        <p class="text-xs text-gray-400 mt-0.5">Emitido el {{ cert.issue_date }}</p>
                                    </div>
                                    <a :href="cert.download_url" target="_blank"
                                        class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-sm flex-shrink-0">
                                        <ExternalLink class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- RECOMMENDATIONS -->
                        <div v-if="recommendations.length > 0" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <Sparkles class="w-5 h-5 text-primary" />
                                    Recomendado para ti
                                </h2>
                                <Link :href="route('student.explore.courses')" class="text-[11px] text-primary font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
                                    Explorar catálogo <ArrowRight class="w-3 h-3" />
                                </Link>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <article v-for="rec in recommendations" :key="rec.id"
                                    class="bg-white rounded-2xl border border-gray-100 overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-400 flex flex-col">
                                    <div class="relative h-36 overflow-hidden bg-gray-100">
                                        <img v-if="rec.image" :src="rec.image" :alt="rec.title"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent"></div>
                                        <div class="absolute bottom-3 left-3">
                                            <span class="px-2.5 py-1 bg-white/90 backdrop-blur-md text-[9px] font-bold text-primary uppercase tracking-widest rounded-full">
                                                {{ rec.category?.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col flex-1">
                                        <h4 class="text-sm font-bold text-gray-900 mb-4 line-clamp-2 leading-snug group-hover:text-primary transition-colors">
                                            {{ rec.title }}
                                        </h4>
                                        <Link :href="route('cursos.show', { slug: rec.slug, dashboard: true })"
                                            class="mt-auto inline-flex items-center gap-1.5 text-[10px] font-bold text-primary uppercase tracking-widest hover:gap-2.5 transition-all">
                                            Ver detalles <ArrowRight class="w-3 h-3" />
                                        </Link>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- ── RIGHT COLUMN (1/3) ── -->
                    <aside class="space-y-5">

                        <!-- ACADEMIC PERFORMANCE CARD -->
                        <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                            <div class="absolute -top-16 -right-16 w-48 h-48 bg-[#D4AF37]/10 rounded-full blur-[60px]"></div>
                            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-blue-500/5 rounded-full blur-[60px]"></div>

                            <div class="relative z-10 space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="p-3 bg-white/10 rounded-2xl">
                                        <BarChart3 class="w-5 h-5 text-[#D4AF37]" />
                                    </div>
                                    <span class="px-3 py-1 bg-emerald-500/15 text-emerald-400 text-[9px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20 flex items-center gap-1.5">
                                        <TrendingUp class="w-3 h-3" /> Rendimiento
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-white mb-1">Desempeño Académico</h3>
                                    <p class="text-white/40 text-xs">Tu historial de evaluaciones y logros.</p>
                                </div>

                                <!-- Score + Certificates -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white/5 rounded-2xl p-4 text-center">
                                        <p class="text-3xl font-bold text-white">
                                            {{ stats.average_score > 0 ? stats.average_score : '--' }}
                                        </p>
                                        <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Prom. Exámenes</p>
                                    </div>
                                    <div class="bg-white/5 rounded-2xl p-4 text-center">
                                        <p class="text-3xl font-bold text-[#D4AF37]">
                                            {{ animatedStats.certificate_count }}
                                        </p>
                                        <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Diplomas</p>
                                    </div>
                                </div>

                                <!-- Performance bar (score visual) -->
                                <div v-if="stats.average_score > 0" class="space-y-2">
                                    <div class="flex justify-between text-[10px] text-white/40 uppercase tracking-widest">
                                        <span>Puntuación promedio</span>
                                        <span>{{ stats.average_score }}/20</span>
                                    </div>
                                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-[#D4AF37] to-amber-300 rounded-full transition-all duration-1000"
                                            :style="{ width: (stats.average_score / 20 * 100) + '%' }"></div>
                                    </div>
                                    <p class="text-[10px] text-emerald-400 flex items-center gap-1" v-if="stats.average_score >= 14">
                                        <CheckCircle2 class="w-3 h-3" /> Rendimiento aprobatorio
                                    </p>
                                    <p class="text-[10px] text-amber-400 flex items-center gap-1" v-else>
                                        <AlertCircle class="w-3 h-3" /> Sigue esforzándote
                                    </p>
                                </div>

                                <Link :href="route('student.certificates.index')"
                                    class="block w-full py-3.5 bg-white text-gray-900 text-xs text-center font-bold uppercase tracking-widest rounded-xl hover:bg-[#D4AF37] hover:text-white transition-all shadow-xl active:scale-95">
                                    Ver mis certificados
                                </Link>
                            </div>
                        </div>

                        <!-- SUBSCRIPTION STATUS -->
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

                        <!-- QUICK LINKS -->
                        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-3">
                            <h3 class="text-sm font-bold text-gray-900 mb-4">Accesos rápidos</h3>

                            <Link :href="route('student.exams.index')"
                                class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-gray-50 transition-colors group">
                                <div class="p-2 bg-purple-50 rounded-xl group-hover:scale-110 transition-transform">
                                    <ClipboardCheck class="w-4 h-4 text-purple-500" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Mis evaluaciones</p>
                                    <p class="text-[10px] text-gray-400">{{ stats.available_exams }} disponibles</p>
                                </div>
                                <ChevronRight class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                            </Link>

                            <Link :href="route('student.live-classes.index')"
                                class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-gray-50 transition-colors group">
                                <div class="p-2 bg-amber-50 rounded-xl group-hover:scale-110 transition-transform">
                                    <Calendar class="w-4 h-4 text-amber-500" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Clases en vivo</p>
                                    <p class="text-[10px] text-gray-400">{{ stats.upcoming_classes }} próximas</p>
                                </div>
                                <ChevronRight class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                            </Link>

                            <Link :href="route('student.explore.publications')"
                                class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-gray-50 transition-colors group">
                                <div class="p-2 bg-blue-50 rounded-xl group-hover:scale-110 transition-transform">
                                    <BookOpen class="w-4 h-4 text-blue-500" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Publicaciones</p>
                                    <p class="text-[10px] text-gray-400">Libros y artículos</p>
                                </div>
                                <ChevronRight class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                            </Link>

                            <Link :href="route('student.explore.masterclasses')"
                                class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-gray-50 transition-colors group">
                                <div class="p-2 bg-rose-50 rounded-xl group-hover:scale-110 transition-transform">
                                    <Star class="w-4 h-4 text-rose-500" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Masterclasses</p>
                                    <p class="text-[10px] text-gray-400">Sesiones especializadas</p>
                                </div>
                                <ChevronRight class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                            </Link>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <!-- ══════════ LIVE CLASS MODAL ══════════ -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showLiveModal && nextLiveClass"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                    @click.self="showLiveModal = false">
                    <Transition
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="scale-95 opacity-0 translate-y-4"
                        enter-to-class="scale-100 opacity-100 translate-y-0"
                    >
                        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden">
                            <!-- Modal Header -->
                            <div class="relative bg-gradient-to-br from-[#2C2C15] to-[#1a1a0a] p-8 text-white overflow-hidden">
                                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E')"></div>
                                <div class="absolute top-0 right-0 w-40 h-40 bg-[#D4AF37]/10 rounded-full blur-[60px]"></div>

                                <button @click="showLiveModal = false"
                                    class="absolute top-5 right-5 p-2 bg-white/10 hover:bg-white/20 rounded-xl text-white transition-colors">
                                    <X class="w-5 h-5" />
                                </button>

                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span v-if="countdown.isLive" class="flex items-center gap-1.5 px-3 py-1 bg-red-500/20 border border-red-500/30 rounded-full">
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

                            <!-- Modal Content -->
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
                                    <button @click="showLiveModal = false"
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
    </AppLayout>
</template>

<style scoped>
/* smooth transitions for animated stats */
.transition-all {
    transition-property: all;
}
</style>
