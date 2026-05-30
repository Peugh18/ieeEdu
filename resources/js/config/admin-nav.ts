import {
    LayoutGrid, BookOpen, Award, Folder, Crown, Users, CreditCard, FileText, Newspaper, Image as ImageIcon, Briefcase
} from 'lucide-vue-next';
import type { NavGroup } from '@/types/navigation';

export const adminNavItems: NavGroup[] = [
    {
        label: 'General',
        items: [
            { title: 'Dashboard',      href: 'admin.dashboard',           icon: LayoutGrid },
        ]
    },
    {
        label: 'Académico',
        items: [
            { title: 'Usuarios',       href: 'admin.users.index',         icon: Users },
            { title: 'Cursos',         href: 'admin.courses.index',       icon: BookOpen },
            { title: 'Certificados',   href: 'admin.certificates.index',  icon: Award },
            { title: 'Categorías',     href: 'admin.categories.index',    icon: Folder },
        ]
    },
    {
        label: 'Finanzas',
        items: [
            { title: 'Pagos',          href: 'admin.payments.index',      icon: CreditCard },
            { title: 'Suscripciones',  href: 'admin.subscriptions.index', icon: Crown },
        ]
    },
    {
        label: 'Contenido',
        items: [
            { title: 'Libros',         href: 'admin.books.index',         icon: FileText },
            { title: 'Artículos',      href: 'admin.articles.index',      icon: Newspaper },
        ]
    },
    {
        label: 'Marketing',
        items: [
            { title: 'Banners',        href: 'admin.banners.index',       icon: ImageIcon },
            { title: 'Consultorías',   href: 'admin.consultancies.index', icon: Briefcase },
        ]
    }
];
