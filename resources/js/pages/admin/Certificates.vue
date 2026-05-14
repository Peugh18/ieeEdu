<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Search, Award, Clock, FileText, Download, CheckCircle2, ChevronRight, Filter, Activity
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

interface CertificateItem {
    id: number;
    code: string;
    file_path: string | null;
    issue_date: string;
    user: { id?: number; name: string; email: string };
    course: { id?: number; title: string } | null;
}

const props = defineProps<{
    certificates: { data: CertificateItem[]; links: any[]; total: number; per_page: number };
    filters: { search?: string; per_page?: string };
    stats: { total: number };
}>();

// ─── Filters ────────────────────────────────────────────────────
const searchInput  = ref(props.filters.search || '');
const perPage      = ref(props.filters.per_page || '20');

function applyFilters() {
    router.get(route('admin.certificates.index'), {
        search:   searchInput.value || undefined,
        per_page: perPage.value !== '20' ? perPage.value : undefined,
    }, { preserveState: true, replace: true });
}

watch([perPage], applyFilters);
let searchTimer: any;
watch(searchInput, () => { clearTimeout(searchTimer); searchTimer = setTimeout(applyFilters, 400); });

// ─── Helpers ─────────────────────────────────────────────────────
const initials = (n: string) => n.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
const avatarColors = ['bg-indigo-50 text-indigo-700','bg-blue-50 text-blue-700','bg-emerald-50 text-emerald-700','bg-amber-50 text-amber-700','bg-rose-50 text-rose-700'];
const aCls     = (id: number) => avatarColors[id % avatarColors.length];
const fDate    = (d: string) => new Date(d).toLocaleDateString('es-PE', { day:'2-digit', month:'short', year:'numeric' });
const pgLinks  = computed(() => props.certificates.links?.filter((l: any) => l.url) ?? []);
</script>

<template>
    <Head title="Gestión de Certificados - iieEdu Admin" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <!-- ── Header ── -->
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span>Académico</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900">Historial de Certificados</span>
                    </div>
                    <h1 class="font-serif text-5xl text-slate-900 leading-tight">Control de <span class="italic">Certificados</span></h1>
                </div>
            </div>

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="group relative cursor-pointer overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 group-hover:text-slate-600 transition-colors">Total Emitidos</span>
                            <Award class="h-4 w-4 text-slate-300 group-hover:text-slate-500 transition-colors" />
                        </div>
                        <p class="text-4xl font-black tracking-tight text-emerald-600">{{ props.stats.total }}</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <Award class="w-full h-full" />
                    </div>
                </div>
            </div>

            <!-- ── Filter Bar ── -->
            <div class="bg-slate-50 rounded-[2.5rem] p-4 border border-slate-100 flex flex-col lg:flex-row items-center gap-4">
                <div class="relative flex-1 w-full lg:w-auto">
                    <Search class="absolute left-5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input v-model="searchInput" placeholder="Buscar por estudiante, curso o código..." class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all" />
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="relative flex-1 lg:flex-none min-w-[160px]">
                        <Filter class="absolute left-4 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <select v-model="perPage" class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-10 pr-10 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer">
                            <option value="10">10 por página</option>
                            <option value="20">20 por página</option>
                            <option value="50">50 por página</option>
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
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Estudiante</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Programa</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Código</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Fecha Emisión</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="!props.certificates.data.length">
                            <td colspan="5" class="py-24 text-center">
                                <Activity class="w-12 h-12 text-slate-200 mx-auto mb-4" />
                                <p class="text-slate-400 font-medium">No se han detectado certificados con los criterios seleccionados.</p>
                            </td>
                        </tr>
                        <tr v-for="c in props.certificates.data" :key="c.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${aCls(c.user.id || 0)} shadow-sm`">
                                        {{ initials(c.user.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-1">{{ c.user.name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 tracking-wider line-clamp-1">{{ c.user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-700">{{ c.course?.title ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">
                                    <FileText class="h-3 w-3" />
                                    {{ c.code }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-slate-500">{{ fDate(c.issue_date) }}</span>
                                    <span class="text-[9px] text-slate-300 font-medium uppercase tracking-widest mt-0.5">ID: #{{ c.id }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                    <a v-if="c.id" :href="'/admin/certificates/' + c.id + '/download?action=stream'" target="_blank" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 transition-all" title="Ver Certificado">
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
            <div v-if="pgLinks.length > 1" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mostrando {{ props.certificates.data.length }} de {{ props.stats.total }} certificados</p>
                <div class="flex items-center gap-1.5">
                    <Link v-for="link in pgLinks" :key="link.label" :href="link.url"
                        class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600'"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <BottomNav />
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
