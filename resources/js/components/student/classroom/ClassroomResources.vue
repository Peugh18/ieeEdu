<script setup lang="ts">
import type { Material } from '@/types/classroom';
import { ArrowRight, Clock, Download, ExternalLink } from 'lucide-vue-next';

defineProps<{
    materials?: Material[];
}>();

const emit = defineEmits<{
    (e: 'download', mat: Material): void;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 pb-24 md:grid-cols-2 md:gap-6 md:pb-32">
        <template v-if="materials?.length">
            <div
                v-for="mat in materials"
                :key="mat.id"
                @click="emit('download', mat)"
                class="group flex transform cursor-pointer items-center justify-between gap-4 rounded-xl border border-outline-variant/10 bg-white p-5 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:-translate-y-1 hover:border-primary/40 hover:shadow-lg dark:border-outline-variant/20 dark:bg-surface dark:hover:border-primary-fixed/40 md:rounded-2xl"
            >
                <div class="flex min-w-0 items-center gap-4">
                    <div
                        class="dark:bg-surface-2 flex h-12 w-12 items-center justify-center rounded-xl border border-outline-variant/10 bg-surface-container-low transition-colors group-hover:bg-primary dark:border-outline-variant/20 dark:group-hover:bg-primary-fixed"
                    >
                        <Download
                            v-if="mat.file_path"
                            class="h-5 w-5 text-primary transition-colors group-hover:text-white dark:text-primary-fixed dark:group-hover:text-on-primary-fixed"
                        />
                        <ExternalLink v-else class="h-5 w-5 text-indigo-500 transition-colors group-hover:text-white dark:text-indigo-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="mb-1 truncate text-sm font-semibold text-on-background dark:text-on-surface">{{ mat.title }}</h4>
                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-md bg-primary/5 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-primary dark:bg-primary/20 dark:text-primary-fixed-dim"
                                >{{ mat.kind }}</span
                            >
                            <span
                                class="whitespace-nowrap text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/40 dark:text-on-surface-variant/50"
                                >Recurso Institucional</span
                            >
                        </div>
                    </div>
                </div>
                <ArrowRight
                    class="w-4.5 h-4.5 text-outline-variant transition-all group-hover:translate-x-1 group-hover:text-primary dark:group-hover:text-primary-fixed-dim"
                />
            </div>
        </template>
        <div
            v-else
            class="dark:bg-surface-2 col-span-full rounded-2xl border border-dashed border-outline-variant/20 bg-surface-container-low py-20 text-center shadow-inner dark:border-outline-variant/30 md:rounded-[2rem]"
        >
            <Clock class="mx-auto mb-4 h-12 w-12 animate-pulse text-outline-variant/40" />
            <p class="mx-auto max-w-xs font-serif text-lg leading-relaxed text-on-surface-variant/60 dark:text-on-surface-variant/70">
                No se han adjuntado recursos documentales para esta sesión técnica.
            </p>
        </div>
    </div>
</template>
