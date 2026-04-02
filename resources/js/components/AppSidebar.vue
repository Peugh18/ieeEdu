<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: 'admin.dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: 'admin.users.index',
        icon: Folder,
    },
    {
        title: 'Cursos',
        href: 'admin.courses.index',
        icon: BookOpen,
    },
    {
        title: 'Categorías',
        href: 'admin.categories.index',
        icon: Folder,
    },
    {
        title: 'Pagos',
        href: 'admin.payments.index',
        icon: Folder,
    },
    {
        title: 'Libros',
        href: 'admin.books.index',
        icon: BookOpen,
    },
    {
        title: 'Artículos',
        href: 'admin.articles.index',
        icon: Folder,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('admin.dashboard')" class="flex items-center gap-2">
                            <AppLogo />
                            <span class="font-serif font-bold text-on-surface">IEE - Admin</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
