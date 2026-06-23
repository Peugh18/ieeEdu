<script setup lang="ts">
import type { ConsultoriaPaso } from '@/types/consultoria';

defineProps<{
    pasos: ConsultoriaPaso[];
}>();
</script>

<template>
    <section class="relative overflow-hidden bg-surface-container-low py-16 md:py-20">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/3 top-0 h-96 w-96 rounded-full bg-primary/[0.04] blur-[100px]"></div>
        </div>
        <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-4 flex items-center gap-3">
                <div class="h-px w-8 bg-primary/40"></div>
                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-primary">Metodología</span>
            </div>
            <h2 class="mb-12 font-serif text-3xl font-black text-on-surface md:mb-16 md:text-4xl">
                Cómo <em class="italic not-italic text-primary">trabajamos</em>
            </h2>
            <div class="grid gap-6 md:grid-cols-3">
                <div
                    v-for="paso in pasos"
                    :key="paso.n"
                    class="group relative flex flex-col justify-between rounded-[2rem] border border-outline-variant/15 bg-surface p-8 transition-all duration-300 hover:border-primary/20 hover:shadow-xl"
                >
                    <div>
                        <div class="mb-6 flex items-center gap-4">
                            <span
                                class="select-none font-serif text-5xl font-black tabular-nums leading-none text-primary/10 transition-colors group-hover:text-primary/20"
                                >{{ paso.n }}</span
                            >
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-primary/10 transition-all duration-300 group-hover:scale-105 group-hover:bg-primary/20"
                            >
                                <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="paso.svg"></svg>
                            </div>
                        </div>
                        <h3 class="mb-3 font-serif text-xl font-black italic text-on-surface transition-colors duration-300 group-hover:text-primary">
                            {{ paso.titulo }}
                        </h3>
                        <p class="text-xs leading-relaxed text-on-surface-variant/80">{{ paso.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
