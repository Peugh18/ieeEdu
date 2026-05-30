<script setup lang="ts">
import { Video, BookOpen } from 'lucide-vue-next';

interface InventoryStats {
    publishedCourses: number;
    draftCourses: number;
    recordedCourses: number;
    liveCourses: number;
    totalBooks: number;
    availableBooks: number;
}

defineProps<{
    stats: InventoryStats;
}>();
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Cursos -->
        <article class="bg-primary-fixed/30 rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-primary/10 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-primary-fixed">
                    <Video class="w-5 h-5" />
                </div>
                <h3 class="font-serif font-bold text-gray-900">Inventario de Cursos</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ stats.publishedCourses }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Publicados</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-serif font-black text-gray-500 tabular-nums">{{ stats.draftCourses }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Borradores</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-serif font-black text-amber-600 tabular-nums">{{ stats.recordedCourses }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Grabados</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-serif font-black text-primary tabular-nums">{{ stats.liveCourses }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">En Vivo</p>
                </div>
            </div>
        </article>

        <!-- Libros -->
        <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <BookOpen class="w-5 h-5" />
                </div>
                <h3 class="font-serif font-bold text-gray-900">Publicaciones</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-2xl p-4 text-center border border-blue-100">
                    <p class="text-3xl font-serif font-black text-blue-600 tabular-nums">{{ stats.totalBooks }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-blue-400 mt-1">Total Libros</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                    <p class="text-3xl font-serif font-black text-gray-900 tabular-nums">{{ stats.availableBooks }}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Disponibles</p>
                </div>
            </div>
        </article>
    </div>
</template>
