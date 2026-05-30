<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import BrandLogo from '@/components/BrandLogo.vue';

const { state } = useSidebar();

defineProps<{
    isAdmin: boolean;
}>();
</script>

<template>
    <div :class="['flex items-center p-4 relative overflow-hidden transition-all duration-300', { 'px-0': state === 'collapsed' }]">
        <!-- Subtítulo divider base -->
        <div class="absolute bottom-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-outline-variant/20 to-transparent"></div>

        <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex flex-col items-center w-full gap-1.5 py-2">
            <div class="relative w-full flex items-center justify-center min-h-[3.5rem]">
                
                <div
                    class="w-full flex flex-col items-center transition-all duration-300"
                    :class="state === 'collapsed' ? 'opacity-0 scale-75 -translate-y-4 pointer-events-none absolute' : 'opacity-100 scale-100 translate-y-0'"
                >
                    <!-- BrandLogo using generic img to ensure sizing fits max 2.5rem -->
                    <BrandLogo forceTheme="dark" imageClass="h-[2.5rem] w-auto max-w-full object-contain object-center block mx-auto transition-transform duration-300 hover:scale-105" />
                    <span class="mt-1.5 text-[10px] font-bold tracking-[0.15em] uppercase text-on-surface-variant/70 whitespace-nowrap text-center w-full">
                        {{ isAdmin ? 'Panel Administrativo' : 'Aula Virtual' }}
                    </span>
                </div>

                <div
                    class="transition-all duration-300"
                    :class="state === 'expanded' ? 'opacity-0 scale-125 translate-y-4 pointer-events-none absolute' : 'opacity-100 scale-100 translate-y-0'"
                >
                    <img src="/images/empresa/IEE-Logo.png" alt="IEE Icon" class="h-8 w-auto object-contain drop-shadow-sm transition-transform duration-300 hover:scale-110" />
                </div>

            </div>
        </Link>
    </div>
</template>
