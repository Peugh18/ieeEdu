<script setup lang="ts">
defineProps<{
    show: boolean;
}>();
</script>

<template>
    <div
        v-show="show"
        class="relative overflow-hidden rounded-[2.5rem] border border-outline-variant/10 bg-surface-container-lowest p-8 shadow-2xl shadow-surface-tint/5 duration-500 animate-in fade-in slide-in-from-bottom-4 md:p-14"
    >
        <slot />
    </div>
</template>
