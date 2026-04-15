<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import {
    AlertCircle, Check, CheckCircle2, Clock,
    Download, Eye, FileImage, Plus, RefreshCw,
    Search, X, XCircle, Wallet, TrendingUp, Filter,
    Calendar, CreditCard, Hash, ExternalLink, ChevronRight
} from 'lucide-vue-next';
import { computed, ref, watch, onMounted } from 'vue';
import { useForm as useInertiaForm } from '@inertiajs/vue3';

interface PaymentItem {
    id: number; status: string; amount: number; comprobante: string | null; created_at: string;
    user: { id?: number; name: string; email: string };
    course: { id?: number; title: string } | null;
}
interface CourseOption { id: number; title: string; price: number; sale_price: number | null; }
interface UserResult  { id: number; name: string; email: string; }

const props = defineProps<{
    payments: { data: PaymentItem[]; links: any[]; total: number; per_page: number };
    filters: { status?: string; search?: string; date?: string; per_page?: string };
    stats: { total: number; pendiente: number; en_revision: number; aprobado: number; rechazado: number };
    courses: CourseOption[];
}>();

const page = usePage();
const flash = computed(() => (page.props as any).flash ?? {});

// ─── Filters ────────────────────────────────────────────────────
const searchInput  = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const dateFilter   = ref(props.filters.date || '');
const perPage      = ref(props.filters.per_page || '20');

function applyFilters() {
    router.get(route('admin.payments.index'), {
        search:   searchInput.value || undefined,
        status:   statusFilter.value || undefined,
        date:     dateFilter.value || undefined,
        per_page: perPage.value !== '20' ? perPage.value : undefined,
    }, { preserveState: true, replace: true });
}
watch([statusFilter, dateFilter, perPage], applyFilters);
let searchTimer: any;
watch(searchInput, () => { clearTimeout(searchTimer); searchTimer = setTimeout(applyFilters, 400); });

// ─── Create Payment Modal ────────────────────────────────────────
const showCreate  = ref(false);
const createForm = useInertiaForm({
    user_id: 0,
    course_id: '',
    amount: '',
    status: 'en_revision',
    comprobante: null as File | null,
});

// ── User AJAX search ─────────────────────────────────────────────
const userQuery    = ref('');
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openCreate') === '1') {
        showCreate.value = true;
        if (props.filters.search) userQuery.value = props.filters.search;
    }
});
const userResults  = ref<UserResult[]>([]);
const selectedUser = ref<UserResult | null>(null);
const showDropdown = ref(false);
let userTimer: any;
let isSelecting = false;

function onUserFocus() { if (userResults.value.length) showDropdown.value = true; }
function onUserBlur() { setTimeout(() => { showDropdown.value = false; }, 200); }

watch(userQuery, (val) => {
    if (isSelecting) { isSelecting = false; return; }
    clearTimeout(userTimer);
    selectedUser.value = null;
    createForm.user_id = 0;
    if (val.length < 2) { userResults.value = []; showDropdown.value = false; return; }
    userTimer = setTimeout(async () => {
        const { data } = await axios.get(route('admin.users.search'), { params: { q: val } });
        userResults.value = data;
        showDropdown.value = true;
    }, 300);
});

function selectUser(u: UserResult) {
    isSelecting = true;
    selectedUser.value = u;
    createForm.user_id = u.id;
    userQuery.value = u.name + ' — ' + u.email;
    showDropdown.value = false;
}

watch(() => createForm.course_id, (id) => {
    const c = props.courses.find(c => String(c.id) === String(id));
    if (c) createForm.amount = String(c.sale_price && c.sale_price < c.price ? c.sale_price : c.price);
});

function onFileChange(e: Event) {
    const el = e.target as HTMLInputElement;
    createForm.comprobante = el.files?.[0] ?? null;
}

function resetCreate() {
    createForm.reset();
    createForm.clearErrors();
    userQuery.value = '';
    selectedUser.value = null;
    userResults.value = [];
    showDropdown.value = false;
}

