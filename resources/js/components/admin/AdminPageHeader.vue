<script setup lang="ts">
import { Plus } from 'lucide-vue-next';

withDefaults(defineProps<{
    badge?: string;
    title: string;
    titleAccent: string;
    subtitle?: string;
    actionLabel?: string;
    actionOrder?: string; // 'first' | 'last' (default: 'last')
}>(), {
    actionOrder: 'last',
});

const emit = defineEmits<{ action: [] }>();
</script>

<template>
    <div class="mb-12">
        <div v-if="badge" class="flex items-center gap-2 mb-6">
            <span class="px-4 py-1.5 rounded-full bg-on-background text-[8px] font-black uppercase tracking-[0.3em] text-primary-fixed shadow-sm dark:bg-primary-fixed dark:text-on-background">
                {{ badge }}
            </span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-2">
                <h1 class="font-serif text-5xl font-bold text-on-background leading-tight dark:text-primary-fixed">
                    {{ title }} <span class="italic text-primary dark:text-[#b0af7a]">{{ titleAccent }}</span>
                </h1>
                <p v-if="subtitle" class="text-[9px] font-black uppercase tracking-[0.25em] text-[#9ca3af] dark:text-[#6b7280]">
                    {{ subtitle }}
                </p>
            </div>

            <button
                v-if="actionLabel"
                @click="emit('action')"
                :class="actionOrder === 'first' ? 'order-first md:order-last' : ''"
                class="inline-flex items-center gap-3 rounded-full bg-on-background px-8 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-primary-fixed shadow-2xl hover:bg-[#2a2a1a] hover:scale-[1.05] active:scale-95 transition-all duration-300 dark:bg-primary-fixed dark:text-on-background dark:hover:bg-[#d5d4a0]"
            >
                <Plus class="h-3 w-3" :stroke-width="4" />
                {{ actionLabel }}
            </button>
        </div>
    </div>
</template>
