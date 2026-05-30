<script setup lang="ts">
defineProps<{
    subIncome: number;
    courseIncome: number;
    subIncomeShare: string | number;
    courseIncomeShare: string | number;
    activeSubs: number;
    expiredSubs: number;
}>();
</script>

<template>
    <article class="bg-surface-dim rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-primary/10 space-y-6 md:space-y-8 flex-1">
        <h3 class="font-serif text-lg font-bold text-gray-900 italic">Composición de Caja</h3>

        <div class="space-y-6">
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Membresías</span>
                    <div class="text-right">
                        <span class="text-sm font-black text-primary tabular-nums">{{ subIncomeShare }}%</span>
                        <span class="block text-[9px] text-gray-400 font-bold italic">S/ {{ subIncome.toLocaleString() }}</span>
                    </div>
                </div>
                <div class="h-2 bg-white rounded-full overflow-hidden border border-gray-100">
                     <div class="h-full bg-primary rounded-full transition-all duration-[2s]" :style="{width: subIncomeShare + '%'}"></div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Cursos</span>
                    <div class="text-right">
                        <span class="text-sm font-black text-amber-500 tabular-nums">{{ courseIncomeShare }}%</span>
                        <span class="block text-[9px] text-gray-400 font-bold italic">S/ {{ courseIncome.toLocaleString() }}</span>
                    </div>
                </div>
                <div class="h-2 bg-white rounded-full overflow-hidden border border-gray-100">
                     <div class="h-full bg-amber-400 rounded-full transition-all duration-[2s]" :style="{width: courseIncomeShare + '%'}"></div>
                </div>
            </div>
        </div>

        <!-- Totales -->
        <div class="pt-6 border-t border-primary/10 grid grid-cols-2 gap-4">
            <div class="text-center">
                <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ activeSubs }}</p>
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Subs Activas</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ expiredSubs }}</p>
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Vencidas</p>
            </div>
        </div>
    </article>
</template>
