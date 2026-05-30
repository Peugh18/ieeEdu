<script setup lang="ts">
import { Lightbulb } from 'lucide-vue-next';

defineProps<{
    insight: string;
}>();
</script>

<template>
    <article class="bg-primary rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 text-white flex gap-5 items-start group">
        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 group-hover:rotate-12 transition-transform">
            <Lightbulb class="w-6 h-6 text-primary-fixed" />
        </div>
        <div class="space-y-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-primary-fixed/60">Análisis Estratégico</p>
            <p class="text-[13px] font-serif italic leading-relaxed text-white/90">{{ insight }}</p>
        </div>
    </article>
</template>