function submitCreate() {
    if (!createForm.user_id) { createForm.setError('user_id', 'Selecciona un estudiante.'); return; }
    createForm.post(route('admin.payments.store'), {
        forceFormData: true,
        onSuccess: () => { showCreate.value = false; resetCreate(); },
        preserveScroll: true
    });
}

// ─── Actions ────────────────────────────────────────────────
function approve(p: PaymentItem) { router.patch(route('admin.payments.approve', { payment: p.id }), {}, { preserveScroll: true }); }
function reject(p: PaymentItem) {
    if (!confirm(`¿Rechazar pago de ${p.user.name}?`)) return;
    router.patch(route('admin.payments.reject', { payment: p.id }), {}, { preserveScroll: true });
}

const detailPayment = ref<PaymentItem | null>(null);

// ─── Helpers ─────────────────────────────────────────────────────
const statusCfg: Record<string, { label: string; cls: string; icon: any }> = {
    pendiente:   { label: 'Pendiente',   cls: 'bg-blue-50 text-blue-700 ring-blue-700/10',    icon: Clock },
    en_revision: { label: 'En revisión', cls: 'bg-amber-50 text-amber-700 ring-amber-700/10',    icon: RefreshCw },
    aprobado:    { label: 'Aprobado',    cls: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    rechazado:   { label: 'Rechazado',   cls: 'bg-rose-50 text-rose-700 ring-rose-700/10',        icon: XCircle },
};
const initials = (n: string) => n.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
const avatarColors = ['bg-indigo-50 text-indigo-700','bg-blue-50 text-blue-700','bg-emerald-50 text-emerald-700','bg-amber-50 text-amber-700','bg-rose-50 text-rose-700'];
const aCls     = (id: number) => avatarColors[id % avatarColors.length];
const fDate    = (d: string) => new Date(d).toLocaleDateString('es-PE', { day:'2-digit', month:'short', year:'numeric' });
const fMoney   = (n: number|string) => 'S/ ' + Number(n).toFixed(2);
const pgLinks  = computed(() => props.payments.links?.filter((l: any) => l.url) ?? []);
const createPreviewUrl = (file: File) => URL.createObjectURL(file);
</script>

<template>
    <Head title="Gestión de Pagos - iieEdu Admin" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <!-- ── Header ── -->
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span>Finanzas</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900">Historial de Pagos</span>
                    </div>
                    <h1 class="font-serif text-5xl text-slate-900 leading-tight">Control de <span class="italic">Transacciones</span></h1>
                </div>
                <button @click="showCreate = true" class="h-14 inline-flex items-center justify-center gap-3 rounded-2xl bg-primary px-8 text-sm font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                    <Plus class="h-5 w-5" /> Registrar Nuevo Pago
                </button>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div v-for="s in [
                    { key:'',            label:'Total Global',  val:props.stats.total,       icon:Wallet,      cls:'text-slate-900' },
                    { key:'pendiente',   label:'Pendientes',    val:props.stats.pendiente,   icon:Clock,       cls:'text-blue-600' },
                    { key:'en_revision', label:'Por Validar',   val:props.stats.en_revision, icon:RefreshCw,   cls:'text-amber-600' },
                    { key:'aprobado',    label:'Aprobados',     val:props.stats.aprobado,    icon:CheckCircle2, cls:'text-emerald-600' },
                    { key:'rechazado',   label:'Rechazados',    val:props.stats.rechazado,   icon:XCircle,     cls:'text-rose-600' },
                ]" :key="s.key"
                    @click="statusFilter = s.key"
                    class="group relative cursor-pointer overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300"
                    :class="statusFilter === s.key ? 'ring-2 ring-primary border-transparent' : 'hover:shadow-md hover:border-slate-200'"
                >
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 group-hover:text-slate-600 transition-colors">{{ s.label }}</span>
                            <component :is="s.icon" class="h-4 w-4 text-slate-300 group-hover:text-slate-500 transition-colors" />
                        </div>
                        <p class="text-4xl font-black tracking-tight" :class="s.cls">{{ s.val }}</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <component :is="s.icon" class="w-full h-full" />
                    </div>
                </div>
            </div>

            <!-- ── Filter Bar ── -->
            <div class="bg-slate-50 rounded-[2.5rem] p-4 border border-slate-100 flex flex-col lg:flex-row items-center gap-4">
                <div class="relative flex-1 w-full lg:w-auto">
                    <Search class="absolute left-5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input v-model="searchInput" placeholder="Buscar por estudiante, curso o ID..." class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all" />
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="relative flex-1 lg:flex-none min-w-[160px]">
                        <Filter class="absolute left-4 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <select v-model="statusFilter" class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-10 pr-10 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer">
                            <option value="">Todos los Estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_revision">En revisión</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                    </div>
                    <div class="relative flex-1 lg:flex-none min-w-[160px]">
                        <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <input v-model="dateFilter" type="date" class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-10 pr-4 text-xs font-bold text-slate-600 outline-none appearance-none" />
                    </div>
                </div>
            </div>

            <!-- ── Table View ── -->
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante / Programa</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Inversión</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estatus</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Fecha</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="!props.payments.data.length">
                            <td colspan="5" class="py-24 text-center">
                                <Activity class="w-12 h-12 text-slate-200 mx-auto mb-4" />
                                <p class="text-slate-400 font-medium">No se han detectado transacciones con los criterios seleccionados.</p>
                            </td>
                        </tr>
                        <tr v-for="p in props.payments.data" :key="p.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${aCls(p.id)} shadow-sm`">
                                        {{ initials(p.user.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ p.user.name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wider line-clamp-1">{{ p.course?.title ?? 'Pago Directo / Externo' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <span class="text-base font-black text-slate-900 leading-none">{{ fMoney(p.amount) }}</span>
                                    <span v-if="p.comprobante" class="mt-1 flex items-center gap-1 text-[9px] font-bold text-blue-500 uppercase tracking-tighter">
                                        <FileImage class="w-2.5 h-2.5" /> Con Evidencia
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                        :class="statusCfg[p.status]?.cls ?? 'bg-slate-50 text-slate-500'">
                                        <component :is="statusCfg[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                                        {{ statusCfg[p.status]?.label ?? p.status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-slate-500">{{ fDate(p.created_at) }}</span>
                                    <span class="text-[9px] text-slate-300 font-medium uppercase tracking-widest mt-0.5">ID: #{{ p.id }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                    <button @click="detailPayment = p" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Gestionar">
                                        <Eye class="h-4 w-4" />
                                    </button>
                                    <template v-if="p.status !== 'aprobado'">
                                        <button @click="approve(p)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 transition-all" title="Aprobar">
                                            <CheckCircle2 class="h-4 w-4" />
                                        </button>
                                        <button v-if="p.status !== 'rechazado'" @click="reject(p)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Rechazar">
                                            <XCircle class="h-4 w-4" />
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ── Pagination ── -->
            <div v-if="pgLinks.length > 1" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mostrando {{ props.payments.data.length }} de {{ props.stats.total }} transacciones</p>
                <div class="flex items-center gap-1.5">
                    <Link v-for="link in pgLinks" :key="link.label" :href="link.url"
                        class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600'"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- ───────────────── DETAIL DRAWER (MODERNO) ─────────────────────── -->
        <Teleport to="body">
            <Transition name="slide">
                <div v-if="detailPayment" class="fixed inset-0 z-50 flex items-center justify-end bg-slate-900/40 backdrop-blur-sm" @click.self="detailPayment = null">
                    <div class="h-full w-full max-w-md bg-white shadow-2xl overflow-y-auto flex flex-col">
                        <div class="sticky top-0 z-20 bg-slate-900 p-8 text-white">
                            <div class="absolute top-0 right-0 p-8 opacity-10"><Hash class="w-24 h-24" /></div>
                            <button @click="detailPayment = null" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <X class="h-5 w-5" />
                            </button>
                            <span class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-outline-variant/60">Validación de Pago</span>
                            <h2 class="font-serif text-3xl mt-1">Transacción <span class="italic text-outline-variant">#{{ detailPayment.id }}</span></h2>
                        </div>

                        <div class="p-8 space-y-8 flex-1">
                            <div :class="`flex items-center gap-3 p-4 rounded-2xl ring-1 ring-inset ${statusCfg[detailPayment.status]?.cls}`">
                                <component :is="statusCfg[detailPayment.status]?.icon ?? AlertCircle" class="h-5 w-5" />
                                <span class="font-extrabold text-[10px] uppercase tracking-widest">Estatus Actual: {{ statusCfg[detailPayment.status]?.label }}</span>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-4">
                                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-3">Estudiante</p>
                                        <div class="flex items-center gap-3">
                                            <div :class="`h-10 w-10 rounded-xl flex items-center justify-center font-bold text-xs ${aCls(detailPayment.user.id || 0)}`">{{ initials(detailPayment.user.name) }}</div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm leading-tight">{{ detailPayment.user.name }}</p>
                                                <p class="text-xs text-slate-400 font-medium">{{ detailPayment.user.email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 grid grid-cols-2 gap-4">
                                        <div class="col-span-2 pb-2 border-b border-slate-200/50">
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Programa</p>
                                            <p class="font-bold text-slate-900 leading-tight">{{ detailPayment.course?.title ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Inversión</p>
                                            <p class="text-lg font-black text-primary">{{ fMoney(detailPayment.amount) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-1">Registro</p>
                                            <p class="text-sm font-bold text-slate-600">{{ fDate(detailPayment.created_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <p class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Comprobante de Pago</p>
                                    <div v-if="detailPayment.comprobante" class="relative group">
                                        <a :href="detailPayment.comprobante" target="_blank" class="block rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm bg-slate-50">
                                            <img :src="detailPayment.comprobante" alt="Comprobante" class="w-full object-contain max-h-[400px] group-hover:scale-105 transition-transform duration-700" />
                                            <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/10 transition-colors flex items-center justify-center">
                                                <ExternalLink class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                            </div>
                                        </a>
                                        <a :href="detailPayment.comprobante" download class="mt-4 w-full h-12 flex items-center justify-center gap-2 rounded-2xl bg-slate-50 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">
                                            <Download class="h-4 w-4" /> Guardar Copia Local
                                        </a>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-12 rounded-[2rem] bg-slate-50 border border-slate-100 border-dashed">
                                        <FileImage class="h-10 w-10 text-slate-200 mb-3" />
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sin Archivo Adjunto</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 border-t border-slate-100 bg-slate-50/50 space-y-3">
                            <template v-if="detailPayment.status !== 'aprobado'">
                                <button @click="approve(detailPayment); detailPayment = null" class="w-full h-14 rounded-2xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <CheckCircle2 class="w-5 h-5" /> Aprobar y Habilitar Acceso
                                </button>
                                <button v-if="detailPayment.status !== 'rechazado'" @click="reject(detailPayment); detailPayment = null" class="w-full h-14 rounded-2xl bg-white border border-rose-100 text-sm font-bold text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center justify-center gap-2">
                                    <XCircle class="w-5 h-5" /> Rechazar Operación
                                </button>
                            </template>
                            <div v-else class="h-14 flex items-center justify-center gap-2 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-sm">
                                <Check class="h-5 w-5" /> Transacción Finalizada Correctamente
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ───────────────── CREATE MODAL (ESTILO COMPARTIDO) ───────────────── -->
        <Teleport to="body">
            <Transition name="modal-bounce">
                <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                    <div class="w-full max-w-lg rounded-[2.5rem] bg-white shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
                        <div class="bg-primary p-8 text-white relative">
                            <div class="absolute top-0 right-0 p-8 opacity-10"><Plus class="w-24 h-24" /></div>
                            <h2 class="font-serif text-3xl">Registrar <span class="italic underline decoration-white/20 underline-offset-8">Venta</span></h2>
                            <p class="mt-2 text-outline-variant text-sm italic">Gestión de Ingreso Manual</p>
                            <button @click="showCreate = false; resetCreate()" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                        <div class="p-8 space-y-6 overflow-y-auto">
                            <div class="relative">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2 block">Estudiante *</label>
                                <div class="relative group">
                                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                    <input v-model="userQuery" placeholder="Buscar por nombre o email..." @focus="onUserFocus" @blur="onUserBlur"
                                        class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all" />
                                    <span v-if="selectedUser" class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black uppercase text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg ring-1 ring-emerald-500/20 transition-all">Vinculado</span>
                                </div>
                                <div v-if="showDropdown && userResults.length" class="absolute z-10 mt-2 w-full rounded-2xl border border-slate-100 bg-white shadow-2xl overflow-hidden">
                                    <button v-for="u in userResults" :key="u.id" type="button" @mousedown.prevent="selectUser(u)"
                                        class="flex w-full items-center gap-3 px-6 py-4 text-left hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 group">
                                        <div :class="`h-10 w-10 rounded-xl flex items-center justify-center text-[10px] font-black ${aCls(u.id)} group-hover:scale-90 transition-transform`">{{ initials(u.name) }}</div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ u.name }}</p>
                                            <p class="text-xs text-slate-400 font-medium truncate">{{ u.email }}</p>
                                        </div>
                                        <ChevronRight class="ml-auto w-4 h-4 text-slate-200 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                                    </button>
                                </div>
                                <p v-if="createForm.errors.user_id" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-widest">{{ createForm.errors.user_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Especialidad Requerida *</label>
                                <select v-model="createForm.course_id" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-primary appearance-none cursor-pointer transition-all">
                                    <option value="">— Selecciona un curso del catálogo —</option>
                                    <option v-for="c in props.courses" :key="c.id" :value="c.id">{{ c.title }}</option>
                                </select>
                                <p v-if="createForm.errors.course_id" class="text-xs text-rose-500">{{ createForm.errors.course_id }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Monto Final (S/) *</label>
                                    <input v-model="createForm.amount" type="number" step="0.01" placeholder="0.00" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 focus:border-primary outline-none" />
                                    <p v-if="createForm.errors.amount" class="text-xs text-rose-500">{{ createForm.errors.amount }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estado Inicial</label>
                                    <select v-model="createForm.status" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 focus:border-primary outline-none">
                                        <option value="pendiente">⏳ Pendiente</option>
                                        <option value="en_revision">🔍 En revisión</option>
                                        <option value="aprobado">✅ Aprobado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Cargar Evidencia (Opcional)</label>
                                <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 hover:border-primary/30 transition-colors group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-500"><FileImage class="h-5 w-5 text-slate-400" /></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-700 truncate max-w-[200px]">{{ createForm.comprobante ? createForm.comprobante.name : 'Vincular Comprobante' }}</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium">PNG, JPG o WEBP (Máx 5MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
                                </label>
                                <img v-if="createForm.comprobante" :src="createPreviewUrl(createForm.comprobante)" class="mt-4 max-h-32 w-full rounded-2xl object-contain border border-slate-100 bg-slate-50" />
                            </div>
                        </div>

                        <div class="flex-shrink-0 flex justify-end gap-3 p-8 border-t border-slate-50 bg-slate-50/50">
                            <button type="button" @click="showCreate = false; resetCreate()" class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">Cancelar</button>
                            <button @click="submitCreate" :disabled="createForm.processing || !createForm.user_id || !createForm.course_id || !createForm.amount"
                                class="h-12 px-10 rounded-2xl bg-primary text-sm font-black text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all">
                                {{ createForm.processing ? 'Procesando...' : 'Registrar Pago' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Flash -->
        <Transition enter-active-class="transition duration-500" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                    <Check class="h-6 w-6" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-500">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); opacity: 0; }

.modal-bounce-enter-active { animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-bounce-leave-active { animation: modal-bounce-in 0.3s reverse ease-in; }

@keyframes modal-bounce-in {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
