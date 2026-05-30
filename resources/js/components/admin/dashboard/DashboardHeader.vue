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
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 pb-12 border-b border-primary/10">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] tracking-[0.3em] font-black uppercase text-primary/60">Panel de Inteligencia · iieEdu</span>
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-gray-900 leading-none tracking-tight">
                Dashboard <span class="italic text-primary">Administrativo</span>
            </h1>
            <p class="text-gray-400 text-sm max-w-xl">Métricas en tiempo real de ingresos, suscripciones, cursos, libros y engagement estudiantil.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            <div class="px-6 py-3.5 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-widest">{{ formattedDate }}</span>
            </div>
            <button @click="emit('downloadPDF')" class="px-8 py-3.5 bg-on-background text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 group">
                <Download class="w-4 h-4 opacity-50 group-hover:opacity-100 transition" />
                Reporte PDF
            </button>
        </div>
    </header>
</template>
