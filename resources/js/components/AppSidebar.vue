<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData, type User } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Calendar, ClipboardCheck, Award, Settings } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';

const adminNavItems: NavItem[] = [
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

const studentNavItems: NavItem[] = [
    {
        title: 'Mi Dashboard',
        href: 'dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Mis Cursos',
        href: 'student.courses.index',
        icon: BookOpen,
    },
    {
        title: 'Clases en Vivo',
        href: 'student.live-classes.index',
        icon: Calendar,
    },
    {
        title: 'Mis Exámenes',
        href: 'student.exams.index',
        icon: ClipboardCheck,
    },
    {
        title: 'Certificados',
        href: 'student.certificates.index',
        icon: Award,
    },
    {
        title: 'Ajustes',
        href: 'profile.edit',
        icon: Settings,
    },
];

const mainNavItems = isAdmin ? adminNavItems : studentNavItems;
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border/30">
        <SidebarHeader class="p-4">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex items-center gap-3 group">
                            <AppLogo />
                            <div class="flex flex-col gap-0.5 group-hover:translate-x-1 transition-transform">
                                <span class="font-serif font-bold text-sm tracking-tighter italic text-[#57572a] leading-none uppercase">{{ isAdmin ? 'Admin' : 'Aula Virtual' }}</span>
                                <span class="text-[9px] font-bold text-muted-foreground tracking-[0.2em] uppercase leading-none">IIE - Institute</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="px-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="p-6 border-t border-sidebar-border/30">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
