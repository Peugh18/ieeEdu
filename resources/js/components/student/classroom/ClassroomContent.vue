<script setup lang="ts">
import { Play, Clock, ExternalLink } from 'lucide-vue-next';
import type { Lesson } from '@/types/classroom';

defineProps<{
    currentLesson: Lesson | null;
    lessonState: 'recorded' | 'scheduled' | 'live' | 'processing';
    videoId: string | null;
    countdown: string;
    activeTab: 'content' | 'resources';
}>();

const emit = defineEmits<{
    (e: 'update:activeTab', val: 'content' | 'resources'): void;
    (e: 'completeLesson'): void;
    (e: 'enterForum'): void;
}>();
</script>

<template>
    <div class="w-full bg-surface-dim dark:bg-surface-dim relative aspect-video shadow-md overflow-hidden border-b border-outline-variant/10">
        <!-- RECORDED: Show Video Player -->
        <template v-if="lessonState === 'recorded'">
            <div v-if="videoId" class="w-full h-full relative">
                <div class="js-plyr w-full h-full" :data-plyr-provider="'youtube'" :data-plyr-embed-id="videoId"></div>
            </div>
            <div v-else class="w-full h-full flex flex-col items-center justify-center p-6 md:p-12 text-center bg-surface-container">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center mb-6 border border-primary/15 dark:border-primary/30">
                    <Play class="w-8 h-8 text-primary dark:text-primary-fixed" />
                </div>
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-on-background dark:text-on-surface mb-2">Material en Aula</h2>
                <p class="text-on-surface-variant/70 dark:text-on-surface-variant/60 text-sm md:text-base max-w-sm">
                    {{ currentLesson?.description || 'Esta sesión está siendo preparada por el equipo docente.' }}
                </p>
            </div>
        </template>

        <!-- SCHEDULED & LIVE -->
        <template v-else-if="lessonState === 'scheduled' || lessonState === 'live'">
             <div class="absolute inset-0 bg-surface-container flex flex-col items-center justify-center p-6 md:p-10 text-center">
                <div class="relative z-10 w-full max-w-4xl space-y-6 md:space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-surface dark:bg-surface-2 border border-outline-variant/20 dark:border-outline-variant/30 rounded-full shadow-sm">
                        <div class="w-2 h-2 rounded-full" :class="lessonState === 'live' ? 'bg-red-500 animate-pulse' : 'bg-[#D4AF37]'"></div>
                        <span class="text-[9px] font-extrabold text-on-surface-variant dark:text-on-surface uppercase tracking-wider">
                            {{ lessonState === 'live' ? 'Sesión en Vivo Interactiva' : 'Programada para hoy' }}
                        </span>
                    </div>
                    
                    <h2 class="text-2xl md:text-5xl font-serif font-bold text-on-background dark:text-on-surface leading-tight tracking-tight">
                        {{ currentLesson?.title }}
                    </h2>

                    <div class="bg-white dark:bg-surface p-6 md:p-8 border border-outline-variant/20 dark:border-outline-variant/30 rounded-2xl md:rounded-[2rem] inline-block shadow-md mx-auto">
                        <div v-if="lessonState === 'scheduled'" class="text-5xl md:text-7xl font-serif font-bold text-[#D4AF37] tracking-tighter mb-4 italic tabular-nums">
                            {{ countdown }}
                        </div>
                        <div v-else class="text-5xl md:text-7xl font-sans font-black text-emerald-600 dark:text-emerald-400 tracking-tighter mb-4 animate-pulse">
                            STREAMING
                        </div>
                        
                        <p class="text-[9px] md:text-[10px] font-bold text-on-surface-variant/40 dark:text-on-surface-variant/60 uppercase tracking-widest mb-6 border-t border-outline-variant/20 dark:border-outline-variant/10 pt-4">
                            Fecha de clase: {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'Pendiente' }}
                        </p>
                        
                        <div v-if="lessonState === 'live'">
                            <a 
                                :href="currentLesson?.live_link" 
                                target="_blank" 
                                @click="emit('completeLesson')"
                                class="px-10 py-4 bg-primary dark:bg-primary-fixed text-white dark:text-on-primary-fixed font-bold text-[11px] uppercase tracking-wider rounded-xl hover:bg-on-background dark:hover:bg-white dark:hover:text-on-background transition-all transform hover:-translate-y-1 flex items-center gap-3 shadow-md group"
                            >
                                <ExternalLink class="w-4 h-4" /> Entrar al Aula Privada
                            </a>
                        </div>
                        <div v-else class="px-10 py-4 border border-outline-variant/20 dark:border-outline-variant/10 text-on-surface-variant/40 dark:text-on-surface/40 font-bold text-[10px] uppercase tracking-wider rounded-xl bg-surface-container-low dark:bg-surface-2">
                            Acceso en Espera
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- PROCESSING -->
        <template v-else-if="lessonState === 'processing'">
            <div class="absolute inset-0 bg-surface-container flex flex-col items-center justify-center p-6 md:p-10 text-center">
                <div class="relative mb-6">
                    <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center animate-pulse"></div>
                    <Clock class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8 text-[#D4AF37] animate-spin" style="animation-duration: 3s;" />
                </div>
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-on-background dark:text-on-surface mb-3">Procesado de Grabación</h2>
                <p class="text-on-surface-variant/80 dark:text-on-surface-variant/80 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
                    La cátedra en vivo ha concluido con éxito. Nuestro equipo de soporte académico está editando la grabación para su alojamiento definitivo. Estará disponible en minutos.
                </p>
            </div>
        </template>
    </div>

    <!-- Info Details: The Reading Space -->
    <div class="p-6 md:p-12 lg:p-16 space-y-8 md:space-y-12 max-w-5xl mx-auto">
        <!-- Premium Tabs Navigation -->
        <div class="flex items-center gap-8 border-b border-outline-variant/10">
            <button 
                v-for="tab in ['content', 'resources']" :key="tab"
                @click="emit('update:activeTab', tab as any)"
                class="pb-4 text-xs md:text-sm font-bold uppercase tracking-wider transition-all relative group"
                :class="activeTab === tab ? 'text-primary dark:text-primary-fixed-dim' : 'text-on-surface-variant/40 dark:text-on-surface-variant/50 hover:text-on-surface-variant dark:hover:text-on-surface'"
            >
                {{ tab === 'content' ? 'Nota Académica' : 'Documentación' }}
                <span class="absolute bottom-0 left-0 w-full h-[3px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10 dark:bg-primary-fixed/20"></span>
                <span v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-[3px] bg-primary dark:bg-primary-fixed-dim rounded-full animate-in zoom-in duration-300"></span>
            </button>
        </div>

        <!-- Tab Content -->
        <div v-show="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10">
             <div class="space-y-4">
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-on-background dark:text-on-surface leading-tight">
                    {{ currentLesson?.title }}
                </h2>
                <div class="relative pl-6 border-l-2 border-primary/20 dark:border-primary/40">
                    <div class="prose prose-p:text-on-surface-variant dark:prose-p:text-on-surface/80 prose-p:font-sans prose-p:text-base md:prose-p:text-lg prose-p:leading-relaxed max-w-none">
                        <p v-if="currentLesson?.description" class="whitespace-pre-line">{{ currentLesson.description }}</p>
                        <p v-else class="text-on-surface-variant/40 dark:text-on-surface-variant/50 italic text-base">Descripción en revisión académica.</p>
                    </div>
                </div>
             </div>

             <!-- Interactive Invitation -->
             <div class="bg-surface-container-low dark:bg-surface-2 rounded-2xl md:rounded-[2.5rem] p-6 md:p-10 border border-outline-variant/10 dark:border-outline-variant/20 flex flex-col md:flex-row items-center justify-between gap-5 md:gap-8 relative overflow-hidden group shadow-sm transition-all duration-300">
                 <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                 <div class="space-y-2 relative z-10 max-w-2xl">
                     <h3 class="text-primary dark:text-primary-fixed-dim font-serif font-bold text-lg md:text-xl tracking-tight">¿Desea profundizar en algún punto?</h3>
                     <p class="text-on-surface-variant dark:text-on-surface-variant/80 text-sm md:text-base leading-relaxed">
                         Le invitamos a participar en el foro académico de esta sesión. Sus aportes enriquecen la comunidad estudiantil del Instituto IEE.
                     </p>
                 </div>
                 <div class="relative z-10 w-full md:w-auto flex-shrink-0">
                     <button 
                         @click="emit('enterForum')"
                         class="w-full md:w-auto px-6 py-3.5 md:px-8 md:py-4 bg-primary dark:bg-primary-fixed dark:text-on-primary-fixed text-white rounded-xl font-bold text-[10px] uppercase tracking-wider shadow-md hover:shadow-lg shadow-primary/10 dark:shadow-none hover:bg-on-background dark:hover:bg-white dark:hover:text-on-background-fixed transform hover:-translate-y-1 transition-all active:scale-95 whitespace-nowrap"
                     >
                         Dialogar con el Mentor
                     </button>
                 </div>
             </div>
        </div>

        <div v-show="activeTab === 'resources'">
            <slot name="resources"></slot>
        </div>
    </div>
</template>
