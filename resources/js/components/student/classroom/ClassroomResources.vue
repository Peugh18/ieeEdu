<script setup lang="ts">
import { Download, ExternalLink, ArrowRight, Clock } from 'lucide-vue-next';
import type { Material } from '@/types/classroom';

defineProps<{
    materials?: Material[];
}>();

const emit = defineEmits<{
    (e: 'download', mat: Material): void;
}>();
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 pb-24 md:pb-32">
        <template v-if="materials?.length">
            <div 
                v-for="mat in materials" :key="mat.id" 
                @click="emit('download', mat)"
                class="p-5 bg-white dark:bg-surface border border-outline-variant/10 dark:border-outline-variant/20 rounded-xl md:rounded-2xl group hover:border-primary/40 dark:hover:border-primary-fixed/40 hover:shadow-lg transition-all flex items-center justify-between gap-4 cursor-pointer transform hover:-translate-y-1 animate-in fade-in slide-in-from-bottom-2 duration-300"
            >
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-12 h-12 bg-surface-container-low dark:bg-surface-2 rounded-xl group-hover:bg-primary dark:group-hover:bg-primary-fixed transition-colors flex items-center justify-center border border-outline-variant/10 dark:border-outline-variant/20">
                        <Download v-if="mat.file_path" class="w-5 h-5 text-primary dark:text-primary-fixed group-hover:text-white dark:group-hover:text-on-primary-fixed transition-colors" />
                        <ExternalLink v-else class="w-5 h-5 text-indigo-500 dark:text-indigo-400 group-hover:text-white transition-colors" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-semibold text-on-background dark:text-on-surface truncate mb-1">{{ mat.title }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] text-primary dark:text-primary-fixed-dim font-bold uppercase tracking-wider px-2 py-0.5 bg-primary/5 dark:bg-primary/20 rounded-md">{{ mat.kind }}</span>
                            <span class="text-[9px] text-on-surface-variant/40 dark:text-on-surface-variant/50 font-bold uppercase tracking-widest whitespace-nowrap">Recurso Institucional</span>
                        </div>
                    </div>
                </div>
                <ArrowRight class="w-4.5 h-4.5 text-outline-variant group-hover:text-primary dark:group-hover:text-primary-fixed-dim group-hover:translate-x-1 transition-all" />
            </div>
        </template>
        <div v-else class="col-span-full py-20 text-center bg-surface-container-low dark:bg-surface-2 rounded-2xl md:rounded-[2rem] border border-dashed border-outline-variant/20 dark:border-outline-variant/30 shadow-inner">
            <Clock class="w-12 h-12 text-outline-variant/40 mx-auto mb-4 animate-pulse" />
            <p class="text-on-surface-variant/60 dark:text-on-surface-variant/70 font-serif text-lg max-w-xs mx-auto leading-relaxed">
                No se han adjuntado recursos documentales para esta sesión técnica.
            </p>
        </div>
    </div>
</template>
