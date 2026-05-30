<script setup lang="ts">
import UserMenuContent from '@/components/UserMenuContent.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useSidebar } from '@/components/ui/sidebar';
import type { User } from '@/types';
import { ChevronsUpDown } from 'lucide-vue-next';
import { computed } from 'vue';

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
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <button
                type="button"
                class="group mx-2 mt-1 mb-0.5 w-[calc(100%-1rem)] p-1.5 rounded-xl flex items-center gap-2.5 bg-surface-container-low hover:bg-surface-container hover:shadow-sm border border-outline-variant/10 transition-all duration-300 text-left outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                :class="{ 'justify-center mx-2 px-0 py-2': state === 'collapsed' }"
                aria-label="Abrir menú de cuenta"
            >
                <div class="relative flex-shrink-0">
                    <img :src="avatarUrl" :alt="user.name" class="w-8 h-8 rounded-full object-cover border-2 border-outline-variant/20 shadow-sm transition-transform duration-300 group-hover:scale-105" />
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 border-[1.5px] border-surface"></div>
                </div>

                <div v-if="state === 'expanded'" class="flex-1 min-w-0 flex flex-col justify-center overflow-hidden">
                    <span class="text-sm font-bold tracking-wide text-on-surface truncate leading-tight">
                        {{ user.name }}
                    </span>
                    <span class="text-[11px] font-medium text-on-surface-variant/60 group-hover:text-primary transition-colors truncate">
                        {{ isAdmin ? 'Admin · Mi cuenta' : 'Estudiante' }}
                    </span>
                </div>

                <ChevronsUpDown v-if="state === 'expanded'" class="w-4 h-4 shrink-0 text-on-surface-variant/50 group-hover:text-on-surface-variant" />
            </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            class="w-56 rounded-xl border border-outline-variant/15 bg-surface-container-lowest shadow-xl p-1"
            :side="state === 'collapsed' ? 'right' : 'bottom'"
            :align="state === 'collapsed' ? 'start' : 'center'"
            :side-offset="6"
        >
            <UserMenuContent :user="user" :is-admin="isAdmin" />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
