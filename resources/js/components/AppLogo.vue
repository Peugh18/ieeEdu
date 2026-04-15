<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-10 items-center justify-center rounded-full bg-primary/10 border-2 border-primary/20 shadow-inner relative overflow-hidden group-hover:scale-110 transition-transform">
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent"></div>
        <span class="font-serif text-lg font-black italic text-primary drop-shadow-sm z-10">I</span>
    </div>
</template>
