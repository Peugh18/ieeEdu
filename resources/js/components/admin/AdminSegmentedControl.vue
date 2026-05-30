<script setup lang="ts">
defineProps<{
    options: Record<string, string>;
    modelValue: string;
}>();

const emit = defineEmits<{ 'update:modelValue': [value: string] }>();
</script>

<template>
    <div class="inline-flex w-full sm:w-auto bg-surface-container-high p-1 rounded-2xl border border-outline-variant/30 shrink-0">
        <div class="grid grid-cols-2 sm:flex w-full">
            <button
                v-for="(label, key) in options"
                :key="key"
                type="button"
                @click="emit('update:modelValue', key)"
                class="px-4 py-2.5 text-xs font-bold uppercase tracking-wide rounded-[1.25rem] transition-all duration-200 sm:flex-1 whitespace-nowrap"
                :class="modelValue === key
                    ? 'bg-surface text-primary shadow-sm ring-1 ring-outline-variant/20'
                    : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container/50'"
            >
                {{ label }}
            </button>
        </div>
    </div>
</template>
