<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
    dbSlides?: Record<string, unknown>[];
}>();

// Slides por defecto (fallback si no hay datos en BD)
const defaultSlides = [
    {
        image: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1920&q=80',
        tag: 'Programas de Especialización',
        heading: 'Fortalece tus competencias profesionales',
        body: 'Cursos en vivo y grabados diseñados por expertos en economía, gestión pública, liderazgo y más — para el profesional peruano.',
        cta1: { text: 'Explorar Cursos', href: '/cursos' },
        cta2: { text: 'Ver Programas', href: '/cursos' },
    },
    {
        image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1920&q=80',
        tag: 'Primera Sesión Gratis',
        heading: 'Masterclasses en vivo con expertos',
        body: 'Sesiones exclusivas con líderes del sector empresarial y público. Tu primera masterclass es completamente gratuita.',
        cta1: { text: 'Ver Masterclasses', href: '/masterclass' },
        cta2: { text: 'Comenzar Gratis', href: '/masterclass' },
    },
    {
        image: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=1920&q=80',
        tag: 'Soluciones Corporativas',
        heading: 'Asesoría estratégica para tu empresa',
        body: 'Consultoría especializada para instituciones públicas y privadas. Diagnóstico, planificación y ejecución con expertos.',
        cta1: { text: 'Agendar Consultoría', href: '/consultoria' },
        cta2: { text: 'Ver Servicios', href: '/consultoria' },
    },
];

// Slides activos: si vienen de la BD con imagen o texto, los usamos; si no, fallback
const slides = computed(() => {
    if (props.dbSlides && props.dbSlides.length > 0 && props.dbSlides.some((s) => s.image_path || s.heading)) {
        return props.dbSlides
            .filter((s) => s.image_path || s.heading)
            .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
            .map((s) => ({
                image: s.image_path || defaultSlides[(s.order ?? 1) - 1]?.image || defaultSlides[0].image,
                tag: s.show_text ? (s.heading || 'IEE — Instituto de Economía y Empresa') : 'IEE — Instituto de Economía y Empresa',
                heading: s.heading || '',
                body: s.subheading || '',
                cta1: { text: s.button_text || 'Ver más', href: s.button_link || '/' },
                cta2: { text: 'Explorar', href: s.button_link || '/' },
            }));
    }
    return defaultSlides;
});

const current = ref(0);
const textReady = ref(true); // Empezamos en true para que no haya flash en blanco
const isPaused = ref(false);
const counters = ref({ pros: 0, years: 0, programs: 0 });
let timer: ReturnType<typeof setInterval> | null = null;

