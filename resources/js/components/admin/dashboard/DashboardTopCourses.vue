<script setup lang="ts">
import { ChevronRight, Video } from 'lucide-vue-next';

interface CourseSale {
    id: number;
    title: string;
    approved_payments_count: number;
    total_earned: number;
    category?: { name: string };
    type: string;
    price: number;
    image?: string;
}

defineProps<{
    courseSales: CourseSale[];
}>();
</script>

<template>
    <article class="space-y-6 overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-5 shadow-sm md:space-y-8 md:rounded-[3rem] md:p-10">
        <header class="flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="font-serif text-2xl font-bold text-gray-900">Top Cursos · Ranking de Ventas</h3>
                <p class="text-xs font-medium italic text-gray-400">Volumen de matrículas y facturación individual.</p>
            </div>
            <ChevronRight class="h-5 w-5 text-gray-300" />
        </header>

        <div class="space-y-4">
            <template v-if="courseSales.length">
                <div
                    v-for="(course, idx) in courseSales"
                    :key="course.id"
                    class="group flex cursor-pointer items-center gap-5 rounded-2xl border border-gray-50 bg-gray-50/30 p-5 transition-all hover:bg-white hover:shadow-lg dark:border-slate-800/40 dark:bg-transparent dark:hover:bg-white/10 dark:hover:shadow-none"
                >
                    <!-- Rank -->
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-black transition-colors"
                        :class="idx === 0 ? 'bg-primary text-primary-fixed' : idx === 1 ? 'bg-gray-100 text-gray-700' : 'bg-gray-50 text-gray-400'"
                    >
                        {{ idx + 1 }}
                    </div>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-gray-900 transition-colors group-hover:text-primary">{{ course.title }}</p>
                        <div class="mt-1 flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{
                                course.type === 'grabado' ? '🎬 Grabado' : '📡 En Vivo'
                            }}</span>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="hidden w-24 space-y-1 sm:block">
                        <div class="h-1.5 overflow-hidden rounded-full bg-gray-100">
                            <div
                                class="duration-[1500ms] h-full rounded-full transition-all"
                                :class="idx === 0 ? 'bg-primary' : 'bg-gray-300'"
                                :style="{ width: (course.approved_payments_count / (courseSales[0]?.approved_payments_count || 1)) * 100 + '%' }"
                            ></div>
                        </div>
                        <p class="text-right text-[9px] font-bold text-gray-400">{{ course.approved_payments_count }} ventas</p>
                    </div>

                    <!-- Total ganado -->
                    <div class="shrink-0 text-right">
                        <p class="font-serif text-lg font-black tabular-nums text-gray-900">S/ {{ (course.total_earned ?? 0).toLocaleString() }}</p>
                        <p class="text-[9px] font-bold italic text-gray-400">facturado</p>
                    </div>
                </div>
            </template>
            <div v-else class="py-12 text-center text-gray-300">
                <Video class="mx-auto mb-3 h-12 w-12" />
                <p class="text-sm font-medium">Sin ventas registradas aún</p>
            </div>
        </div>
    </article>
</template>
