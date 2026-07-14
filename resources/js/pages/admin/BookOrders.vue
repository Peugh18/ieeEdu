<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BookOrder } from '@/types/book-order';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Filter, MapPin, Package, Search, Truck, ExternalLink, Wallet } from 'lucide-vue-next';
import { computed } from 'vue';
import AppSelect from '@/components/ui/AppSelect.vue';

const props = defineProps<{
    orders: PaginatedResponse<BookOrder>;
    filters: { status?: string; search?: string; per_page?: string };
    stats: { awaiting_address: number; preparing: number; shipped: number; delivered: number };
    statusLabels: Record<string, string>;
}>();

const filterForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(
        route('admin.book-orders.index'),
        {
            search: filterForm.search || undefined,
            status: filterForm.status || undefined,
            per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterForm, applyFilters);
const paginationLinks = usePaginationLinks(props.orders.links);

const totalOrdersCount = computed(() => {
    return props.stats.awaiting_address + props.stats.preparing + props.stats.shipped + props.stats.delivered;
});

const statusColors: Record<string, string> = {
    awaiting_address: 'bg-amber-50 text-amber-700 ring-amber-700/10',
    preparing: 'bg-blue-50 text-blue-700 ring-blue-700/10',
    shipped: 'bg-violet-50 text-violet-700 ring-violet-700/10',
    delivered: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
    cancelled: 'bg-rose-50 text-rose-700 ring-rose-700/10',
};

const initials = (name?: string) => {
    if (!name) return '??';
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
};

const avatarColors = [
    'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
    'bg-amber-50 text-amber-700 ring-amber-700/10',
    'bg-rose-50 text-rose-700 ring-rose-700/10',
    'bg-blue-50 text-blue-700 ring-blue-700/10',
    'bg-purple-50 text-purple-700 ring-purple-700/10',
];
const aCls = (id: number) => avatarColors[id % avatarColors.length];

const fMoney = (n: number | string) => 'S/ ' + Number(n).toFixed(2);
</script>

<template>
    <Head title="Pedidos de Libros - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- ── Header ── -->
            <AdminPageHeader
                title="Despachos de "
                titleAccent="libros"
                subtitle="Gestiona envíos físicos de libros comprados por los estudiantes."
            />

            <!-- ── Tab Bar ── -->
            <div class="flex gap-1 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-1">
                <Link
                    :href="route('admin.payments.index')"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"
                >
                    <Wallet class="h-3.5 w-3.5" />
                    Comprobantes de pago
                </Link>
                <button
                    type="button"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all bg-primary text-white shadow-md"
                >
                    <Package class="h-3.5 w-3.5" />
                    Despachos de libros
                </button>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <AdminStatsCard
                    v-for="s in [
                        { key: '', label: 'Total Pedidos', val: totalOrdersCount, icon: Package, cls: 'text-on-surface' },
                        { key: 'awaiting_address', label: 'Sin Dirección', val: stats.awaiting_address, icon: MapPin, cls: 'text-amber-500' },
                        { key: 'preparing', label: 'Preparando', val: stats.preparing, icon: Clock, cls: 'text-blue-500' },
                        { key: 'shipped', label: 'En Camino', val: stats.shipped, icon: Truck, cls: 'text-violet-500' },
                        { key: 'delivered', label: 'Entregados', val: stats.delivered, icon: CheckCircle2, cls: 'text-emerald-500' },
                    ]"
                    :key="s.key"
                    :label="s.label"
                    :value="s.val"
                    :value-class="s.cls"
                    class="cursor-pointer"
                    :class="filterForm.status === s.key ? 'ring-2 ring-primary ring-offset-1' : ''"
                    @click="filterForm.status = s.key; applyFilters();"
                >
                    <template #icon><component :is="s.icon" class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><component :is="s.icon" class="h-full w-full" /></template>
                </AdminStatsCard>
            </div>

            <!-- ── Filter Bar ── -->
            <div class="flex flex-col items-center gap-3 rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-low p-3 lg:flex-row">
                <div class="relative w-full flex-1 lg:w-auto">
                    <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant/40" />
                    <input
                        v-model="filterForm.search"
                        placeholder="Buscar por estudiante, libro o correo..."
                        class="h-14 w-full rounded-2xl border border-outline-variant/20 bg-surface pl-12 pr-6 text-sm font-medium text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/5"
                        @keydown.enter.prevent="applyFilters"
                    />
                </div>
                <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
                    <div class="relative min-w-[180px] flex-1 lg:flex-none">
                        <Filter class="absolute left-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-on-surface-variant/40" />
                        <AppSelect
                            v-model="filterForm.status"
                            :options="[
                                { value: '', label: 'Todos los Estados' },
                                ...Object.entries(statusLabels).map(([value, label]) =&gt; ({ value, label }))
                            ]"
                            class="h-14 border-outline-variant/20 bg-surface font-bold pl-10 shadow-none text-xs"
                        />
                    </div>
                    <div class="relative min-w-[160px] flex-1 lg:flex-none">
                        <AppSelect
                            v-model="filterForm.per_page"
                            :options="[
                                { value: '10', label: '10 por hoja' },
                                { value: '20', label: '20 por hoja' },
                                { value: '50', label: '50 por hoja' }
                            ]"
                            class="h-14 border-outline-variant/20 bg-surface font-bold px-4 shadow-none text-xs"
                        />
                    </div>
                </div>
            </div>

            <!-- ── Table View ── -->
            <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-sm">
                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="border-b border-slate-100 bg-slate-50/80">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Pedido / Libro</th>
                                <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante</th>
                                <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">
                                    Estatus Envío
                                </th>
                                <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Destino</th>
                                <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="!orders.data.length">
                                <td colspan="5" class="py-24 text-center">
                                    <Package class="mx-auto mb-4 h-12 w-12 text-slate-200" />
                                    <p class="font-medium text-slate-400">No se han detectado pedidos de libros con los criterios seleccionados.</p>
                                </td>
                            </tr>
                            <tr v-for="order in orders.data" :key="order.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${aCls(order.id)} shadow-sm`"
                                        >
                                            #{{ order.id }}
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary"
                                            >
                                                {{ order.book?.title ?? 'Libro no especificado' }}
                                            </p>
                                            <div class="mt-1 flex items-center gap-1.5">
                                                <a
                                                    v-if="order.payment?.comprobante"
                                                    :href="order.payment.comprobante"
                                                    target="_blank"
                                                    title="Ver comprobante"
                                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-widest text-blue-700 hover:bg-blue-100 transition-colors"
                                                >
                                                    <ExternalLink class="h-2.5 w-2.5" />
                                                    Comprobante
                                                </a>
                                                <span v-if="order.payment?.amount" class="text-[10px] font-semibold text-slate-400">
                                                    {{ fMoney(order.payment.amount) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div v-if="order.user" class="flex flex-col">
                                        <span class="text-sm font-bold leading-tight text-slate-900">{{ order.user.name }}</span>
                                        <span class="mt-0.5 text-xs text-slate-400">{{ order.user.email }}</span>
                                        <span v-if="order.shipping_phone" class="mt-0.5 text-[10px] font-medium text-slate-400"
                                            >Telf: {{ order.shipping_phone }}</span
                                        >
                                    </div>
                                    <span v-else class="text-xs italic text-slate-300">— Sin usuario —</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                            :class="statusColors[order.shipping_status] ?? 'bg-slate-50 text-slate-500 ring-slate-700/10'"
                                        >
                                            <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
                                            {{ statusLabels[order.shipping_status] ?? order.shipping_status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col items-center text-center">
                                        <span v-if="order.district" class="flex items-center justify-center gap-1 text-xs font-bold text-slate-600">
                                            <MapPin class="h-3 w-3 text-slate-400" />
                                            {{ order.district }}, {{ order.province }}
                                        </span>
                                        <span class="mt-0.5 text-[9px] font-medium uppercase tracking-widest text-slate-400">{{
                                            order.department ?? 'Departamento no especificado'
                                        }}</span>
                                        <span
                                            v-if="order.shipping_address"
                                            class="mt-0.5 line-clamp-1 max-w-[200px] text-[9px] text-slate-400"
                                            :title="order.shipping_address"
                                        >
                                            {{ order.shipping_address }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link
                                            v-if="order.payment"
                                            :href="route('admin.payments.show', { payment: order.payment.id })"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition-all hover:border-primary hover:bg-primary/5 hover:text-primary"
                                            title="Ver Detalle de Pago / Configurar envío"
                                        >
                                            <Truck class="h-4 w-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="paginationLinks.length > 1" class="flex flex-col items-center justify-between gap-4 px-2 md:flex-row">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    Mostrando {{ orders.data.length }} de {{ orders.total }} pedidos
                </p>
                <div class="flex items-center gap-1.5">
                    <Link
                        v-for="link in paginationLinks"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="
                            link.active
                                ? 'bg-slate-900 text-white shadow-lg'
                                : 'border border-slate-100 bg-white text-slate-400 hover:border-slate-300 hover:text-slate-600'
                        "
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
}
</style>
