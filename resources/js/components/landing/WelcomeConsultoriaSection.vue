<script setup lang="ts"></script>

<template>
    <section id="consultoria" class="reveal relative overflow-hidden bg-surface-container-low py-16 md:py-24">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-20 -top-20 h-[600px] w-[600px] rounded-full bg-primary/[0.05] blur-[140px]"></div>
        </div>
        <div class="relative z-10 mx-auto max-w-4xl px-6 text-center md:px-8">
            <div class="mb-8 flex items-center justify-center gap-3">
                <div class="h-px w-8 bg-primary/30"></div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Consultoría</span>
                <div class="h-px w-8 bg-primary/30"></div>
            </div>

            <h2 class="mb-4 font-serif text-3xl leading-tight text-on-surface md:text-5xl">
                ¿Tu organización necesita<br class="hidden md:block" />
                <span class="italic text-primary">soluciones estratégicas?</span>
            </h2>

            <p class="mx-auto mb-8 max-w-2xl text-base leading-relaxed text-on-surface-variant md:text-lg">
                Más de 15 años transformando entidades públicas y privadas del Perú con metodologías probadas.
            </p>

            <a
                href="/consultoria"
                class="group inline-flex items-center gap-3 rounded-xl bg-primary px-8 py-4 text-base font-bold text-on-primary shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl"
            >
                Solicitar consultoría
                <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </section>
</template>
