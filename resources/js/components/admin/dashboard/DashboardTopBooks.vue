<script setup lang="ts">
import { BookOpen, DollarSign, Download } from 'lucide-vue-next';

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
    <article class="space-y-6 overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-5 shadow-sm md:rounded-[3rem] md:p-10">
        <header class="flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="font-serif text-xl font-bold text-gray-900">Top Libros</h3>
                <p class="text-xs font-medium text-gray-400">Ventas aprobadas y descargas.</p>
            </div>
            <BookOpen class="h-5 w-5 text-blue-400" />
        </header>

        <div v-if="topBooks.length" class="space-y-3">
            <div
                v-for="(book, idx) in topBooks"
                :key="book.id"
                class="flex items-center gap-4 rounded-2xl border border-gray-50 bg-gray-50/40 p-4 transition-all hover:bg-white hover:shadow-md dark:border-slate-800/40 dark:bg-transparent dark:hover:bg-white/10 dark:hover:shadow-none"
            >
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-black"
                    :class="idx === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500'"
                >
                    {{ idx + 1 }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-gray-900">{{ book.title }}</p>
                    <p class="mt-0.5 text-[10px] uppercase tracking-widest text-gray-400">{{ book.category }}</p>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1">
                    <span v-if="Number(book.total_earned) > 0" class="flex items-center gap-1 text-xs font-black tabular-nums text-violet-700">
                        <DollarSign class="h-3 w-3" />
                        S/ {{ Number(book.total_earned).toLocaleString() }}
                    </span>
                    <span class="flex items-center gap-2 text-[10px] font-bold text-gray-500">
                        <span>{{ book.approved_sales_count ?? 0 }} ventas</span>
                        <span>·</span>
                        <span class="flex items-center gap-0.5"><Download class="h-3 w-3" />{{ book.downloads_count ?? 0 }}</span>
                    </span>
                </div>
            </div>
        </div>
        <p v-else class="py-8 text-center text-sm text-gray-400">Aún no hay actividad en libros.</p>
    </article>
</template>
