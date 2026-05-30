<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next';

defineProps<{
    show: boolean;
    message: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="bg-white rounded-[3rem] shadow-2xl max-w-sm w-full p-10 text-center border border-outline-variant/10 animate-in zoom-in-95 duration-200">
            <div class="w-14 h-14 md:w-20 md:h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
                <AlertCircle class="w-7 h-7 md:w-10 md:h-10 text-red-600 animate-pulse" />
            </div>
            <h3 class="text-xl md:text-2xl font-serif font-bold italic text-on-surface mb-2 md:mb-3">Atención</h3>
            <p class="text-on-surface-variant leading-relaxed font-serif italic text-sm md:text-base mb-6 md:mb-8">{{ message }}</p>
            <button @click="emit('close')" class="w-full py-3.5 md:py-4 bg-primary text-white font-bold rounded-xl md:rounded-2xl shadow-xl shadow-primary/20 hover:opacity-90 transition-all uppercase tracking-widest text-[10px] active:scale-95">Aceptar</button>
        </div>
    </div>
</template>
