<script setup lang="ts">
defineProps<{
    show: boolean;
}>();
</script>

<template>
    <div
        v-show="show"
        class="rounded-[2.5rem] bg-surface-container-lowest p-8 md:p-14 shadow-2xl shadow-surface-tint/5 border border-outline-variant/10 animate-in fade-in slide-in-from-bottom-4 duration-500 relative overflow-hidden"
    >
        <slot />
    </div>
</template>
