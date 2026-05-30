<script setup lang="ts">
</script>

<template>
  <section id="consultoria" class="py-16 md:py-24 bg-surface-container-low overflow-hidden relative reveal">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div class="absolute -top-20 -left-20 w-[600px] h-[600px] bg-primary/[0.05] rounded-full blur-[140px]"></div>
    </div>
    <div class="max-w-4xl mx-auto px-6 md:px-8 relative z-10 text-center">
      <div class="flex items-center justify-center gap-3 mb-8">
        <div class="w-8 h-px bg-primary/30"></div>
        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Consultoría</span>
        <div class="w-8 h-px bg-primary/30"></div>
      </div>

      <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight">
        ¿Tu organización necesita<br class="hidden md:block"/>
        <span class="italic text-primary">soluciones estratégicas?</span>
      </h2>

      <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-8 max-w-2xl mx-auto">
        Más de 15 años transformando entidades públicas y privadas del Perú con metodologías probadas.
      </p>

      <a href="/consultoria" class="group inline-flex items-center gap-3 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
        Solicitar consultoría
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
      </a>
    </div>
  </section>
</template>
