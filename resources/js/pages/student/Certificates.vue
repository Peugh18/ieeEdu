<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Award, Download, Eye, ShieldCheck, Printer, Search, LayoutGrid, List } from 'lucide-vue-next';

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
    return props.certificates.filter(cert => 
        (cert.title && cert.title.toLowerCase().includes(query)) || 
        (cert.course_title && cert.course_title.toLowerCase().includes(query))
    );
});
</script>

<template>
    <Head title="Mis Certificados - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background text-on-background flex justify-center overflow-x-hidden">
            
            <div class="w-full max-w-7xl p-4 md:p-12 space-y-8 md:space-y-12">
                <!-- Academic Header -->
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-10">
                    <div class="space-y-2 md:space-y-4">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 border border-primary/10 rounded-full">
                            <div class="w-2 h-2 rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Credenciales Institucionales</span>
                        </div>
                        <h1 class="text-2xl md:text-5xl font-serif font-bold italic tracking-tight text-on-background">Honores y Certificaciones</h1>
                        <p class="hidden md:block text-on-surface-variant font-serif italic text-lg max-w-2xl leading-relaxed">Su trayectoria de excelencia académica debidamente acreditada y validada por nuestra institución.</p>
                    </div>
                    
                    <!-- Search Input and View Toggle -->
                    <div v-if="certificates.length > 0" class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto">
                        <div class="relative w-full md:w-80">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-on-surface-variant/40 focus-within:text-primary transition-colors">
                                <Search class="w-4 h-4" />
                            </div>
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Buscar certificación..."
                                class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-outline-variant/30 dark:border-slate-800 rounded-2xl text-xs font-medium text-on-background placeholder-on-surface-variant/40 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 shadow-sm transition-all"
                            />
                        </div>

                        <!-- Grid/List Toggle -->
                        <div class="flex items-center bg-slate-100 dark:bg-slate-800/50 p-1 rounded-xl border border-slate-200/20">
                            <button 
                                @click="viewMode = 'grid'" 
                                class="p-2 rounded-lg transition-all"
                                :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                            >
                                <LayoutGrid class="w-4 h-4" />
                            </button>
                            <button 
                                @click="viewMode = 'list'" 
                                class="p-2 rounded-lg transition-all"
                                :class="viewMode === 'list' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                            >
                                <List class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </header>

                <div v-if="certificates.length > 0" class="space-y-6">
                    <template v-if="filteredCertificates.length > 0">
                        <!-- Grid View -->
                        <div v-if="viewMode === 'grid'" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            <article v-for="cert in filteredCertificates" :key="cert.id" 
                                class="group bg-white dark:bg-slate-900 rounded-[2.5rem] border border-outline-variant/20 dark:border-slate-800/80 shadow-sm hover:shadow-2xl hover:shadow-primary/10 dark:hover:shadow-primary/5 transition-all duration-700 overflow-hidden flex flex-col relative hover:-translate-y-2"
                            >
                                <!-- Premium framing -->
                                <div class="relative aspect-[16/11] bg-background p-2 overflow-hidden flex items-center justify-center border-b border-outline-variant/10 dark:border-slate-800/50">
                                    <!-- Verification Overlay (desktop hover only) -->
                                    <div class="hidden md:flex absolute inset-0 bg-primary/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-700 items-center justify-center gap-6 z-30">
                                        <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'" target="_blank" v-if="cert.download_url" class="w-14 h-14 bg-white text-primary rounded-2xl shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all"><Eye class="w-6 h-6 shrink-0" /></a>
                                        <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'" download v-if="cert.download_url" class="w-14 h-14 bg-[#D4AF37] text-white rounded-2xl shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all"><Download class="w-6 h-6 shrink-0" /></a>
                                    </div>

                                    <div class="w-full h-full relative z-10 group-hover:scale-105 group-hover:rotate-1 transition-transform duration-1000 p-4">
                                        <img :src="cert.image" :alt="cert.title" class="w-full h-full object-contain rounded-xl shadow-2xl border border-outline-variant/10 dark:border-slate-800/50" />
                                    </div>

                                    <!-- Decorative institutional seal watermark -->
                                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] group-hover:scale-150 transition-transform duration-[transition-duration:3s]">
                                        <Award class="w-64 h-64 text-primary" />
                                    </div>

                                    <div class="absolute top-6 right-6 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl p-2.5 rounded-2xl border border-white/20 dark:border-slate-700/50 shadow-xl z-20">
                                       <ShieldCheck class="w-5 h-5 text-[#D4AF37]" />
                                    </div>
                                </div>

                                <div class="p-6 flex flex-col gap-4 flex-1 bg-white dark:bg-slate-900">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37]"></div>
                                            <span class="text-[9px] font-black text-outline-variant uppercase tracking-[0.3em] font-serif italic">Programa de Postgrado</span>
                                        </div>
                                        <h3 class="font-serif font-bold text-lg text-on-background italic leading-snug group-hover:text-primary transition-colors line-clamp-2" :title="cert.course_title">{{ cert.course_title }}</h3>
                                    </div>

                                    <div class="mt-auto pt-4 border-t border-outline-variant/10 flex flex-col gap-4">
                                        <div class="flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-2">
                                                <Award class="w-4 h-4 text-primary" />
                                                <span class="font-bold text-on-surface-variant italic">Acreditado</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400">Código: {{ cert.code }}</span>
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'" v-if="cert.download_url" target="_blank" class="flex-1 py-3 rounded-xl bg-background dark:bg-slate-800 border border-outline-variant/30 dark:border-slate-700 text-on-surface-variant text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white dark:hover:bg-slate-750 hover:text-primary hover:border-primary/30 transition-all flex items-center justify-center gap-1.5 italic active:scale-95">
                                                <Printer class="w-4 h-4" /> Ver
                                            </a>
                                            <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'" v-if="cert.download_url" download class="flex-1 py-3 rounded-xl bg-primary text-white text-[9px] font-black uppercase tracking-[0.2em] hover:opacity-90 transition-all flex items-center justify-center gap-1.5 italic shadow-md shadow-primary/15 active:scale-95">
                                                <Download class="w-4 h-4" /> Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- List View -->
                        <div v-else class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-outline-variant/20 dark:border-slate-800 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="overflow-x-auto custom-scrollbar">
                                <table class="w-full text-left border-collapse min-w-[700px]">
                                    <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800">
                                        <tr>
                                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Certificación</th>
                                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-center">Código</th>
                                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-center">Fecha de Emisión</th>
                                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                        <tr v-for="cert in filteredCertificates" :key="cert.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300">
                                            <td class="px-8 py-5 relative">
                                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-8 bg-slate-50 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 rounded-lg overflow-hidden shadow-inner flex-shrink-0 relative">
                                                        <img :src="cert.image" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex flex-col min-w-0">
                                                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight group-hover:text-primary transition-colors line-clamp-1" :title="cert.course_title">{{ cert.course_title }}</h4>
                                                        <span class="text-[9px] font-bold uppercase tracking-widest text-[#9ca3af] dark:text-slate-500 mt-1">Programa de Postgrado</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700 text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400">
                                                    {{ cert.code }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                    {{ cert.issue_date }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                                    <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'" target="_blank" v-if="cert.download_url" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:border-primary hover:text-primary hover:bg-white dark:hover:bg-slate-900 transition-all duration-300" title="Visualizar">
                                                        <Printer class="h-4 w-4" />
                                                    </a>
                                                    <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'" download v-if="cert.download_url" class="w-10 h-10 rounded-xl bg-primary border border-transparent flex items-center justify-center text-white hover:opacity-90 transition-all duration-300" title="Descargar">
                                                        <Download class="h-4 w-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <!-- Search Empty State -->
                    <div v-else class="py-20 flex flex-col items-center text-center bg-white dark:bg-slate-900 rounded-[4rem] border border-dashed border-outline-variant/30 dark:border-slate-800 shadow-inner group">
                         <div class="w-16 h-16 bg-background dark:bg-slate-800 rounded-2xl border border-outline-variant/20 dark:border-slate-700/50 flex items-center justify-center mb-6">
                            <Search class="w-6 h-6 text-outline-variant dark:text-slate-500" />
                         </div>
                         <h3 class="text-xl font-serif font-bold italic text-on-background mb-2">Sin coincidencias</h3>
                         <p class="text-on-surface-variant font-serif italic text-xs leading-relaxed max-w-xs">No encontramos certificaciones que coincidan con "{{ searchQuery }}".</p>
                    </div>
                </div>

                <!-- Cinematic Empty State -->
                <div v-else class="py-32 flex flex-col items-center text-center bg-white dark:bg-slate-900 rounded-[4rem] border border-dashed border-outline-variant/30 dark:border-slate-800 shadow-inner group">
                     <div class="w-24 h-24 bg-background dark:bg-slate-800 rounded-[2rem] border border-outline-variant/20 dark:border-slate-700/50 flex items-center justify-center mb-10 group-hover:bg-primary/5 transition-colors">
                        <Award class="w-10 h-10 text-outline-variant dark:text-slate-500" />
                     </div>
                     <h2 class="text-3xl font-serif font-bold italic text-on-background mb-4">Expediente en formación</h2>
                     <p class="text-on-surface-variant font-serif italic text-base leading-relaxed max-w-sm mb-12">Continúe con sus cátedras magistrales y evaluaciones para formalizar sus competencias académicas.</p>
                     <Link :href="route('student.courses.index')" class="px-10 py-5 bg-primary text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl hover:bg-on-background transition-all shadow-2xl shadow-primary/20 active:scale-95 italic">Proseguir estudios</Link>
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
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.group:hover .group-hover\:rotate-1 {
    transform: scale(1.05) rotate(1deg);
}
</style>
