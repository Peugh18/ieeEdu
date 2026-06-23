<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: number;
        label: string;
        min?: number;
        max?: number;
        step?: number;
    }>(),
    {
        min: 0,
        max: 100,
        step: 0.5,
    },
);

const emit = defineEmits<{ 'update:modelValue': [value: number] }>();

const fillPercent = computed(() => {
    const range = props.max - props.min;
    if (range <= 0) return 0;
    return ((props.modelValue - props.min) / range) * 100;
});

function onInput(e: Event) {
    const raw = parseFloat((e.target as HTMLInputElement).value);
    if (!isNaN(raw)) emit('update:modelValue', raw);
}

function onNumberInput(e: Event) {
    const raw = parseFloat((e.target as HTMLInputElement).value);
    if (isNaN(raw)) return;
    emit('update:modelValue', Math.min(props.max, Math.max(props.min, raw)));
}
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-3">
            <label class="text-sm font-semibold text-on-surface-variant">{{ label }}</label>
            <span class="text-sm font-bold tabular-nums text-primary">{{ Math.round(modelValue) }}%</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="relative flex h-8 flex-1 items-center">
                <div class="absolute inset-x-0 h-2 overflow-hidden rounded-full bg-surface-container-highest">
                    <div class="h-full rounded-full bg-primary/30 transition-all duration-150" :style="{ width: `${fillPercent}%` }" />
                </div>
                <input
                    type="range"
                    :value="modelValue"
                    :min="min"
                    :max="max"
                    :step="step"
                    class="certificate-slider relative z-10 h-8 w-full cursor-pointer appearance-none bg-transparent"
                    @input="onInput"
                />
            </div>

            <input
                type="number"
                :value="Math.round(modelValue * 10) / 10"
                :min="min"
                :max="max"
                :step="step"
                class="w-16 rounded-xl border border-outline-variant/30 bg-surface-container-high py-2 text-center text-sm font-bold text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30"
                @input="onNumberInput"
            />
        </div>
    </div>
</template>

<style scoped>
.certificate-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 1.125rem;
    height: 1.125rem;
    border-radius: 9999px;
    background: var(--color-primary, #57572a);
    border: 2px solid white;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.15);
    cursor: grab;
}

.certificate-slider::-moz-range-thumb {
    width: 1.125rem;
    height: 1.125rem;
    border-radius: 9999px;
    background: var(--color-primary, #57572a);
    border: 2px solid white;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.15);
    cursor: grab;
}

.certificate-slider::-webkit-slider-runnable-track {
    background: transparent;
}

.certificate-slider::-moz-range-track {
    background: transparent;
}
</style>