function countUp(target: number, key: 'pros' | 'years' | 'programs', ms = 2000) {
    const s = performance.now();
    const tick = (now: number) => {
        const p = Math.min((now - s) / ms, 1);
        counters.value[key] = Math.round((1 - Math.pow(1 - p, 3)) * target);
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

function goTo(i: number) {
    if (i === current.value) return;
    textReady.value = false;
    setTimeout(() => {
        current.value = i;
        requestAnimationFrame(() =>
            requestAnimationFrame(() => {
                textReady.value = true;
            }),
        );
    }, 280);
    restartTimer();
}

function next() {
    goTo((current.value + 1) % slides.value.length);
}
function prev() {
    goTo((current.value - 1 + slides.value.length) % slides.value.length);
}

function restartTimer() {
    if (timer) clearInterval(timer);
    timer = setInterval(() => {
        if (!isPaused.value) next();
    }, 6000);
}

onMounted(() => {
    requestAnimationFrame(() => {
        textReady.value = true;
    });
    restartTimer();
    setTimeout(() => {
        countUp(500, 'pros', 2200);
        countUp(15, 'years', 1600);
        countUp(25, 'programs', 1800);
    }, 800);
});
onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <section class="relative min-h-[92vh] overflow-hidden bg-surface md:min-h-screen" @mouseenter="isPaused = true" @mouseleave="isPaused = false">
        <!-- ── Background slides (crossfade + subtle Ken Burns) ── -->
        <div
            v-for="(slide, i) in slides"
            :key="i"
            :class="[
                'duration-[transition-duration:1200ms] absolute inset-0 transition-opacity ease-in-out',
                i === current ? 'opacity-100' : 'opacity-0',
            ]"
        >
            <img
                :src="slide.image"
                :alt="slide.tag"
                :class="[
                    'duration-[transition-duration:6000ms] h-full w-full object-cover transition-transform ease-linear',
                    i === current ? 'scale-110' : 'scale-100',
                ]"
                loading="lazy"
            />
        </div>

        <!-- ── Light scrim (lets photo breathe, just enough contrast) ── -->
        <div class="hero-scrim absolute inset-0 z-[1]"></div>

        <!-- ── Main layout ── -->
        <div class="relative z-10 flex min-h-[92vh] items-center md:min-h-screen">
            <div class="mx-auto w-full max-w-7xl px-4 pb-28 pt-24 sm:px-6 md:px-8 md:pb-36 md:pt-36">
                <!-- Glassmorphism content card -->
                <div class="hero-card relative max-w-xl overflow-hidden rounded-[1.75rem] p-7 sm:p-9 md:p-10">
                    <!-- Card accent line -->
                    <div
                        class="absolute left-8 right-8 top-0 h-[2px] rounded-full bg-gradient-to-r from-transparent via-primary/50 to-transparent"
                    ></div>

                    <!-- Tag -->
                    <div :class="['mb-5 transition-all duration-500', textReady ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0']">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-primary sm:text-[11px]"
                        >
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-primary"></span>
                            </span>
                            {{ slides[current].tag }}
                        </span>
                    </div>

                    <!-- Headline -->
                    <h1 class="mb-4 font-serif font-bold">
                        <span
                            :class="[
                                'block text-[2rem] leading-[1.1] text-on-surface transition-all duration-500 sm:text-4xl md:text-5xl lg:text-[3.5rem]',
                                textReady ? 'translate-y-0 opacity-100' : 'translate-y-5 opacity-0',
                            ]"
                        >
                            {{ slides[current].heading }}
                        </span>
                    </h1>

                    <!-- Body -->
                    <p
                        :class="[
                            'mb-7 max-w-md text-sm leading-relaxed text-on-surface-variant transition-all delay-150 duration-500 md:text-base',
                            textReady ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0',
                        ]"
                    >
                        {{ slides[current].body }}
                    </p>

                    <!-- CTAs -->
                    <div
                        :class="[
                            'flex flex-col gap-3 transition-all delay-200 duration-500 sm:flex-row',
                            textReady ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0',
                        ]"
                    >
                        <a
                            :href="slides[current].cta1.href"
                            class="hero-shimmer group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-xl bg-primary px-7 py-3.5 text-sm font-bold text-on-primary shadow-lg shadow-primary/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl"
                        >
                            <span class="relative z-10">{{ slides[current].cta1.text }}</span>
                            <svg
                                class="relative z-10 h-3.5 w-3.5 transition-transform group-hover:translate-x-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a
                            :href="slides[current].cta2.href"
                            class="hero-cta2 group inline-flex items-center justify-center gap-2 rounded-xl px-7 py-3.5 text-sm font-bold text-on-surface transition-all duration-300 hover:-translate-y-0.5"
                        >
                            {{ slides[current].cta2.text }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Nav arrows (left / right) ── -->
        <button
            @click="prev"
            class="hero-nav-btn absolute left-3 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full transition-all hover:scale-110 sm:left-5 md:left-8 md:flex"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button
            @click="next"
            class="hero-nav-btn absolute right-3 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full transition-all hover:scale-110 sm:right-5 md:right-8 md:flex"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- ── Floating bottom bar ── -->
        <div class="absolute bottom-5 left-4 right-4 z-20 sm:left-6 sm:right-6 md:left-8 md:right-8">
            <div class="hero-bottom-pill mx-auto flex max-w-7xl items-center justify-between gap-4 rounded-2xl px-5 py-3.5 sm:px-6">
                <!-- Left: dots + slide counter + progress -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <button
                            v-for="(_, i) in slides"
                            :key="i"
                            @click="goTo(i)"
                            :class="[
                                'rounded-full transition-all duration-300 focus:outline-none',
                                i === current ? 'h-1.5 w-7 bg-primary' : 'h-1.5 w-1.5 bg-on-surface-variant/25 hover:bg-on-surface-variant/50',
                            ]"
                        />
                    </div>
                    <span class="font-mono text-[11px] text-on-surface-variant/50"
                        >{{ String(current + 1).padStart(2, '0') }}<span class="mx-0.5 text-outline-variant/30">/</span
                        >{{ String(slides.length).padStart(2, '0') }}</span
                    >
                    <!-- Mini progress -->
                    <div class="hidden h-[2px] w-16 overflow-hidden rounded-full bg-outline-variant/10 sm:block">
                        <div :key="current" :class="['hero-progress h-full rounded-full bg-primary/60', isPaused ? 'paused' : '']"></div>
                    </div>
                </div>
                <!-- Right: stats -->
                <div class="hidden items-center gap-5 text-[11px] md:flex">
                    <div class="flex items-center gap-1.5">
                        <span class="font-serif text-sm font-bold tabular-nums text-on-surface">{{ counters.pros }}+</span>
                        <span class="uppercase tracking-wider text-on-surface-variant/50">Capacitados</span>
                    </div>
                    <div class="h-3.5 w-px bg-outline-variant/15"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-serif text-sm font-bold tabular-nums text-on-surface">{{ counters.years }}+</span>
                        <span class="uppercase tracking-wider text-on-surface-variant/50">Años</span>
                    </div>
                    <div class="h-3.5 w-px bg-outline-variant/15"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-serif text-sm font-bold tabular-nums text-on-surface">{{ counters.programs }}+</span>
                        <span class="uppercase tracking-wider text-on-surface-variant/50">Programas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ── Scrim: very light veil so photo stays vivid ── */
