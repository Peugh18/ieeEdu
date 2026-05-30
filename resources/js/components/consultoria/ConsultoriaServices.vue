<script setup lang="ts">
defineProps<{
    areas: string[];
}>();
</script>

<template>
    <section class="py-16 md:py-24 bg-transparent">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-primary/40"></div>
                <span class="text-[11px] font-black text-primary tracking-[0.22em] uppercase">Portafolio de Servicios</span>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
                <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-on-surface leading-tight font-black">
                    ¿En qué podemos <em class="not-italic text-primary italic">ayudarte?</em>
                </h2>
                <a href="#solicitar" class="flex-shrink-0 group inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-primary hover:gap-3 transition-all">
                    Solicitar ahora
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="(area, i) in areas" :key="area" class="group bg-surface border border-outline-variant/15 p-6 rounded-[1.75rem] hover:border-primary/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between cursor-default">
                    <div class="flex items-start gap-4">
                        <span class="text-[11px] font-black text-primary bg-primary/5 w-6 h-6 rounded-lg flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-300">{{ String(i + 1).padStart(2, '0') }}</span>
                        <span class="text-sm font-serif italic font-bold text-on-surface-variant group-hover:text-on-surface transition-colors leading-snug flex-1">{{ area }}</span>
                    </div>
                    <div class="flex justify-end mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-1">
                            Solicitar
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
