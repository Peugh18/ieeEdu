<script setup lang="ts">
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { PaymentListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Head, router, useForm as useInertiaForm, usePage } from '@inertiajs/vue3';
import { Check, CheckCircle2, Clock, RefreshCw, Wallet, XCircle } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

// Components
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import PaymentDetailDrawer from '@/components/admin/payments/PaymentDetailDrawer.vue';
import PaymentsCreateModal from '@/components/admin/payments/PaymentsCreateModal.vue';
import PaymentsFilters from '@/components/admin/payments/PaymentsFilters.vue';
import PaymentsTable from '@/components/admin/payments/PaymentsTable.vue';

interface CourseOption {
    id: number;
    title: string;
    price: number;
    sale_price: number | null;
}

interface BookOption {
    id: number;
    title: string;
    price: number | string;
}

const props = defineProps<{
    payments: { data: PaymentListItem[]; links: PaginationLink[]; total: number; per_page: number };
    filters: { status?: string; search?: string; date?: string; per_page?: string; type?: string };
    stats: { total: number; pendiente: number; en_revision: number; aprobado: number; rechazado: number; book_income?: number; book_sales?: number };
    courses: CourseOption[];
    books: BookOption[];
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

// ─── Filters ────────────────────────────────────────────────────
const filterFormObj = useInertiaForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    date: props.filters.date || '',
    type: props.filters.type || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(
        route('admin.payments.index'),
        {
            search: filterFormObj.search || undefined,
            status: filterFormObj.status || undefined,
            date: filterFormObj.date || undefined,
            type: filterFormObj.type || undefined,
            per_page: filterFormObj.per_page !== '20' ? filterFormObj.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterFormObj, applyFilters);

// ─── Create Payment Modal ────────────────────────────────────────
const showCreate = ref(false);
const initialSearch = ref('');

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openCreate') === '1') {
        showCreate.value = true;
        if (props.filters.search) initialSearch.value = props.filters.search;
    }
});

// ─── Detail Drawer ────────────────────────────────────────────────
const detailPayment = ref<PaymentListItem | null>(null);

// ─── Actions ────────────────────────────────────────────────
function approve(p: PaymentListItem) {
    router.patch(route('admin.payments.approve', { payment: p.id }), {}, { preserveScroll: true });
}

function reject(p: PaymentListItem) {
    if (!confirm(`¿Rechazar pago de ${p.user.name}?`)) return;
    router.patch(route('admin.payments.reject', { payment: p.id }), {}, { preserveScroll: true });
}

function revert(p: PaymentListItem) {
    if (!confirm(`¿Revertir aprobación de ${p.user.name}? El estudiante perderá el acceso y el pago volverá a revisión.`)) return;
    router.patch(route('admin.payments.revert', { payment: p.id }), {}, { preserveScroll: true });
}

const pgLinks = usePaginationLinks(props.payments.links);
</script>

<template>
    <Head title="Gestión de Pagos - iieEdu Admin" />
    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-10 px-4 py-8">
            <!-- ── Header ── -->
            <AdminPageHeader
                title="Comprobantes de "
                titleAccent="pago"
                subtitle="Valida transferencias de cursos, libros y membresías."
                actionLabel="Registrar pago"
                compact
                @action="showCreate = true"
            />

            <!-- Filtro por tipo -->
            <div class="flex flex-wrap items-center gap-2">
                <button
                    v-for="opt in [
                        { key: '', label: 'Todos' },
                        { key: 'course', label: 'Cursos' },
                        { key: 'membership', label: 'Membresías' },
                        { key: 'books', label: 'Libros' },
                    ]"
                    :key="opt.key"
                    type="button"
                    class="rounded-xl px-4 py-2 text-xs font-bold transition-all"
                    :class="
                        filterFormObj.type === opt.key
                            ? 'bg-slate-900 text-white'
                            : 'border border-slate-200 bg-white text-slate-500 hover:border-slate-300'
                    "
                    @click="
                        filterFormObj.type = opt.key;
                        applyFilters();
                    "
                >
                    {{ opt.label }}
                </button>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div
                    v-for="s in [
                        { key: '', label: 'Total Global', val: stats.total, icon: Wallet, cls: 'text-slate-900' },
                        { key: 'pendiente', label: 'Pendientes', val: stats.pendiente, icon: Clock, cls: 'text-blue-600' },
                        { key: 'en_revision', label: 'Por Validar', val: stats.en_revision, icon: RefreshCw, cls: 'text-amber-600' },
                        { key: 'aprobado', label: 'Aprobados', val: stats.aprobado, icon: CheckCircle2, cls: 'text-emerald-600' },
                        { key: 'rechazado', label: 'Rechazados', val: stats.rechazado, icon: XCircle, cls: 'text-rose-600' },
                    ]"
                    :key="s.key"
                    @click="filterFormObj.status = s.key"
                    class="group relative cursor-pointer overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300"
                    :class="filterFormObj.status === s.key ? 'border-transparent ring-2 ring-primary' : 'hover:border-slate-200 hover:shadow-md'"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 transition-colors group-hover:text-slate-600"
                                >{{ s.label }}</span
                            >
                            <component :is="s.icon" class="h-4 w-4 text-slate-300 transition-colors group-hover:text-slate-500" />
                        </div>
                        <p class="text-4xl font-black tracking-tight" :class="s.cls">{{ s.val }}</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 h-20 w-20 opacity-[0.03] transition-opacity group-hover:opacity-[0.08]">
                        <component :is="s.icon" class="h-full w-full" />
                    </div>
                </div>
            </div>

            <!-- ── Filter Bar ── -->
            <PaymentsFilters
                v-model:search="filterFormObj.search"
                v-model:status="filterFormObj.status"
                v-model:date="filterFormObj.date"
                @filter="applyFilters"
            />

            <!-- ── Table View ── -->
            <PaymentsTable
                :payments="payments.data"
                :total="stats.total"
                :paginationLinks="pgLinks"
                @view="(p) => (detailPayment = p)"
                @approve="approve"
                @reject="reject"
                @revert="revert"
            />
        </div>

        <!-- ───────────────── DETAIL DRAWER (MODERNO) ─────────────────────── -->
        <PaymentDetailDrawer
            :payment="detailPayment"
            @close="detailPayment = null"
            @approve="
                (p) => {
                    approve(p);
                    detailPayment = null;
                }
            "
            @reject="
                (p) => {
                    reject(p);
                    detailPayment = null;
                }
            "
            @revert="
                (p) => {
                    revert(p);
                    detailPayment = null;
                }
            "
        />

        <!-- ───────────────── CREATE MODAL (ESTILO COMPARTIDO) ───────────────── -->
        <PaymentsCreateModal :show="showCreate" :courses="courses" :books="books" :initialSearch="initialSearch" @close="showCreate = false" />

        <!-- Flash -->
        <Transition
            enter-active-class="transition duration-500"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
        >
            <div
                v-if="flash.success"
                class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500">
                    <Check class="h-6 w-6" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-500">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
