<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import { 
    Play, Clock, ExternalLink, Bookmark, Share2, 
    MessageSquare, FileText, HelpCircle, Heart, 
    Reply, Edit2, Trash2, Send, CheckCircle2, X,
    ChevronDown, ChevronUp, Lock, ArrowRight, Download
} from 'lucide-vue-next';
import type { Course, Lesson, Material } from '@/types/classroom';

const props = defineProps<{
    course: Course;
    currentLesson: Lesson | null;
    lessonState: 'recorded' | 'scheduled' | 'live' | 'processing';
    videoId: string | null;
    countdown: string;
    activeTab: 'description' | 'resources' | 'notes' | 'questions';
}>();

const instructorName = computed(() => props.course?.instructor_name || 'Sarah Jenkins');
const instructorTitle = computed(() => props.course?.instructor_title || 'Lead Designer at Vercel');
const instructorImage = computed(() => {
    if (props.course?.instructor_image) {
        let img = props.course.instructor_image;
        if (img.startsWith('http') || img.startsWith('/') || img.startsWith('storage')) {
            if (img.startsWith('storage')) return '/' + img;
            return img;
        }
        return '/storage/' + img;
    }
    return '/images/sarah_avatar.png';
});

const emit = defineEmits<{
    (e: 'update:activeTab', val: 'description' | 'resources' | 'notes' | 'questions'): void;
    (e: 'completeLesson'): void;
}>();

// Personal Notes logic
const personalNotes = ref('');
const isSaved = ref(true);
let saveTimeout: any = null;

watch(() => props.currentLesson?.id, (newId) => {
    if (newId) {
        personalNotes.value = localStorage.getItem(`iie_notes_${newId}`) || '';
        isSaved.value = true;
    } else {
        personalNotes.value = '';
    }
}, { immediate: true });

function handleNotesInput() {
    isSaved.value = false;
    if (saveTimeout) clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        if (props.currentLesson?.id) {
            localStorage.setItem(`iie_notes_${props.currentLesson.id}`, personalNotes.value);
            isSaved.value = true;
        }
    }, 800);
}

// FAQs state
const faqs = ref([
    {
        q: '¿Cómo descargo los materiales del curso?',
        a: 'Dirígete a la pestaña "Recursos" y haz clic en el botón de descarga del archivo correspondiente. Los recursos se abrirán en una nueva pestaña.',
        open: false
    },
    {
        q: '¿Cuál es el requisito para rendir la evaluación final?',
        a: 'Debes completar la visualización de todos los módulos curriculares al 100%. Una vez cumplido este requisito, se habilitará automáticamente la evaluación final en el panel lateral.',
        open: false
    },
    {
        q: '¿Cómo puedo obtener mi certificado oficial?',
        a: 'Al aprobar la evaluación final con la nota mínima requerida, tu certificado digital de aprobación se generará instantáneamente y estará listo para su descarga.',
        open: false
    },
    {
        q: '¿Tienen algún canal de soporte académico?',
        a: 'Sí, puedes usar el Foro Académico ubicado en el panel lateral (o el botón de chat en móvil) para interactuar con tus compañeros y docentes, o contactarnos de manera directa mediante el botón de WhatsApp en la vista general.',
        open: false
    }
]);

function toggleFaq(index: number) {
    faqs.value[index].open = !faqs.value[index].open;
}

// Mock bookmark state
const isBookmarked = ref(false);
function toggleBookmark() {
    isBookmarked.value = !isBookmarked.value;
}

// Mock share action
function handleShare() {
    if (navigator.share) {
        navigator.share({
            title: props.currentLesson?.title || 'Clase de ieeEdu',
            text: props.currentLesson?.description || '',
            url: window.location.href
        }).catch(err => console.log(err));
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Enlace copiado al portapapeles');
    }
}
</script>

