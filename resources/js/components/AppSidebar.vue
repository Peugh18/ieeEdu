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
                        <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex items-center gap-2">
                            <AppLogo />
                            <span class="font-serif font-bold text-on-surface">{{ isAdmin ? 'IEE - Admin' : 'Aula Virtual' }}</span>
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
