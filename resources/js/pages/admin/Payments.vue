<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import {
    AlertCircle, Check, CheckCircle2, Clock,
    Download, Eye, FileImage, Plus, RefreshCw,
    Search, X, XCircle
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
        
        // Si hay una búsqueda, podemos intentar pre-seleccionar o al menos dejar la búsqueda escrita
        if (props.filters.search) {
             userQuery.value = props.filters.search;
        }
    }
});
const userResults  = ref<UserResult[]>([]);
const selectedUser = ref<UserResult | null>(null);
const showDropdown = ref(false);
let userTimer: any;
let isSelecting = false; // Flag to prevent circular reset

function onUserFocus() {
    if (userResults.value.length) showDropdown.value = true;
}
function onUserBlur() {
    setTimeout(() => { showDropdown.value = false; }, 200);
}

watch(userQuery, (val) => {
    if (isSelecting) {
        isSelecting = false;
        return;
    }
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

// ── Auto-fill amount from course ─────────────────────────────────
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
    if (!createForm.user_id) {
        createForm.setError('user_id', 'Selecciona un estudiante.');
        return;
    }

    createForm.post(route('admin.payments.store'), {
        forceFormData: true,
        onSuccess: () => {
            showCreate.value = false;
            resetCreate();
        },
        preserveScroll: true
    });
}

// ─── Quick Actions ────────────────────────────────────────────────
function approve(p: PaymentItem) { router.patch(route('admin.payments.approve', { payment: p.id }), {}, { preserveScroll: true }); }
function reject(p: PaymentItem) {
    if (!confirm(`¿Rechazar pago de ${p.user.name}?`)) return;
    router.patch(route('admin.payments.reject', { payment: p.id }), {}, { preserveScroll: true });
}

// ─── Detail Drawer ────────────────────────────────────────────────
const detailPayment = ref<PaymentItem | null>(null);

// ─── Helpers ─────────────────────────────────────────────────────
const statusCfg: Record<string, { label: string; cls: string; icon: any }> = {
    pendiente:   { label: 'Pendiente',   cls: 'bg-slate-100 text-slate-600',    icon: Clock },
    en_revision: { label: 'En revisión', cls: 'bg-amber-100 text-amber-700',    icon: RefreshCw },
    aprobado:    { label: 'Aprobado',    cls: 'bg-emerald-100 text-emerald-700', icon: CheckCircle2 },
    rechazado:   { label: 'Rechazado',   cls: 'bg-red-100 text-red-600',        icon: XCircle },
};
const initials = (n: string) => n.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
const colors   = ['bg-purple-100 text-purple-700','bg-blue-100 text-blue-700','bg-emerald-100 text-emerald-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700'];
const aCls     = (id: number) => colors[id % colors.length];
const fDate    = (d: string) => new Date(d).toLocaleDateString('es-PE', { day:'2-digit', month:'short', year:'numeric' });
const fMoney   = (n: number|string) => 'S/ ' + Number(n).toFixed(2);
const pgLinks  = computed(() => props.payments.links?.filter((l: any) => l.url) ?? []);
// Expose browser global for use in template
const createPreviewUrl = (file: File) => URL.createObjectURL(file);
</script>

<template>
    <Head title="Pagos - Admin IEE" />
    <AppLayout>

        <!-- ── Header ─────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-on-surface">Gestión de <span class="italic text-[#57572A]">Pagos</span></h1>
                <p class="mt-1 text-sm text-on-surface-variant">Aprueba comprobantes y desbloquea cursos manualmente.</p>
            </div>
            <button @click="showCreate = true" class="inline-flex items-center gap-2 rounded-xl bg-[#57572A] px-5 py-2.5 text-sm font-bold text-white shadow hover:opacity-95">
                <Plus class="h-4 w-4" /> Registrar pago
            </button>
        </div>

        <!-- ── Stats (clickeables para filtrar) ──────────────── -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-5">
            <div v-for="s in [
                { key:'',            label:'Total',       val:props.stats.total,       cls:'text-on-surface' },
                { key:'pendiente',   label:'Pendientes',  val:props.stats.pendiente,   cls:'text-slate-600' },
                { key:'en_revision', label:'En revisión', val:props.stats.en_revision, cls:'text-amber-600' },
                { key:'aprobado',    label:'Aprobados',   val:props.stats.aprobado,    cls:'text-emerald-600' },
                { key:'rechazado',   label:'Rechazados',  val:props.stats.rechazado,   cls:'text-red-600' },
            ]" :key="s.key"
                class="cursor-pointer rounded-2xl border border-outline-variant/10 bg-white p-4 shadow-sm hover:border-[#57572A]/30 transition-colors"
                :class="statusFilter === s.key ? 'ring-2 ring-[#57572A]/30' : ''"
                @click="statusFilter = s.key"
            >
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ s.label }}</p>
                <p class="mt-2 text-3xl font-bold" :class="s.cls">{{ s.val }}</p>
            </div>
        </div>

        <!-- ── Filters ────────────────────────────────────────── -->
        <div class="mb-6 rounded-2xl border border-outline-variant/10 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex flex-1 items-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2">
                    <Search class="h-4 w-4 flex-shrink-0 text-on-surface-variant" />
                    <input v-model="searchInput" placeholder="Buscar por usuario o curso..." class="w-full bg-transparent text-sm outline-none placeholder:text-on-surface-variant/60" />
                </div>
                <div class="flex flex-wrap gap-2">
                    <select v-model="statusFilter" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm outline-none">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_revision">En revisión</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                    <input v-model="dateFilter" type="date" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm outline-none" />
                    <select v-model="perPage" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm outline-none">
                        <option value="10">10 / pág.</option>
                        <option value="20">20 / pág.</option>
                        <option value="50">50 / pág.</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ── Table ──────────────────────────────────────────── -->
        <div class="rounded-3xl border border-outline-variant/10 bg-white overflow-hidden shadow-sm">
            <div class="grid grid-cols-12 gap-2 bg-surface-container-low px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/70">
                <div class="col-span-4">Usuario / Curso</div>
                <div class="col-span-2">Monto</div>
                <div class="col-span-2">Estado</div>
                <div class="col-span-2">Fecha</div>
                <div class="col-span-2 text-right">Acciones</div>
            </div>

            <div v-if="!props.payments.data.length" class="py-16 text-center text-sm text-on-surface-variant">
                No se encontraron pagos con los filtros aplicados.
            </div>

            <div v-else>
                <div v-for="p in props.payments.data" :key="p.id"
                    class="grid grid-cols-12 items-center gap-2 border-t border-outline-variant/10 px-6 py-4 hover:bg-surface-container-lowest transition-colors">

                    <div class="col-span-4 flex items-center gap-3 min-w-0">
                        <div :class="`flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold ${aCls(p.id)}`">
                            {{ initials(p.user.name) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-on-surface">{{ p.user.name }}</p>
                            <p class="truncate text-xs text-on-surface-variant">{{ p.course?.title ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="col-span-2 text-sm font-bold text-on-surface">{{ fMoney(p.amount) }}</div>

                    <div class="col-span-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide"
                            :class="statusCfg[p.status]?.cls ?? 'bg-slate-100 text-slate-500'">
                            <component :is="statusCfg[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                            {{ statusCfg[p.status]?.label ?? p.status }}
                        </span>
                    </div>

                    <div class="col-span-2 text-sm text-on-surface-variant">{{ fDate(p.created_at) }}</div>

                    <div class="col-span-2 flex items-center justify-end gap-1">
                        <span v-if="p.comprobante" title="Tiene comprobante"><FileImage class="h-4 w-4 text-blue-400" /></span>
                        <button @click="detailPayment = p" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-low hover:text-[#57572A]" title="Ver detalle"><Eye class="h-4 w-4" /></button>
                        <button v-if="p.status !== 'aprobado'" @click="approve(p)" class="rounded-lg p-2 text-on-surface-variant hover:bg-emerald-50 hover:text-emerald-600" title="Aprobar"><CheckCircle2 class="h-4 w-4" /></button>
                        <button v-if="p.status !== 'rechazado' && p.status !== 'aprobado'" @click="reject(p)" class="rounded-lg p-2 text-on-surface-variant hover:bg-red-50 hover:text-red-600" title="Rechazar"><XCircle class="h-4 w-4" /></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Pagination ─────────────────────────────────────── -->
        <div v-if="pgLinks.length > 1" class="mt-4 flex items-center justify-between text-sm text-on-surface-variant">
            <p>{{ props.payments.total }} pago{{ props.payments.total !== 1 ? 's' : '' }} en total</p>
            <div class="flex gap-1.5">
                <Link v-for="link in pgLinks" :key="link.label" :href="link.url"
                    class="rounded-xl border px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="link.active ? 'border-[#57572A] bg-[#57572A] text-white' : 'border-outline-variant/20 bg-white text-on-surface hover:bg-surface-container-low'"
                    v-html="link.label" />
            </div>
        </div>

        <!-- Flash -->
        <Transition enter-active-class="transition duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-emerald-600 px-5 py-3 text-white shadow-2xl">
                <Check class="h-5 w-5" /><span class="text-sm font-semibold">{{ flash.success }}</span>
            </div>
        </Transition>

        <!-- ───────────────── DETAIL DRAWER ─────────────────────── -->
        <Teleport to="body">
            <Transition name="slide">
                <div v-if="detailPayment" class="fixed inset-0 z-50 flex items-center justify-end bg-black/40" @click.self="detailPayment = null">
                    <div class="h-full w-full max-w-md overflow-y-auto bg-white shadow-2xl">
                        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-outline-variant/10 bg-white px-7 py-5">
                            <h2 class="font-serif text-xl text-on-surface">Pago <span class="italic">#{{ detailPayment.id }}</span></h2>
                            <button @click="detailPayment = null" class="rounded-xl p-2 hover:bg-surface-container-low"><X class="h-5 w-5 text-on-surface-variant" /></button>
                        </div>

                        <div :class="`flex items-center gap-3 px-7 py-4 ${statusCfg[detailPayment.status]?.cls}`">
                            <component :is="statusCfg[detailPayment.status]?.icon ?? AlertCircle" class="h-5 w-5" />
                            <span class="font-bold text-sm">{{ statusCfg[detailPayment.status]?.label }}</span>
                        </div>

                        <div class="space-y-5 p-7">
                            <!-- User -->
                            <div class="rounded-2xl border border-outline-variant/10 p-4">
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Estudiante</p>
                                <p class="font-bold text-on-surface">{{ detailPayment.user.name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ detailPayment.user.email }}</p>
                            </div>

                            <!-- Course + Amount -->
                            <div class="rounded-2xl border border-outline-variant/10 p-4 space-y-3">
                                <div><p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Curso</p><p class="mt-1 font-semibold text-on-surface">{{ detailPayment.course?.title ?? '—' }}</p></div>
                                <div><p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Monto</p><p class="mt-1 text-2xl font-bold text-[#57572A]">{{ fMoney(detailPayment.amount) }}</p></div>
                                <div><p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Fecha</p><p class="mt-1 text-sm text-on-surface">{{ fDate(detailPayment.created_at) }}</p></div>
                            </div>

                            <!-- Comprobante -->
                            <div class="rounded-2xl border border-outline-variant/10 p-4">
                                <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Comprobante</p>
                                <template v-if="detailPayment.comprobante">
                                    <a :href="detailPayment.comprobante" target="_blank">
                                        <img :src="detailPayment.comprobante" alt="Comprobante" class="w-full rounded-xl object-contain max-h-72 cursor-zoom-in hover:opacity-90 transition-opacity" />
                                    </a>
                                    <a :href="detailPayment.comprobante" download class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#57572A] hover:underline">
                                        <Download class="h-3.5 w-3.5" /> Descargar
                                    </a>
                                </template>
                                <div v-else class="flex items-center gap-2 rounded-xl bg-surface-container-low p-4 text-sm text-on-surface-variant">
                                    <FileImage class="h-5 w-5 opacity-40" /> Sin comprobante adjunto
                                </div>
                            </div>

                            <!-- Actions -->
                            <div v-if="detailPayment.status !== 'aprobado'" class="space-y-2">
                                <button @click="approve(detailPayment); detailPayment = null" class="w-full rounded-2xl bg-emerald-600 py-3 text-sm font-bold text-white hover:opacity-90">✓ Aprobar y desbloquear</button>
                                <button v-if="detailPayment.status !== 'rechazado'" @click="reject(detailPayment); detailPayment = null" class="w-full rounded-2xl border border-red-200 bg-red-50 py-3 text-sm font-bold text-red-600 hover:bg-red-100">✕ Rechazar pago</button>
                            </div>
                            <div v-else class="rounded-2xl bg-emerald-50 p-4 text-center text-sm font-bold text-emerald-700">✓ Curso desbloqueado</div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ───────────────── CREATE PAYMENT MODAL ───────────────── -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl flex flex-col max-h-[95vh]">
                        <!-- Header -->
                        <div class="flex-shrink-0 flex items-center justify-between border-b border-outline-variant/10 px-7 py-5">
                            <div>
                                <h2 class="font-serif text-2xl text-on-surface">Registrar <span class="italic">Pago</span></h2>
                                <p class="mt-0.5 text-xs text-on-surface-variant">Yape · Transferencia · Efectivo</p>
                            </div>
                            <button @click="showCreate = false; resetCreate()" class="rounded-xl p-2 hover:bg-surface-container-low"><X class="h-5 w-5 text-on-surface-variant" /></button>
                        </div>

                        <!-- Scrollable body -->
                        <div class="overflow-y-auto flex-1 p-7 space-y-5">

                            <!-- ── User search (AJAX) ──────────────────── -->
                            <div class="relative">
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Estudiante *</label>
                                <div class="flex items-center gap-2 rounded-xl border border-outline-variant/30 px-3 py-2.5 focus-within:border-[#57572A] transition-colors">
                                    <Search class="h-4 w-4 flex-shrink-0 text-on-surface-variant" />
                                    <input
                                        v-model="userQuery"
                                        placeholder="Escribe nombre o email..."
                                        class="w-full bg-transparent text-sm outline-none"
                                        @focus="onUserFocus"
                                        @blur="onUserBlur"
                                    />
                                    <span v-if="selectedUser" class="text-[10px] font-bold text-emerald-600">✓ seleccionado</span>
                                </div>

                                <!-- Dropdown results -->
                                <div v-if="showDropdown && userResults.length" class="absolute z-10 mt-1 w-full rounded-2xl border border-outline-variant/20 bg-white shadow-xl overflow-hidden">
                                    <button
                                        v-for="u in userResults" :key="u.id" type="button"
                                        @mousedown.prevent="selectUser(u)"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-surface-container-low transition-colors border-b border-outline-variant/10 last:border-0"
                                    >
                                        <div :class="`flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold ${aCls(u.id)}`">{{ initials(u.name) }}</div>
                                        <div>
                                            <p class="text-sm font-bold text-on-surface">{{ u.name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ u.email }}</p>
                                        </div>
                                    </button>
                                </div>
                                <div v-else-if="userQuery.length >= 2 && !userResults.length && !selectedUser" class="mt-1 rounded-xl border border-outline-variant/20 bg-surface-container-low p-3 text-xs text-on-surface-variant">
                                    No se encontraron estudiantes.
                                </div>
                                <p class="mt-1 text-[11px] text-on-surface-variant/60">Escribe al menos 2 caracteres para buscar</p>
                                <p v-if="createForm.errors.user_id" class="mt-1 text-xs text-red-500">{{ createForm.errors.user_id }}</p>
                            </div>

                            <!-- ── Course ─────────────────────────────── -->
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Curso *</label>
                                <select v-model="createForm.course_id" class="w-full rounded-xl border border-outline-variant/30 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors">
                                    <option value="">— Selecciona un curso —</option>
                                    <option v-for="c in props.courses" :key="c.id" :value="c.id">{{ c.title }}</option>
                                </select>
                                <p v-if="createForm.errors.course_id" class="mt-1 text-xs text-red-500">{{ createForm.errors.course_id }}</p>
                            </div>

                            <!-- ── Amount + Status ───────────────────── -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Monto (S/) *</label>
                                    <input v-model="createForm.amount" type="number" step="0.01" min="0" placeholder="0.00"
                                        class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A]" />
                                    <p v-if="createForm.errors.amount" class="mt-1 text-xs text-red-500">{{ createForm.errors.amount }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Estado inicial</label>
                                    <select v-model="createForm.status" class="w-full rounded-xl border border-outline-variant/30 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#57572A]">
                                        <option value="pendiente">⏳ Pendiente</option>
                                        <option value="en_revision">🔍 En revisión</option>
                                        <option value="aprobado">✅ Aprobar ya</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ── Comprobante ────────────────────────── -->
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Comprobante (opcional)</label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-outline-variant/40 bg-surface-container-lowest p-4 hover:border-[#57572A]/40 transition-colors">
                                    <FileImage class="h-6 w-6 text-on-surface-variant/50" />
                                    <div>
                                        <p class="text-sm font-semibold text-on-surface-variant">{{ createForm.comprobante ? createForm.comprobante.name : 'Subir imagen del comprobante' }}</p>
                                        <p class="text-[11px] text-on-surface-variant/50">JPG, PNG, WEBP — Máx. 5 MB</p>
                                    </div>
                                    <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
                                </label>
                                <img v-if="createForm.comprobante" :src="createPreviewUrl(createForm.comprobante)" class="mt-2 max-h-32 w-full rounded-xl object-contain border border-outline-variant/10" />
                            </div>

                            <!-- Info note -->
                            <div v-if="createForm.status === 'aprobado'" class="flex items-start gap-2 rounded-xl bg-emerald-50 p-3 text-xs text-emerald-700">
                                <CheckCircle2 class="mt-0.5 h-4 w-4 flex-shrink-0" />
                                <span>El curso se desbloqueará <strong>inmediatamente</strong> para el estudiante.</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex-shrink-0 flex justify-end gap-3 border-t border-outline-variant/10 px-7 py-5">
                            <button type="button" @click="showCreate = false; resetCreate()" class="rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-semibold hover:bg-surface-container-low">Cancelar</button>
                            <button
                                @click="submitCreate"
                                :disabled="createForm.processing || !createForm.user_id || !createForm.course_id || !createForm.amount"
                                class="rounded-xl bg-[#57572A] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 disabled:opacity-50 transition-opacity"
                            >
                                {{ createForm.processing ? 'Registrando...' : 'Registrar pago' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active, .slide-enter-active, .slide-leave-active { transition: all 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateX(100%); }
</style>
