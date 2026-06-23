<script setup lang="ts">
defineProps<{
    options: Record<string, string>;
    modelValue: string;
}>();

const emit = defineEmits<{ 'update:modelValue': [value: string] }>();
</script>

<template>
    <div class="inline-flex w-full shrink-0 rounded-2xl border border-outline-variant/30 bg-surface-container-high p-1 sm:w-auto">
        <div class="grid w-full grid-cols-2 sm:flex">
            <button
                v-for="(label, key) in options"
                :key="key"
                type="button"
                @click="emit('update:modelValue', key)"
                class="whitespace-nowrap rounded-[1.25rem] px-4 py-2.5 text-xs font-bold uppercase tracking-wide transition-all duration-200 sm:flex-1"
                :class="
                    modelValue === key
                        ? 'bg-surface text-primary shadow-sm ring-1 ring-outline-variant/20'
                        : 'text-on-surface-variant hover:bg-surface-container/50 hover:text-on-surface'
                "
            >
                {{ label }}
            </button>
        </div>
    </div>
</template>
