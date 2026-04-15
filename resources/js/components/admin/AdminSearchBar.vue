<script setup lang="ts">
import { Search, LayoutGrid, List } from 'lucide-vue-next';

withDefaults(defineProps<{
    modelValue: string;
    placeholder?: string;
    resultCount?: number;
    resultLabel?: string;
    showViewToggle?: boolean;
    viewMode?: 'grid' | 'list';
    syncLabel?: string;
    syncAccent?: string;
}>(), {
    placeholder: 'Buscar...',
    resultLabel: 'Resultados',
    showViewToggle: true,
    viewMode: 'grid',
    syncLabel: 'Sync',
    syncAccent: 'Archive',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'update:viewMode': [mode: 'grid' | 'list'];
}>();
</script>

<template>
    <div class="mb-12 flex flex-col gap-8">
        <!-- Search + Filters slot -->
        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- Search Input -->
            <div class="relative flex-1 group">
                <Search class="absolute left-6 top-1/2 -translate-y-1/2 h-5 w-5 text-on-background/30 group-focus-within:text-primary transition-all duration-500 dark:text-primary-fixed/30" />
                <input
                    :value="modelValue"
                    @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                    type="search"
                    :placeholder="placeholder"
                    class="w-full bg-white border-2 border-on-background/5 rounded-[2rem] py-5 pl-16 pr-8 text-[11px] font-black uppercase tracking-[0.2em] text-on-background outline-none focus:ring-8 focus:ring-primary/5 focus:border-primary/20 transition-all placeholder:text-on-background/20 shadow-xl shadow-on-background/2 cursor-text dark:bg-on-background dark:text-primary-fixed dark:border-primary-fixed/10 dark:placeholder:text-primary-fixed/20"
                >
            </div>

            <!-- Filters slot (category pills, etc.) -->
            <slot name="filters" />
        </div>

        <!-- Meta row -->
        <div class="flex items-center justify-between px-6">
            <div class="flex items-center gap-4">
                <div class="h-[1px] w-12 bg-on-background/10 dark:bg-primary-fixed/10"></div>
                <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af] dark:text-[#6b7280]">
                    {{ syncLabel }} <span class="text-on-background dark:text-primary-fixed">{{ syncAccent }}</span>
                </span>
            </div>

            <div class="flex items-center gap-4 ml-auto">
                <!-- View toggle -->
                <div v-if="showViewToggle" class="flex items-center p-1 bg-surface-container rounded-lg border border-on-background/5 dark:bg-[#2a2a1a] dark:border-primary-fixed/10">
                    <button
                        @click="emit('update:viewMode', 'grid')"
                        type="button"
                        class="p-2 rounded-md transition-all duration-300"
                        :class="viewMode === 'grid' ? 'bg-white shadow-sm text-on-background dark:bg-[#3a3a2a] dark:text-primary-fixed' : 'text-on-background/20 hover:text-on-background dark:text-primary-fixed/20 dark:hover:text-primary-fixed'"
                    >
                        <LayoutGrid class="h-4 w-4" />
                    </button>
                    <button
                        @click="emit('update:viewMode', 'list')"
                        type="button"
                        class="p-2 rounded-md transition-all duration-300"
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-on-background dark:bg-[#3a3a2a] dark:text-primary-fixed' : 'text-on-background/20 hover:text-on-background dark:text-primary-fixed/20 dark:hover:text-primary-fixed'"
                    >
                        <List class="h-4 w-4" />
                    </button>
                </div>

                <span v-if="resultCount !== undefined" class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af] dark:text-[#6b7280]">
                    {{ resultLabel }}: <span class="text-on-background text-xs font-serif italic dark:text-primary-fixed">{{ resultCount }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
