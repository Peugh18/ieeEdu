<script setup lang="ts">
import { Download } from 'lucide-vue-next';

defineProps<{
    formattedDate: string;
}>();

const emit = defineEmits<{
    (e: 'downloadPDF'): void;
}>();
</script>

<template>
    <header class="flex flex-col items-start justify-between gap-8 border-b border-primary/10 pb-12 lg:flex-row lg:items-end">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/60">Panel de Inteligencia · iieEdu</span>
            </div>
            <h1 class="font-serif text-4xl leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
                Dashboard <span class="italic text-primary">Administrativo</span>
            </h1>
            <p class="max-w-xl text-sm text-gray-400">Métricas en tiempo real de ingresos, suscripciones, cursos, libros y engagement estudiantil.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-white px-6 py-3.5 shadow-sm">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-700">{{ formattedDate }}</span>
            </div>
            <button
                @click="emit('downloadPDF')"
                class="group flex items-center gap-3 rounded-2xl bg-on-background px-8 py-3.5 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all hover:scale-105 active:scale-95"
            >
                <Download class="h-4 w-4 opacity-50 transition group-hover:opacity-100" />
                Reporte PDF
            </button>
        </div>
    </header>
</template>
