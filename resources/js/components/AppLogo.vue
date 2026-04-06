<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-10 items-center justify-center rounded-full bg-[#57572a]/10 border-2 border-[#57572a]/20 shadow-inner relative overflow-hidden group-hover:scale-110 transition-transform">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#57572a]/20 to-transparent"></div>
        <span class="font-serif text-lg font-black italic text-[#57572a] drop-shadow-sm z-10">I</span>
    </div>
</template>
