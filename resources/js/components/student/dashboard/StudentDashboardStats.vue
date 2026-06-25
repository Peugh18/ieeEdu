<script setup lang="ts">
import type { SummaryStats } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { Award, BookOpen, Calendar, ChevronRight, ClipboardCheck } from 'lucide-vue-next';

defineProps<{
    stats: SummaryStats;
}>();
</script>

<template>
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <Link
            :href="route('student.courses.index')"
            class="group flex items-center gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-50 md:gap-4 md:p-6"
        >
            <div class="flex-shrink-0 rounded-2xl bg-blue-50 p-3 transition-transform group-hover:scale-110">
                <BookOpen class="h-6 w-6 text-blue-500" />
            </div>
            <div>
                <p class="text-2xl font-bold leading-none text-gray-900 md:text-3xl">{{ stats.active_courses }}</p>
                <p class="mt-1 text-[10px] font-medium text-gray-400 md:text-xs">Cursos activos</p>
            </div>
            <ChevronRight class="ml-auto h-4 w-4 text-gray-200 transition-colors group-hover:text-blue-400" />
        </Link>

        <Link
            :href="route('student.live-classes.index')"
            class="group flex items-center gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-50 md:gap-4 md:p-6"
        >
            <div class="flex-shrink-0 rounded-2xl bg-amber-50 p-3 transition-transform group-hover:scale-110">
                <Calendar class="h-6 w-6 text-amber-500" />
            </div>
            <div>
                <p class="text-2xl font-bold leading-none text-gray-900 md:text-3xl">{{ stats.upcoming_classes }}</p>
                <p class="mt-1 text-[10px] font-medium text-gray-400 md:text-xs">Clases próximas</p>
            </div>
            <ChevronRight class="ml-auto h-4 w-4 text-gray-200 transition-colors group-hover:text-amber-400" />
        </Link>

        <Link
            :href="route('student.exams.index')"
            class="group flex items-center gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-50 md:gap-4 md:p-6"
        >
            <div class="flex-shrink-0 rounded-2xl bg-purple-50 p-3 transition-transform group-hover:scale-110">
                <ClipboardCheck class="h-6 w-6 text-purple-500" />
            </div>
            <div>
                <p class="text-2xl font-bold leading-none text-gray-900 md:text-3xl">{{ stats.available_exams }}</p>
                <p class="mt-1 text-[10px] font-medium text-gray-400 md:text-xs">Evaluaciones</p>
            </div>
            <ChevronRight class="ml-auto h-4 w-4 text-gray-200 transition-colors group-hover:text-purple-400" />
        </Link>

        <Link
            :href="route('student.certificates.index')"
            class="group flex items-center gap-3 rounded-2xl bg-gradient-to-br from-primary to-[#3d3d1e] p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/20 md:gap-4 md:p-6"
        >
            <div class="flex-shrink-0 rounded-2xl bg-white/10 p-3 transition-transform group-hover:scale-110">
                <Award class="h-6 w-6 text-[#D4AF37]" />
            </div>
            <div>
                <p class="text-2xl font-bold leading-none text-white md:text-3xl">{{ stats.certificate_count }}</p>
                <p class="mt-1 text-[10px] font-medium text-white/50 md:text-xs">Certificados</p>
            </div>
            <ChevronRight class="ml-auto h-4 w-4 text-white/20 transition-colors group-hover:text-[#D4AF37]" />
        </Link>
    </section>
</template>
