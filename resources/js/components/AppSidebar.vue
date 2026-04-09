<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData, type User } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Calendar, ClipboardCheck, Award, Settings, LogOut } from 'lucide-vue-next';
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
];

const mainNavItems = isAdmin ? adminNavItems : studentNavItems;
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border/30">
        <SidebarHeader class="p-4 relative">
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
            
            <div class="mt-4 pt-4 border-t border-sidebar-border/30">
                <Link :href="isAdmin ? route('profile.edit') : route('student.profile.index')" class="flex items-center gap-3 p-2 rounded-xl hover:bg-outline-variant/10 transition-colors group cursor-pointer w-full text-left">
                    <img :src="user.avatar ? '/storage/'+user.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=fff`" class="w-9 h-9 rounded-full object-cover shadow-sm group-hover:scale-105 transition-transform shrink-0" />
                    <div class="flex flex-col overflow-hidden w-full">
                        <span class="text-xs font-bold text-on-surface truncate uppercase">{{ user.name }}</span>
                        <span class="text-[9px] text-on-surface-variant hover:text-primary transition-colors tracking-widest uppercase">
                            Configuración
                        </span>
                    </div>
                </Link>
            </div>
        </SidebarHeader>

        <SidebarContent class="px-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="p-4 border-t border-sidebar-border/30">
            <Link method="post" as="button" :href="route('logout')" class="flex items-center gap-3 w-full p-3 rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors group font-bold text-xs uppercase tracking-widest">
                <LogOut class="w-4 h-4 opacity-70 group-hover:opacity-100" />
                Cerrar Sesión
            </Link>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
