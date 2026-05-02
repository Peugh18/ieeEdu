<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { LayoutGrid, BookOpen, Calendar, ClipboardCheck, Award, LogOut, Users, CreditCard, Menu } from 'lucide-vue-next';
import { computed } from 'vue';
import { useSidebar } from '@/components/ui/sidebar';

// El prop 'active' se mantiene por compatibilidad hacia atrás,
// pero ahora la detección es automática usando la URL actual
defineProps<{
    active?: string;
}>();

const page = usePage();
const user = page.props.auth.user as any;
const isAdmin = user?.role === 'admin';

// Importante: useSidebar solo funciona si el componente está dentro de un SidebarProvider
// Como BottomNav puede estar fuera, capturamos el error o usamos un fallback
let setOpenMobile: ((value: boolean) => void) | null = null;
try {
    const sidebar = useSidebar();
    setOpenMobile = sidebar.setOpenMobile;
} catch (e) {
    // Fuera del contexto del sidebar
}

const studentItems = [
    { key: 'dashboard',     label: 'Inicio',      icon: LayoutGrid,     routeName: 'dashboard',                      path: '/dashboard' },
    { key: 'courses',       label: 'Cursos',       icon: BookOpen,       routeName: 'student.courses.index',          path: '/student/courses' },
    { key: 'live-classes',  label: 'Clases',       icon: Calendar,       routeName: 'student.live-classes.index',     path: '/student/live-classes' },
    { key: 'exams',         label: 'Exámenes',     icon: ClipboardCheck, routeName: 'student.exams.index',            path: '/student/exams' },
    { key: 'certificates',  label: 'Certs',        icon: Award,          routeName: 'student.certificates.index',     path: '/student/certificates' },
];

const adminItems = [
    { key: 'admin-dashboard', label: 'Inicio',    icon: LayoutGrid,     routeName: 'admin.dashboard',                path: '/admin/dashboard' },
    { key: 'admin-users',     label: 'Usuarios',  icon: Users,          routeName: 'admin.users.index',              path: '/admin/users' },
    { key: 'admin-courses',   label: 'Cursos',    icon: BookOpen,       routeName: 'admin.courses.index',            path: '/admin/courses' },
    { key: 'admin-payments',  label: 'Pagos',     icon: CreditCard,     routeName: 'admin.payments.index',           path: '/admin/payments' },
];

const items = computed(() => isAdmin ? adminItems : studentItems);

// Detecta automáticamente el ítem activo por URL
function isActive(itemPath: string): boolean {
    const url = page.url;
    // Dashboard es exacto para evitar falsos positivos
    if (itemPath === '/dashboard') return url === '/dashboard' || url.startsWith('/dashboard?');
    if (itemPath === '/admin/dashboard') return url === '/admin/dashboard' || url.startsWith('/admin/dashboard?');
    return url.startsWith(itemPath);
}

function handleMenuClick() {
    if (setOpenMobile) {
        setOpenMobile(true);
    } else {
        // Fallback si no está el provider
        console.warn('SidebarProvider not found');
    }
}

function logout() {
    if (confirm('¿Deseas cerrar sesión?')) {
        router.post(route('logout'));
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
                class="flex flex-col items-center gap-0.5 py-1.5 px-3 rounded-xl transition-all active:scale-90 relative"
                :class="isActive(item.path)
                    ? 'text-primary'
                    : 'text-on-surface-variant hover:text-primary'"
            >
                <!-- Indicador superior activo -->
                <span
                    v-if="isActive(item.path)"
                    class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-6 h-1 bg-primary rounded-full"
                ></span>
                <component :is="item.icon" class="w-[18px] h-[18px]" :class="isActive(item.path) ? 'drop-shadow-sm' : ''" />
                <span class="text-[8px] font-black uppercase tracking-widest">{{ item.label }}</span>
            </Link>

            <!-- Menú Drawer para Admin -->
            <button
                v-if="isAdmin"
                @click="handleMenuClick"
                class="flex flex-col items-center gap-0.5 py-1.5 px-3 rounded-xl transition-all active:scale-90 text-on-surface-variant hover:text-primary"
            >
                <Menu class="w-[18px] h-[18px]" />
                <span class="text-[8px] font-black uppercase tracking-widest">Menú</span>
            </button>

            <!-- Botón de Cerrar Sesión en Móvil (solo si no es admin) -->
            <button
                v-else
                @click="logout"
                class="flex flex-col items-center gap-0.5 py-1.5 px-3 rounded-xl transition-all active:scale-90 text-red-400 hover:text-red-600"
            >
                <LogOut class="w-[18px] h-[18px]" />
                <span class="text-[8px] font-black uppercase tracking-widest">Salir</span>
            </button>
        </div>
    </nav>
</template>
