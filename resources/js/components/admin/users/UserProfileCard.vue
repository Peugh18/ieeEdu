<script setup lang="ts">
import type { UserDetail } from '@/types/user-detail';
import { Link } from '@inertiajs/vue3';
import {
    Award,
    BookOpen,
    Calendar,
    CheckCircle2,
    CreditCard,
    GraduationCap,
    Mail,
    Phone,
    ShieldCheck,
    ToggleLeft,
    ToggleRight,
    TrendingUp,
    Wallet,
    XCircle,
} from 'lucide-vue-next';

const props = defineProps<{
    user: UserDetail;
}>();

const emit = defineEmits<{
    (e: 'toggleStatus'): void;
}>();

function initials(name: string) {
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

const avatarColors = ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700'];
const userColor = avatarColors[props.user.id % avatarColors.length];

const completed = props.user.enrollments.filter((e) => e.completed_at && e.passed).length;
const approvedPayments = props.user.payments.filter((p) => p.status === 'aprobado').length;
</script>

<template>
    <div class="relative">
        <div class="absolute inset-0 -z-10 rounded-[3rem] bg-gradient-to-r from-slate-900 to-slate-800 opacity-[0.03]"></div>
        <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white p-8 shadow-sm md:p-12">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="relative z-10 flex flex-col items-start gap-10 lg:flex-row">
                <div class="group relative">
                    <div
                        :class="`flex h-40 w-40 flex-shrink-0 items-center justify-center rounded-[2.5rem] text-5xl font-bold shadow-xl shadow-slate-200 ring-8 ring-white transition-transform duration-500 group-hover:scale-[1.02] ${userColor}`"
                    >
                        <img
                            v-if="user.profile_photo_url"
                            :src="user.profile_photo_url"
                            :alt="user.name"
                            class="h-full w-full rounded-[2.5rem] object-cover"
                        />
                        <template v-else>{{ initials(user.name) }}</template>
                    </div>
                    <div
                        v-if="user.status === 'activo'"
                        class="absolute -bottom-2 -right-2 rounded-2xl bg-emerald-500 p-2.5 text-white shadow-lg ring-4 ring-white"
                        title="Estado: Activo"
                    >
                        <CheckCircle2 class="h-5 w-5" />
                    </div>
                    <div
                        v-else
                        class="absolute -bottom-2 -right-2 rounded-2xl bg-slate-300 p-2.5 text-white shadow-lg ring-4 ring-white"
                        title="Estado: Inactivo"
                    >
                        <XCircle class="h-5 w-5" />
                    </div>
                </div>

                <div class="flex-1 space-y-8">
                    <div class="flex flex-col justify-between gap-6 md:flex-row md:items-start">
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <h1 class="font-serif text-5xl leading-tight text-slate-900">{{ user.name }}</h1>
                                    <button @click="emit('toggleStatus')" class="mt-2 text-slate-300 transition-colors hover:text-primary">
                                        <ToggleRight v-if="user.status === 'activo'" class="h-8 w-8 text-emerald-500" />
                                        <ToggleLeft v-else class="h-8 w-8" />
                                    </button>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-widest ring-1 ring-inset"
                                        :class="
                                            user.role === 'admin'
                                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-700/10'
                                                : 'bg-blue-50 text-blue-700 ring-blue-700/10'
                                        "
                                    >
                                        <ShieldCheck v-if="user.role === 'admin'" class="h-3.5 w-3.5" />
                                        <GraduationCap v-else class="h-3.5 w-3.5" />
                                        {{ user.role === 'admin' ? 'Coordinación' : 'Estudiante' }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs font-semibold italic text-slate-400"
                                        >User ID: #{{ user.id.toString().padStart(5, '0') }}</span
                                    >
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-6">
                                <div class="group flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 transition-all group-hover:bg-primary/5 group-hover:text-primary"
                                    >
                                        <Mail class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Email Académico</span>
                                        <span class="text-sm font-semibold leading-none text-slate-700">{{ user.email }}</span>
                                    </div>
                                </div>
                                <div v-if="user.telefono" class="group flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 transition-all group-hover:bg-primary/5 group-hover:text-primary"
                                    >
                                        <Phone class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Contacto</span>
                                        <span class="text-sm font-semibold leading-none text-slate-700">{{ user.telefono }}</span>
                                    </div>
                                </div>
                                <div class="group flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 transition-all group-hover:bg-primary/5 group-hover:text-primary"
                                    >
                                        <Calendar class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Miembro desde</span>
                                        <span class="text-sm font-semibold leading-none text-slate-700">{{ formatDate(user.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex min-w-[240px] flex-col gap-3">
                            <Link
                                :href="route('admin.payments.index', { search: user.email, openCreate: 1 })"
                                class="inline-flex h-16 items-center justify-center gap-4 rounded-3xl bg-primary px-10 text-base font-black text-white shadow-2xl shadow-primary/30 transition-all hover:scale-[1.02] active:scale-95"
                            >
                                <CreditCard class="h-6 w-6" /> Registrar Venta / Acceso
                            </Link>
                            <p class="px-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                Gestionar matrícula y pago de forma centralizada
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 rounded-[2rem] border border-slate-100/50 bg-slate-50 p-6 md:grid-cols-4">
                        <div class="space-y-1 border-r border-slate-200/60 pr-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Inscripciones</p>
                            <div class="flex items-end gap-2">
                                <span class="text-2xl font-black leading-none text-slate-900">{{ user.enrollments.length }}</span
                                ><BookOpen class="mb-0.5 h-4 w-4 text-primary" />
                            </div>
                        </div>
                        <div class="space-y-1 border-r border-slate-200/60 px-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-emerald-500">Graduado en</p>
                            <div class="flex items-end gap-2">
                                <span class="text-2xl font-black leading-none text-emerald-600">{{ completed }}</span
                                ><Award class="mb-0.5 h-4 w-4 text-emerald-500" />
                            </div>
                        </div>
                        <div class="space-y-1 border-r border-slate-200/60 px-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Total Transado</p>
                            <div class="flex items-end gap-2">
                                <span class="text-2xl font-black leading-none text-slate-900">{{ user.payments.length }}</span
                                ><TrendingUp class="mb-0.5 h-4 w-4 text-slate-300" />
                            </div>
                        </div>
                        <div class="space-y-1 pl-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-primary">Inversión</p>
                            <div class="flex items-end gap-2">
                                <span class="text-2xl font-black leading-none text-primary">{{ approvedPayments }}</span
                                ><Wallet class="mb-0.5 h-4 w-4 text-primary/40" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
