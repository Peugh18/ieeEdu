<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue';
import { useSidebar } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';

const { state } = useSidebar();

defineProps<{
    isAdmin: boolean;
}>();
</script>

<template>
    <div
        :class="[
            'relative flex flex-col items-center overflow-hidden transition-all duration-300',
            isAdmin ? 'p-2' : 'p-4',
            { 'px-0': state === 'collapsed' },
        ]"
    >
        <div class="absolute bottom-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-outline-variant/20 to-transparent"></div>

        <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex w-full flex-col items-center gap-1 py-1">
            <div class="relative flex w-full items-center justify-center" :class="isAdmin ? 'min-h-[2.75rem]' : 'min-h-[3.5rem]'">
                <div
                    class="flex w-full flex-col items-center transition-all duration-300"
                    :class="
                        state === 'collapsed'
                            ? 'pointer-events-none absolute -translate-y-4 scale-75 opacity-0'
                            : 'translate-y-0 scale-100 opacity-100'
                    "
                >
                    <!-- BrandLogo using generic img to ensure sizing fits max 2.5rem -->
                    <BrandLogo
                        :imageClass="
                            isAdmin
                                ? 'h-[2rem] w-auto max-w-full object-contain object-center block mx-auto transition-transform duration-300 hover:scale-105'
                                : 'h-[2.5rem] w-auto max-w-full object-contain object-center block mx-auto transition-transform duration-300 hover:scale-105'
                        "
                    />
                    <span
                        v-if="!isAdmin"
                        class="mt-1.5 w-full whitespace-nowrap text-center text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant/70"
                    >
                        Aula Virtual
                    </span>
                    <span
                        v-else-if="state === 'expanded'"
                        class="mt-1 w-full whitespace-nowrap text-center text-[10px] font-bold uppercase tracking-[0.18em] text-primary dark:text-[#e7e6ab]"
                    >
                        Admin
                    </span>
                </div>

                <div
                    class="transition-all duration-300"
                    :class="
                        state === 'expanded'
                            ? 'pointer-events-none absolute translate-y-4 scale-125 opacity-0'
                            : 'translate-y-0 scale-100 opacity-100'
                    "
                >
                    <img
                        src="/images/empresa/IEE-Logo.png"
                        alt="IEE Icon"
                        class="h-8 w-auto object-contain drop-shadow-sm transition-transform duration-300 hover:scale-110"
                    />
                </div>
            </div>
        </Link>

        <!-- Redirect to Landing Page (Website) Link -->
        <Link
            v-if="state === 'expanded'"
            :href="route('home')"
            class="mt-2.5 flex select-none items-center gap-1.5 rounded-full border border-slate-200/40 bg-slate-50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.1em] text-slate-500 transition-all hover:bg-primary/10 hover:text-primary dark:border-slate-800/40 dark:bg-slate-900/40 dark:text-slate-400 dark:hover:bg-primary/20 dark:hover:text-primary"
        >
            <Globe class="h-3 w-3 animate-pulse text-primary/70" />
            <span>Regresar a la web</span>
        </Link>
        <Link
            v-else
            :href="route('home')"
            class="mt-2 rounded-lg border border-slate-200/40 bg-slate-50 p-1.5 text-slate-500 transition-all hover:bg-primary/10 hover:text-primary dark:border-slate-800/40 dark:bg-slate-900/40 dark:text-slate-400 dark:hover:bg-primary/20 dark:hover:text-primary"
            title="Volver al Sitio Web Principal"
        >
            <Globe class="h-3.5 w-3.5 text-primary/70" />
        </Link>
    </div>
</template>
