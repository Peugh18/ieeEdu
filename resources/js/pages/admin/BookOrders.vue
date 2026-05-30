<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import AdminSearchBar from '@/components/admin/AdminSearchBar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Package, Truck, MapPin } from 'lucide-vue-next';
import type { BookOrder } from '@/types/book-order';
import type { PaginatedResponse } from '@/types/pagination';

const props = defineProps<{
    orders: PaginatedResponse<BookOrder>;
    filters: { status?: string; search?: string; per_page?: string };
    stats: { awaiting_address: number; preparing: number; shipped: number; delivered: number };
    statusLabels: Record<string, string>;
}>();

const searchQuery = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');

watch([searchQuery, statusFilter], () => {
    router.get(route('admin.book-orders.index'), {
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
});

const statusColors: Record<string, string> = {
    awaiting_address: 'bg-amber-50 text-amber-700',
    preparing: 'bg-blue-50 text-blue-700',
    shipped: 'bg-violet-50 text-violet-700',
    delivered: 'bg-emerald-50 text-emerald-700',
    cancelled: 'bg-red-50 text-red-600',
};
</script>

<template>
    <Head title="Admin - Pedidos de libros" />
    <AppLayout>
        <AdminPageHeader title="Pedidos de libros" subtitle="Envíos físicos en todo el Perú." compact />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl bg-amber-50 border border-amber-100 p-4 text-center">
                <p class="text-2xl font-black text-amber-700 tabular-nums">{{ stats.awaiting_address }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-amber-600 mt-1">Sin dirección</p>
            </div>
            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4 text-center">
                <p class="text-2xl font-black text-blue-700 tabular-nums">{{ stats.preparing }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-blue-600 mt-1">Preparando</p>
            </div>
            <div class="rounded-2xl bg-violet-50 border border-violet-100 p-4 text-center">
                <p class="text-2xl font-black text-violet-700 tabular-nums">{{ stats.shipped }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-violet-600 mt-1">En camino</p>
            </div>
            <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-center">
                <p class="text-2xl font-black text-emerald-700 tabular-nums">{{ stats.delivered }}</p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-600 mt-1">Entregados</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <AdminSearchBar v-model="searchQuery" placeholder="Buscar alumno o libro..." class="flex-1" />
            <select v-model="statusFilter" class="rounded-2xl border border-outline-variant/20 px-4 py-3 text-sm font-bold bg-white">
                <option value="">Todos los estados</option>
                <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <div class="rounded-[2rem] border border-outline-variant/10 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px]">
                    <thead class="bg-surface-container-low/50 border-b border-outline-variant/10">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60">Pedido</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60">Alumno</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60">Libro</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60">Envío</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-primary/[0.02]">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold">#{{ order.payment?.id ?? order.id }}</p>
                                <p class="text-[10px] text-slate-400">{{ statusLabels[order.shipping_status] ?? order.shipping_status }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold">{{ order.user?.name }}</p>
                                <p class="text-xs text-slate-400">{{ order.user?.email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">{{ order.book?.title }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase" :class="statusColors[order.shipping_status] ?? 'bg-slate-100'">
                                    {{ statusLabels[order.shipping_status] }}
                                </span>
                                <p v-if="order.department" class="text-[10px] text-slate-500 mt-1 flex items-center gap-1"><MapPin class="w-3 h-3" /> {{ order.province }}, {{ order.department }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link v-if="order.payment" :href="route('admin.payments.show', { payment: order.payment.id })" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                    <Truck class="w-3.5 h-3.5" /> Gestionar
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="!orders.data.length" class="p-16 text-center text-slate-400">
                <Package class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p class="text-sm font-medium">No hay pedidos con estos filtros</p>
            </div>
        </div>

        <AdminPagination v-if="orders.data.length" :links="orders.links" class="mt-6" />
    </AppLayout>
</template>
