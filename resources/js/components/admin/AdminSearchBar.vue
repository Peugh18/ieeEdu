<script setup lang="ts">
import { LayoutGrid, List, Search } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        resultCount?: number;
        resultLabel?: string;
        showViewToggle?: boolean;
        viewMode?: 'grid' | 'list';
        syncLabel?: string;
        syncAccent?: string;
    }>(),
    {
        placeholder: 'Buscar...',
        resultLabel: 'Resultados',
        showViewToggle: true,
        viewMode: 'grid',
        syncLabel: 'Sync',
        syncAccent: 'Archive',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'update:viewMode': [mode: 'grid' | 'list'];
}>();
</script>

<template>
    <div class="mb-12 flex flex-col gap-8">
        <!-- Search + Filters slot -->
        <div class="flex flex-col items-center gap-4 md:flex-row">
            <!-- Search Input -->
            <div class="group relative flex-1">
                <Search
                    class="absolute left-6 top-1/2 h-5 w-5 -translate-y-1/2 text-on-background/30 transition-all duration-500 group-focus-within:text-primary dark:text-primary-fixed/30"
                />
                <input
                    :value="modelValue"
                    @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                    type="search"
                    :placeholder="placeholder"
                    class="shadow-on-background/2 w-full cursor-text rounded-[2rem] border-2 border-on-background/5 bg-white py-5 pl-16 pr-8 text-[11px] font-black uppercase tracking-[0.2em] text-on-background shadow-xl outline-none transition-all placeholder:text-on-background/20 focus:border-primary/20 focus:ring-8 focus:ring-primary/5 dark:border-primary-fixed/10 dark:bg-on-background dark:text-primary-fixed dark:placeholder:text-primary-fixed/20"
                />
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

            <div class="ml-auto flex items-center gap-4">
                <!-- View toggle -->
                <div
                    v-if="showViewToggle"
                    class="flex items-center rounded-lg border border-on-background/5 bg-surface-container p-1 dark:border-primary-fixed/10 dark:bg-[#2a2a1a]"
                >
                    <button
                        @click="emit('update:viewMode', 'grid')"
                        type="button"
                        class="rounded-md p-2 transition-all duration-300"
                        :class="
                            viewMode === 'grid'
                                ? 'bg-white text-on-background shadow-sm dark:bg-[#3a3a2a] dark:text-primary-fixed'
                                : 'text-on-background/20 hover:text-on-background dark:text-primary-fixed/20 dark:hover:text-primary-fixed'
                        "
                    >
                        <LayoutGrid class="h-4 w-4" />
                    </button>
                    <button
                        @click="emit('update:viewMode', 'list')"
                        type="button"
                        class="rounded-md p-2 transition-all duration-300"
                        :class="
                            viewMode === 'list'
                                ? 'bg-white text-on-background shadow-sm dark:bg-[#3a3a2a] dark:text-primary-fixed'
                                : 'text-on-background/20 hover:text-on-background dark:text-primary-fixed/20 dark:hover:text-primary-fixed'
                        "
                    >
                        <List class="h-4 w-4" />
                    </button>
                </div>

                <span v-if="resultCount !== undefined" class="text-[9px] font-black uppercase tracking-[0.4em] text-[#9ca3af] dark:text-[#6b7280]">
                    {{ resultLabel }}: <span class="font-serif text-xs italic text-on-background dark:text-primary-fixed">{{ resultCount }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
