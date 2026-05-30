<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { LayoutGrid, BookOpen, ClipboardCheck, CreditCard, Menu, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import { useSidebar } from '@/components/ui/sidebar';
import type { SharedData } from '@/types';

defineProps<{
    active?: string;
}>();

const page = usePage<SharedData>();
const user = page.props.auth?.user;
const isAdmin = user?.role === 'admin';

let setOpenMobile: ((value: boolean) => void) | null = null;
try {
    const sidebar = useSidebar();
    setOpenMobile = sidebar.setOpenMobile;
} catch (_e) {
    // Fuera del contexto del sidebar
}

const studentItems = [
    { key: 'dashboard', label: 'Inicio', icon: LayoutGrid, routeName: 'dashboard', path: '/dashboard' },
    { key: 'courses', label: 'Cursos', icon: BookOpen, routeName: 'student.courses.index', path: '/student/courses' },
    { key: 'exams', label: 'Exámenes', icon: ClipboardCheck, routeName: 'student.exams.index', path: '/student/exams' },
    { key: 'payments', label: 'Pagos', icon: CreditCard, routeName: 'student.payments.index', path: '/student/payments' },
];

const adminItems = [
    { key: 'admin-dashboard', label: 'Inicio', icon: LayoutGrid, routeName: 'admin.dashboard', path: '/admin/dashboard' },
    { key: 'admin-users', label: 'Usuarios', icon: Users, routeName: 'admin.users.index', path: '/admin/users' },
    { key: 'admin-courses', label: 'Cursos', icon: BookOpen, routeName: 'admin.courses.index', path: '/admin/courses' },
    { key: 'admin-payments', label: 'Pagos', icon: CreditCard, routeName: 'admin.payments.index', path: '/admin/payments' },
];

const items = computed(() => (isAdmin ? adminItems : studentItems));

function isActive(itemPath: string): boolean {
    const url = page.url;
    if (itemPath === '/dashboard') {
        return url === '/dashboard' || url.startsWith('/dashboard?');
    }
    if (itemPath === '/admin/dashboard') {
        return url === '/admin/dashboard' || url.startsWith('/admin/dashboard?');
    }
    if (itemPath === '/admin/courses') {
        return url.startsWith('/admin/courses') || url.startsWith('/admin/categories') || url.startsWith('/admin/certificates');
    }
    if (itemPath === '/admin/payments') {
        return url.startsWith('/admin/payments') || url.startsWith('/admin/subscriptions');
    }
    if (itemPath === '/student/exams') {
        return url.startsWith('/student/exams');
    }
    if (itemPath === '/student/courses') {
        return url.startsWith('/student/courses') || url.startsWith('/student/classroom');
    }
    return url.startsWith(itemPath);
}

function openMenu() {
    if (setOpenMobile) {
        setOpenMobile(true);
    }
}
</script>

<template>
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-outline-variant/10 shadow-[0_-8px_30px_rgba(0,0,0,0.07)] pb-safe">
        <div class="flex items-center justify-around px-1 py-1.5">
            <Link
                v-for="item in items"
                :key="item.key"
                :href="route(item.routeName)"
                class="flex flex-col items-center gap-0.5 py-1.5 px-2.5 rounded-xl transition-all active:scale-90 relative min-w-0 flex-1"
                :class="isActive(item.path) ? 'text-primary' : 'text-on-surface-variant hover:text-primary'"
            >
                <span
                    v-if="isActive(item.path)"
                    class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-6 h-1 bg-primary rounded-full"
                />
                <component :is="item.icon" class="w-[18px] h-[18px] shrink-0" />
                <span class="text-[8px] font-black uppercase tracking-wider truncate max-w-full">{{ item.label }}</span>
            </Link>

            <button
                type="button"
                @click="openMenu"
                class="flex flex-col items-center gap-0.5 py-1.5 px-2.5 rounded-xl transition-all active:scale-90 text-on-surface-variant hover:text-primary min-w-0 flex-1"
            >
                <Menu class="w-[18px] h-[18px] shrink-0" />
                <span class="text-[8px] font-black uppercase tracking-wider">Más</span>
            </button>
        </div>
    </nav>
</template>
