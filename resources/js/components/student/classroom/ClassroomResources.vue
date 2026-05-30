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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 pb-24 md:pb-32">
        <template v-if="materials?.length">
            <div 
                v-for="mat in materials" :key="mat.id" 
                @click="emit('download', mat)"
                class="p-8 bg-white border border-outline-variant/20 rounded-[2.5rem] group hover:border-primary/40 hover:shadow-[0_20px_40px_rgba(87,87,42,0.08)] transition-all flex items-center justify-between gap-6 cursor-pointer transform hover:-translate-y-2 animate-in fade-in slide-in-from-bottom-3 duration-500"
            >
                <div class="flex items-center gap-6 min-w-0">
                    <div class="w-14 h-14 bg-background rounded-[1.25rem] group-hover:bg-primary transition-colors flex items-center justify-center border border-outline-variant/20">
                        <Download v-if="mat.file_path" class="w-6 h-6 text-primary group-hover:text-white transition-colors" />
                        <ExternalLink v-else class="w-6 h-6 text-indigo-400 group-hover:text-white transition-colors" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-base font-bold text-on-background truncate mb-1">{{ mat.title }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] text-primary font-black uppercase tracking-[0.2em] px-2 py-0.5 bg-primary/5 rounded">{{ mat.kind }}</span>
                            <span class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-widest whitespace-nowrap">Recurso Institucional</span>
                        </div>
                    </div>
                </div>
                <ArrowRight class="w-5 h-5 text-outline-variant group-hover:text-primary group-hover:translate-x-1 transition-all" />
            </div>
        </template>
        <div v-else class="col-span-full py-40 text-center bg-background rounded-[4rem] border border-dashed border-outline-variant/40">
            <Clock class="w-20 h-20 text-outline-variant/40 mx-auto mb-8 animate-pulse" />
            <p class="text-on-surface-variant/60 font-serif italic text-2xl max-w-sm mx-auto leading-relaxed">
                No se han adjuntado recursos documentales para esta sesión técnica.
            </p>
        </div>
    </div>
</template>
