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
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}&background=57572A&color=e7e6ab&bold=true&size=128`,
);
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <button
                type="button"
                class="group mx-2 mb-0.5 mt-1 flex w-[calc(100%-1rem)] items-center gap-2.5 rounded-xl border border-outline-variant/10 bg-surface-container-low p-1.5 text-left outline-none transition-all duration-300 hover:bg-surface-container hover:shadow-sm focus-visible:ring-2 focus-visible:ring-primary/40"
                :class="{ 'mx-2 justify-center px-0 py-2': state === 'collapsed' }"
                aria-label="Abrir menú de cuenta"
            >
                <div class="relative flex-shrink-0">
                    <img
                        :src="avatarUrl"
                        :alt="user.name"
                        class="h-8 w-8 rounded-full border-2 border-outline-variant/20 object-cover shadow-sm transition-transform duration-300 group-hover:scale-105"
                    />
                    <div class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-[1.5px] border-surface bg-emerald-400"></div>
                </div>

                <div v-if="state === 'expanded'" class="flex min-w-0 flex-1 flex-col justify-center overflow-hidden">
                    <span class="truncate text-sm font-bold leading-tight tracking-wide text-on-surface">
                        {{ user.name }}
                    </span>
                    <span class="truncate text-[11px] font-medium text-on-surface-variant/60 transition-colors group-hover:text-primary">
                        {{ isAdmin ? 'Admin · Mi cuenta' : 'Estudiante' }}
                    </span>
                </div>

                <ChevronsUpDown v-if="state === 'expanded'" class="h-4 w-4 shrink-0 text-on-surface-variant/50 group-hover:text-on-surface-variant" />
            </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            class="w-56 rounded-xl border border-outline-variant/15 bg-surface-container-lowest p-1 shadow-xl"
            :side="state === 'collapsed' ? 'right' : 'bottom'"
            :align="state === 'collapsed' ? 'start' : 'center'"
            :side-offset="6"
        >
            <UserMenuContent :user="user" :is-admin="isAdmin" />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
