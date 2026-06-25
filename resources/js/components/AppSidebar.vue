<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarRail } from '@/components/ui/sidebar';
import { TooltipProvider } from '@/components/ui/tooltip';
import { type SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import AppSidebarLogo from './AppSidebarLogo.vue';
import AppSidebarLogout from './AppSidebarLogout.vue';
import AppSidebarNav from './AppSidebarNav.vue';
import AppSidebarUserCard from './AppSidebarUserCard.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';
</script>

<template>
    <TooltipProvider :delay-duration="0">
        <!-- Using global generic tailwind classes to set sidebar dark theme background without scoped css -->
        <Sidebar
            collapsible="icon"
            variant="inset"
            class="border-r border-outline-variant/10 bg-surface-container-lowest shadow-lg dark:bg-[#161610]"
        >
            <SidebarHeader class="overflow-hidden bg-transparent p-0">
                <AppSidebarLogo :is-admin="isAdmin" />
                <AppSidebarUserCard :user="user" :is-admin="isAdmin" />
            </SidebarHeader>

            <!-- AppSidebarNav component itself is flex-1 overflow-y-auto, so SidebarContent acts as wrapper -->
            <SidebarContent class="flex flex-col overflow-hidden bg-transparent p-0">
                <AppSidebarNav />
            </SidebarContent>

            <SidebarFooter class="bg-transparent p-0">
                <AppSidebarLogout />
            </SidebarFooter>

            <SidebarRail />
        </Sidebar>
    </TooltipProvider>
</template>
