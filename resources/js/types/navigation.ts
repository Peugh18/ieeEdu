import type { LucideIcon } from 'lucide-vue-next';

export interface NavItem {
    title: string;
    href: string;
    icon: LucideIcon;
    badge?: string | number;
    /** Prefijos de URL que marcan este ítem como activo (ej. /admin/payments). */
    matchPaths?: string[];
}

export interface NavGroup {
    label?: string;
    items: NavItem[];
}
