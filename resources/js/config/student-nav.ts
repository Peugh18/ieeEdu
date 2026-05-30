import { LayoutGrid, BookOpen, Calendar, ClipboardCheck, Award, CreditCard } from 'lucide-vue-next';
import type { NavItem } from '@/types/navigation';

export const studentNavItems: NavItem[] = [
    { title: 'Mi Dashboard',   href: 'dashboard',                    icon: LayoutGrid },
    { title: 'Mis Cursos',     href: 'student.courses.index',        icon: BookOpen },
    { title: 'Clases en Vivo', href: 'student.live-classes.index',   icon: Calendar },
    { title: 'Mis Exámenes',   href: 'student.exams.index',          icon: ClipboardCheck },
    { title: 'Mis Pagos',      href: 'student.payments.index',       icon: CreditCard },
    { title: 'Certificados',   href: 'student.certificates.index',   icon: Award },
];
