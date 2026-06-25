<script setup lang="ts">
import { BookOpen, Video } from 'lucide-vue-next';

interface InventoryStats {
    publishedCourses: number;
    draftCourses: number;
    recordedCourses: number;
    liveCourses: number;
    totalBooks: number;
    availableBooks: number;
    totalBookDownloads: number;
    bookDownloadsThisMonth: number;
    bookWhatsappLeads: number;
    bookIncome: number;
    bookSalesCount: number;
}

defineProps<{
    stats: InventoryStats;
}>();
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Cursos -->
        <article class="space-y-6 rounded-[2rem] border border-primary/10 bg-primary-fixed/30 p-6 md:rounded-[2.5rem] md:p-9">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-fixed">
                    <Video class="h-5 w-5" />
                </div>
                <h3 class="font-serif font-bold text-gray-900">Inventario de Cursos</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-gray-900">{{ stats.publishedCourses }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Publicados</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-gray-500">{{ stats.draftCourses }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Borradores</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-amber-600">{{ stats.recordedCourses }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Grabados</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-primary">{{ stats.liveCourses }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">En Vivo</p>
                </div>
            </div>
        </article>

        <!-- Libros -->
        <article class="space-y-6 rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm md:rounded-[2.5rem] md:p-9">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <BookOpen class="h-5 w-5" />
                </div>
                <h3 class="font-serif font-bold text-gray-900">Publicaciones</h3>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-blue-600">{{ stats.totalBooks }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-blue-400">Total Libros</p>
                </div>
                <div class="rounded-2xl border border-violet-100 bg-violet-50 p-4 text-center">
                    <p class="font-serif text-xl font-black tabular-nums text-violet-700">S/ {{ Number(stats.bookIncome || 0).toLocaleString() }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-violet-500">Recaudado</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-slate-800">{{ stats.bookSalesCount ?? 0 }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">Ventas</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-emerald-600">{{ stats.totalBookDownloads }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-emerald-400">Descargas</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-amber-600">{{ stats.bookDownloadsThisMonth }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-amber-500">Descargas / mes</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-center">
                    <p class="font-serif text-2xl font-black tabular-nums text-gray-900">{{ stats.availableBooks }}</p>
                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Disponibles</p>
                </div>
            </div>
            <p v-if="stats.bookWhatsappLeads > 0" class="text-center text-[10px] font-medium text-gray-400">
                {{ stats.bookWhatsappLeads }} consultas de compra vía WhatsApp
            </p>
        </article>
    </div>
</template>
