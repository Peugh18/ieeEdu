<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    badge?: string;
    title: string;
    titleAccent?: string;
    subtitle?: string;
    actionLabel?: string;
    actionOrder?: 'first' | 'last';
    backLink?: string;
    backLabel?: string;
    compact?: boolean;
}>(), {
    actionOrder: 'last',
    compact: false,
});

const emit = defineEmits<{ action: [] }>();

const backHref = computed(() => (props.backLink ? route(props.backLink) : null));
</script>

<template>
    <div class="mb-8 md:mb-10">
        <Link
            v-if="backHref && backLabel"
            :href="backHref"
            class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors"
        >
            <ChevronLeft class="h-4 w-4" />
            {{ backLabel }}
        </Link>

        <div v-if="badge" class="mb-4">
            <span class="inline-flex px-3 py-1 rounded-full bg-primary/10 text-[11px] font-bold uppercase tracking-widest text-primary">
                {{ badge }}
            </span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
            <div class="space-y-1.5 min-w-0">
                <h1
                    class="font-serif font-bold text-on-surface leading-tight"
                    :class="compact ? 'text-2xl md:text-3xl' : 'text-3xl md:text-4xl lg:text-5xl'"
                >
                    {{ title }}
                    <span v-if="titleAccent" class="italic text-primary">{{ titleAccent }}</span>
                </h1>
                <p v-if="subtitle" class="text-sm text-on-surface-variant font-medium">
                    {{ subtitle }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <slot name="actions" />

                <button
                    v-if="actionLabel"
                    type="button"
                    @click="emit('action')"
                    :class="actionOrder === 'first' ? 'order-first md:order-last' : ''"
                    class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg hover:bg-primary/90 active:scale-[0.98] transition-all"
                >
                    <Plus class="h-4 w-4" />
                    {{ actionLabel }}
                </button>

                <slot />
            </div>
        </div>
    </div>
</template>
