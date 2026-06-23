<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginationLink } from '@/types/pagination';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Activity, Award, Download, FileText, Filter, Search } from 'lucide-vue-next';

interface CertificateItem {
    id: number;
    code: string;
    file_path: string | null;
    issue_date: string;
    user: { id?: number; name: string; email: string };
    course: { id?: number; title: string } | null;
}

const props = defineProps<{
    certificates: { data: CertificateItem[]; links: PaginationLink[]; total: number; per_page: number };
    filters: { search?: string; per_page?: string };
    stats: { total: number };
}>();

import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';

// ─── Filters ────────────────────────────────────────────────────
const filterForm = useForm({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(
        route('admin.certificates.index'),
        {
            search: filterForm.search || undefined,
            per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterForm, applyFilters);

// ─── Helpers ─────────────────────────────────────────────────────
const initials = (n: string) =>
    n
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
const avatarColors = [
    'bg-indigo-50 text-indigo-700',
    'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700',
    'bg-amber-50 text-amber-700',
    'bg-rose-50 text-rose-700',
];
const aCls = (id: number) => avatarColors[id % avatarColors.length];
const fDate = (d: string) => new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
const pgLinks = usePaginationLinks(props.certificates.links);
</script>

<template>
    <Head title="Gestión de Certificados - iieEdu Admin" />
    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-10 px-4 py-8">
            <!-- ── Header ── -->
            <AdminPageHeader title="Certificados " titleAccent="emitidos" subtitle="Historial de certificados generados para estudiantes." compact />

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div
                    class="group relative cursor-pointer overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:border-slate-200 hover:shadow-md"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 transition-colors group-hover:text-slate-600"
                                >Total Emitidos</span
                            >
                            <Award class="h-4 w-4 text-slate-300 transition-colors group-hover:text-slate-500" />
                        </div>
                        <p class="text-4xl font-black tracking-tight text-emerald-600">{{ props.stats.total }}</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 h-20 w-20 opacity-[0.03] transition-opacity group-hover:opacity-[0.08]">
                        <Award class="h-full w-full" />
                    </div>
                </div>
            </div>

            <!-- ── Filter Bar ── -->
            <div class="flex flex-col items-center gap-4 rounded-[2.5rem] border border-slate-100 bg-slate-50 p-4 lg:flex-row">
                <div class="relative w-full flex-1 lg:w-auto">
                    <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="filterForm.search"
                        placeholder="Buscar por estudiante, curso o código..."
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-6 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                    />
                </div>
                <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
                    <div class="relative min-w-[160px] flex-1 lg:flex-none">
                        <Filter class="absolute left-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                        <select
                            v-model="filterForm.per_page"
                            class="h-14 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-white pl-10 pr-10 text-xs font-bold text-slate-600 outline-none"
                        >
                            <option value="10">10 por página</option>
                            <option value="20">20 por página</option>
                            <option value="50">50 por página</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Table View ── -->
            <div class="relative overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-sm">
                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="border-b border-slate-100 bg-slate-50/80">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante</th>
                                <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Programa</th>
                                <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Código</th>
                                <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">
                                    Fecha Emisión
                                </th>
                                <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="!props.certificates.data.length">
                                <td colspan="5" class="py-24 text-center">
                                    <Activity class="mx-auto mb-4 h-12 w-12 text-slate-200" />
                                    <p class="font-medium text-slate-400">No se han detectado certificados con los criterios seleccionados.</p>
                                </td>
                            </tr>
                            <tr v-for="c in props.certificates.data" :key="c.id" class="group transition-all duration-300 hover:bg-slate-50/50">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${aCls(c.user.id || 0)} shadow-sm`"
                                        >
                                            {{ initials(c.user.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary"
                                            >
                                                {{ c.user.name }}
                                            </p>
                                            <p class="mt-0.5 line-clamp-1 text-[10px] font-medium tracking-wider text-slate-400">
                                                {{ c.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-700">{{ c.course?.title ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600"
                                    >
                                        <FileText class="h-3 w-3" />
                                        {{ c.code }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-slate-500">{{ fDate(c.issue_date) }}</span>
                                        <span class="mt-0.5 text-[9px] font-medium uppercase tracking-widest text-slate-300">ID: #{{ c.id }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-end gap-1.5 opacity-40 transition-opacity group-hover:opacity-100">
                                        <a
                                            v-if="c.id"
                                            :href="'/admin/certificates/' + c.id + '/download?action=stream'"
                                            target="_blank"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-primary hover:bg-slate-50 hover:text-primary"
                                            title="Ver Certificado"
                                        >
                                            <Download class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Pagination ── -->
            <div v-if="pgLinks.length > 1" class="flex flex-col items-center justify-between gap-4 px-2 md:flex-row">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    Mostrando {{ props.certificates.data.length }} de {{ props.stats.total }} certificados
                </p>
                <div class="flex items-center gap-1.5">
                    <Link
                        v-for="link in pgLinks"
                        :key="link.label"
                        :href="link.url ?? '#'"
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
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
