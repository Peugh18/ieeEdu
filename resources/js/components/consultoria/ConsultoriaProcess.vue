<script setup lang="ts">
import type { ConsultoriaPaso } from '@/types/consultoria';

defineProps<{
    pasos: ConsultoriaPaso[];
}>();
</script>

<template>
    <section class="py-16 md:py-20 bg-surface-container-low relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/3 w-96 h-96 bg-primary/[0.04] rounded-full blur-[100px]"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-primary/40"></div>
                <span class="text-[11px] font-black text-primary tracking-[0.22em] uppercase">Metodología</span>
            </div>
            <h2 class="font-serif text-3xl md:text-4xl text-on-surface mb-12 md:mb-16 font-black">
                Cómo <em class="not-italic text-primary italic">trabajamos</em>
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div v-for="paso in pasos" :key="paso.n" class="group bg-surface border border-outline-variant/15 p-8 rounded-[2rem] hover:shadow-xl hover:border-primary/20 transition-all duration-300 relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-6">
                            <span class="font-serif text-5xl font-black text-primary/10 leading-none select-none tabular-nums group-hover:text-primary/20 transition-colors">{{ paso.n }}</span>
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 group-hover:scale-105 transition-all duration-300">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="paso.svg"></svg>
                            </div>
                        </div>
                        <h3 class="font-serif font-black text-on-surface text-xl mb-3 group-hover:text-primary transition-colors duration-300 italic">{{ paso.titulo }}</h3>
                        <p class="text-on-surface-variant/80 text-xs leading-relaxed">{{ paso.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
