import {
    LayoutGrid,
    BookOpen,
    Users,
    CreditCard,
    FileText,
    FolderTree,
    Award,
    Crown,
    Package,
    BookMarked,
    Newspaper,
    Image,
    Briefcase,
    Building2,
    Sparkles,
} from 'lucide-vue-next';
import type { NavGroup } from '@/types/navigation';

/** Navegación admin: un enlace por pantalla. Sin hubs ni pestañas duplicadas. */
export const adminNavItems: NavGroup[] = [
    {
        label: 'General',
        items: [
            {
                title: 'Dashboard',
                href: 'admin.dashboard',
                icon: LayoutGrid,
                matchPaths: ['/admin/dashboard'],
            },
        ],
    },
    {
        label: 'Académico',
        items: [
            {
                title: 'Usuarios',
                href: 'admin.users.index',
                icon: Users,
                matchPaths: ['/admin/users'],
            },
            {
                title: 'Catálogo de cursos',
                href: 'admin.courses.index',
                icon: BookOpen,
                matchPaths: ['/admin/courses'],
            },
            {
                title: 'Categorías',
                href: 'admin.categories.index',
                icon: FolderTree,
                matchPaths: ['/admin/categories'],
            },
            {
                title: 'Certificados',
                href: 'admin.certificates.index',
                icon: Award,
                matchPaths: ['/admin/certificates'],
            },
        ],
    },
    {
        label: 'Ventas',
        items: [
            {
                title: 'Comprobantes',
                href: 'admin.payments.index',
                icon: CreditCard,
                matchPaths: ['/admin/payments'],
            },
            {
                title: 'Membresías',
                href: 'admin.subscriptions.index',
                icon: Crown,
                matchPaths: ['/admin/subscriptions'],
            },
            {
                title: 'Pedidos de libros',
                href: 'admin.book-orders.index',
                icon: Package,
                matchPaths: ['/admin/book-orders'],
            },
        ],
    },
    {
        label: 'Contenido',
        items: [
            {
                title: 'Libros',
                href: 'admin.books.index',
                icon: BookMarked,
                matchPaths: ['/admin/books'],
            },
            {
                title: 'Artículos',
                href: 'admin.articles.index',
                icon: Newspaper,
                matchPaths: ['/admin/articles'],
            },
        ],
    },
    {
        label: 'Sitio web',
        items: [
            {
                title: 'Banners',
                href: 'admin.banners.index',
                icon: Image,
                matchPaths: ['/admin/banners'],
            },
            {
                title: 'Consultorías',
                href: 'admin.consultancies.index',
                icon: Briefcase,
                matchPaths: ['/admin/consultancies'],
            },
            {
                title: 'Datos de empresa',
                href: 'admin.settings.company',
                icon: Building2,
                matchPaths: ['/admin/settings/company'],
            },
            {
                title: 'Planes Premium',
                href: 'admin.settings.plans',
                icon: Sparkles,
                matchPaths: ['/admin/settings/plans'],
            },
        ],
    },
];
