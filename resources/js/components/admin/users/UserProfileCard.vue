<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, GraduationCap, CheckCircle2, XCircle, Mail, Phone, Calendar, ToggleLeft, ToggleRight, CreditCard, BookOpen, Award, TrendingUp, Wallet } from 'lucide-vue-next';
import type { UserDetail } from '@/types/user-detail';

const props = defineProps<{
    user: UserDetail;
}>();

const emit = defineEmits<{
    (e: 'toggleStatus'): void;
}>();

function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

const avatarColors = ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700'];
const userColor = avatarColors[props.user.id % avatarColors.length];

const completed = props.user.enrollments.filter(e => e.completed_at && e.passed).length;
const approvedPayments = props.user.payments.filter(p => p.status === 'aprobado').length;
</script>

<template>
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800 rounded-[3rem] opacity-[0.03] -z-10"></div>
        <div class="bg-white rounded-[3rem] border border-slate-100 p-8 md:p-12 shadow-sm overflow-hidden relative">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="flex flex-col lg:flex-row gap-10 items-start relative z-10">
                <div class="relative group">
                    <div :class="`flex h-40 w-40 flex-shrink-0 items-center justify-center rounded-[2.5rem] text-5xl font-bold shadow-xl shadow-slate-200 ring-8 ring-white transition-transform duration-500 group-hover:scale-[1.02] ${userColor}`">
                        <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="h-full w-full rounded-[2.5rem] object-cover" />
                        <template v-else>{{ initials(user.name) }}</template>
                    </div>
                    <div v-if="user.status === 'activo'" class="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2.5 rounded-2xl shadow-lg ring-4 ring-white" title="Estado: Activo">
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
                                    <h1 class="font-serif text-5xl text-slate-900 leading-tight">{{ user.name }}</h1>
                                    <button @click="emit('toggleStatus')" class="mt-2 text-slate-300 hover:text-primary transition-colors">
                                        <ToggleRight v-if="user.status === 'activo'" class="w-8 h-8 text-emerald-500" />
                                        <ToggleLeft v-else class="w-8 h-8" />
                                    </button>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-widest ring-1 ring-inset"
                                        :class="user.role === 'admin' ? 'bg-indigo-50 text-indigo-700 ring-indigo-700/10' : 'bg-blue-50 text-blue-700 ring-blue-700/10'">
                                        <ShieldCheck v-if="user.role === 'admin'" class="h-3.5 w-3.5" />
                                        <GraduationCap v-else class="h-3.5 w-3.5" />
                                        {{ user.role === 'admin' ? 'Coordinación' : 'Estudiante' }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs font-semibold text-slate-400 italic">User ID: #{{ user.id.toString().padStart(5, '0') }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-6">
                                <div class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary/5 group-hover:text-primary transition-all"><Mail class="w-4 h-4" /></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Email Académico</span>
                                        <span class="text-sm font-semibold text-slate-700 leading-none">{{ user.email }}</span>
                                    </div>
                                </div>
                                <div v-if="user.telefono" class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary/5 group-hover:text-primary transition-all"><Phone class="w-4 h-4" /></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Contacto</span>
                                        <span class="text-sm font-semibold text-slate-700 leading-none">{{ user.telefono }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary/5 group-hover:text-primary transition-all"><Calendar class="w-4 h-4" /></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Miembro desde</span>
                                        <span class="text-sm font-semibold text-slate-700 leading-none">{{ formatDate(user.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 min-w-[240px]">
                            <Link :href="route('admin.payments.index', { search: user.email, openCreate: 1 })"
                                class="h-16 inline-flex items-center justify-center gap-4 rounded-3xl bg-primary px-10 text-base font-black text-white shadow-2xl shadow-primary/30 hover:scale-[1.02] active:scale-95 transition-all">
                                <CreditCard class="h-6 w-6" /> Registrar Venta / Acceso
                            </Link>
                            <p class="text-[10px] text-center font-bold text-slate-400 uppercase tracking-widest px-4">Gestionar matrícula y pago de forma centralizada</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-slate-50 rounded-[2rem] border border-slate-100/50">
                        <div class="space-y-1 border-r border-slate-200/60 pr-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Inscripciones</p>
                            <div class="flex items-end gap-2"><span class="text-2xl font-black text-slate-900 leading-none">{{ user.enrollments.length }}</span><BookOpen class="w-4 h-4 text-primary mb-0.5" /></div>
                        </div>
                        <div class="space-y-1 border-r border-slate-200/60 px-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-emerald-500">Graduado en</p>
                            <div class="flex items-end gap-2"><span class="text-2xl font-black text-emerald-600 leading-none">{{ completed }}</span><Award class="w-4 h-4 text-emerald-500 mb-0.5" /></div>
                        </div>
                        <div class="space-y-1 border-r border-slate-200/60 px-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-400">Total Transado</p>
                            <div class="flex items-end gap-2"><span class="text-2xl font-black text-slate-900 leading-none">{{ user.payments.length }}</span><TrendingUp class="w-4 h-4 text-slate-300 mb-0.5" /></div>
                        </div>
                        <div class="space-y-1 pl-4">
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.1em] text-primary">Inversión</p>
                            <div class="flex items-end gap-2"><span class="text-2xl font-black text-primary leading-none">{{ approvedPayments }}</span><Wallet class="w-4 h-4 text-primary/40 mb-0.5" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
