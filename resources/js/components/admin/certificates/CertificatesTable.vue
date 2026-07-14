<script setup lang="ts">
import { Activity, Download, FileText } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { PaginationLink } from '@/types/pagination';

interface CertificateItem {
    id: number;
    code: string;
    file_path: string | null;
    issue_date: string;
    user: { id?: number; name: string; email: string };
    course: { id?: number; title: string } | null;
}

defineProps<{
    certificates: CertificateItem[];
    total: number;
    paginationLinks: PaginationLink[];
}>();

const initials = (n: string) =>
    n.split(' ').slice(0, 2).map((w) => w[0]).join('').toUpperCase();

const avatarColors = [
    'bg-indigo-50 text-indigo-700',
    'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700',
    'bg-amber-50 text-amber-700',
    'bg-rose-50 text-rose-700',
];
const aCls = (id: number) => avatarColors[id % avatarColors.length];
const fDate = (d: string) =>
    new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
</script>

<template>
    <div class="relative overflow-hidden rounded-[3rem] border border-outline-variant/10 bg-surface-container-lowest shadow-sm">
        <div class="custom-scrollbar overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="border-b border-outline-variant/10 bg-surface-container-low/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-on-surface-variant/60">Estudiante</th>
                        <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-on-surface-variant/60">Programa</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-on-surface-variant/60">Código</th>
                        <th class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-on-surface-variant/60">Fecha Emisión</th>
                        <th class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-on-surface-variant/60">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <tr v-if="!certificates.length">
                        <td colspan="5" class="py-24 text-center">
                            <Activity class="mx-auto mb-4 h-12 w-12 text-on-surface-variant/20" />
                            <p class="font-medium text-on-surface-variant/50">No se han detectado certificados con los criterios seleccionados.</p>
                        </td>
                    </tr>
                    <tr
                        v-for="c in certificates"
                        :key="c.id"
                        class="group transition-all duration-300 hover:bg-surface-container-low/50"
                    >
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-xs font-bold ${aCls(c.user.id || 0)} shadow-sm`">
                                    {{ initials(c.user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 text-sm font-bold leading-tight text-on-surface transition-colors group-hover:text-primary">
                                        {{ c.user.name }}
                                    </p>
                                    <p class="mt-0.5 line-clamp-1 text-[10px] font-medium tracking-wider text-on-surface-variant/60">
                                        {{ c.user.email }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-bold text-on-surface">{{ c.course?.title ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-surface-container px-3 py-1.5 text-[10px] font-bold text-on-surface-variant">
                                <FileText class="h-3 w-3" />
                                {{ c.code }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-on-surface-variant">{{ fDate(c.issue_date) }}</span>
                                <span class="mt-0.5 text-[9px] font-medium uppercase tracking-widest text-on-surface-variant/40">ID: #{{ c.id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-1.5 opacity-40 transition-opacity group-hover:opacity-100">
                                <a
                                    v-if="c.id"
                                    :href="'/admin/certificates/' + c.id + '/download?action=stream'"
                                    target="_blank"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface text-on-surface-variant transition-all hover:border-primary hover:bg-surface-container hover:text-primary"
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

    <!-- Pagination -->
    <div v-if="paginationLinks.length > 1" class="flex flex-col items-center justify-between gap-4 px-2 md:flex-row">
        <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant/50">
            Mostrando {{ certificates.length }} de {{ total }} certificados
        </p>
        <div class="flex items-center gap-1.5">
            <Link
                v-for="link in paginationLinks"
                :key="link.label"
                :href="link.url ?? '#'"
                class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                :class="
                    link.active
                        ? 'bg-on-surface text-surface shadow-lg'
                        : 'border border-outline-variant/20 bg-surface text-on-surface-variant hover:border-outline-variant hover:text-on-surface'
                "
                v-html="link.label"
            />
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.2); border-radius: 4px; }
</style>