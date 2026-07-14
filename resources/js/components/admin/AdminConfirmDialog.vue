<script setup lang="ts">
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { AlertTriangle, Info } from 'lucide-vue-next';

const { state } = useConfirmDialog();

function handleConfirm() {
    if (state.value.onConfirm) {
        state.value.onConfirm();
    }
}

function handleCancel() {
    if (state.value.onCancel) {
        state.value.onCancel();
    }
}
</script>

<template>
    <div
        v-if="state.isOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-on-background/60 p-4 backdrop-blur-sm transition-all duration-300 dark:bg-black/80"
        @click.self="handleCancel"
    >
        <div
            class="w-full max-w-md overflow-hidden rounded-[2rem] bg-surface shadow-2xl duration-300 animate-in fade-in zoom-in slide-in-from-bottom-8 dark:bg-surface-1"
        >
            <div class="flex flex-col items-center gap-4 p-8 text-center">
                <!-- Icono animado -->
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full"
                    :class="state.danger ? 'bg-error/10 text-error' : 'bg-primary/10 text-primary'"
                >
                    <AlertTriangle v-if="state.danger" class="h-8 w-8" stroke-width="2.5" />
                    <Info v-else class="h-8 w-8" stroke-width="2.5" />
                </div>

                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-on-surface">{{ state.title }}</h3>
                    <p class="text-sm font-medium text-on-surface-variant">{{ state.description }}</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="grid grid-cols-2 gap-px bg-outline-variant/30">
                <button
                    @click="handleCancel"
                    class="bg-surface py-4 text-sm font-bold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface dark:bg-surface-1"
                >
                    {{ state.cancelLabel }}
                </button>
                <button
                    @click="handleConfirm"
                    class="bg-surface py-4 text-sm font-bold transition-colors dark:bg-surface-1"
                    :class="
                        state.danger
                            ? 'text-error hover:bg-error/10 hover:text-error'
                            : 'text-primary hover:bg-primary/10 hover:text-primary-active'
                    "
                >
                    {{ state.confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</template>