<template>
    <div class="w-full bg-surface-dim dark:bg-surface-dim relative rounded-3xl overflow-hidden shadow-2xl shadow-primary/5 border border-outline-variant/10 aspect-video mb-8">
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

    <!-- Title and Instructor info row -->
    <div class="space-y-6 mb-8">
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-serif font-bold text-on-background dark:text-on-surface leading-tight tracking-tight">
            {{ currentLesson?.title }}
        </h1>
        
        <div class="pb-6 border-b border-outline-variant/10">
            <!-- Instructor Info -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden border border-outline-variant/20 bg-surface-container flex items-center justify-center">
                    <img :src="instructorImage" :alt="instructorName" class="w-full h-full object-cover" />
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant">
                        Instructed by <span class="font-bold text-on-surface">{{ instructorName }}</span>
                    </p>
                    <p class="text-[10px] text-on-surface-variant/60 font-medium">{{ instructorTitle }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="flex flex-col">
        <!-- Premium Tabs Navigation -->
        <div class="flex items-center gap-8 overflow-x-auto scrollbar-none order-1 border-b border-outline-variant/10 pb-4 mb-8 bg-transparent w-full max-w-full">
            <button 
                v-for="tab in ['description', 'resources', 'notes', 'questions']" 
                :key="tab"
                @click="emit('update:activeTab', tab as any)"
                class="pb-4 text-[11px] md:text-[12px] font-black uppercase tracking-wider transition-all relative shrink-0 group"
                :class="activeTab === tab ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
            >
                <template v-if="tab === 'description'">Descripción</template>
                <template v-else-if="tab === 'resources'">Recursos</template>
                <template v-else-if="tab === 'notes'">Mis Notas</template>
                <template v-else-if="tab === 'questions'">Preguntas Frecuentes</template>

                <span class="absolute bottom-0 left-0 w-full h-[3px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10"></span>
                <span v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-[3px] bg-primary rounded-full animate-in zoom-in duration-300"></span>
            </button>
        </div>

        <!-- Tabs Content -->
        <div class="min-h-[250px] order-2">
            <!-- 1. Description Tab -->
            <div v-show="activeTab === 'description'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6">
                <div class="prose max-w-none text-on-surface-variant/90 leading-relaxed font-sans text-sm md:text-base">
                    <p v-if="currentLesson?.description" class="whitespace-pre-line leading-relaxed">{{ currentLesson.description }}</p>
                    <p v-else class="text-on-surface-variant/40 italic text-base">Descripción en revisión académica.</p>
                </div>
            </div>

            <!-- 2. Resources Tab -->
            <div v-show="activeTab === 'resources'" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                <slot name="resources"></slot>
            </div>


            <!-- 4. Personal Notes Tab -->
            <div v-show="activeTab === 'notes'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-on-background flex items-center gap-2">
                            <FileText class="w-4.5 h-4.5 text-primary" />
                            Notas Académicas Personales
                        </h3>
                        <p class="text-[10px] text-on-surface-variant/60">Tus notas son personales y se guardan automáticamente en este navegador.</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider" :class="isSaved ? 'text-emerald-600' : 'text-amber-600'">
                        <span class="w-1.5 h-1.5 rounded-full" :class="isSaved ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                        {{ isSaved ? 'Guardado' : 'Guardando...' }}
                    </div>
                </div>
                
                <textarea 
                    v-model="personalNotes"
                    @input="handleNotesInput"
                    placeholder="Escribe aquí tus ideas, apuntes y notas clave de la clase actual para repasarlas más tarde..."
                    class="w-full min-h-[250px] p-6 bg-white border border-outline-variant/20 focus:border-primary/40 rounded-2xl shadow-inner text-sm leading-relaxed text-on-surface focus:ring-1 focus:ring-primary/20 resize-y font-sans font-medium"
                ></textarea>
            </div>

            <!-- 5. Questions (FAQ) Tab -->
            <div v-show="activeTab === 'questions'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-4 pb-12">
                <h3 class="text-sm font-bold text-on-background flex items-center gap-2 mb-2">
                    <HelpCircle class="w-4.5 h-4.5 text-primary" />
                    Preguntas Frecuentes
                </h3>
                
                <div class="space-y-3">
                    <div 
                        v-for="(faq, index) in faqs" 
                        :key="index"
                        class="border border-outline-variant/10 rounded-2xl bg-white shadow-sm overflow-hidden transition-all duration-300"
                    >
                        <button 
                            @click="toggleFaq(index)"
                            class="w-full px-6 py-4 flex items-center justify-between text-left font-bold text-xs md:text-sm text-on-background hover:text-primary transition-colors focus:outline-none"
                        >
                            <span>{{ faq.q }}</span>
                            <ChevronUp v-if="faq.open" class="w-4 h-4 text-primary shrink-0" />
                            <ChevronDown v-else class="w-4 h-4 text-on-surface-variant/40 shrink-0" />
                        </button>
                        
                        <div 
                            v-show="faq.open"
                            class="px-6 pb-5 pt-1 text-xs md:text-sm text-on-surface-variant/80 border-t border-outline-variant/5 bg-surface-container-low/30 leading-relaxed font-medium animate-in slide-in-from-top-1 duration-200"
                        >
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.prose p {
    margin-bottom: 1.25em;
}
</style>
