<script setup lang="ts">
import { MonitorPlay, Upload, X, AlertTriangle } from 'lucide-vue-next';

const slide = defineModel<{
    imagePreview: string | null;
    file: File | null;
}>('slide', { required: true });

const errorMessage = defineModel<string>('errorMessage', { required: true });

defineProps<{
    recommendedSpecs: string;
    isCarousel: boolean;
    slideIndex: number;
}>();

const emit = defineEmits<{
    (e: 'triggerUpload'): void;
    (e: 'fileChange', event: Event): void;
}>();
</script>

<template>
    <div class="rounded-[2.5rem] p-8 shadow-sm border border-outline-variant/20 bg-surface-container-lowest">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xl font-serif flex items-center gap-2 text-on-surface">
                <MonitorPlay class="w-5 h-5 text-primary" />
                Fotografía de Cabecera {{ isCarousel ? `(${slideIndex + 1} / 3)` : '' }}
            </h3>
            <div class="px-3 py-1.5 rounded-lg border border-dashed border-outline-variant/30 text-[10px] font-bold uppercase tracking-widest text-center bg-surface-container-high text-primary">
                {{ recommendedSpecs }}
            </div>
        </div>

        <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="errorMessage" class="mb-6 rounded-[1.5rem] p-4 flex gap-4 overflow-hidden relative shadow-lg bg-surface border-l-4 border-l-rose-500 border-t border-t-rose-500/10 border-r border-r-rose-500/10 border-b border-b-rose-500/10">
                <div class="absolute inset-0 opacity-5 bg-rose-500"></div>
                <div class="relative z-10 flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-rose-500/10">
                    <AlertTriangle class="w-5 h-5 text-rose-500" />
                </div>
                <div class="relative z-10 flex-1 py-1">
                    <h4 class="text-sm font-bold tracking-tight mb-1 text-rose-500">Ups, la imagen no es adecuada</h4>
                    <p class="text-xs font-semibold leading-relaxed whitespace-pre-wrap text-on-surface-variant">{{ errorMessage }}</p>
                </div>
                <button @click="errorMessage = ''" class="relative z-10 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full transition-colors hover:bg-black/10 text-on-surface-variant">
                    <X class="w-4 h-4" />
                </button>
            </div>
        </transition>

        <div class="relative group rounded-[2rem] overflow-hidden border-2 border-dashed flex items-center justify-center min-h-[340px] transition-all cursor-pointer"
            :class="slide.imagePreview ? 'border-primary' : 'border-outline-variant/50 bg-surface-container-high'"
            @click="emit('triggerUpload')"
        >
            <img v-if="slide.imagePreview" :src="slide.imagePreview" class="absolute inset-0 w-full h-full object-cover" />
            <div v-else class="flex flex-col items-center justify-center p-8 z-0">
                <MonitorPlay class="w-12 h-12 mb-4 text-on-surface-variant/40" />
                <div class="text-center font-medium text-on-surface-variant">
                    Haz clic para seleccionar una fotografía<br>
                    <span class="text-xs uppercase tracking-widest font-bold text-primary">{{ recommendedSpecs }}</span>
                </div>
            </div>

            <div class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-sm p-6"
                :class="slide.imagePreview ? 'bg-black/40' : 'bg-transparent'"
            >
                <div class="h-12 inline-flex items-center gap-2 rounded-xl px-6 text-sm font-bold shadow-xl bg-primary text-white">
                    <Upload class="w-4 h-4" />
                    {{ slide.imagePreview ? 'Cambiar Fotografía' : 'Subir Fotografía' }}
                </div>
                <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-lg text-center bg-surface text-on-surface-variant">
                    {{ recommendedSpecs }}
                </span>
            </div>
        </div>
    </div>
</template>
