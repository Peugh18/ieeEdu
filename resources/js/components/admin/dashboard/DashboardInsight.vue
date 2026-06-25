<script setup lang="ts">
import { Lightbulb } from 'lucide-vue-next';

defineProps<{
    insight: string;
}>();
</script>

<template>
    <article class="group flex items-start gap-5 rounded-[2rem] bg-primary p-6 text-white md:rounded-[2.5rem] md:p-8">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10 transition-transform group-hover:rotate-12">
            <Lightbulb class="h-6 w-6 text-primary-fixed" />
        </div>
        <div class="space-y-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-primary-fixed/60">Análisis Estratégico</p>
            <p class="font-serif text-[13px] italic leading-relaxed text-white/90">{{ insight }}</p>
        </div>
    </article>
</template>
