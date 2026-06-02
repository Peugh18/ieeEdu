<script setup lang="ts">
import { BookOpen, Download, DollarSign } from 'lucide-vue-next';

interface TopBook {
    id: number;
    title: string;
    category: string;
    price: number | string;
    author?: string | null;
    cover_image?: string | null;
    downloads_count: number;
    approved_sales_count: number;
    total_earned: number | string | null;
}

defineProps<{
    topBooks: TopBook[];
}>();
</script>

<template>
    <article class="bg-white rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 border border-gray-100 shadow-sm space-y-6 overflow-hidden">
        <header class="flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="font-serif text-xl font-bold text-gray-900">Top Libros</h3>
                <p class="text-xs text-gray-400 font-medium">Ventas aprobadas y descargas.</p>
            </div>
            <BookOpen class="w-5 h-5 text-blue-400" />
        </header>

        <div v-if="topBooks.length" class="space-y-3">
            <div
                v-for="(book, idx) in topBooks"
                :key="book.id"
                class="flex items-center gap-4 p-4 rounded-2xl border border-gray-50 dark:border-slate-800/40 bg-gray-50/40 dark:bg-transparent hover:bg-white dark:hover:bg-white/10 hover:shadow-md dark:hover:shadow-none transition-all"
            >
                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
                    :class="idx === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500'"
                >
                    {{ idx + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-900 truncate">{{ book.title }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">{{ book.category }}</p>
                </div>
                <div class="flex flex-col items-end gap-1 shrink-0">
                    <span v-if="Number(book.total_earned) > 0" class="flex items-center gap-1 text-violet-700 text-xs font-black tabular-nums">
                        <DollarSign class="w-3 h-3" />
                        S/ {{ Number(book.total_earned).toLocaleString() }}
                    </span>
                    <span class="flex items-center gap-2 text-[10px] text-gray-500 font-bold">
                        <span>{{ book.approved_sales_count ?? 0 }} ventas</span>
                        <span>·</span>
                        <span class="flex items-center gap-0.5"><Download class="w-3 h-3" />{{ book.downloads_count ?? 0 }}</span>
                    </span>
                </div>
            </div>
        </div>
        <p v-else class="text-sm text-gray-400 text-center py-8">Aún no hay actividad en libros.</p>
    </article>
</template>
