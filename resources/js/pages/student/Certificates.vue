<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Award, Download, Eye, LayoutGrid, List, Printer, Search, ShieldCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Certificate {
    id: number;
    title: string;
    course_title: string;
    issue_date: string;
    image: string;
    code: string;
    is_available: boolean;
    download_url?: string;
}

const props = defineProps<{
    certificates: Certificate[];
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Certificados', href: '/student/certificates' },
];

const searchQuery = ref('');
const viewMode = ref<'grid' | 'list'>('grid');

const filteredCertificates = computed(() => {
    if (!searchQuery.value) return props.certificates;
    const query = searchQuery.value.toLowerCase().trim();
    return props.certificates.filter(
        (cert) => (cert.title && cert.title.toLowerCase().includes(query)) || (cert.course_title && cert.course_title.toLowerCase().includes(query)),
    );
});
</script>

<template>
    <Head title="Mis Certificados - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-screen justify-center overflow-x-hidden bg-background text-on-background">
            <div class="w-full max-w-7xl px-4 py-6 md:px-8 md:py-8 space-y-6 md:space-y-8">
                <!-- Clean Modern Header -->
                <header class="mb-6 flex flex-col justify-between gap-6 md:flex-row md:items-start">
                    <div class="flex-1">
                        <div class="mb-2 flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                <Award class="h-4 w-4 text-primary" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Credenciales Institucionales</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-background md:text-3xl">Mis Certificados</h1>
                        <p class="mt-1 max-w-2xl text-sm text-on-surface-variant/70">
                            Tu trayectoria de excelencia académica debidamente acreditada y validada por nuestra institución.
                        </p>
                    </div>

                    <!-- Search Input and View Toggle -->
                    <div v-if="certificates.length > 0" class="flex w-full shrink-0 flex-col items-center gap-4 sm:flex-row md:w-auto mt-2 md:mt-0">
                        <div class="group relative w-full sm:w-64">
                            <Search class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-outline-variant transition-colors group-focus-within:text-primary" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Buscar certificado..."
                                class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest py-2.5 pl-10 pr-4 text-sm text-on-background transition-all placeholder:text-on-surface-variant/50 focus:border-primary/40 focus:ring-4 focus:ring-primary/10"
                            />
                        </div>

                        <!-- Grid/List Toggle -->
                        <div class="flex items-center rounded-xl border border-slate-200/20 bg-slate-100 p-1 dark:bg-slate-800/50">
                            <button
                                @click="viewMode = 'grid'"
                                class="rounded-lg p-2 transition-all"
                                :class="
                                    viewMode === 'grid' ? 'bg-white text-primary shadow-sm dark:bg-slate-900' : 'text-slate-400 hover:text-slate-600'
                                "
                            >
                                <LayoutGrid class="h-4 w-4" />
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                class="rounded-lg p-2 transition-all"
                                :class="
                                    viewMode === 'list' ? 'bg-white text-primary shadow-sm dark:bg-slate-900' : 'text-slate-400 hover:text-slate-600'
                                "
                            >
                                <List class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </header>

                <div v-if="certificates.length > 0" class="space-y-6">
                    <template v-if="filteredCertificates.length > 0">
                        <!-- Grid View -->
                        <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            <article
                                v-for="cert in filteredCertificates"
                                :key="cert.id"
                                class="group relative flex flex-col overflow-hidden rounded-[2.5rem] border border-outline-variant/20 bg-white shadow-sm transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/10 dark:border-slate-800/80 dark:bg-slate-900 dark:hover:shadow-primary/5"
                            >
                                <!-- Premium framing -->
                                <div
                                    class="relative flex aspect-[16/11] items-center justify-center overflow-hidden border-b border-outline-variant/10 bg-background p-2 dark:border-slate-800/50"
                                >
                                    <!-- Verification Overlay (desktop hover only) -->
                                    <div
                                        class="absolute inset-0 z-30 hidden items-center justify-center gap-6 bg-primary/80 opacity-0 backdrop-blur-sm transition-all duration-700 group-hover:opacity-100 md:flex"
                                    >
                                        <a
                                            :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'"
                                            target="_blank"
                                            v-if="cert.download_url"
                                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-primary shadow-2xl transition-all hover:scale-110 active:scale-95"
                                            ><Eye class="h-6 w-6 shrink-0"
                                        /></a>
                                        <a
                                            :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'"
                                            download
                                            v-if="cert.download_url"
                                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#D4AF37] text-white shadow-2xl transition-all hover:scale-110 active:scale-95"
                                            ><Download class="h-6 w-6 shrink-0"
                                        /></a>
                                    </div>

                                    <div
                                        class="relative z-10 h-full w-full p-4 transition-transform duration-1000 group-hover:rotate-1 group-hover:scale-105"
                                    >
                                        <img
                                            :src="cert.image"
                                            :alt="cert.title"
                                            class="h-full w-full rounded-xl border border-outline-variant/10 object-contain shadow-2xl dark:border-slate-800/50"
                                        />
                                    </div>

                                    <!-- Decorative institutional seal watermark -->
                                    <div
                                        class="duration-[transition-duration:3s] absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] transition-transform group-hover:scale-150"
                                    >
                                        <Award class="h-64 w-64 text-primary" />
                                    </div>

                                    <div
                                        class="absolute right-6 top-6 z-20 rounded-2xl border border-white/20 bg-white/90 p-2.5 shadow-xl backdrop-blur-xl dark:border-slate-700/50 dark:bg-slate-800/90"
                                    >
                                        <ShieldCheck class="h-5 w-5 text-[#D4AF37]" />
                                    </div>
                                </div>

                                <div class="flex flex-1 flex-col gap-4 bg-white p-6 dark:bg-slate-900">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#D4AF37]"></div>
                                            <span class="font-serif text-[9px] font-black uppercase italic tracking-[0.3em] text-outline-variant"
                                                >Programa de Postgrado</span
                                            >
                                        </div>
                                        <h3
                                            class="line-clamp-2 font-serif text-lg font-bold italic leading-snug text-on-background transition-colors group-hover:text-primary"
                                            :title="cert.course_title"
                                        >
                                            {{ cert.course_title }}
                                        </h3>
                                    </div>

                                    <div class="mt-auto flex flex-col gap-4 border-t border-outline-variant/10 pt-4">
                                        <div class="flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-2">
                                                <Award class="h-4 w-4 text-primary" />
                                                <span class="font-bold italic text-on-surface-variant">Acreditado</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400">Código: {{ cert.code }}</span>
                                        </div>

                                        <div class="flex gap-2">
                                            <a
                                                :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'"
                                                v-if="cert.download_url"
                                                target="_blank"
                                                class="dark:hover:bg-slate-750 flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-background py-3 text-[9px] font-black uppercase italic tracking-[0.2em] text-on-surface-variant transition-all hover:border-primary/30 hover:bg-white hover:text-primary active:scale-95 dark:border-slate-700 dark:bg-slate-800"
                                            >
                                                <Printer class="h-4 w-4" /> Ver
                                            </a>
                                            <a
                                                :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'"
                                                v-if="cert.download_url"
                                                download
                                                class="flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-primary py-3 text-[9px] font-black uppercase italic tracking-[0.2em] text-white shadow-md shadow-primary/15 transition-all hover:opacity-90 active:scale-95"
                                            >
                                                <Download class="h-4 w-4" /> Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- List View (Responsive) -->
                        <div v-else class="duration-500 animate-in fade-in slide-in-from-bottom-4">
                            <!-- Mobile List (Stacked Cards) -->
                            <div class="space-y-4 md:hidden">
                                <div
                                    v-for="cert in filteredCertificates"
                                    :key="cert.id"
                                    class="space-y-4 rounded-2xl border border-outline-variant/15 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="relative h-10 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-slate-200/50 bg-slate-50 dark:border-slate-700/50 dark:bg-slate-800"
                                        >
                                            <img :src="cert.image" :alt="cert.course_title" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="flex min-w-0 flex-col">
                                            <span class="mb-0.5 text-[9px] font-bold uppercase tracking-widest text-[#D4AF37]"
                                                >Programa de Postgrado</span
                                            >
                                            <h4
                                                class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 dark:text-slate-100"
                                                :title="cert.course_title"
                                            >
                                                {{ cert.course_title }}
                                            </h4>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 text-xs font-medium text-on-surface-variant/70 dark:border-slate-800/60"
                                    >
                                        <div class="flex flex-col">
                                            <span class="text-[9px] uppercase tracking-wider text-slate-400 dark:text-slate-500">Código</span>
                                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ cert.code }}</span>
                                        </div>
                                        <div class="flex flex-col text-right">
                                            <span class="text-[9px] uppercase tracking-wider text-slate-400 dark:text-slate-500">Emisión</span>
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ cert.issue_date }}</span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 pt-2">
                                        <a
                                            :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'"
                                            v-if="cert.download_url"
                                            target="_blank"
                                            class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-background py-3 text-[10px] font-black uppercase tracking-[0.15em] text-on-surface-variant transition-all hover:bg-white hover:text-primary active:scale-95 dark:border-slate-700 dark:bg-slate-800"
                                        >
                                            <Printer class="h-4 w-4" /> Ver
                                        </a>
                                        <a
                                            :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'"
                                            v-if="cert.download_url"
                                            download
                                            class="flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-primary py-3 text-[10px] font-black uppercase tracking-[0.15em] text-white shadow-md shadow-primary/15 transition-all hover:opacity-90 active:scale-95"
                                        >
                                            <Download class="h-4 w-4" /> Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop List (Table Layout) -->
                            <div
                                class="hidden overflow-hidden rounded-[2.5rem] border border-outline-variant/20 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 md:block"
                            >
                                <div class="custom-scrollbar overflow-x-auto">
                                    <table class="w-full min-w-[700px] border-collapse text-left">
                                        <thead class="border-b border-slate-100 bg-slate-50/80 dark:border-slate-800 dark:bg-slate-800/80">
                                            <tr>
                                                <th
                                                    class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                                >
                                                    Certificación
                                                </th>
                                                <th
                                                    class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                                >
                                                    Código
                                                </th>
                                                <th
                                                    class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                                >
                                                    Fecha de Emisión
                                                </th>
                                                <th
                                                    class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                                >
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                            <tr
                                                v-for="cert in filteredCertificates"
                                                :key="cert.id"
                                                class="group transition-all duration-300 hover:bg-slate-50/50 dark:hover:bg-slate-800/50"
                                            >
                                                <td class="relative px-8 py-5">
                                                    <div
                                                        class="absolute left-0 top-1/2 h-0 w-1 -translate-y-1/2 rounded-r-full bg-primary transition-all duration-500 group-hover:h-12"
                                                    ></div>
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="relative h-8 w-12 flex-shrink-0 overflow-hidden rounded-lg border border-slate-200/50 bg-slate-50 shadow-inner dark:border-slate-700/50 dark:bg-slate-800"
                                                        >
                                                            <img :src="cert.image" class="h-full w-full object-cover" />
                                                        </div>
                                                        <div class="flex min-w-0 flex-col">
                                                            <h4
                                                                class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary dark:text-slate-100"
                                                                :title="cert.course_title"
                                                            >
                                                                {{ cert.course_title }}
                                                            </h4>
                                                            <span
                                                                class="mt-1 text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] dark:text-slate-500"
                                                                >Programa de Postgrado</span
                                                            >
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 text-center">
                                                    <span
                                                        class="inline-flex items-center rounded-md border border-slate-200/60 bg-slate-50 px-2.5 py-1 font-mono text-[10px] font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-400"
                                                    >
                                                        {{ cert.code }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-5 text-center">
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                        {{ cert.issue_date }}
                                                    </span>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <div
                                                        class="flex items-center justify-end gap-1.5 opacity-80 transition-opacity group-hover:opacity-100"
                                                    >
                                                        <a
                                                            :href="
                                                                cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'
                                                            "
                                                            target="_blank"
                                                            v-if="cert.download_url"
                                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-400 transition-all duration-300 hover:border-primary hover:bg-white hover:text-primary dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500 dark:hover:bg-slate-900"
                                                            title="Visualizar"
                                                        >
                                                            <Printer class="h-4 w-4" />
                                                        </a>
                                                        <a
                                                            :href="
                                                                cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'
                                                            "
                                                            download
                                                            v-if="cert.download_url"
                                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-transparent bg-primary text-white transition-all duration-300 hover:opacity-90"
                                                            title="Descargar"
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
                        </div>
                    </template>

                    <!-- Search Empty State -->
                    <div
                        v-else
                        class="group flex flex-col items-center rounded-[4rem] border border-dashed border-outline-variant/30 bg-white py-20 text-center shadow-inner dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div
                            class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-outline-variant/20 bg-background dark:border-slate-700/50 dark:bg-slate-800"
                        >
                            <Search class="h-6 w-6 text-outline-variant dark:text-slate-500" />
                        </div>
                        <h3 class="mb-2 font-serif text-xl font-bold italic text-on-background">Sin coincidencias</h3>
                        <p class="max-w-xs font-serif text-xs italic leading-relaxed text-on-surface-variant">
                            No encontramos certificaciones que coincidan con "{{ searchQuery }}".
                        </p>
                    </div>
                </div>

                <!-- Cinematic Empty State -->
                <div
                    v-else
                    class="group flex flex-col items-center rounded-[4rem] border border-dashed border-outline-variant/30 bg-white py-32 text-center shadow-inner dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="mb-10 flex h-24 w-24 items-center justify-center rounded-[2rem] border border-outline-variant/20 bg-background transition-colors group-hover:bg-primary/5 dark:border-slate-700/50 dark:bg-slate-800"
                    >
                        <Award class="h-10 w-10 text-outline-variant dark:text-slate-500" />
                    </div>
                    <h2 class="mb-4 font-serif text-3xl font-bold italic text-on-background">Expediente en formación</h2>
                    <p class="mb-12 max-w-sm font-serif text-base italic leading-relaxed text-on-surface-variant">
                        Continúe con sus cátedras magistrales y evaluaciones para formalizar sus competencias académicas.
                    </p>
                    <Link
                        :href="route('student.courses.index')"
                        class="rounded-2xl bg-primary px-10 py-5 text-[10px] font-black uppercase italic tracking-[0.3em] text-white shadow-2xl shadow-primary/20 transition-all hover:bg-on-background active:scale-95"
                        >Proseguir estudios</Link
                    >
                </div>
            </div>
        </div>
        <BottomNav active="certificates" />
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
    background: rgba(87, 87, 42, 0.08);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.15);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

article {
    animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group:hover .group-hover\:rotate-1 {
    transform: scale(1.05) rotate(1deg);
}
</style>
