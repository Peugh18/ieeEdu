<script setup lang="ts">
import type { SummaryStats } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, ClipboardCheck, Sparkles, Star } from 'lucide-vue-next';

defineProps<{
    stats: SummaryStats;
}>();
</script>

<template>
    <div class="space-y-4">
        <h2 class="flex items-center gap-2 text-xl font-bold text-gray-900">
            <Sparkles class="h-5 w-5 text-primary" />
            Accesos rápidos
        </h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <Link
                :href="route('student.exams.index')"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-5 text-center transition-all duration-300 hover:border-purple-200 hover:shadow-md"
            >
                <div class="mb-3 rounded-2xl bg-purple-50 p-3 transition-transform group-hover:scale-110">
                    <ClipboardCheck class="h-6 w-6 text-purple-500" />
                </div>
                <h4 class="text-xs font-bold text-gray-800">Mis evaluaciones</h4>
                <p class="mt-1 text-[10px] text-gray-400">{{ stats.available_exams }} disponibles</p>
            </Link>

            <Link
                :href="route('student.live-classes.index')"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-5 text-center transition-all duration-300 hover:border-amber-200 hover:shadow-md"
            >
                <div class="mb-3 rounded-2xl bg-amber-50 p-3 transition-transform group-hover:scale-110">
                    <Calendar class="h-6 w-6 text-amber-500" />
                </div>
                <h4 class="text-xs font-bold text-gray-800">Clases en vivo</h4>
                <p class="mt-1 text-[10px] text-gray-400">{{ stats.upcoming_classes }} próximas</p>
            </Link>

            <Link
                :href="route('student.explore.publications')"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-5 text-center transition-all duration-300 hover:border-blue-200 hover:shadow-md"
            >
                <div class="mb-3 rounded-2xl bg-blue-50 p-3 transition-transform group-hover:scale-110">
                    <BookOpen class="h-6 w-6 text-blue-500" />
                </div>
                <h4 class="text-xs font-bold text-gray-800">Publicaciones</h4>
                <p class="mt-1 text-[10px] text-gray-400">Libros y artículos</p>
            </Link>

            <Link
                :href="route('student.explore.masterclasses')"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-5 text-center transition-all duration-300 hover:border-rose-200 hover:shadow-md"
            >
                <div class="mb-3 rounded-2xl bg-rose-50 p-3 transition-transform group-hover:scale-110">
                    <Star class="h-6 w-6 text-rose-500" />
                </div>
                <h4 class="text-xs font-bold text-gray-800">Masterclasses</h4>
                <p class="mt-1 text-[10px] text-gray-400">Sesiones especiales</p>
            </Link>
        </div>
    </div>
</template>
