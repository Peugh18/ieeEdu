import type { NavGroup } from '@/types/navigation';
import {
    Award,
    BookOpen,
    Briefcase,
    Building2,
    CreditCard,
    FolderTree,
    Image,
    LayoutGrid,
    Sparkles,
    Users,
} from 'lucide-vue-next';

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
                matchPaths: ['/admin/payments', '/admin/book-orders'],
            },
        ],
    },
    {
        label: 'Contenido',
        items: [
            {
                title: 'Publicaciones',
                href: 'admin.books.index',
                icon: BookOpen,
                matchPaths: ['/admin/books', '/admin/articles'],
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
