<script setup lang="ts">
import Navigation from '@/components/landing/Navigation.vue';
import WelcomeFooter from '@/components/landing/WelcomeFooter.vue';
import WelcomeFooterCta from '@/components/landing/WelcomeFooterCta.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, Briefcase, GraduationCap, Lightbulb, ShieldCheck, Target, TrendingUp, Users } from 'lucide-vue-next';
import { onMounted, onUnmounted } from 'vue';

let revealObserver: IntersectionObserver | null = null;
let staggerObs: IntersectionObserver | null = null;

onMounted(() => {
    revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    revealObserver?.unobserve(e.target);
                }
            });
        },
        { threshold: 0.08, rootMargin: '0px 0px -40px 0px' },
    );
    document.querySelectorAll('.reveal').forEach((el) => revealObserver?.observe(el));

    staggerObs = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    const children = e.target.querySelectorAll(':scope > *');
                    children.forEach((child, i) => {
                        (child as HTMLElement).style.transitionDelay = `${i * 100}ms`;
                        child.classList.add('revealed');
                    });
                    staggerObs?.unobserve(e.target);
                }
            });
        },
        { threshold: 0.05 },
    );
    document.querySelectorAll('.reveal-stagger').forEach((el) => staggerObs?.observe(el));
});

onUnmounted(() => {
    revealObserver?.disconnect();
    staggerObs?.disconnect();
});
</script>

