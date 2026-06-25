<script setup lang="ts">
defineProps<{
    subIncome: number;
    courseIncome: number;
    bookIncome: number;
    subIncomeShare: string | number;
    courseIncomeShare: string | number;
    bookIncomeShare: string | number;
    activeSubs: number;
    expiredSubs: number;
}>();
</script>

<template>
    <article class="flex-1 space-y-6 rounded-[2rem] border border-primary/10 bg-surface-dim p-6 md:space-y-8 md:rounded-[2.5rem] md:p-9">
        <h3 class="font-serif text-lg font-bold italic text-gray-900">Composición de Caja</h3>

        <div class="space-y-5">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Membresías</span>
                    <div class="text-right">
                        <span class="text-sm font-black tabular-nums text-primary">{{ subIncomeShare }}%</span>
                        <span class="block text-[9px] font-bold italic text-gray-400">S/ {{ subIncome.toLocaleString() }}</span>
                    </div>
                </div>
                <div class="h-2 overflow-hidden rounded-full border border-gray-100 bg-white">
                    <div class="duration-[2s] h-full rounded-full bg-primary transition-all" :style="{ width: subIncomeShare + '%' }"></div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Cursos</span>
                    <div class="text-right">
                        <span class="text-sm font-black tabular-nums text-amber-500">{{ courseIncomeShare }}%</span>
                        <span class="block text-[9px] font-bold italic text-gray-400">S/ {{ courseIncome.toLocaleString() }}</span>
                    </div>
                </div>
                <div class="h-2 overflow-hidden rounded-full border border-gray-100 bg-white">
                    <div class="duration-[2s] h-full rounded-full bg-amber-400 transition-all" :style="{ width: courseIncomeShare + '%' }"></div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Libros</span>
                    <div class="text-right">
                        <span class="text-sm font-black tabular-nums text-blue-500">{{ bookIncomeShare }}%</span>
                        <span class="block text-[9px] font-bold italic text-gray-400">S/ {{ bookIncome.toLocaleString() }}</span>
                    </div>
                </div>
                <div class="h-2 overflow-hidden rounded-full border border-gray-100 bg-white">
                    <div class="duration-[2s] h-full rounded-full bg-blue-500 transition-all" :style="{ width: bookIncomeShare + '%' }"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-primary/10 pt-6">
            <div class="text-center">
                <p class="font-serif text-2xl font-black tabular-nums text-gray-900">{{ activeSubs }}</p>
                <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Subs Activas</p>
            </div>
            <div class="text-center">
                <p class="font-serif text-2xl font-black tabular-nums text-gray-900">{{ expiredSubs }}</p>
                <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Vencidas</p>
            </div>
        </div>
    </article>
</template>
