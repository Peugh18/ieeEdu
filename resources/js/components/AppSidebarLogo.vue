<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import BrandLogo from '@/components/BrandLogo.vue';
import { Globe } from 'lucide-vue-next';

const { state } = useSidebar();

defineProps<{
    isAdmin: boolean;
}>();
</script>

<template>
    <div :class="['flex flex-col items-center relative overflow-hidden transition-all duration-300', isAdmin ? 'p-2' : 'p-4', { 'px-0': state === 'collapsed' }]">
        <div class="absolute bottom-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-outline-variant/20 to-transparent"></div>

        <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex flex-col items-center w-full gap-1 py-1">
            <div class="relative w-full flex items-center justify-center" :class="isAdmin ? 'min-h-[2.75rem]' : 'min-h-[3.5rem]'">
                
                <div
                    class="w-full flex flex-col items-center transition-all duration-300"
                    :class="state === 'collapsed' ? 'opacity-0 scale-75 -translate-y-4 pointer-events-none absolute' : 'opacity-100 scale-100 translate-y-0'"
                >
                    <!-- BrandLogo using generic img to ensure sizing fits max 2.5rem -->
                    <BrandLogo :imageClass="isAdmin ? 'h-[2rem] w-auto max-w-full object-contain object-center block mx-auto transition-transform duration-300 hover:scale-105' : 'h-[2.5rem] w-auto max-w-full object-contain object-center block mx-auto transition-transform duration-300 hover:scale-105'" />
                    <span v-if="!isAdmin" class="mt-1.5 text-[10px] font-bold tracking-[0.15em] uppercase text-on-surface-variant/70 whitespace-nowrap text-center w-full">
                        Aula Virtual
                    </span>
                    <span v-else-if="state === 'expanded'" class="mt-1 text-[10px] font-bold tracking-[0.18em] uppercase text-primary dark:text-[#e7e6ab] whitespace-nowrap text-center w-full">
                        Admin
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

        <!-- Redirect to Landing Page (Website) Link -->
        <Link 
            v-if="state === 'expanded'"
            :href="route('home')" 
            class="mt-2.5 flex items-center gap-1.5 px-3 py-1 bg-slate-50 hover:bg-primary/10 text-slate-500 hover:text-primary dark:bg-slate-900/40 dark:text-slate-400 dark:hover:bg-primary/20 dark:hover:text-primary rounded-full text-[9px] font-black uppercase tracking-[0.1em] transition-all select-none border border-slate-200/40 dark:border-slate-800/40"
        >
            <Globe class="w-3 h-3 text-primary/70 animate-pulse" />
            <span>Regresar a la web</span>
        </Link>
        <Link 
            v-else
            :href="route('home')" 
            class="mt-2 p-1.5 bg-slate-50 hover:bg-primary/10 text-slate-500 hover:text-primary dark:bg-slate-900/40 dark:text-slate-400 dark:hover:bg-primary/20 dark:hover:text-primary rounded-lg transition-all border border-slate-200/40 dark:border-slate-800/40"
            title="Volver al Sitio Web Principal"
        >
            <Globe class="w-3.5 h-3.5 text-primary/70" />
        </Link>
    </div>
</template>
