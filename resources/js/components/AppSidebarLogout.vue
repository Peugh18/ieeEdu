<script setup lang="ts">
import { useSidebar } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';

const { state } = useSidebar();

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="p-3">
        <div class="mx-2 mb-3 h-px bg-outline-variant/10"></div>
        <Tooltip :disabled="state === 'expanded'">
            <TooltipTrigger as-child>
                <button
                    @click="logout"
                    class="group flex w-full items-center gap-3 rounded-xl border-l-[3px] border-l-transparent px-3 py-2.5 font-medium text-on-surface-variant transition-all duration-300 hover:bg-error/10 hover:text-error"
                    :class="{ 'justify-center': state === 'collapsed' }"
                >
                    <LogOut class="h-[18px] w-[18px] flex-shrink-0 transition-transform duration-300 group-hover:scale-110" />
                    <span class="flex-1 truncate text-left text-sm tracking-tight" v-if="state === 'expanded'"> Cerrar Sesión </span>
                </button>
            </TooltipTrigger>
            <TooltipContent side="right" class="border-error-container/20 bg-error font-bold text-on-error">
                <p>Cerrar Sesión</p>
            </TooltipContent>
        </Tooltip>
    </div>
</template>
