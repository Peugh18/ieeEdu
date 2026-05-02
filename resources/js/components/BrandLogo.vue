<template>
    <div :class="['relative flex items-center', containerClass]">
        <!-- Logo Light para modo claro -->
        <img 
            v-if="forceTheme !== 'dark'"
            src="/images/empresa/LogoLight.png" 
            alt="Instituto de Economía y Empresa" 
            :class="[imageClass, forceTheme === 'light' ? 'block' : 'block dark:hidden']"
        />
        <!-- Logo Dark para modo oscuro -->
        <img 
            v-if="forceTheme !== 'light'"
            src="/images/empresa/LogoDark.png" 
            alt="Instituto de Economía y Empresa" 
            :class="[imageClass, forceTheme === 'dark' ? 'block' : 'hidden dark:block']"
        />
    </div>
</template>

<script setup lang="ts">
defineProps<{
    containerClass?: string;
    imageClass?: string;
    forceTheme?: 'light' | 'dark'; // Permite forzar un logo si el contenedor no cambia con el tema global
}>();
</script>
