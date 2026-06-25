<script setup lang="ts">
/**
 * AdminFormField — wrapper de campo de formulario (label + input + error)
 *
 * Unifica el patrón:
 *   <div class="space-y-2">
 *     <label ...>...</label>
 *     <input ... />
 *     <p v-if="error" ...>{{ error }}</p>
 *   </div>
 *
 * Detectado en Articles.vue, Books.vue (≥90% idéntico).
 */
withDefaults(
    defineProps<{
        label: string;
        error?: string;
        hint?: string;
    }>(),
    {
        error: '',
        hint: '',
    },
);
</script>

<template>
    <div class="space-y-2">
        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-on-background/60 dark:text-primary-fixed/60">
            {{ label }}
        </label>

        <!-- The actual input / select / textarea comes via slot -->
        <slot />

        <p v-if="error" class="mt-1 text-[8px] font-bold uppercase tracking-widest text-red-500">
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1 text-[9px] font-medium italic text-[#9ca3af] dark:text-[#6b7280]">
            {{ hint }}
        </p>
    </div>
</template>
