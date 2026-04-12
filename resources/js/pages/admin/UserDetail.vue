<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowLeft, ShieldCheck, GraduationCap,
    BookOpen, CreditCard, CheckCircle2, Clock, AlertCircle,
    XCircle, Mail, Phone, Calendar,
    ToggleLeft, ToggleRight, Wallet,
    TrendingUp, Hash, Award, BookMarked
} from 'lucide-vue-next';

interface EnrollmentItem {
    id: number;
    course_id: number;
    enrolled_at: string;
    completed_at: string | null;
    passed: boolean | null;
    course: { id: number; title: string; image: string | null; type: string } | null;
}
interface PaymentItem {
    id: number;
    status: string;
    amount: number;
    created_at: string;
    course: { id: number; title: string } | null;
}
interface UserDetail {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
    profile_photo_url?: string;
    created_at: string;
    enrollments: EnrollmentItem[];
    payments: PaymentItem[];
}

const props = defineProps<{
    user: UserDetail;
    enrolledIds: number[];
}>();

const page = usePage();
const flash = computed(() => (page.props as any).flash ?? {});

const activeTab = ref<'cursos' | 'pagos'>('cursos');

function toggleStatus() {
    router.patch(route('admin.users.toggleStatus', { user: props.user.id }), {}, { preserveScroll: true });
}

function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

function formatMoney(n: number | string) {
    return 'S/ ' + Number(n).toFixed(2);
}

