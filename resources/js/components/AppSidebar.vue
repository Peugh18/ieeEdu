<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarRail } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { TooltipProvider } from '@/components/ui/tooltip';
import AppSidebarLogo from './AppSidebarLogo.vue';
import AppSidebarUserCard from './AppSidebarUserCard.vue';
import AppSidebarNav from './AppSidebarNav.vue';
import AppSidebarLogout from './AppSidebarLogout.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';
</script>

<template>
    <TooltipProvider :delay-duration="0">
        <!-- Using global generic tailwind classes to set sidebar dark theme background without scoped css -->
        <Sidebar collapsible="icon" variant="inset" class="dark:bg-[#161610] bg-surface-container-lowest border-r border-outline-variant/10 shadow-lg">
            <SidebarHeader class="p-0 overflow-hidden bg-transparent">
                <AppSidebarLogo :is-admin="isAdmin" />
                <AppSidebarUserCard :user="user" :is-admin="isAdmin" />
            </SidebarHeader>

            <!-- AppSidebarNav component itself is flex-1 overflow-y-auto, so SidebarContent acts as wrapper -->
            <SidebarContent class="p-0 overflow-hidden bg-transparent flex flex-col">
                <AppSidebarNav />
            </SidebarContent>

            <SidebarFooter class="p-0 bg-transparent">
                <AppSidebarLogout />
            </SidebarFooter>

            <SidebarRail />
        </Sidebar>
    </TooltipProvider>
</template>
