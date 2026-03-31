<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface PaymentItem {
    id: number;
    user: { name: string; email: string };
    course: { title: string };
    status: string;
    amount: number;
    created_at: string;
}

const props = defineProps<{ payments: { data: PaymentItem[]; links: any[] }; filters: { status?: string; search?: string } }>();

const filterForm = useForm({ status: props.filters.status || '', search: props.filters.search || '' });

function applyFilters() {
    filterForm.get(route('admin.payments.index'), { preserveState: true, replace: true });
}

function approve(payment: PaymentItem) {
    useForm().patch(route('admin.payments.approve', payment.id), { preserveState: true });
}

function reject(payment: PaymentItem) {
    useForm().patch(route('admin.payments.reject', payment.id), { preserveState: true });
}

const list = computed(() => props.payments.data);
const paymentLinks = computed(() => props.payments.links.filter((link: any) => link.url));
</script>

<template>
    <Head title="Admin Pagos" />
    <AppLayout>
        <div class="mb-6 rounded-lg border p-4">
            <h2 class="text-lg font-semibold">Gestión de pagos</h2>
            <div class="mt-2 flex flex-wrap gap-2">
                <input v-model="filterForm.search" placeholder="Buscar usuario" class="w-full max-w-sm rounded border p-2" />
                <select v-model="filterForm.status" class="rounded border p-2">
                    <option value="">Todos</option>
                    <option value="pendiente">pendiente</option>
                    <option value="en_revision">en_revision</option>
                    <option value="aprobado">aprobado</option>
                    <option value="rechazado">rechazado</option>
                </select>
                <button class="rounded bg-slate-800 px-3 py-2 text-white" @click.prevent="applyFilters">Filtrar</button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border">
            <table class="w-full border-collapse">
                <thead class="bg-slate-100 text-left text-sm font-medium">
                    <tr>
                        <th class="px-3 py-2">Usuario</th>
                        <th class="px-3 py-2">Curso</th>
                        <th class="px-3 py-2">Monto</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in list" :key="payment.id" class="border-t">
                        <td class="px-3 py-2">{{ payment.user.name }} ({{ payment.user.email }})</td>
                        <td class="px-3 py-2">{{ payment.course.title }}</td>
                        <td class="px-3 py-2">S/ {{ payment.amount }}</td>
                        <td class="px-3 py-2">{{ payment.status }}</td>
                        <td class="px-3 py-2">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                        <td class="px-3 py-2 flex flex-wrap gap-2">
                            <Link :href="route('admin.payments.show', payment.id)" class="rounded bg-blue-500 px-2 py-1 text-white">Ver</Link>
                            <button v-if="payment.status !== 'aprobado'" class="rounded bg-emerald-600 px-2 py-1 text-white" @click="approve(payment)">Aprobar</button>
                            <button v-if="payment.status !== 'rechazado'" class="rounded bg-red-600 px-2 py-1 text-white" @click="reject(payment)">Rechazar</button>
                        </td>
                    </tr>
                    <tr v-if="!list.length"><td colspan="6" class="p-4 text-center">No hay pagos</td></tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <ul class="flex gap-2">
                <li v-for="link in paymentLinks" :key="link.label">
                    <a :href="link.url" class="rounded border px-3 py-1">{{ link.label }}</a>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
