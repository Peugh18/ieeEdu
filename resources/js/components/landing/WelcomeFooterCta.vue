<script setup lang="ts">
</script>

<template>
  <section class="relative overflow-hidden bg-surface-container-low reveal">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-primary/[0.07] rounded-full blur-[120px] -translate-y-1/2"></div>
      <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px] translate-y-1/2"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 md:px-8 py-20 md:py-28 text-center">
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 border border-green-500/30 mb-8 animate-pulse">
        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        <span class="text-sm font-bold text-green-600 tracking-wide">TU PRIMERA CLASE ES GRATIS</span>
      </div>

      <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight font-bold">
        ¿Listo para seguir <br class="hidden md:block"/>
        <span class="italic text-primary">aprendiendo?</span>
      </h2>

      <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-8 max-w-lg mx-auto">
        Empieza hoy mismo — miles de profesionales del Perú ya confían en nosotros.
      </p>

      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/cursos" class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
          Empezar ahora
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <a href="/cursos" class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-surface-container border border-outline-variant/30 text-on-surface rounded-xl font-bold text-base hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
          Ver cursos
        </a>
      </div>
    </div>
  </section>
</template>
