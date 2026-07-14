<script setup lang="ts">
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { PaymentListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Head, Link, router, useForm as useInertiaForm, usePage } from '@inertiajs/vue3';
import { Check, CheckCircle2, Clock, RefreshCw, Wallet, XCircle, Package } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

// Components
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
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

interface PlanOption {
    slug: string;
    name: string;
    price: number;
    months: number;
}

const props = defineProps<{
    payments: { data: PaymentListItem[]; links: PaginationLink[]; total: number; per_page: number };
    filters: { status?: string; search?: string; date?: string; per_page?: string; type?: string; sub_status?: string };
    stats: { total: number; pendiente: number; en_revision: number; aprobado: number; rechazado: number; book_income?: number; book_sales?: number };
    courses: CourseOption[];
    books: BookOption[];
    planOptions: PlanOption[];
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

// ─── Filters ────────────────────────────────────────────────────
const filterFormObj = useInertiaForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    date: props.filters.date || '',
    type: props.filters.type || '',
    sub_status: props.filters.sub_status || '',
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
            sub_status: filterFormObj.sub_status || undefined,
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

function destroy(p: PaymentListItem) {
    if (!confirm(`¿Estás súper seguro de ELIMINAR el pago de ${p.user.name}? Esta acción borrará el registro de la base de datos de forma irreversible.`)) return;
    router.delete(route('admin.payments.destroy', { payment: p.id }), { preserveScroll: true });
}

const pgLinks = usePaginationLinks(props.payments.links);
</script>

<template>
    <Head title="Gestión de Pagos - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- ── Header ── -->
            <AdminPageHeader
                title="Comprobantes de "
                titleAccent="pago"
                subtitle="Valida transferencias de cursos, libros y membresías."
                actionLabel="Registrar pago"
                @action="showCreate = true"
            />

            <!-- ── Tab Bar ── -->
            <div class="flex gap-1 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-1">
                <button
                    type="button"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all bg-primary text-white shadow-md"
                >
                    <Wallet class="h-3.5 w-3.5" />
                    Comprobantes de pago
                </button>
                <Link
                    :href="route('admin.book-orders.index')"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"
                >
                    <Package class="h-3.5 w-3.5" />
                    Despachos de libros
                </Link>
            </div>

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
            
            <!-- Filtro de Membresía (solo visible si type === 'membership') -->
            <div v-if="filterFormObj.type === 'membership'" class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 mr-2">Vigencia:</span>
                <button
                    v-for="opt in [
                        { key: '', label: 'Todas', icon: null },
                        { key: 'activas', label: 'Activas', icon: CheckCircle2, color: 'text-emerald-500' },
                        { key: 'por_vencer', label: 'Por Vencer (5 días)', icon: Clock, color: 'text-amber-500' },
                        { key: 'expiradas', label: 'Expiradas', icon: XCircle, color: 'text-rose-500' },
                    ]"
                    :key="opt.key"
                    type="button"
                    class="rounded-xl px-4 py-1.5 text-xs font-bold transition-all"
                    :class="
                        filterFormObj.sub_status === opt.key
                            ? 'bg-primary/10 text-primary border border-primary/20'
                            : 'border border-slate-200 bg-white text-slate-500 hover:border-slate-300'
                    "
                    @click="
                        filterFormObj.sub_status = opt.key;
                        applyFilters();
                    "
                >
                    <div class="flex items-center gap-1.5">
                        <component v-if="opt.icon" :is="opt.icon" :class="['h-3.5 w-3.5', opt.color]" />
                        <span>{{ opt.label }}</span>
                    </div>
                </button>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <AdminStatsCard
                    v-for="s in [
                        { key: '', label: 'Total Global', val: stats.total, icon: Wallet, cls: 'text-on-surface' },
                        { key: 'pendiente', label: 'Pendientes', val: stats.pendiente, icon: Clock, cls: 'text-blue-500' },
                        { key: 'en_revision', label: 'Por Validar', val: stats.en_revision, icon: RefreshCw, cls: 'text-amber-500' },
                        { key: 'aprobado', label: 'Aprobados', val: stats.aprobado, icon: CheckCircle2, cls: 'text-emerald-500' },
                        { key: 'rechazado', label: 'Rechazados', val: stats.rechazado, icon: XCircle, cls: 'text-rose-500' },
                    ]"
                    :key="s.key"
                    :label="s.label"
                    :value="s.val"
                    :value-class="s.cls"
                    class="cursor-pointer"
                    :class="filterFormObj.status === s.key ? 'ring-2 ring-primary ring-offset-1' : ''"
                    @click="filterFormObj.status = s.key"
                >
                    <template #icon><component :is="s.icon" class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><component :is="s.icon" class="h-full w-full" /></template>
                </AdminStatsCard>
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
                :type="filterFormObj.type"
                @view="(p) => (detailPayment = p)"
                @approve="approve"
                @reject="reject"
                @revert="revert"
                @destroy="destroy"
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
            @destroy="
                (p) => {
                    destroy(p);
                    detailPayment = null;
                }
            "
        />

        <!-- ───────────────── CREATE MODAL (ESTILO COMPARTIDO) ───────────────── -->
        <PaymentsCreateModal :show="showCreate" :courses="courses" :books="books" :planOptions="planOptions" :initialSearch="initialSearch" @close="showCreate = false" />

        <AdminFlashToast
            :show="!!flash.success"
            :message="flash.success ?? ''"
            variant="success"
        />
    </AppLayout>
</template>
