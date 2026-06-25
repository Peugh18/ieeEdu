<script setup lang="ts">
interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div
        class="relative flex aspect-square size-10 items-center justify-center overflow-hidden rounded-full border-2 border-primary/20 bg-primary/10 shadow-inner transition-transform group-hover:scale-110"
    >
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent"></div>
        <span class="z-10 font-serif text-lg font-black italic text-primary drop-shadow-sm">I</span>
    </div>
</template>
