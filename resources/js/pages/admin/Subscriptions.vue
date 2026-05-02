<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { 
    Crown, Search, Plus, Filter, MoreVertical, 
    Calendar, User, CheckCircle2, XCircle, Trash2,
    Check, RotateCw, Activity, Sparkles, AlertCircle,
    ChevronRight, CreditCard, Hash, Users, Clock
} from 'lucide-vue-next';
import axios from 'axios';

interface Subscription {
    id: number;
    user: { id: number; name: string; email: string; };
    type: string;
    start_date: string;
    end_date: string;
    status: 'activa' | 'expirada' | 'cancelada';
    payment_amount?: number;
    payment_capture?: string;
}

interface Props {
    subscriptions: { data: Subscription[]; links: any[]; total: number; per_page: number };
    filters: { search?: string };
}

const props = defineProps<Props>();
const page = usePage();
const flash = computed(() => (page.props.flash as { success?: string; error?: string }) || {});

const showAddModal = ref(false);
const usersResults = ref<any[]>([]);
const searchUserQuery = ref('');
const isSearchingUser = ref(false);
const showUserDropdown = ref(false);

const form = useForm({
    user_id: '',
    type: 'Trimestral',
    months: 3,
    amount: 350,
    comprobante: null as File | null,
});

watch(() => form.type, (newType) => {
    if (newType === 'Trimestral') { form.months = 3; form.amount = 350; }
    else if (newType === 'Semestral') { form.months = 6; form.amount = 600; }
    else if (newType === 'Anual') { form.months = 12; form.amount = 990; }
});

const onFileChange = (e: Event) => {
    const el = e.target as HTMLInputElement;
    form.comprobante = el.files?.[0] ?? null;
};

const createPreviewUrl = (file: File) => URL.createObjectURL(file);

const searchUsers = async () => {
    if (searchUserQuery.value.length < 2) { usersResults.value = []; showUserDropdown.value = false; return; }
    isSearchingUser.value = true;
    try {
        const res = await axios.get(route('admin.users.search'), { params: { q: searchUserQuery.value } });
        usersResults.value = res.data;
        showUserDropdown.value = true;
    } catch (e) { console.error(e); } finally { isSearchingUser.value = false; }
};

const selectUser = (user: any) => {
    form.user_id = user.id;
    searchUserQuery.value = user.name + ' (' + user.email + ')';
    showUserDropdown.value = false;
    usersResults.value = [];
};

const submit = () => {
    form.post(route('admin.subscriptions.store'), {
        forceFormData: true,
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            searchUserQuery.value = '';
        }
    });
};

const toggleStatus = (sub: Subscription) => { router.patch(route('admin.subscriptions.toggle', { subscription: sub.id }), {}, { preserveScroll: true }); };
const deleteSub = (sub: Subscription) => {
    if (!confirm('¿Eliminar esta suscripción del historial?')) return;
    router.delete(route('admin.subscriptions.destroy', { subscription: sub.id }), { preserveScroll: true });
};

