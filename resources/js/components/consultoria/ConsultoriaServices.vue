<script setup lang="ts">
defineProps<{
    areas: string[];
}>();
</script>

<template>
    <section class="bg-transparent py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-4 flex items-center gap-3">
                <div class="h-px w-8 bg-primary/40"></div>
                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-primary">Portafolio de Servicios</span>
            </div>
            <div class="mb-12 flex flex-col items-start justify-between gap-4 md:flex-row md:items-end">
                <h2 class="font-serif text-3xl font-black leading-tight text-on-surface md:text-4xl lg:text-5xl">
                    ¿En qué podemos <em class="italic not-italic text-primary">ayudarte?</em>
                </h2>
                <a
                    href="#solicitar"
                    class="group inline-flex flex-shrink-0 items-center gap-1.5 text-xs font-black uppercase tracking-widest text-primary transition-all hover:gap-3"
                >
                    Solicitar ahora
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(area, i) in areas"
                    :key="area"
                    class="group flex cursor-default flex-col justify-between rounded-[1.75rem] border border-outline-variant/15 bg-surface p-6 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-xl"
                >
                    <div class="flex items-start gap-4">
                        <span
                            class="flex h-6 w-6 items-center justify-center rounded-lg bg-primary/5 text-[11px] font-black text-primary transition-all duration-300 group-hover:bg-primary group-hover:text-white"
                            >{{ String(i + 1).padStart(2, '0') }}</span
                        >
                        <span
                            class="flex-1 font-serif text-sm font-bold italic leading-snug text-on-surface-variant transition-colors group-hover:text-on-surface"
                            >{{ area }}</span
                        >
                    </div>
                    <div class="mt-4 flex justify-end opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <span class="flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-primary">
                            Solicitar
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