const paymentStatusConfig: Record<string, { label: string; class: string; icon: any }> = {
    aprobado:    { label: 'Aprobado',    class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    en_revision: { label: 'En revisión', class: 'bg-amber-50 text-amber-700 ring-amber-700/10',    icon: Clock },
    pendiente:   { label: 'Pendiente',   class: 'bg-blue-50 text-blue-700 ring-blue-700/10',      icon: AlertCircle },
    rechazado:   { label: 'Rechazado',   class: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: XCircle },
};

const enrollmentStatus = (e: EnrollmentItem) => {
    if (e.completed_at && e.passed) return { label: 'Completado', class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 };
    return { label: 'En progreso', class: 'bg-blue-50 text-blue-700 ring-blue-700/10', icon: Clock };
};

const avatarColors = [
    'bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700',
];
const userColor = avatarColors[props.user.id % avatarColors.length];
</script>

<template>
    <Head :title="`Ficha: ${props.user.name} - iieEdu Admin`" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
            <!-- ── Nav ── -->
            <div class="flex items-center justify-between">
                <Link :href="route('admin.users.index')" class="group inline-flex items-center gap-2.5 text-sm font-bold text-slate-500 hover:text-[#57572A] transition-all">
                    <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-[#57572A] group-hover:bg-[#57572A]/5 transition-all">
                        <ArrowLeft class="h-4 w-4" />
                    </div>
                    Volver al Directorio
                </Link>
                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                    <span class="text-slate-300">Admin</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-300">Usuarios</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900">Perfil</span>
                </div>
            </div>

            <!-- ── Primary Profile Info ── -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800 rounded-[3rem] opacity-[0.03] -z-10"></div>
                <div class="bg-white rounded-[3rem] border border-slate-100 p-8 md:p-12 shadow-sm overflow-hidden relative">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#57572A]/5 rounded-full blur-3xl"></div>
                    <div class="flex flex-col lg:flex-row gap-10 items-start relative z-10">
                        <div class="relative group">
                            <div :class="`flex h-40 w-40 flex-shrink-0 items-center justify-center rounded-[2.5rem] text-5xl font-bold shadow-xl shadow-slate-200 ring-8 ring-white transition-transform duration-500 group-hover:scale-[1.02] ${userColor}`">
                                <template v-if="props.user.profile_photo_url">
                                    <img :src="props.user.profile_photo_url" :alt="props.user.name" class="h-full w-full rounded-[2.5rem] object-cover" />
                                </template>
                                <template v-else>{{ initials(props.user.name) }}</template>
                            </div>
                            <div v-if="props.user.status === 'activo'" class="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2.5 rounded-2xl shadow-lg ring-4 ring-white" title="Estado: Activo">
                                <CheckCircle2 class="w-5 h-5" />
                            </div>
                            <div v-else class="absolute -bottom-2 -right-2 bg-slate-300 text-white p-2.5 rounded-2xl shadow-lg ring-4 ring-white" title="Estado: Inactivo">
                                <XCircle class="w-5 h-5" />
                            </div>
                        </div>

                        <div class="flex-1 space-y-8">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <h1 class="font-serif text-5xl text-slate-900 leading-tight">{{ props.user.name }}</h1>
                                            <button @click="toggleStatus" class="mt-2 text-slate-300 hover:text-[#57572A] transition-colors">
                                                <ToggleRight v-if="props.user.status === 'activo'" class="w-8 h-8 text-emerald-500" />
                                                <ToggleLeft v-else class="w-8 h-8" />
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-widest ring-1 ring-inset"
                                                :class="props.user.role === 'admin' ? 'bg-indigo-50 text-indigo-700 ring-indigo-700/10' : 'bg-blue-50 text-blue-700 ring-blue-700/10'"
                                            >
                                                <ShieldCheck v-if="props.user.role === 'admin'" class="h-3.5 w-3.5" />
                                                <GraduationCap v-else class="h-3.5 w-3.5" />
                                                {{ props.user.role === 'admin' ? 'Coordinación' : 'Estudiante' }}
                                            </span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-xs font-semibold text-slate-400 italic">User ID: #{{ props.user.id.toString().padStart(5, '0') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-6">
                                        <div class="flex items-center gap-3 group">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#57572A]/5 group-hover:text-[#57572A] transition-all">
                                                <Mail class="w-4 h-4" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Email Académico</span>
                                                <span class="text-sm font-semibold text-slate-700 leading-none">{{ props.user.email }}</span>
                                            </div>
                                        </div>
                                        <div v-if="props.user.telefono" class="flex items-center gap-3 group">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#57572A]/5 group-hover:text-[#57572A] transition-all">
                                                <Phone class="w-4 h-4" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Contacto</span>
                                                <span class="text-sm font-semibold text-slate-700 leading-none">{{ props.user.telefono }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 group">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#57572A]/5 group-hover:text-[#57572A] transition-all">
                                                <Calendar class="w-4 h-4" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Miembro desde</span>
                                                <span class="text-sm font-semibold text-slate-700 leading-none">{{ formatDate(props.user.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 min-w-[240px]">
                                    <!-- UNIFIED BUTTON: CENTRALIZED ENROLLMENT AND PAYMENT -->
                                    <Link
                                        :href="route('admin.payments.index', { search: props.user.email, openCreate: 1 })"
                                        class="h-16 inline-flex items-center justify-center gap-4 rounded-3xl bg-[#57572A] px-10 text-base font-black text-white shadow-2xl shadow-[#57572A]/30 hover:scale-[1.02] active:scale-95 transition-all"
                                    >
                                        <CreditCard class="h-6 w-6" />
                                        Registrar Venta / Acceso
                                    </Link>
                                    <p class="text-[10px] text-center font-bold text-slate-400 uppercase tracking-widest px-4">Gestionar matrícula y pago de forma centralizada</p>
                                </div>
                            </div>

                            <!-- Performance Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-slate-50 rounded-[2rem] border border-slate-100/50">
                                <div class="space-y-1 border-r border-slate-200/60 pr-4">
                                    <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Inscripciones</p>
                                    <div class="flex items-end gap-2">
                                        <span class="text-2xl font-black text-slate-900 leading-none">{{ props.user.enrollments.length }}</span>
                                        <BookOpen class="w-4 h-4 text-[#57572A] mb-0.5" />
                                    </div>
                                </div>
                                <div class="space-y-1 border-r border-slate-200/60 px-4">
                                    <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-emerald-500">Graduado en</p>
                                    <div class="flex items-end gap-2">
                                        <span class="text-2xl font-black text-emerald-600 leading-none">
                                            {{ props.user.enrollments.filter(e => e.completed_at && e.passed).length }}
                                        </span>
                                        <Award class="w-4 h-4 text-emerald-500 mb-0.5" />
                                    </div>
                                </div>
                                <div class="space-y-1 border-r border-slate-200/60 px-4">
                                    <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Total Transado</p>
                                    <div class="flex items-end gap-2">
                                        <span class="text-2xl font-black text-slate-900 leading-none">{{ props.user.payments.length }}</span>
                                        <TrendingUp class="w-4 h-4 text-slate-300 mb-0.5" />
                                    </div>
                                </div>
                                <div class="space-y-1 pl-4">
                                    <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-[#57572A]">Inversión</p>
                                    <div class="flex items-end gap-2">
                                        <span class="text-2xl font-black text-[#57572A] leading-none">
                                           {{ props.user.payments.filter(p => p.status === 'aprobado').length }}
                                        </span>
                                        <Wallet class="w-4 h-4 text-[#57572A]/40 mb-0.5" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Dynamic Content ── -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="hidden lg:col-span-3 lg:flex flex-col gap-2">
                    <button
                        v-for="tab in [{ id: 'cursos', label: 'Historial Académico', icon: BookOpen }, { id: 'pagos', label: 'Gestión Financiera', icon: Wallet }]"
                        :key="tab.id"
                        @click="activeTab = tab.id as any"
                        class="flex items-center justify-between px-6 py-4 rounded-2xl border transition-all duration-300"
                        :class="activeTab === tab.id 
                            ? 'bg-slate-900 border-slate-900 text-white shadow-xl shadow-slate-900/10' 
                            : 'bg-white border-transparent text-slate-500 hover:bg-slate-50'"
                    >
                        <div class="flex items-center gap-3">
                            <component :is="tab.icon" class="w-5 h-5" :class="activeTab === tab.id ? 'text-[#C9C7B8]' : 'text-slate-300'" />
                            <span class="text-sm font-bold tracking-tight">{{ tab.label }}</span>
                        </div>
                    </button>
                </div>

                <div class="lg:col-span-9 space-y-6">
                    <div v-if="activeTab === 'cursos'" class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h3 class="font-serif text-2xl text-slate-900">Programas de <span class="italic">Estudio</span></h3>
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">
                                {{ props.user.enrollments.length }} Registros
                            </span>
                        </div>
                        <div v-if="!props.user.enrollments.length" class="bg-white rounded-[2rem] border border-slate-100 p-20 text-center space-y-4">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-200">
                                <BookOpen class="w-10 h-10" />
                            </div>
                            <p class="text-slate-400 font-medium">No se han detectado inscripciones activas.</p>
                        </div>
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="e in props.user.enrollments" :key="e.id" class="group bg-white rounded-[2rem] p-5 border border-slate-100 hover:shadow-xl transition-all duration-500 overflow-hidden relative">
                                <div class="flex gap-5 relative z-10">
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0">
                                        <img v-if="e.course?.image" :src="e.course.image" :alt="e.course?.title" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full items-center justify-center text-slate-200"><BookMarked class="h-8 w-8" /></div>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">{{ e.course?.type ?? 'N/A' }}</span>
                                                <div :class="`px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider ring-1 ring-inset ${enrollmentStatus(e).class}`">
                                                    {{ enrollmentStatus(e).label }}
                                                </div>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-900 truncate leading-snug group-hover:text-[#57572A] transition-colors">{{ e.course?.title ?? 'Curso' }}</h4>
                                        </div>
                                        <div class="flex items-center justify-between pt-2 border-t border-slate-50 text-xs text-slate-400">
                                            <div class="flex items-center gap-1.5"><Calendar class="w-3.5 h-3.5" /><span>{{ formatDate(e.enrolled_at) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'pagos'" class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h3 class="font-serif text-2xl text-slate-900">Historial de <span class="italic">Transacciones</span></h3>
                            <span class="px-3 py-1 rounded-full bg-[#57572A]/5 text-[10px] font-extrabold uppercase tracking-widest text-[#57572A]">
                                {{ props.user.payments.length }} Recibos
                            </span>
                        </div>
                        <div v-if="!props.user.payments.length" class="bg-white rounded-[2rem] border border-slate-100 p-20 text-center space-y-4">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-200"><Wallet class="w-10 h-10" /></div>
                            <p class="text-slate-400 font-medium">No se registran movimientos financieros locales.</p>
                        </div>
                        <div v-else class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/80 border-b border-slate-100">
                                    <tr>
                                        <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Detalle</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Fecha</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Monto</th>
                                        <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-right">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <tr v-for="p in props.user.payments" :key="p.id" class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300"><Hash class="w-5 h-5" /></div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ p.course?.title ?? 'Pago Directo' }}</p>
                                                    <p class="text-[10px] text-slate-400 mt-0.5 uppercase tracking-widest">ID-{{ p.id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center text-sm font-semibold text-slate-600">{{ formatDate(p.created_at) }}</td>
                                        <td class="px-6 py-5 text-center text-base font-black text-slate-900">{{ formatMoney(p.amount) }}</td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                                :class="paymentStatusConfig[p.status]?.class ?? 'bg-slate-50 text-slate-500'">
                                                <component :is="paymentStatusConfig[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                                {{ paymentStatusConfig[p.status]?.label ?? p.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Transition enter-active-class="transition duration-500" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center"><Check class="h-6 w-6 text-white" /></div>
                <div class="flex flex-col"><span class="text-[10px] uppercase font-bold tracking-widest text-emerald-500">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span></div>
            </div>
        </Transition>
    </AppLayout>
</template>
