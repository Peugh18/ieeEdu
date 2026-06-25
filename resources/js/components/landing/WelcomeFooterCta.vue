<script setup lang="ts"></script>

<template>
    <section class="reveal relative overflow-hidden bg-surface-container-low">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/4 top-0 h-[500px] w-[500px] -translate-y-1/2 rounded-full bg-primary/[0.07] blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 h-[400px] w-[400px] translate-y-1/2 rounded-full bg-tertiary-container/[0.08] blur-[100px]"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-4xl px-6 py-20 text-center md:px-8 md:py-28">
            <div class="mb-8 inline-flex animate-pulse items-center gap-2 rounded-full border border-green-500/30 bg-green-500/10 px-4 py-2">
                <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                    />
                </svg>
                <span class="text-sm font-bold tracking-wide text-green-600">TU PRIMERA CLASE ES GRATIS</span>
            </div>

            <h2 class="mb-4 font-serif text-3xl font-bold leading-tight text-on-surface md:text-5xl">
                ¿Listo para seguir <br class="hidden md:block" />
                <span class="italic text-primary">aprendiendo?</span>
            </h2>

            <p class="mx-auto mb-8 max-w-lg text-base leading-relaxed text-on-surface-variant md:text-lg">
                Empieza hoy mismo — miles de profesionales del Perú ya confían en nosotros.
            </p>

            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a
                    href="/cursos"
                    class="group inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-8 py-4 text-base font-bold text-on-primary shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl"
                >
                    Empezar ahora
                    <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <a
                    href="/cursos"
                    class="group inline-flex items-center justify-center gap-2 rounded-xl border border-outline-variant/30 bg-surface-container px-8 py-4 text-base font-bold text-on-surface transition-all duration-300 hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                >
                    Ver cursos
                </a>
            </div>
        </div>
    </section>
</template>
