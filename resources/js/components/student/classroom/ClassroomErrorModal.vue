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
    <div v-if="show" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm duration-200 animate-in fade-in">
        <div
            class="w-full max-w-sm rounded-[3rem] border border-outline-variant/10 bg-white p-10 text-center shadow-2xl duration-200 animate-in zoom-in-95"
        >
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 md:h-20 md:w-20">
                <AlertCircle class="h-7 w-7 animate-pulse text-red-600 md:h-10 md:w-10" />
            </div>
            <h3 class="mb-2 font-serif text-xl font-bold italic text-on-surface md:mb-3 md:text-2xl">Atención</h3>
            <p class="mb-6 font-serif text-sm italic leading-relaxed text-on-surface-variant md:mb-8 md:text-base">{{ message }}</p>
            <button
                @click="emit('close')"
                class="w-full rounded-xl bg-primary py-3.5 text-[10px] font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition-all hover:opacity-90 active:scale-95 md:rounded-2xl md:py-4"
            >
                Aceptar
            </button>
        </div>
    </div>
</template>
