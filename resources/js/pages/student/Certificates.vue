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
        <div class="p-6 max-w-7xl mx-auto space-y-12 pb-20">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-serif font-bold text-gray-900 flex items-center gap-3">
                        <Award class="w-8 h-8 text-[#57572A]" />
                        Mis Certificaciones
                    </h1>
                    <p class="text-gray-500 mt-1">Has completado programas con excelencia académica. Descarga y comparte tus logros.</p>
                </div>
            </header>

            <div v-if="certificates.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <article v-for="cert in certificates" :key="cert.id" 
                    class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl transition-all duration-500 flex flex-col"
                >
                    <div class="relative aspect-[16/11] bg-gray-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center gap-3 z-10">
                            <a :href="cert.download_url" target="_blank" v-if="cert.download_url" class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:scale-110 active:scale-95 transition-all"><Eye class="w-5 h-5" /></a>
                            <a :href="cert.download_url" download v-if="cert.download_url" class="p-3 bg-[#57572A] text-white rounded-full shadow-lg hover:scale-110 active:scale-95 transition-all outline outline-white/20"><Download class="w-5 h-5" /></a>
                        </div>

                        <img :src="cert.image" :alt="cert.title" class="w-full h-full object-contain rounded-lg shadow-sm border border-gray-100 group-hover:blur-[2px] transition-all duration-500" />
                        
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md p-2 rounded-xl border border-white/20 shadow-sm z-20">
                           <FileBadge2 class="w-5 h-5 text-[#D4AF37]" />
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1">
                        <div class="mb-4">
                            <span class="text-[10px] font-bold text-[#57572A] uppercase tracking-widest block mb-1">PROGRAMA ACADÉMICO</span>
                            <h3 class="font-serif font-bold text-lg text-gray-900 leading-snug truncate" :title="cert.course_title">{{ cert.course_title }}</h3>
                        </div>

                        <div class="mt-auto space-y-4">
                            <div class="flex items-center justify-between text-xs font-serif text-gray-400">
                                <div class="flex items-center gap-1.5"><ShieldCheck class="w-4 h-4 text-emerald-500" /> <span>Verificado</span></div>
                                <span>{{ cert.issue_date }}</span>
                            </div>
                            
                            <div class="flex gap-2">
                                <a :href="cert.download_url" v-if="cert.download_url" target="_blank" class="flex-1 py-2.5 rounded-xl bg-gray-50 text-gray-600 text-[11px] font-bold uppercase tracking-widest hover:bg-gray-100 transition-colors flex items-center justify-center gap-2">
                                    <Printer class="w-4 h-4" /> Imprimir
                                </a>
                                <a :href="cert.download_url" v-if="cert.download_url" download class="flex-1 py-2.5 rounded-xl bg-[#57572A] text-white text-[11px] font-bold uppercase tracking-widest hover:bg-[#4a4a24] transition-colors flex items-center justify-center gap-2">
                                    <Download class="w-4 h-4" /> Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Empty state -->
             <div v-else class="py-24 px-6 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                 <div class="inline-flex p-5 bg-white rounded-3xl shadow-sm mb-6">
                    <Award class="w-10 h-10 text-gray-300" />
                 </div>
                 <h2 class="text-2xl font-serif font-bold text-gray-900 mb-2">Aún no tienes certificados</h2>
                 <p class="text-gray-500 max-w-sm mx-auto mb-8 font-serif leading-relaxed">Completa tus cursos activos y aprueba tus exámenes para obtener tus certificaciones profesionales con validez académica.</p>
                 <Link :href="route('student.courses.index')" class="px-8 py-3 bg-[#57572A] text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-[#4a4a24] transition-all shadow-lg active:scale-95">Ir a mis cursos</Link>
             </div>
        </div>
    </AppLayout>
</template>
