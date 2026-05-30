import type { LucideIcon } from 'lucide-vue-next';

export interface NavItem {
    title: string;
    href: string;
    icon: LucideIcon;
    badge?: string | number;
}

export interface NavGroup {
    label: string;
    items: NavItem[];
}
