<script setup lang="ts">
import { Check, Loader2, X } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        show: boolean;
        title: string;
        titleAccent: string;
        subtitle?: string;
        processing?: boolean;
        submitLabel?: string;
        cancelLabel?: string;
        maxWidth?: string; // e.g. 'max-w-xl' | 'max-w-2xl'
    }>(),
    {
        processing: false,
        submitLabel: 'Confirmar',
        cancelLabel: 'Cancelar Operación',
        maxWidth: 'max-w-xl',
    },
);

const emit = defineEmits<{
    close: [];
    submit: [];
}>();
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-on-background/60 p-4 backdrop-blur-md dark:bg-black/70"
        @click.self="emit('close')"
    >
        <div
            class="flex max-h-[95vh] w-full flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl duration-500 animate-in fade-in zoom-in slide-in-from-bottom-8 dark:bg-on-background"
            :class="maxWidth"
        >
            <!-- Header -->
            <div
                class="flex shrink-0 items-center justify-between border-b border-on-background/5 bg-[#fdfdfc]/50 px-6 py-6 dark:border-primary-fixed/10 dark:bg-[#2a2a1a] md:px-10 md:py-8"
            >
                <div class="space-y-1">
                    <h2 class="font-serif text-3xl font-bold leading-none text-on-background dark:text-primary-fixed">
                        {{ title }} <span class="italic text-primary dark:text-[#b0af7a]">{{ titleAccent }}</span>
                    </h2>
                    <p v-if="subtitle" class="text-[10px] font-black uppercase tracking-widest text-[#9ca3af] dark:text-[#6b7280]">
                        {{ subtitle }}
                    </p>
                </div>
                <button
                    @click="emit('close')"
                    class="flex h-10 w-10 items-center justify-center rounded-full transition-colors hover:bg-surface-container dark:hover:bg-[#3a3a2a]"
                >
                    <X class="h-5 w-5 text-on-background/40 dark:text-primary-fixed/40" />
                </button>
            </div>

            <!-- Body slot -->
            <div class="custom-scrollbar flex-1 space-y-8 overflow-y-auto p-6 md:p-10">
                <slot />
            </div>

            <!-- Footer -->
            <div
                class="flex shrink-0 justify-end gap-6 border-t border-on-background/5 bg-surface-container/50 px-6 py-6 dark:border-primary-fixed/10 dark:bg-[#2a2a1a] md:px-12 md:py-8"
            >
                <button
                    @click="emit('close')"
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-on-background/40 transition-colors hover:text-on-background dark:text-primary-fixed/40 dark:hover:text-primary-fixed"
                >
                    {{ cancelLabel }}
                </button>
                <button
                    @click="emit('submit')"
                    :disabled="processing"
                    class="inline-flex items-center gap-3 rounded-full bg-on-background px-12 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-primary-fixed shadow-2xl shadow-on-background/20 transition-all hover:scale-[1.05] hover:bg-[#2a2a1a] disabled:opacity-50 dark:bg-primary-fixed dark:text-on-background dark:hover:bg-[#d5d4a0]"
                >
                    <Loader2 v-if="processing" class="h-4 w-4 animate-spin" />
                    <Check v-else class="h-4 w-4" />
                    {{ submitLabel }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
</style>