const statusCfg: Record<string, { label: string; cls: string; icon: any }> = {
    activa:    { label: 'Activa',    cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    expirada:  { label: 'Expirada',  cls: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: Clock },
    cancelada: { label: 'Cancelada', cls: 'bg-slate-50 text-slate-700 ring-slate-700/10',       icon: XCircle },
};

const formatDate = (date: string) => new Date(date).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
const formatMoney = (n: number) => 'S/ ' + Number(n).toLocaleString('es-PE', { minimumFractionDigits: 2 });
const initials = (n: string) => n.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
const getACls = (id: number) => ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'][id % 5];

const selectedSub = ref<Subscription | null>(null);
</script>

<template>
    <Head title="Suscripciones Premium - Admin IEE" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <!-- ── Header ─────────────────────────────────────────── -->
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span>Accesos</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900">Membresías</span>
                    </div>
                    <h1 class="font-serif text-5xl text-slate-900 leading-tight">Suscripciones <span class="italic">Premium</span></h1>
                    <p class="text-sm text-slate-500 font-medium italic">Control total de accesos ilimitados para la red de estudiantes.</p>
                </div>
                <button 
                    @click="showAddModal = true"
                    class="h-14 inline-flex items-center justify-center gap-3 rounded-2xl bg-primary px-10 text-sm font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all"
                >
                    <Plus class="h-5 w-5" />
                    Nueva Suscripción
                </button>
            </div>

            <!-- ── Stats Grid ──────────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="group relative overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Canal Premium</span>
                            <Crown class="h-4 w-4 text-amber-500/40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total Activas</p>
                            <p class="text-4xl font-black text-slate-900 mt-1">{{ props.subscriptions.data.filter(s => s.status === 'activa').length }}</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-amber-500">
                        <Crown class="w-full h-full" />
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Participación</span>
                            <Users class="h-4 w-4 text-slate-300" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Alcance Global</p>
                            <p class="text-4xl font-black text-slate-900 mt-1">{{ props.subscriptions.total }}</p>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-[2rem] bg-[#1a1a1a] p-6 shadow-xl transition-all duration-300 hover:scale-[1.01]">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-outline-variant/60">Monitoreo</span>
                            <Activity class="h-4 w-4 text-white/40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-outline-variant/40 uppercase tracking-widest leading-none">Análisis de Retención</p>
                            <p class="text-2xl font-black text-white mt-1 italic tracking-tight">Ecosistema Activo</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-[0.08] text-white">
                        <Sparkles class="w-full h-full" />
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-[2rem] bg-emerald-600 p-6 shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:scale-[1.01]">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4 text-white">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-60">Matrícula</span>
                            <CreditCard class="h-4 w-4 opacity-40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest leading-none opacity-60">Ingresos Proyectados</p>
                            <p class="text-2xl font-black mt-1 uppercase tracking-tighter italic">Validación 24/7</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Filters ────────────────────────────────────────── -->
            <div class="bg-slate-50 rounded-[2.5rem] p-3 border border-slate-100 flex flex-col lg:flex-row items-center gap-3">
                <div class="relative flex-1 w-full lg:w-auto">
                    <Search class="absolute left-5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input 
                        type="text" 
                        placeholder="Filtrar por nombre de estudiante o correo..." 
                        class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all" 
                    />
                </div>
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <button class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:border-primary hover:text-primary transition-all">
                        <Filter class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- ── Table ──────────────────────────────────────────── -->
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left min-w-[900px]">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Cuenta</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Plan Académico</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Inversión Recaudada</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estatus</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Mantenimiento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="!props.subscriptions.data.length">
                            <td colspan="5" class="py-24 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-20">
                                    <Crown class="w-12 h-12" />
                                    <p class="text-sm font-bold uppercase tracking-widest italic">Sin suscripciones activas</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="sub in props.subscriptions.data" :key="sub.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${getACls(sub.user.id)} shadow-sm shadow-slate-100 shadow-slate-200 group-hover:scale-95 transition-transform duration-500`" >
                                        {{ initials(sub.user.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ sub.user.name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wider line-clamp-1">{{ sub.user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-black tracking-tighter bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200/50">
                                        <Crown class="h-3.5 w-3.5" />
                                        {{ sub.type }}
                                    </span>
                                    <span class="text-[9px] text-slate-300 font-bold uppercase tracking-widest mt-1.5">{{ formatDate(sub.start_date) }} — {{ formatDate(sub.end_date) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-base font-black text-slate-900 tracking-tighter">{{ sub.payment_amount ? formatMoney(sub.payment_amount) : 'S/ 0.00' }}</span>
                                    <button v-if="sub.payment_capture" @click="selectedSub = sub" class="mt-1 flex items-center gap-1.5 text-[9px] font-black uppercase text-blue-500 hover:text-blue-700 transition-colors">
                                        <Activity class="w-2.5 h-2.5" /> Ver Evidencia
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-black uppercase tracking-widest ring-1 ring-inset"
                                        :class="statusCfg[sub.status]?.cls ?? 'bg-slate-50 text-slate-500'">
                                        <component :is="statusCfg[sub.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                        {{ statusCfg[sub.status]?.label ?? sub.status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-all duration-300">
                                    <Link 
                                        :href="route('admin.subscriptions.toggle', sub.id)" 
                                        method="patch" as="button" preserve-scroll
                                        class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all"
                                        title="Alternar Estado"
                                    >
                                        <RotateCw class="w-4 h-4" />
                                    </Link>
                                    <Link 
                                        :href="route('admin.subscriptions.destroy', sub.id)" 
                                        method="delete" as="button" preserve-scroll
                                        class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                        title="Remover Acceso"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Pagination ─────────────────────────────────────── -->
            <div v-if="props.subscriptions.links.length > 3" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic">Análisis de {{ props.subscriptions.data.length }} registros actuales</p>
                <div class="flex items-center gap-1.5">
                    <Link
                        v-for="link in props.subscriptions.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="[
                            link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600',
                            !link.url ? 'pointer-events-none opacity-40' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- ──────────────── MODAL ADD (HIGH-FIDELITY) ──────────────────── -->
        <Teleport to="body">
            <Transition name="modal-bounce">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="showAddModal = false">
                    <div class="w-full max-w-xl rounded-[2.5rem] bg-white shadow-2xl overflow-hidden flex flex-col transition-all max-h-[95vh]">
                        <div class="bg-[#1a1a1a] p-10 text-white relative shrink-0">
                            <div class="absolute top-0 right-0 p-10 opacity-10"><Crown class="w-24 h-24" /></div>
                            <h2 class="font-serif text-3xl">Activar <span class="italic underline decoration-amber-500 underline-offset-8">Suscripción</span></h2>
                            <p class="mt-4 text-slate-400 text-xs font-bold uppercase tracking-[0.2em]">Habilita el acceso total a la plataforma</p>
                            <button @click="showAddModal = false" class="absolute top-8 right-8 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <XCircle class="h-6 w-6" />
                            </button>
                        </div>
                        
                        <form @submit.prevent="submit" class="p-10 space-y-8 overflow-y-auto">
                            <!-- Student Search -->
                            <div class="space-y-3 relative z-[100]">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Identificar Estudiante *</label>
                                <div class="relative group">
                                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300 group-focus-within:text-primary transition-colors" />
                                    <input 
                                        v-model="searchUserQuery" 
                                        @input="searchUsers"
                                        type="text" 
                                        placeholder="Escribe para buscar por nombre o correo..." 
                                        class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-6 text-sm font-bold text-slate-700 focus:border-primary outline-none transition-all shadow-inner"
                                    />
                                    <div v-if="usersResults.length" class="absolute z-[999] left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden max-h-60 overflow-y-auto">
                                        <button 
                                            v-for="u in usersResults" :key="u.id" type="button" 
                                            @click="selectUser(u)"
                                            class="w-full flex items-center gap-4 p-5 hover:bg-amber-50/50 text-left border-b border-slate-50 last:border-0 transition-all group"
                                        >
                                            <div :class="`w-10 h-10 rounded-xl flex items-center justify-center text-[11px] font-black ${getACls(u.id)} shadow-sm group-hover:scale-90 transition-transform`" >
                                                {{ initials(u.name) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors leading-tight">{{ u.name }}</p>
                                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">{{ u.email }}</p>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                <p v-if="form.errors.user_id" class="px-1 text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1.5">{{ form.errors.user_id }}</p>
                            </div>

                            <!-- Plan Config -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Membresía *</label>
                                    <div class="relative">
                                        <Crown class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-amber-500" />
                                        <select v-model="form.type" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-10 text-sm font-bold text-slate-700 focus:border-amber-500 appearance-none cursor-pointer outline-none transition-all">
                                            <option value="Trimestral">Trimestral</option>
                                            <option value="Semestral">Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </div>
                                    <p v-if="form.errors.type" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.type }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Vigencia (Meses) *</label>
                                    <div class="relative">
                                        <Clock class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300" />
                                        <input v-model.number="form.months" type="number" min="1" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 px-4 text-sm font-bold text-slate-700 focus:border-amber-500 outline-none transition-all shadow-inner" />
                                    </div>
                                    <p v-if="form.errors.months" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.months }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Monto de Inversión (S/) *</label>
                                    <div class="relative">
                                        <CreditCard class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-emerald-500" />
                                        <input v-model.number="form.amount" type="number" step="0.01" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 px-4 text-sm font-bold text-slate-700 focus:border-emerald-500 outline-none transition-all shadow-inner" />
                                    </div>
                                    <p v-if="form.errors.amount" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.amount }}</p>
                                </div>
                            </div>

                            <div class="bg-amber-50/50 p-6 rounded-[2rem] border border-amber-100 flex gap-5 shadow-sm">
                                <div class="w-14 h-14 flex-shrink-0 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-amber-100 italic"><Crown class="w-7 h-7 text-amber-500 lg:group-hover:animate-pulse" /></div>
                                <div class="space-y-2">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-700">Cláusula de Acceso Total</p>
                                    <p class="text-xs text-amber-800 leading-relaxed font-serif italic font-medium">
                                        Al activar este plan, el estudiante tendrá acceso inmediato a <span class="underline decoration-amber-500/30">TODOS</span> los cursos grabados y beneficios del ecosistema por el tiempo estipulado.
                                    </p>
                                </div>
                            </div>

                            <!-- Capture Upload -->
                            <div class="space-y-3">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Soporte Financiero (Captura)</label>
                                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-slate-300 shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-500">
                                            <Activity v-if="form.comprobante" class="w-6 h-6 text-emerald-500" />
                                            <Plus v-else class="w-6 h-6" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-700 truncate max-w-[250px]">{{ form.comprobante ? form.comprobante.name : 'Vincular comprobante de pago' }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Formatos: PNG, JPG (Máx. 5MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
                                </label>
                                <div v-if="form.comprobante" class="relative group mt-2 rounded-2xl overflow-hidden border border-emerald-100 shadow-sm">
                                    <img :src="createPreviewUrl(form.comprobante)" class="max-h-32 w-full object-contain bg-slate-50" />
                                </div>
                                <p v-if="form.errors.comprobante" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.comprobante }}</p>
                            </div>

                            <div class="pt-4 flex gap-4">
                                <button 
                                    type="button" @click="showAddModal = false; form.reset(); searchUserQuery = ''"
                                    class="flex-1 h-14 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing || !form.user_id"
                                    class="flex-1 h-14 bg-[#1a1a1a] text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-slate-900/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 flex items-center justify-center gap-2"
                                >
                                    {{ form.processing ? 'Sincronizando...' : 'Confirmar Activación' }}
                                    <ChevronRight class="w-4 h-4" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Gallery Preview -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="selectedSub" class="fixed inset-0 z-[100] bg-slate-900/95 backdrop-blur-xl flex items-center justify-center p-8" @click="selectedSub = null">
                    <button class="absolute top-10 right-10 text-white/50 hover:text-white transition-colors">
                        <XCircle class="w-10 h-10" />
                    </button>
                    <div class="relative max-w-4xl w-full h-full flex flex-col items-center justify-center gap-6" @click.stop>
                        <template v-if="selectedSub.payment_capture?.includes('/')">
                            <img :src="selectedSub.payment_capture" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10" />
                            <a :href="selectedSub.payment_capture" download class="px-8 py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                                <Plus class="w-4 h-4" /> Descargar Evidencia Permanente
                            </a>
                        </template>
                        <template v-else>
                            <div class="bg-white p-12 rounded-[3rem] max-w-md w-full text-center space-y-6 shadow-2xl">
                                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto">
                                    <CreditCard class="w-10 h-10 text-amber-500" />
                                </div>
                                <div>
                                    <h3 class="font-serif text-2xl text-slate-900">Nota de Pago</h3>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Activación Administrativa</p>
                                </div>
                                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Monto Registrado</span>
                                        <span class="text-lg font-black text-primary italic">S/ {{ formatMoney(selectedSub.payment_amount || 0) }}</span>
                                    </div>
                                    <p class="italic text-slate-600 text-sm">"{{ selectedSub.payment_capture }}"</p>
                                </div>
                                <button @click="selectedSub = null" class="w-full h-14 bg-[#1a1a1a] text-white rounded-2xl font-black text-xs uppercase tracking-widest">
                                    Cerrar Detalle
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Flash messages -->
        <Transition enter-active-class="transition duration-500" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success || flash.error" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${flash.success ? 'bg-emerald-500' : 'bg-rose-500'}`">
                    <Check v-if="flash.success" class="h-6 w-6 text-white" />
                    <AlertCircle v-else class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <span :class="`text-[10px] uppercase font-bold tracking-widest ${flash.success ? 'text-emerald-500' : 'text-rose-500'}`">
                        {{ flash.success ? 'Sistema Sincronizado' : 'Error Técnico' }}
                    </span>
                    <span class="text-sm font-medium">{{ flash.success || flash.error }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.modal-bounce-enter-active { animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-bounce-leave-active { animation: modal-bounce-in 0.3s reverse ease-in; }

@keyframes modal-bounce-in {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
