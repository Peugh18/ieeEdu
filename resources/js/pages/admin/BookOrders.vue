<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    Clock, CheckCircle2, XCircle, MapPin, Package, Truck, Search, Filter
} from 'lucide-vue-next';
import type { BookOrder } from '@/types/book-order';
import type { PaginatedResponse } from '@/types/pagination';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';

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
    router.get(route('admin.book-orders.index'), {
        search: filterForm.search || undefined,
        status: filterForm.status || undefined,
        per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
    }, { preserveState: false, replace: true });
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
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
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
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <!-- ── Header ── -->
            <AdminPageHeader
                title="Pedidos de "
                titleAccent="libros"
                subtitle="Gestiona envíos físicos de libros comprados por los estudiantes."
                compact
            />

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div v-for="s in [
                    { key:'',                 label:'Total Pedidos', val: totalOrdersCount,      icon: Package,      cls:'text-slate-900' },
                    { key:'awaiting_address', label:'Sin Dirección', val: stats.awaiting_address, icon: MapPin,       cls:'text-amber-600' },
                    { key:'preparing',        label:'Preparando',    val: stats.preparing,       icon: Clock,        cls:'text-blue-600' },
                    { key:'shipped',          label:'En Camino',     val: stats.shipped,         icon: Truck,        cls:'text-violet-600' },
                    { key:'delivered',        label:'Entregados',    val: stats.delivered,       icon: CheckCircle2, cls:'text-emerald-600' },
                ]" :key="s.key"
                    @click="filterForm.status = s.key; applyFilters()"
                    class="group relative cursor-pointer overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300"
                    :class="filterForm.status === s.key ? 'ring-2 ring-primary border-transparent' : 'hover:shadow-md hover:border-slate-200'"
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
                    <input 
                        v-model="filterForm.search" 
                        placeholder="Buscar por estudiante, libro o correo..." 
                        class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all"
                        @keydown.enter.prevent="applyFilters"
                    />
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="relative flex-1 lg:flex-none min-w-[180px]">
                        <Filter class="absolute left-4 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <select 
                            v-model="filterForm.status" 
                            class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-10 pr-10 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer"
                        >
                            <option value="">Todos los Estados</option>
                            <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                    <div class="relative flex-1 lg:flex-none min-w-[160px]">
                        <select 
                            v-model="filterForm.per_page" 
                            class="w-full h-14 bg-white border border-slate-200 rounded-2xl px-4 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer"
                        >
                            <option value="10">10 por hoja</option>
                            <option value="20">20 por hoja</option>
                            <option value="50">50 por hoja</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Table View ── -->
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left min-w-[900px]">
                        <thead class="bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Pedido / Libro</th>
                                <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante</th>
                                <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Estatus Envío</th>
                                <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Destino</th>
                                <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="!orders.data.length">
                                <td colspan="5" class="py-24 text-center">
                                    <Package class="w-12 h-12 text-slate-200 mx-auto mb-4" />
                                    <p class="text-slate-400 font-medium">No se han detectado pedidos de libros con los criterios seleccionados.</p>
                                </td>
                            </tr>
                            <tr v-for="order in orders.data" :key="order.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${aCls(order.id)} shadow-sm`">
                                            #{{ order.id }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">
                                                {{ order.book?.title ?? 'Libro no especificado' }}
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span v-if="order.payment?.comprobante" class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-widest bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                                    Comp: {{ order.payment.comprobante }}
                                                </span>
                                                <span v-if="order.payment?.amount" class="text-[10px] text-slate-400 font-semibold">
                                                    {{ fMoney(order.payment.amount) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div v-if="order.user" class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 leading-tight">{{ order.user.name }}</span>
                                        <span class="text-xs text-slate-400 mt-0.5">{{ order.user.email }}</span>
                                        <span v-if="order.shipping_phone" class="text-[10px] text-slate-400 font-medium mt-0.5">Telf: {{ order.shipping_phone }}</span>
                                    </div>
                                    <span v-else class="text-slate-300 text-xs italic">— Sin usuario —</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center">
                                        <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
                                            :class="statusColors[order.shipping_status] ?? 'bg-slate-50 text-slate-500 ring-slate-700/10'">
                                            <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
                                            {{ statusLabels[order.shipping_status] ?? order.shipping_status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col items-center text-center">
                                        <span v-if="order.district" class="text-xs font-bold text-slate-600 flex items-center justify-center gap-1">
                                            <MapPin class="w-3 h-3 text-slate-400" />
                                            {{ order.district }}, {{ order.province }}
                                        </span>
                                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">{{ order.department ?? 'Departamento no especificado' }}</span>
                                        <span v-if="order.shipping_address" class="text-[9px] text-slate-400 line-clamp-1 max-w-[200px] mt-0.5" :title="order.shipping_address">
                                            {{ order.shipping_address }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                        <Link v-if="order.payment" :href="route('admin.payments.show', { payment: order.payment.id })" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Ver Detalle de Pago / Enviar">
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
            <div v-if="paginationLinks.length > 1" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mostrando {{ orders.data.length }} de {{ orders.total }} pedidos</p>
                <div class="flex items-center gap-1.5">
                    <Link v-for="link in paginationLinks" :key="link.label" :href="link.url || '#'"
                        class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600'"
                        v-html="link.label" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
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
