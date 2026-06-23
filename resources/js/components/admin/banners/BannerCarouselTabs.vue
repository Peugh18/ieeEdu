<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';

defineProps<{
    slideCount: number;
    previews?: (string | null)[];
}>();

const activeIndex = defineModel<number>('activeIndex', { required: true });
</script>

<template>
    <div class="rounded-[2rem] border border-outline-variant/20 bg-surface-container-lowest p-2">
        <div class="flex flex-wrap gap-2">
            <button
                v-for="idx in slideCount"
                :key="idx - 1"
                type="button"
                @click="activeIndex = idx - 1"
                class="flex min-w-[7rem] items-center gap-2.5 rounded-[1.25rem] px-4 py-2.5 text-sm font-bold transition-all"
                :class="
                    activeIndex === idx - 1
                        ? 'bg-primary text-white shadow-md ring-2 ring-primary/20'
                        : 'text-on-surface-variant hover:bg-surface-container-high'
                "
            >
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/20"
                    :class="activeIndex === idx - 1 ? 'border-white/30' : 'bg-surface-container-high'"
                >
                    <img v-if="previews?.[idx - 1]" :src="previews[idx - 1]!" alt="" class="h-full w-full object-cover" />
                    <ImageIcon v-else class="h-4 w-4 opacity-50" />
                </span>
                Slide {{ idx }}
            </button>
        </div>
    </div>
</template>
