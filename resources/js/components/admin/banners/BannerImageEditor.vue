<script setup lang="ts">
import { AlertTriangle, MonitorPlay, Upload, X } from 'lucide-vue-next';

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
    <div class="rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-sm">
        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <h3 class="flex items-center gap-2 font-serif text-xl text-on-surface">
                <MonitorPlay class="h-5 w-5 text-primary" />
                Fotografía de Cabecera {{ isCarousel ? `(${slideIndex + 1} / 3)` : '' }}
            </h3>
            <div
                class="rounded-lg border border-dashed border-outline-variant/30 bg-surface-container-high px-3 py-1.5 text-center text-[10px] font-bold uppercase tracking-widest text-primary"
            >
                {{ recommendedSpecs }}
            </div>
        </div>

        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="errorMessage"
                class="relative mb-6 flex gap-4 overflow-hidden rounded-[1.5rem] border-b border-l-4 border-r border-t border-b-rose-500/10 border-l-rose-500 border-r-rose-500/10 border-t-rose-500/10 bg-surface p-4 shadow-lg"
            >
                <div class="absolute inset-0 bg-rose-500 opacity-5"></div>
                <div class="relative z-10 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-rose-500/10">
                    <AlertTriangle class="h-5 w-5 text-rose-500" />
                </div>
                <div class="relative z-10 flex-1 py-1">
                    <h4 class="mb-1 text-sm font-bold tracking-tight text-rose-500">Ups, la imagen no es adecuada</h4>
                    <p class="whitespace-pre-wrap text-xs font-semibold leading-relaxed text-on-surface-variant">{{ errorMessage }}</p>
                </div>
                <button
                    @click="errorMessage = ''"
                    class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-black/10"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </transition>

        <div
            class="group relative flex min-h-[340px] cursor-pointer items-center justify-center overflow-hidden rounded-[2rem] border-2 border-dashed transition-all"
            :class="slide.imagePreview ? 'border-primary' : 'border-outline-variant/50 bg-surface-container-high'"
            @click="emit('triggerUpload')"
        >
            <img v-if="slide.imagePreview" :src="slide.imagePreview" class="absolute inset-0 h-full w-full object-cover" />
            <div v-else class="z-0 flex flex-col items-center justify-center p-8">
                <MonitorPlay class="mb-4 h-12 w-12 text-on-surface-variant/40" />
                <div class="text-center font-medium text-on-surface-variant">
                    Haz clic para seleccionar una fotografía<br />
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">{{ recommendedSpecs }}</span>
                </div>
            </div>

            <div
                class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 p-6 opacity-0 backdrop-blur-sm transition-opacity duration-300 group-hover:opacity-100"
                :class="slide.imagePreview ? 'bg-black/40' : 'bg-transparent'"
            >
                <div class="inline-flex h-12 items-center gap-2 rounded-xl bg-primary px-6 text-sm font-bold text-white shadow-xl">
                    <Upload class="h-4 w-4" />
                    {{ slide.imagePreview ? 'Cambiar Fotografía' : 'Subir Fotografía' }}
                </div>
                <span class="rounded-lg bg-surface px-3 py-1 text-center text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                    {{ recommendedSpecs }}
                </span>
            </div>
        </div>
    </div>
</template>
