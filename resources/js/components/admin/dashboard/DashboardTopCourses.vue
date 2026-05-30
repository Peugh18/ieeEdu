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
    <article class="xl:col-span-2 bg-white rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 border border-gray-100 shadow-sm space-y-6 md:space-y-8 overflow-hidden">
        <header class="flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="font-serif text-2xl font-bold text-gray-900">Top Cursos · Ranking de Ventas</h3>
                <p class="text-xs text-gray-400 font-medium italic">Volumen de matrículas y facturación individual.</p>
            </div>
            <ChevronRight class="w-5 h-5 text-gray-300" />
        </header>

        <div class="space-y-4">
            <template v-if="courseSales.length">
                <div v-for="(course, idx) in courseSales" :key="course.id"
                    class="flex items-center gap-5 p-5 rounded-2xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:shadow-lg transition-all group cursor-pointer">

                    <!-- Rank -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 transition-colors"
                        :class="idx === 0 ? 'bg-primary text-primary-fixed' : idx === 1 ? 'bg-gray-100 text-gray-700' : 'bg-gray-50 text-gray-400'">
                        {{ idx + 1 }}
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 truncate group-hover:text-primary transition-colors">{{ course.title }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ course.type === 'grabado' ? '🎬 Grabado' : '📡 En Vivo' }}</span>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="w-24 space-y-1 hidden sm:block">
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                             <div class="h-full rounded-full transition-all duration-[1500ms]"
                                :class="idx === 0 ? 'bg-primary' : 'bg-gray-300'"
                                :style="{width: (course.approved_payments_count / (courseSales[0]?.approved_payments_count || 1) * 100) + '%'}">
                            </div>
                        </div>
                        <p class="text-[9px] font-bold text-gray-400 text-right">{{ course.approved_payments_count }} ventas</p>
                    </div>

                    <!-- Total ganado -->
                    <div class="text-right shrink-0">
                        <p class="font-serif font-black text-gray-900 tabular-nums text-lg">S/ {{ (course.total_earned ?? 0).toLocaleString() }}</p>
                        <p class="text-[9px] text-gray-400 font-bold italic">facturado</p>
                    </div>
                </div>
            </template>
            <div v-else class="text-center py-12 text-gray-300">
                <Video class="w-12 h-12 mx-auto mb-3" />
                <p class="text-sm font-medium">Sin ventas registradas aún</p>
            </div>
        </div>
    </article>
</template>
