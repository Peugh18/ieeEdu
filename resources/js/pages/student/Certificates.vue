<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Award, Download, Eye, Lock, FileBadge2, ShieldCheck, Share2, Printer } from 'lucide-vue-next';

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
</script>

<template>
    <Head title="Mis Certificados - IEE" />


    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background text-on-background flex justify-center overflow-x-hidden">
            
            <div class="w-full max-w-7xl p-8 md:p-12 space-y-16">
                <!-- Academic Header -->
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-10">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 border border-primary/10 rounded-full">
                            <div class="w-2 h-2 rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Credenciales Institucionales</span>
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-serif font-bold italic tracking-tight text-on-background">Honores y Certificaciones</h1>
                        <p class="text-on-surface-variant font-serif italic text-lg max-w-2xl leading-relaxed">Su trayectoria de excelencia académica debidamente acreditada y validada por nuestra institución.</p>
                    </div>
                </header>

                <div v-if="certificates.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <article v-for="cert in certificates" :key="cert.id" 
                        class="group bg-white rounded-[4rem] border border-outline-variant/20 shadow-sm hover:shadow-2xl hover:shadow-primary/10 transition-all duration-700 overflow-hidden flex flex-col relative"
                    >
                        <!-- Premium framing -->
                        <div class="relative aspect-[16/11] bg-background p-2 overflow-hidden flex items-center justify-center">
                            <!-- Verification Overlay -->
                            <div class="absolute inset-0 bg-primary/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-700 flex items-center justify-center gap-6 z-30">
                                <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'" target="_blank" v-if="cert.download_url" class="w-14 h-14 bg-white text-primary rounded-2xl shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all"><Eye class="w-6 h-6 shrink-0" /></a>
                                <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'" download v-if="cert.download_url" class="w-14 h-14 bg-[#D4AF37] text-white rounded-2xl shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all"><Download class="w-6 h-6 shrink-0" /></a>
                            </div>

                            <div class="w-full h-full relative z-10 group-hover:scale-105 group-hover:rotate-1 transition-transform duration-1000 p-6">
                                <img :src="cert.image" :alt="cert.title" class="w-full h-full object-contain rounded-xl shadow-2xl border border-outline-variant/10" />
                            </div>

                            <!-- Decorative institutional seal watermark -->
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] group-hover:scale-150 transition-transform duration-[3s]">
                                <Award class="w-64 h-64 text-primary" />
                            </div>

                            <div class="absolute top-8 right-8 bg-white/90 backdrop-blur-xl p-3 rounded-2xl border border-white/20 shadow-xl z-20">
                               <ShieldCheck class="w-5 h-5 text-[#D4AF37]" />
                            </div>
                        </div>

                        <div class="p-10 flex flex-col gap-6 flex-1 relative z-10 bg-white">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37]"></div>
                                    <span class="text-[9px] font-black text-outline-variant uppercase tracking-[0.3em] font-serif italic">Programa de Postgrado</span>
                                </div>
                                <h3 class="font-serif font-bold text-xl text-on-background italic leading-[1.3] group-hover:text-primary transition-colors line-clamp-2" :title="cert.course_title">{{ cert.course_title }}</h3>
                            </div>

                            <div class="mt-auto pt-6 border-t border-outline-variant/10 flex flex-col gap-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center border border-primary/10">
                                            <Award class="w-4 h-4 text-primary" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[8px] font-black text-outline-variant uppercase tracking-widest">Estado</span>
                                            <span class="text-[10px] font-bold text-on-surface-variant italic">Acreditado</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[8px] font-black text-outline-variant uppercase tracking-widest block">Fecha Emisión</span>
                                        <span class="text-[10px] font-bold text-on-background italic">{{ cert.issue_date }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex gap-4">
                                    <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=stream'" v-if="cert.download_url" target="_blank" class="flex-1 py-4 rounded-2xl bg-background border border-outline-variant/30 text-on-surface-variant text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-primary hover:border-primary/30 transition-all flex items-center justify-center gap-3 italic">
                                        <Printer class="w-4 h-4" /> Visualizar
                                    </a>
                                    <a :href="cert.download_url + (cert.download_url.includes('?') ? '&' : '?') + 'action=download'" v-if="cert.download_url" download class="flex-1 py-4 rounded-2xl bg-primary text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-on-background transition-all flex items-center justify-center gap-3 italic shadow-xl shadow-primary/20">
                                        <Download class="w-4 h-4" /> Expedir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Cinematic Empty State -->
                <div v-else class="py-32 flex flex-col items-center text-center bg-white rounded-[4rem] border border-dashed border-outline-variant/30 shadow-inner group">
                     <div class="w-24 h-24 bg-background rounded-[2rem] border border-outline-variant/20 flex items-center justify-center mb-10 group-hover:bg-primary/5 transition-colors">
                        <Award class="w-10 h-10 text-outline-variant" />
                     </div>
                     <h2 class="text-3xl font-serif font-bold italic text-on-background mb-4">Expediente en formación</h2>
                     <p class="text-on-surface-variant font-serif italic text-base leading-relaxed max-w-sm mb-12">Continúe con sus cátedras magistrales y evaluaciones para formalizar sus competencias académicas.</p>
                     <Link :href="route('student.courses.index')" class="px-10 py-5 bg-primary text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl hover:bg-on-background transition-all shadow-2xl shadow-primary/20 active:scale-95 italic">Proseguir estudios</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
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
