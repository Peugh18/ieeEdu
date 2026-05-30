<script setup lang="ts">
import { LogOut } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import {
    Tooltip, TooltipContent, TooltipTrigger,
} from '@/components/ui/tooltip';

const { state } = useSidebar();

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="p-3">
        <div class="h-px bg-outline-variant/10 mb-3 mx-2"></div>
        <Tooltip :disabled="state === 'expanded'">
            <TooltipTrigger as-child>
                <button 
                    @click="logout" 
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 text-on-surface-variant hover:bg-error/10 hover:text-error font-medium group border-l-[3px] border-l-transparent"
                    :class="{ 'justify-center': state === 'collapsed' }"
                >
                    <LogOut class="w-[18px] h-[18px] flex-shrink-0 transition-transform duration-300 group-hover:scale-110" />
                    <span class="text-sm tracking-tight truncate flex-1 text-left" v-if="state === 'expanded'">
                        Cerrar Sesión
                    </span>
                </button>
            </TooltipTrigger>
            <TooltipContent side="right" class="bg-error border-error-container/20 text-on-error font-bold">
                <p>Cerrar Sesión</p>
            </TooltipContent>
        </Tooltip>
    </div>
</template>
