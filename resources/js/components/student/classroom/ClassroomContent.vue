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
    <div class="w-full bg-on-background relative aspect-video shadow-2xl overflow-hidden md:rounded-br-[4rem]">
        <!-- RECORDED: Show Video Player -->
        <template v-if="lessonState === 'recorded'">
            <div v-if="videoId" class="w-full h-full relative">
                <div class="js-plyr w-full h-full" :data-plyr-provider="'youtube'" :data-plyr-embed-id="videoId"></div>
            </div>
            <div v-else class="w-full h-full flex flex-col items-center justify-center p-12 text-center bg-gradient-to-br from-on-background to-[#2D302B]">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-8 border border-white/10">
                    <Play class="w-10 h-10 text-white/20" />
                </div>
                <h2 class="text-3xl font-serif font-bold text-white/50 mb-3 italic">Material en Aula</h2>
                <p class="text-outline-variant/40 italic font-serif max-w-sm">
                    {{ currentLesson?.description || 'Esta sesión está siendo preparada por el equipo docente.' }}
                </p>
            </div>
        </template>

        <!-- SCHEDULED & LIVE -->
        <template v-else-if="lessonState === 'scheduled' || lessonState === 'live'">
             <div class="absolute inset-0 bg-gradient-to-br from-[#1A1D1A] to-[#2D302B] flex flex-col items-center justify-center p-10 text-center">
                <div class="relative z-10 w-full max-w-4xl space-y-12">
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-full shadow-2xl">
                        <div class="w-2 h-2 rounded-full" :class="lessonState === 'live' ? 'bg-red-500 animate-pulse' : 'bg-[#D4AF37]'"></div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-[0.3em]">
                            {{ lessonState === 'live' ? 'Sesión en Vivo Interactiva' : 'Programada para hoy' }}
                        </span>
                    </div>
                    
                    <h2 class="text-4xl md:text-7xl font-serif font-bold text-white italic leading-tight tracking-tight">
                        {{ currentLesson?.title }}
                    </h2>

                    <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[4rem] p-12 inline-block shadow-2xl mx-auto">
                        <div v-if="lessonState === 'scheduled'" class="text-8xl font-serif font-bold text-[#D4AF37] tracking-tighter mb-6 italic tabular-nums drop-shadow-[0_10px_30px_rgba(212,175,55,0.2)]">
                            {{ countdown }}
                        </div>
                        <div v-else class="text-8xl font-serif font-bold text-emerald-400 tracking-tighter mb-6 italic tabular-nums animate-pulse">
                            STREAMING
                        </div>
                        
                        <p class="text-[11px] font-black text-outline-variant/60 uppercase tracking-[0.4em] mb-12 italic border-t border-white/5 pt-6">
                            Fecha Institucional: {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'Pendiente' }}
                        </p>
                        
                        <div v-if="lessonState === 'live'">
                            <a 
                                :href="currentLesson?.live_link" 
                                target="_blank" 
                                @click="emit('completeLesson')"
                                class="px-16 py-6 bg-white text-on-background font-black text-[12px] uppercase tracking-[0.3em] rounded-2xl hover:bg-[#D4AF37] hover:text-white transition-all transform hover:-translate-y-2 flex items-center gap-4 shadow-2xl shadow-black/40 group"
                            >
                                <ExternalLink class="w-5 h-5" /> Entrar al Aula Privada
                            </a>
                        </div>
                        <div v-else class="px-16 py-6 border border-white/10 text-white/40 font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl bg-white/5">
                            Acceso en Espera
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- PROCESSING -->
        <template v-else-if="lessonState === 'processing'">
            <div class="absolute inset-0 bg-[#0F110F] flex flex-col items-center justify-center p-10 text-center">
                <div class="relative mb-12">
                    <div class="w-28 h-28 bg-primary/10 rounded-full flex items-center justify-center animate-pulse"></div>
                    <Clock class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 text-[#D4AF37] animate-spin-slow" />
                </div>
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6 italic tracking-tight">Procesado de Grabación</h2>
                <p class="text-outline-variant/60 font-serif italic text-xl max-w-2xl mx-auto leading-relaxed">
                    La cátedra en vivo ha concluido con éxito. Nuestro equipo de soporte académico está editando la grabación para su alojamiento definitivo. Estará disponible en minutos.
                </p>
            </div>
        </template>
    </div>

    <!-- Info Details: The Reading Space -->
    <div class="p-5 md:p-20 space-y-10 md:space-y-20 max-w-6xl mx-auto">
        <!-- Premium Tabs Navigation -->
        <div class="flex items-center gap-12 border-b border-outline-variant/20">
            <button 
                v-for="tab in ['content', 'resources']" :key="tab"
                @click="emit('update:activeTab', tab as any)"
                class="pb-6 text-[11px] font-black uppercase tracking-[0.3em] transition-all relative group"
                :class="activeTab === tab ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
            >
                {{ tab === 'content' ? 'Nota Académica' : 'Documentación' }}
                <span class="absolute bottom-0 left-0 w-full h-[4px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10"></span>
                <span v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-[4px] bg-primary rounded-full animate-in zoom-in duration-500"></span>
            </button>
        </div>

        <!-- Tab Content -->
        <div v-show="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-6 duration-700 space-y-12">
             <div class="space-y-6">
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-on-background italic leading-tight">
                    {{ currentLesson?.title }}
                </h2>
                <div class="relative pl-10 border-l-[3px] border-primary/10">
                    <div class="prose prose-p:text-on-surface-variant prose-p:font-serif prose-p:italic prose-p:text-xl prose-p:leading-loose max-w-none">
                        <p v-if="currentLesson?.description">{{ currentLesson.description }}</p>
                        <p v-else class="text-on-surface-variant/40 italic">Descripción en revisión académica.</p>
                    </div>
                </div>
             </div>

             <!-- Interactive Invitation -->
             <div class="bg-background rounded-[2rem] md:rounded-[3.5rem] p-6 md:p-12 border border-outline-variant/30 flex flex-col md:flex-row items-center justify-between gap-5 md:gap-8 relative overflow-hidden group">
                 <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                 <div class="space-y-3 relative z-10 max-w-2xl">
                     <h3 class="text-primary font-serif font-bold text-xl md:text-2xl italic tracking-tight">¿Desea profundizar en algún punto?</h3>
                     <p class="text-on-surface-variant/80 text-base md:text-lg font-serif italic leading-relaxed">
                         Le invitamos a participar en el foro académico de esta sesión. Sus aportes enriquecen la comunidad estudiantil del Instituto IEE.
                     </p>
                 </div>
                 <div class="relative z-10 w-full md:w-auto">
                     <button 
                         @click="emit('enterForum')"
                         class="w-full md:w-auto px-8 py-4 md:px-10 md:py-5 bg-primary text-white rounded-2xl font-black text-[12px] uppercase tracking-[0.25em] shadow-2xl shadow-primary/20 hover:bg-on-background transform hover:-translate-y-2 transition-all active:scale-95 whitespace-nowrap"
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