.hero-scrim {
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--elite-bg) 35%, transparent) 0%,
            transparent 45%,
            color-mix(in srgb, var(--elite-bg) 50%, transparent) 100%
        ),
        color-mix(in srgb, var(--elite-bg) 15%, transparent);
}

/* ── Glassmorphism content card ── */
.hero-card {
    background: color-mix(in srgb, var(--elite-bg) 72%, transparent);
    backdrop-filter: blur(28px) saturate(1.4);
    -webkit-backdrop-filter: blur(28px) saturate(1.4);
    border: 1px solid color-mix(in srgb, var(--elite-bg) 40%, transparent);
    box-shadow:
        0 8px 32px color-mix(in srgb, var(--elite-bg) 25%, transparent),
        inset 0 1px 0 color-mix(in srgb, var(--elite-bg) 50%, transparent);
}

/* ── Secondary CTA: frosted outline ── */
.hero-cta2 {
    background: color-mix(in srgb, var(--elite-bg) 50%, transparent);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid color-mix(in srgb, var(--elite-bg) 30%, transparent);
}
.hero-cta2:hover {
    background: color-mix(in srgb, var(--elite-bg) 65%, transparent);
    border-color: color-mix(in srgb, var(--elite-bg) 45%, transparent);
}

/* ── Nav buttons ── */
.hero-nav-btn {
    background: color-mix(in srgb, var(--elite-bg) 55%, transparent);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid color-mix(in srgb, var(--elite-bg) 30%, transparent);
    color: var(--elite-text);
}
.hero-nav-btn:hover {
    background: color-mix(in srgb, var(--elite-bg) 75%, transparent);
}

/* ── Bottom pill ── */
.hero-bottom-pill {
    background: color-mix(in srgb, var(--elite-bg) 65%, transparent);
    backdrop-filter: blur(20px) saturate(1.3);
    -webkit-backdrop-filter: blur(20px) saturate(1.3);
    border: 1px solid color-mix(in srgb, var(--elite-bg) 30%, transparent);
}

/* ── Shimmer on primary CTA ── */
.hero-shimmer::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        105deg,
        transparent 40%,
        rgba(255, 255, 255, 0.12) 45%,
        rgba(255, 255, 255, 0.22) 50%,
        rgba(255, 255, 255, 0.12) 55%,
        transparent 60%
    );
    background-size: 250% 100%;
    animation: shimmer 3.5s ease-in-out infinite;
}
@keyframes shimmer {
    0% {
        background-position: 200% center;
    }
    100% {
        background-position: -200% center;
    }
}

/* ── Progress bar ── */
.hero-progress {
    animation: progress-fill 6s linear forwards;
}
.hero-progress.paused {
    animation-play-state: paused;
}
@keyframes progress-fill {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}
</style>
