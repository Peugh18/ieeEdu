<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        badge?: string;
        title: string;
        titleAccent?: string;
        subtitle?: string;
        actionLabel?: string;
        actionOrder?: 'first' | 'last';
        backLink?: string;
        backLabel?: string;
        compact?: boolean;
    }>(),
    {
        actionOrder: 'last',
        compact: false,
    },
);

const emit = defineEmits<{ action: [] }>();

const backHref = computed(() => (props.backLink ? route(props.backLink) : null));
</script>

<template>
    <div class="mb-8 md:mb-10">
        <Link
            v-if="backHref && backLabel"
            :href="backHref"
            class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant transition-colors hover:text-primary"
        >
            <ChevronLeft class="h-4 w-4" />
            {{ backLabel }}
        </Link>

        <div v-if="badge" class="mb-4">
            <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-primary">
                {{ badge }}
            </span>
        </div>

        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center md:gap-6">
            <div class="min-w-0 space-y-1.5">
                <h1
                    class="font-serif font-bold leading-tight text-on-surface"
                    :class="compact ? 'text-2xl md:text-3xl' : 'text-3xl md:text-4xl lg:text-5xl'"
                >
                    {{ title }}
                    <span v-if="titleAccent" class="italic text-primary">{{ titleAccent }}</span>
                </h1>
                <p v-if="subtitle" class="text-sm font-medium text-on-surface-variant">
                    {{ subtitle }}
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-3">
                <slot name="actions" />

                <button
                    v-if="actionLabel"
                    type="button"
                    @click="emit('action')"
                    :class="actionOrder === 'first' ? 'order-first md:order-last' : ''"
                    class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg transition-all hover:bg-primary/90 active:scale-[0.98]"
                >
                    <Plus class="h-4 w-4" />
                    {{ actionLabel }}
                </button>

                <slot />
            </div>
        </div>
    </div>
</template>
