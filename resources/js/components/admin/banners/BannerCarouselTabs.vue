<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';

defineProps<{
    slideCount: number;
    previews?: (string | null)[];
}>();

const activeIndex = defineModel<number>('activeIndex', { required: true });
</script>

<template>
    <div class="rounded-[2rem] p-2 bg-surface-container-lowest border border-outline-variant/20">
        <div class="flex flex-wrap gap-2">
            <button
                v-for="idx in slideCount"
                :key="idx - 1"
                type="button"
                @click="activeIndex = idx - 1"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-[1.25rem] text-sm font-bold transition-all min-w-[7rem]"
                :class="activeIndex === idx - 1
                    ? 'bg-primary text-white shadow-md ring-2 ring-primary/20'
                    : 'text-on-surface-variant hover:bg-surface-container-high'"
            >
                <span
                    class="w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center border border-outline-variant/20"
                    :class="activeIndex === idx - 1 ? 'border-white/30' : 'bg-surface-container-high'"
                >
                    <img
                        v-if="previews?.[idx - 1]"
                        :src="previews[idx - 1]!"
                        alt=""
                        class="w-full h-full object-cover"
                    />
                    <ImageIcon v-else class="w-4 h-4 opacity-50" />
                </span>
                Slide {{ idx }}
            </button>
        </div>
    </div>
</template>
