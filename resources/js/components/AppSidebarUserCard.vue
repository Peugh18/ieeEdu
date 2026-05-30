<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import type { User } from '@/types';

const { state } = useSidebar();

const props = defineProps<{
    user: User;
    isAdmin: boolean;
}>();

const avatarUrl = computed(() =>
    props.user.avatar
        ? '/storage/' + props.user.avatar
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}&background=57572A&color=e7e6ab&bold=true&size=128`
);
</script>

<template>
    <Tooltip :disabled="state === 'expanded'">
        <TooltipTrigger as-child>
            <Link
                :href="isAdmin ? route('profile.edit') : route('student.profile.index')"
                class="group mx-3 mt-3 mb-1 p-2 rounded-2xl flex items-center gap-3 bg-surface-container-low hover:bg-surface-container hover:shadow-sm border border-outline-variant/10 transition-all duration-300"
                :class="{ 'justify-center mx-2 px-0 py-2': state === 'collapsed' }"
            >
                <div class="relative flex-shrink-0">
                    <img :src="avatarUrl" :alt="user.name" class="w-9 h-9 rounded-full object-cover border-2 border-outline-variant/20 shadow-sm transition-transform duration-300 group-hover:scale-105" />
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 border-[1.5px] border-surface"></div>
                </div>

                <div class="flex-1 min-w-0 flex flex-col justify-center overflow-hidden" v-if="state === 'expanded'">
                    <span class="text-sm font-bold tracking-wide text-on-surface truncate">
                        {{ user.name }}
                    </span>
                    <span class="flex items-center gap-1.5 text-xs font-medium tracking-wide text-on-surface-variant/70 group-hover:text-primary transition-colors truncate">
                        <span class="truncate">{{ isAdmin ? 'Administrador' : 'Estudiante' }} &middot; Ver perfil</span>
                    </span>
                </div>
            </Link>
        </TooltipTrigger>
        <TooltipContent side="right" class="bg-surface-container-highest border-outline-variant/10 text-on-surface font-bold">
            <p>{{ user.name }}</p>
        </TooltipContent>
    </Tooltip>
</template>
