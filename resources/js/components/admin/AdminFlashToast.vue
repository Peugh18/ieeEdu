<script setup lang="ts">
import { AlertCircle, Check, X } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        show: boolean;
        message: string;
        variant?: 'success' | 'error';
        subtitle?: string;
    }>(),
    {
        variant: 'success',
    },
);

const emit = defineEmits<{ close: [] }>();
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed bottom-6 right-6 z-50 flex max-w-sm items-start gap-3 rounded-2xl px-5 py-4 text-white shadow-2xl"
            :class="variant === 'success' ? 'bg-emerald-600' : 'bg-red-600'"
        >
            <Check v-if="variant === 'success'" class="mt-0.5 h-5 w-5 shrink-0" />
            <AlertCircle v-else class="mt-0.5 h-5 w-5 shrink-0" />
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">{{ message }}</p>
                <p v-if="subtitle" class="mt-0.5 text-xs opacity-80">{{ subtitle }}</p>
            </div>
            <button type="button" @click="emit('close')" class="shrink-0 opacity-70 transition-opacity hover:opacity-100">
                <X class="h-4 w-4" />
            </button>
        </div>
    </Transition>
</template>
