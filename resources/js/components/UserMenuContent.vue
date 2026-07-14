<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link } from '@inertiajs/vue3';
import { KeyRound, LogOut, Palette, UserRound, CreditCard } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    user: User;
    isAdmin?: boolean;
}>();

const isAdminUser = computed(() => props.isAdmin ?? props.user.role === 'admin');
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-2 py-2 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>

    <DropdownMenuSeparator />

    <DropdownMenuGroup>
        <template v-if="isAdminUser">
            <DropdownMenuItem :as-child="true">
                <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors hover:bg-surface-container-highest dark:hover:bg-surface-3" :href="route('profile.edit')">
                    <UserRound class="h-4 w-4 opacity-70" />
                    Perfil
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem :as-child="true">
                <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors hover:bg-surface-container-highest dark:hover:bg-surface-3" :href="route('password.edit')">
                    <KeyRound class="h-4 w-4 opacity-70" />
                    Contraseña
                </Link>
            </DropdownMenuItem>
            <DropdownMenuItem :as-child="true">
                <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors hover:bg-surface-container-highest dark:hover:bg-surface-3" :href="route('appearance')">
                    <Palette class="h-4 w-4 opacity-70" />
                    Apariencia
                </Link>
            </DropdownMenuItem>
        </template>
        <DropdownMenuItem v-else :as-child="true">
            <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors hover:bg-surface-container-highest dark:hover:bg-surface-3" :href="route('student.profile.index')">
                <UserRound class="h-4 w-4 opacity-70" />
                Mi perfil
            </Link>
        </DropdownMenuItem>
        
        <DropdownMenuItem v-if="!isAdminUser" :as-child="true">
            <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors hover:bg-surface-container-highest dark:hover:bg-surface-3" :href="route('student.payments.index')">
                <CreditCard class="h-4 w-4 opacity-70" />
                Mis pagos
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />

    <DropdownMenuItem :as-child="true">
        <Link class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-950/30" method="post" :href="route('logout')" as="button">
            <LogOut class="h-4 w-4" />
            Cerrar sesión
        </Link>
    </DropdownMenuItem>
</template>
