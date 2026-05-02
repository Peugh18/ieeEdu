<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarRail } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
    LayoutGrid, BookOpen, Calendar, ClipboardCheck, Award,
    Folder, Crown, LogOut, ChevronRight, Settings,
    Users, CreditCard, FileText, Newspaper, Image as ImageIcon
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { useSidebar } from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import BrandLogo from '@/components/BrandLogo.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';
const { state, isMobile } = useSidebar();

const avatarUrl = computed(() =>
    user.avatar
        ? '/storage/' + user.avatar
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=e7e6ab&bold=true&size=128`
);

const userInitials = computed(() => {
    return user.name.split(' ').map((n: string) => n[0]).slice(0, 2).join('').toUpperCase();
});

interface NavItem {
    title: string;
    href: string;
    icon: any;
    badge?: string | number;
}

const adminNavItems: NavItem[] = [
    { title: 'Dashboard',      href: 'admin.dashboard',           icon: LayoutGrid },
    { title: 'Usuarios',       href: 'admin.users.index',         icon: Users },
    { title: 'Cursos',         href: 'admin.courses.index',       icon: BookOpen },
    { title: 'Categorías',     href: 'admin.categories.index',    icon: Folder },
    { title: 'Pagos',          href: 'admin.payments.index',      icon: CreditCard },
    { title: 'Suscripciones',  href: 'admin.subscriptions.index', icon: Crown },
    { title: 'Libros',         href: 'admin.books.index',         icon: FileText },
    { title: 'Artículos',      href: 'admin.articles.index',      icon: Newspaper },
    { title: 'Banners',        href: 'admin.banners.index',       icon: ImageIcon },
];

const studentNavItems: NavItem[] = [
    { title: 'Mi Dashboard',   href: 'dashboard',                    icon: LayoutGrid },
    { title: 'Mis Cursos',     href: 'student.courses.index',        icon: BookOpen },
    { title: 'Clases en Vivo', href: 'student.live-classes.index',   icon: Calendar },
    { title: 'Mis Exámenes',   href: 'student.exams.index',          icon: ClipboardCheck },
    { title: 'Certificados',   href: 'student.certificates.index',   icon: Award },
];

const mainNavItems = isAdmin ? adminNavItems : studentNavItems;

function isActive(href: string): boolean {
    try {
        const resolved = route(href, undefined, false);
        return page.url.startsWith(resolved);
    } catch {
        return false;
    }
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <!-- ═══════════════════════════════════════════════
         IIE Elite Sidebar — Premium Dark Edition
    ════════════════════════════════════════════════ -->
    <TooltipProvider :delay-duration="0">
    <Sidebar collapsible="icon" variant="inset">

        <!-- ── HEADER: Logo + Brand ─────────────────────── -->
        <SidebarHeader class="p-0 overflow-hidden">
            <div :class="['iie-sidebar-logo group', { 'iie-sidebar-logo--collapsed': state === 'collapsed' }]">
                <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex flex-col items-center w-full gap-2 py-4">
                    <!-- Dynamic Logo Logic -->
                    <div class="relative w-full flex items-center justify-center min-h-[4rem]">
                        <!-- Full Logo (Expanded) -->
                        <div 
                            class="iie-logo-full-container transition-all duration-300"
                            :class="state === 'collapsed' ? 'opacity-0 scale-75 -translate-y-4 pointer-events-none absolute' : 'opacity-100 scale-100 translate-y-0'"
                        >
                            <BrandLogo 
                                forceTheme="dark"
                                imageClass="iie-logo-img-full"
                            />
                            <span class="iie-brand-sub block mt-1">{{ isAdmin ? 'Panel Administrativo' : 'Aula Virtual' }}</span>
                        </div>

                        <!-- Icon Logo (Collapsed) -->
                        <div 
                            class="iie-logo-icon-container transition-all duration-300"
                            :class="state === 'expanded' ? 'opacity-0 scale-125 translate-y-4 pointer-events-none absolute' : 'opacity-100 scale-100 translate-y-0'"
                        >
                            <img
                                src="/images/empresa/IEE-Logo.png"
                                alt="IEE Icon"
                                class="iie-logo-icon-img"
                            />
                        </div>
                    </div>
                </Link>
            </div>

            <!-- ── USER PROFILE CARD ───────────────────────── -->
            <Tooltip :disabled="state === 'expanded'">
                <TooltipTrigger as-child>
                    <Link
                        :href="isAdmin ? route('profile.edit') : route('student.profile.index')"
                        :class="['iie-user-card group', { 'iie-user-card--collapsed': state === 'collapsed' }]"
                    >
                        <!-- Avatar -->
                        <div class="iie-avatar-ring">
                            <img :src="avatarUrl" :alt="user.name" class="iie-avatar-img" />
                            <div class="iie-avatar-status"></div>
                        </div>

                        <!-- User info -->
                        <div class="iie-user-info" v-if="state === 'expanded'">
                            <span class="iie-user-name">{{ user.name }}</span>
                            <span class="iie-user-role">
                                <Settings class="iie-user-role-icon" />
                                <span class="truncate">Configuración</span>
                            </span>
                        </div>

                        <!-- Arrow indicator -->
                        <ChevronRight class="iie-user-arrow" v-if="state === 'expanded'" />
                    </Link>
                </TooltipTrigger>
                <TooltipContent side="right" class="bg-[#282818] border-primary-fixed/20 text-primary-fixed font-bold">
                    <p>{{ user.name }}</p>
                </TooltipContent>
            </Tooltip>
        </SidebarHeader>

        <!-- ── NAVIGATION ────────────────────────────────── -->
        <SidebarContent class="iie-nav-content">
            <!-- Section Label -->
            <div class="iie-section-label" v-if="state === 'expanded'">
                <span>Plataforma</span>
            </div>
            <div v-else class="h-4"></div>

            <!-- Nav Items -->
            <nav class="iie-nav-list">
                <Tooltip v-for="item in mainNavItems" :key="item.title" :disabled="state === 'expanded'">
                    <TooltipTrigger as-child>
                        <Link
                            :href="route(item.href)"
                            :class="[
                                'iie-nav-item', 
                                { 'iie-nav-item--active': isActive(item.href) },
                                { 'iie-nav-item--collapsed': state === 'collapsed' }
                            ]"
                        >
                            <!-- Active Bar -->
                            <div v-if="isActive(item.href)" class="iie-nav-active-bar"></div>

                            <!-- Icon -->
                            <div class="iie-nav-icon-wrap">
                                <component :is="item.icon" class="iie-nav-icon" />
                            </div>

                            <!-- Label -->
                            <span class="iie-nav-label" v-if="state === 'expanded'">{{ item.title }}</span>

                            <!-- Badge -->
                            <span v-if="item.badge && state === 'expanded'" class="iie-nav-badge">{{ item.badge }}</span>

                            <!-- Active Arrow -->
                            <ChevronRight v-if="isActive(item.href) && state === 'expanded'" class="iie-nav-chevron" />
                        </Link>
                    </TooltipTrigger>
                    <TooltipContent side="right" class="bg-[#282818] border-primary-fixed/20 text-primary-fixed font-bold">
                        <div class="flex items-center gap-2">
                            <span>{{ item.title }}</span>
                            <span v-if="item.badge" class="bg-primary-fixed/20 px-1.5 py-0.5 rounded text-[10px]">{{ item.badge }}</span>
                        </div>
                    </TooltipContent>
                </Tooltip>
            </nav>
        </SidebarContent>

        <!-- ── FOOTER: Logout ─────────────────────────────── -->
        <SidebarFooter class="iie-sidebar-footer">
            <div class="iie-footer-divider"></div>
            <Tooltip :disabled="state === 'expanded'">
                <TooltipTrigger as-child>
                    <button @click="logout" :class="['iie-logout-btn group', { 'iie-logout-btn--collapsed': state === 'collapsed' }]">
                        <div class="iie-logout-icon-wrap">
                            <LogOut class="iie-logout-icon" />
                        </div>
                        <span class="iie-logout-label" v-if="state === 'expanded'">Cerrar Sesión</span>
                    </button>
                </TooltipTrigger>
                <TooltipContent side="right" class="bg-[#ba1a1a] border-white/20 text-white font-bold">
                    <p>Cerrar Sesión</p>
                </TooltipContent>
            </Tooltip>
        </SidebarFooter>

        <SidebarRail />
    </Sidebar>
    </TooltipProvider>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════
   IIE ELITE SIDEBAR — Design System Variables
   Palette: dark charcoal-olive, golden olive, cream
════════════════════════════════════════════════════ */

/* ── CSS VARIABLES ──────────────────────────────── */
.iie-sidebar-logo,
.iie-user-card,
.iie-nav-content,
.iie-sidebar-footer {
    --iie-dark:       #161610;
    --iie-dark-2:     #1f1f14;
    --iie-dark-3:     #282818;
    --iie-primary:    #57572a;
    --iie-primary-l:  #707040;
    --iie-gold:       #e7e6ab;
    --iie-gold-dim:   #caca91;
    --iie-cream:      #f5f4e8;
    --iie-muted:      rgba(231, 230, 171, 0.35);
    --iie-muted-2:    rgba(231, 230, 171, 0.15);
    --iie-muted-3:    rgba(231, 230, 171, 0.06);
    --iie-border:     rgba(231, 230, 171, 0.08);
    --iie-shadow:     0 0 40px rgba(87, 87, 42, 0.25);
    --iie-glow:       0 0 20px rgba(231, 230, 171, 0.12);
    --iie-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ═══════════════════════════════════════════════════
   SIDEBAR BASE OVERRIDE — Shadow the shadcn defaults
════════════════════════════════════════════════════ */
:deep([data-sidebar="sidebar"]) {
    background: #161610 !important;
    border-right: 1px solid rgba(231, 230, 171, 0.06) !important;
    box-shadow: 4px 0 32px rgba(0, 0, 0, 0.5) !important;
}

:deep([data-sidebar="content"]) {
    background: transparent !important;
}

/* ═══════════════════════════════════════════════════
   LOGO SECTION
════════════════════════════════════════════════════ */
.iie-sidebar-logo {
    display: flex;
    align-items: center;
    padding: 1.5rem 1rem 1.25rem;
    position: relative;
    overflow: hidden;
}

.iie-sidebar-logo::after {
    content: '';
    position: absolute;
    bottom: 0; left: 1rem; right: 1rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(231, 230, 171, 0.15), transparent);
}

/* Single full logo — big and centered */
.iie-logo-img-full {
    height: 3.5rem;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    object-position: center;
    display: block;
    margin: 0 auto;
    transition: transform 0.35s ease;
}

.iie-sidebar-logo .group:hover .iie-logo-img-full {
    transform: scale(1.04);
}

.iie-brand-sub {
    font-size: 0.58rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: rgba(231, 230, 171, 0.35);
    white-space: nowrap;
    line-height: 1;
    text-align: center;
    width: 100%;
}

/* Collapsed Logo Image */
.iie-logo-icon-img {
    height: 2.5rem;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 0 12px rgba(231, 230, 171, 0.15));
    transition: transform 0.3s ease;
}

.iie-sidebar-logo:hover .iie-logo-icon-img {
    transform: scale(1.1);
}

/* ═══════════════════════════════════════════════════
   USER CARD
════════════════════════════════════════════════════ */
.iie-user-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    margin: 0.75rem 0.75rem 0;
    border-radius: 1rem;
    background: rgba(231, 230, 171, 0.04);
    border: 1px solid rgba(231, 230, 171, 0.08);
    transition: var(--iie-transition);
    position: relative;
    overflow: hidden;
    text-decoration: none;
}

.iie-user-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(87, 87, 42, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: inherit;
}

.iie-user-card:hover::before {
    opacity: 1;
}

.iie-user-card:hover {
    background: rgba(231, 230, 171, 0.07);
    border-color: rgba(231, 230, 171, 0.15);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

/* Avatar Ring */
.iie-avatar-ring {
    position: relative;
    flex-shrink: 0;
}

.iie-avatar-img {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(231, 230, 171, 0.2);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
    transition: var(--iie-transition);
}

.iie-user-card:hover .iie-avatar-img {
    border-color: rgba(231, 230, 171, 0.4);
    transform: scale(1.06);
}

.iie-avatar-status {
    position: absolute;
    bottom: 0; right: 0;
    width: 0.6rem;
    height: 0.6rem;
    border-radius: 50%;
    background: #4ade80;
    border: 2px solid #161610;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

/* User Info */
.iie-user-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    overflow: hidden;
    min-width: 0;
}

.iie-user-name {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(231, 230, 171, 0.9);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}

.iie-user-role {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(231, 230, 171, 0.3);
    transition: color 0.2s ease;
}

.iie-user-card:hover .iie-user-role {
    color: rgba(231, 230, 171, 0.6);
}

.iie-user-role-icon {
    width: 0.65rem;
    height: 0.65rem;
    flex-shrink: 0;
}

.iie-user-arrow {
    width: 0.875rem;
    height: 0.875rem;
    color: rgba(231, 230, 171, 0.2);
    flex-shrink: 0;
    transition: var(--iie-transition);
}

.iie-user-card:hover .iie-user-arrow {
    color: rgba(231, 230, 171, 0.5);
    transform: translateX(3px);
}

/* ═══════════════════════════════════════════════════
   NAVIGATION CONTENT
════════════════════════════════════════════════════ */
.iie-nav-content {
    padding: 0.5rem 0.75rem;
    overflow-y: auto;
}

.iie-nav-content::-webkit-scrollbar { width: 2px; }
.iie-nav-content::-webkit-scrollbar-track { background: transparent; }
.iie-nav-content::-webkit-scrollbar-thumb { background: rgba(231, 230, 171, 0.1); border-radius: 10px; }

/* Section Label */
.iie-section-label {
    padding: 0.875rem 0.5rem 0.5rem;
}

.iie-section-label span {
    font-size: 0.6rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: rgba(231, 230, 171, 0.2);
    line-height: 1;
}

/* Nav List */
.iie-nav-list {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

/* Nav Item */
.iie-nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.75rem;
    border-radius: 0.75rem;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    transition: var(--iie-transition);
    cursor: pointer;
    border: 1px solid transparent;
}

/* Default State */
.iie-nav-item:not(.iie-nav-item--active) {
    background: transparent;
    color: rgba(231, 230, 171, 0.45);
}

.iie-nav-item:not(.iie-nav-item--active):hover {
    background: rgba(231, 230, 171, 0.05);
    color: rgba(231, 230, 171, 0.8);
    border-color: rgba(231, 230, 171, 0.06);
    transform: translateX(3px);
}

/* ── ACTIVE STATE ───────────────────────────────── */
.iie-nav-item--active {
    background: linear-gradient(135deg, rgba(87, 87, 42, 0.9), rgba(112, 112, 64, 0.85));
    border-color: rgba(231, 230, 171, 0.15);
    color: #e7e6ab;
    box-shadow:
        0 4px 20px rgba(87, 87, 42, 0.5),
        0 0 0 1px rgba(231, 230, 171, 0.08) inset;
    transform: translateX(0);
}

/* Active Glow Overlay */
.iie-nav-active-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 20% 50%, rgba(231, 230, 171, 0.12), transparent 60%);
    pointer-events: none;
    border-radius: inherit;
}

/* Active Left Bar */
.iie-nav-active-bar {
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 3px;
    border-radius: 0 6px 6px 0;
    background: linear-gradient(180deg, #e7e6ab, #caca91);
    box-shadow: 0 0 12px rgba(231, 230, 171, 0.6);
}

/* Nav Icon Wrap */
.iie-nav-icon-wrap {
    width: 1.875rem;
    height: 1.875rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: var(--iie-transition);
}

.iie-nav-item:not(.iie-nav-item--active) .iie-nav-icon-wrap {
    background: rgba(231, 230, 171, 0.05);
}

.iie-nav-item--active .iie-nav-icon-wrap {
    background: rgba(231, 230, 171, 0.15);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.iie-nav-item:not(.iie-nav-item--active):hover .iie-nav-icon-wrap {
    background: rgba(231, 230, 171, 0.08);
    transform: scale(1.1);
}

/* Nav Icon */
.iie-nav-icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
    transition: var(--iie-transition);
}

.iie-nav-item--active .iie-nav-icon {
    filter: drop-shadow(0 0 4px rgba(231, 230, 171, 0.4));
}

/* Nav Label */
.iie-nav-label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: var(--iie-transition);
}

/* Badge */
.iie-nav-badge {
    font-size: 0.6rem;
    font-weight: 800;
    padding: 0.15rem 0.45rem;
    border-radius: 999px;
    background: rgba(231, 230, 171, 0.15);
    color: rgba(231, 230, 171, 0.7);
    border: 1px solid rgba(231, 230, 171, 0.1);
    line-height: 1.4;
}

.iie-nav-item--active .iie-nav-badge {
    background: rgba(231, 230, 171, 0.25);
    color: #e7e6ab;
}

/* Active Chevron */
.iie-nav-chevron {
    width: 0.875rem;
    height: 0.875rem;
    color: rgba(231, 230, 171, 0.5);
    flex-shrink: 0;
    animation: pulseRight 1.8s ease-in-out infinite;
}

@keyframes pulseRight {
    0%, 100% { transform: translateX(0); opacity: 0.5; }
    50%       { transform: translateX(2px); opacity: 1; }
}

/* ═══════════════════════════════════════════════════
   FOOTER — LOGOUT
════════════════════════════════════════════════════ */
.iie-sidebar-footer {
    padding: 0.75rem;
}

.iie-footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(231, 230, 171, 0.08), transparent);
    margin-bottom: 0.75rem;
}

.iie-logout-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.75rem;
    border-radius: 0.75rem;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    transition: var(--iie-transition);
    text-align: left;
}

.iie-logout-btn:hover {
    background: rgba(186, 26, 26, 0.12);
    border-color: rgba(186, 26, 26, 0.2);
    transform: translateX(2px);
}

.iie-logout-icon-wrap {
    width: 1.875rem;
    height: 1.875rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(231, 230, 171, 0.04);
    border: 1px solid rgba(231, 230, 171, 0.06);
    transition: var(--iie-transition);
}

.iie-logout-btn:hover .iie-logout-icon-wrap {
    background: rgba(186, 26, 26, 0.15);
    border-color: rgba(186, 26, 26, 0.25);
}

.iie-logout-icon {
    width: 1rem;
    height: 1rem;
    color: rgba(231, 230, 171, 0.3);
    transition: var(--iie-transition);
}

.iie-logout-btn:hover .iie-logout-icon {
    color: #f87171;
    filter: drop-shadow(0 0 6px rgba(248, 113, 113, 0.4));
}

.iie-logout-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(231, 230, 171, 0.3);
    transition: var(--iie-transition);
}

.iie-logout-btn:hover .iie-logout-label {
    color: #f87171;
}

/* ═══════════════════════════════════════════════════
   COLLAPSED / ICON MODE — Preserve brand feel
   Note: Shadcn uses [data-collapsible=icon] on the sidebar,
   but we also use our local state for smoother animations.
════════════════════════════════════════════════════ */

/* Dynamic Width for Logo */
.iie-sidebar-logo {
    transition: var(--iie-transition);
}

.iie-sidebar-logo--collapsed {
    padding-left: 0;
    padding-right: 0;
}

.iie-logo-full-container,
.iie-logo-icon-container {
    will-change: transform, opacity;
}

/* User Card Collapsed */
.iie-user-card--collapsed {
    margin-left: 0.5rem;
    margin-right: 0.5rem;
    padding: 0.75rem 0;
    justify-content: center;
    border-radius: 0.75rem;
}

/* Nav Item Collapsed */
.iie-nav-item--collapsed {
    padding-left: 0;
    padding-right: 0;
    justify-content: center;
    gap: 0;
}

.iie-nav-item--collapsed .iie-nav-icon-wrap {
    margin: 0 auto;
}

/* Logout Button Collapsed */
.iie-logout-btn--collapsed {
    padding-left: 0;
    padding-right: 0;
    justify-content: center;
}

/* Smooth Progressive Hiding for labels (if any remained) */
.iie-nav-label, .iie-user-info, .iie-logout-label {
    animation: fadeIn 0.3s ease forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(-4px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>