<template>
    <Head>
        <title>Nosotros | IEE - Instituto de Economía y Empresa</title>
        <meta name="description" content="Transformando la gestión empresarial y pública en el Perú desde 2009." />
    </Head>

    <div class="font-body flex min-h-screen flex-col bg-surface text-on-surface selection:bg-primary-container selection:text-on-primary-container">
        <Navigation />

        <main class="flex-1 pt-24 md:pt-32">
            <!-- Hero Section -->
            <section class="relative overflow-hidden px-6 py-16 md:px-8 md:py-24">
                <!-- Background decoration -->
                <div class="absolute inset-0 z-0 bg-surface-container-lowest opacity-50 dark:opacity-5"></div>
                <div class="absolute -right-40 -top-40 z-0 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>

                <div class="relative z-10 mx-auto max-w-4xl text-center">
                    <span
                        class="reveal mb-4 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-bold tracking-widest text-primary dark:bg-primary/20"
                    >
                        NUESTRA ESENCIA
                    </span>
                    <h1 class="reveal font-serif text-4xl font-bold leading-tight text-on-surface md:text-5xl lg:text-6xl">
                        Transformando la gestión empresarial y pública en el Perú.
                    </h1>
                    <p class="reveal mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-on-surface-variant md:text-xl">
                        Desde 2009, el <strong class="font-semibold text-primary">Instituto de Economía y Empresa (IEE)</strong> cierra la brecha
                        entre el conocimiento técnico y la toma de decisiones estratégicas.
                    </p>
                </div>
            </section>

            <!-- Historia Section -->
            <section class="bg-surface-container-low px-6 py-16 dark:bg-[#1f201d] md:px-8 md:py-24">
                <div class="mx-auto max-w-7xl">
                    <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                        <div class="reveal">
                            <h2 class="mb-6 font-serif text-3xl font-bold text-on-surface md:text-4xl">
                                Nacimos para generar soluciones basadas en evidencia.
                            </h2>
                            <div class="space-y-6 text-lg leading-relaxed text-on-surface-variant">
                                <p>
                                    El IEE surgió en la ciudad de Trujillo como una respuesta directa a los desafíos de nuestro país: altos niveles de
                                    informalidad, baja productividad y limitaciones en la gestión institucional.
                                </p>
                                <p>
                                    Nuestro propósito siempre ha sido claro: brindar soluciones prácticas y aplicables que contribuyan al desarrollo
                                    económico y empresarial del Perú.
                                </p>
                                <p>
                                    Hoy, nos consolidamos como un referente nacional en el diseño de soluciones para el sector productivo y público.
                                </p>
                            </div>
                        </div>
                        <div class="reveal relative">
                            <!-- Placeholder for an image, could be an office shot or team -->
                            <div
                                class="relative flex aspect-video w-full items-center justify-center overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-variant dark:border-slate-700/30"
                            >
                                <span class="material-symbols-outlined text-6xl text-outline-variant opacity-50" translate="no">location_city</span>
                                <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 to-transparent"></div>
                            </div>
                            <!-- Small floating card -->
                            <div
                                class="absolute -bottom-6 -left-6 rounded-xl border border-outline-variant/10 bg-surface p-6 shadow-xl dark:border-slate-700/50"
                            >
                                <div class="mb-1 text-3xl font-bold text-primary">15+</div>
                                <div class="text-sm font-medium uppercase tracking-wider text-on-surface-variant">Años de experiencia</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Propósito (Misión y Visión) -->
            <section class="px-6 py-16 md:px-8 md:py-24">
                <div class="mx-auto max-w-7xl">
                    <div class="reveal-stagger grid gap-8 md:grid-cols-2">
                        <!-- Mision -->
                        <div
                            class="rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm transition-colors duration-300 hover:border-primary/30 dark:border-slate-700/30 dark:bg-[#191b18]"
                        >
                            <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary dark:bg-primary/20">
                                <Target class="h-7 w-7" />
                            </div>
                            <h3 class="mb-4 font-serif text-2xl font-bold text-on-surface">Nuestra Misión</h3>
                            <p class="text-lg leading-relaxed text-on-surface-variant">
                                Contribuir a la generación de valor para el desarrollo empresarial, económico y social, mediante servicios de
                                consultoría, formación de competencias y producción de conocimiento técnico de alta calidad, orientados a resultados,
                                ética y sostenibilidad.
                            </p>
                        </div>

                        <!-- Vision -->
                        <div
                            class="rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm transition-colors duration-300 hover:border-primary/30 dark:border-slate-700/30 dark:bg-[#191b18]"
                        >
                            <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary dark:bg-primary/20">
                                <Lightbulb class="h-7 w-7" />
                            </div>
                            <h3 class="mb-4 font-serif text-2xl font-bold text-on-surface">Nuestra Visión</h3>
                            <p class="text-lg leading-relaxed text-on-surface-variant">
                                Ser una consultora de alcance nacional reconocida por su solvencia técnica, impacto institucional y contribución al
                                desarrollo económico empresarial y social del país.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Valores -->
            <section class="bg-surface-container-low px-6 py-16 dark:bg-[#1f201d] md:px-8 md:py-24">
                <div class="mx-auto max-w-7xl text-center">
                    <h2 class="reveal mb-12 font-serif text-3xl font-bold text-on-surface md:text-4xl">Los valores que guían nuestro trabajo</h2>

                    <div class="reveal-stagger grid grid-cols-2 gap-6 md:grid-cols-3 lg:gap-8">
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <Users class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Liderazgo</h4>
                            <p class="text-sm text-on-surface-variant">Guiamos el cambio con visión clara.</p>
                        </div>
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <Users class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Trabajo en Equipo</h4>
                            <p class="text-sm text-on-surface-variant">Sumamos talentos y esfuerzos.</p>
                        </div>
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <Lightbulb class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Innovación</h4>
                            <p class="text-sm text-on-surface-variant">Creamos soluciones modernas y ágiles.</p>
                        </div>
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <TrendingUp class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Competitividad</h4>
                            <p class="text-sm text-on-surface-variant">Buscamos estándares de excelencia.</p>
                        </div>
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <Users class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Equidad</h4>
                            <p class="text-sm text-on-surface-variant">Oportunidades y trato justo para todos.</p>
                        </div>
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface p-6 text-center dark:border-slate-700/30"
                        >
                            <ShieldCheck class="mb-4 h-8 w-8 text-primary" />
                            <h4 class="mb-2 font-bold text-on-surface">Ética</h4>
                            <p class="text-sm text-on-surface-variant">Transparencia e integridad total.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lineas de accion -->
            <section class="px-6 py-16 md:px-8 md:py-24">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-16 text-center">
                        <span
                            class="reveal mb-3 inline-block rounded-full bg-primary/10 px-3 py-1 text-xs font-bold tracking-widest text-primary dark:bg-primary/20"
                            >NUESTROS PILARES</span
                        >
                        <h2 class="reveal font-serif text-3xl font-bold text-on-surface md:text-4xl">¿Cómo impulsamos el desarrollo?</h2>
                    </div>

                    <div class="reveal-stagger grid gap-8 md:grid-cols-3">
                        <!-- Tarjeta 1 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm transition-all duration-300 hover:shadow-md dark:border-slate-700/30 dark:bg-[#191b18]"
                        >
                            <div
                                class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-transform group-hover:scale-110 dark:bg-primary/20"
                            >
                                <GraduationCap class="h-7 w-7" />
                            </div>
                            <h3 class="mb-3 font-serif text-xl font-bold text-on-surface">Formación Ejecutiva</h3>
                            <p class="mb-8 flex-1 leading-relaxed text-on-surface-variant">
                                Programas, diplomados y cursos especializados (Gestión Empresarial, Finanzas, IA y Sector Público) diseñados para
                                fortalecer capacidades en líderes y funcionarios.
                            </p>
                            <Link
                                :href="route('cursos.index')"
                                class="inline-flex items-center gap-2 font-bold text-primary transition-colors group-hover:text-primary/80"
                            >
                                Ver Catálogo de Cursos <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>

                        <!-- Tarjeta 2 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm transition-all duration-300 hover:shadow-md dark:border-slate-700/30 dark:bg-[#191b18]"
                        >
                            <div
                                class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-transform group-hover:scale-110 dark:bg-primary/20"
                            >
                                <Briefcase class="h-7 w-7" />
                            </div>
                            <h3 class="mb-3 font-serif text-xl font-bold text-on-surface">Consultoría Especializada</h3>
                            <p class="mb-8 flex-1 leading-relaxed text-on-surface-variant">
                                Brindamos soluciones integrales en planeamiento, transformación digital, estructuración de proyectos (APP, OxI) y
                                mejora continua.
                            </p>
                            <Link
                                :href="route('consultoria')"
                                class="inline-flex items-center gap-2 font-bold text-primary transition-colors group-hover:text-primary/80"
                            >
                                Solicitar Consultoría <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>

                        <!-- Tarjeta 3 -->
                        <div
                            class="group flex flex-col rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-8 shadow-sm transition-all duration-300 hover:shadow-md dark:border-slate-700/30 dark:bg-[#191b18]"
                        >
                            <div
                                class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-transform group-hover:scale-110 dark:bg-primary/20"
                            >
                                <BookOpen class="h-7 w-7" />
                            </div>
                            <h3 class="mb-3 font-serif text-xl font-bold text-on-surface">Generación de Conocimiento</h3>
                            <p class="mb-8 flex-1 leading-relaxed text-on-surface-variant">
                                Análisis de la realidad económica y productiva a través de nuestros libros y boletines especializados (como
                                "Desarrollo en Acción").
                            </p>
                            <Link
                                :href="route('publicaciones.index')"
                                class="inline-flex items-center gap-2 font-bold text-primary transition-colors group-hover:text-primary/80"
                            >
                                Explorar Publicaciones <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <div class="section-divider"></div>
            <WelcomeFooterCta />
        </main>

        <WelcomeFooter />
    </div>
</template>

<style scoped>
/* Scroll reveal */
.reveal {
    opacity: 0;
    transform: translateY(36px);
    transition:
        opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger children */
.reveal-stagger > * {
    opacity: 0;
    transform: translateY(24px);
    transition:
        opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal-stagger > .revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Section dividers */
.section-divider {
    height: 1px;
    background: linear-gradient(
        to right,
        transparent,
        var(--md-sys-color-outline-variant, #c4c7c5) 20%,
        var(--md-sys-color-outline-variant, #c4c7c5) 80%,
        transparent
    );
    opacity: 0.15;
    max-width: 80rem;
    margin: 0 auto;
}
</style>
