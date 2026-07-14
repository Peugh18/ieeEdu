<script setup lang="ts">
import type { Course, Lesson } from '@/types/classroom';
import { ChevronDown, ChevronUp, Clock, ExternalLink, FileText, HelpCircle, Play } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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

watch(
    () => props.currentLesson?.id,
    (newId) => {
        if (newId) {
            personalNotes.value = localStorage.getItem(`iie_notes_${newId}`) || '';
            isSaved.value = true;
        } else {
            personalNotes.value = '';
        }
    },
    { immediate: true },
);

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
        open: false,
    },
    {
        q: '¿Cuál es el requisito para rendir la evaluación final?',
        a: 'Debes completar la visualización de todos los módulos curriculares al 100%. Una vez cumplido este requisito, se habilitará automáticamente la evaluación final en el panel lateral.',
        open: false,
    },
    {
        q: '¿Cómo puedo obtener mi certificado oficial?',
        a: 'Al aprobar la evaluación final con la nota mínima requerida, tu certificado digital de aprobación se generará instantáneamente y estará listo para su descarga.',
        open: false,
    },
    {
        q: '¿Tienen algún canal de soporte académico?',
        a: 'Sí, puedes usar el Foro Académico ubicado en el panel lateral (o el botón de chat en móvil) para interactuar con tus compañeros y docentes, o contactarnos de manera directa mediante el botón de WhatsApp en la vista general.',
        open: false,
    },
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
        navigator
            .share({
                title: props.currentLesson?.title || 'Clase de ieeEdu',
                text: props.currentLesson?.description || '',
                url: window.location.href,
            })
            .catch((err) => console.log(err));
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Enlace copiado al portapapeles');
    }
}
</script>

<template>
    <div
        class="relative mb-8 aspect-video w-full overflow-hidden rounded-3xl border border-outline-variant/10 bg-surface-dim shadow-2xl shadow-primary/5 dark:bg-surface-dim"
    >
        <!-- RECORDED: Show Video Player -->
        <template v-if="lessonState === 'recorded'">
            <div v-if="videoId" class="relative h-full w-full">
                <div class="js-plyr h-full w-full" :data-plyr-provider="'youtube'" :data-plyr-embed-id="videoId"></div>
            </div>
            <div v-else class="flex h-full w-full flex-col items-center justify-center bg-surface-container p-6 text-center md:p-12">
                <div
                    class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl border border-primary/15 bg-primary/10 dark:border-primary/30 dark:bg-primary/20"
                >
                    <Play class="h-8 w-8 text-primary dark:text-primary-fixed" />
                </div>
                <h2 class="mb-2 font-serif text-2xl font-bold text-on-background dark:text-on-surface md:text-3xl">Material en Aula</h2>
                <p class="max-w-sm text-sm text-on-surface-variant/70 dark:text-on-surface-variant/60 md:text-base">
                    {{ currentLesson?.description || 'Esta sesión está siendo preparada por el equipo docente.' }}
                </p>
            </div>
        </template>

        <!-- SCHEDULED & LIVE -->
        <template v-else-if="lessonState === 'scheduled' || lessonState === 'live'">
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-surface-container p-6 text-center md:p-10">
                <div class="relative z-10 w-full max-w-4xl space-y-6 md:space-y-8">
                    <div
                        class="dark:bg-surface-2 inline-flex items-center gap-2 rounded-full border border-outline-variant/20 bg-surface px-4 py-1.5 shadow-sm dark:border-outline-variant/30"
                    >
                        <div class="h-2 w-2 rounded-full" :class="lessonState === 'live' ? 'animate-pulse bg-red-500' : 'bg-[#D4AF37]'"></div>
                        <span class="text-[9px] font-extrabold uppercase tracking-wider text-on-surface-variant dark:text-on-surface">
                            {{ lessonState === 'live' ? 'Sesión en Vivo Interactiva' : 'Programada para hoy' }}
                        </span>
                    </div>

                    <h2 class="font-serif text-2xl font-bold leading-tight tracking-tight text-on-background dark:text-on-surface md:text-5xl">
                        {{ currentLesson?.title }}
                    </h2>

                    <div
                        class="mx-auto inline-block rounded-2xl border border-outline-variant/20 bg-white p-6 shadow-md dark:border-outline-variant/30 dark:bg-surface md:rounded-[2rem] md:p-8"
                    >
                        <div
                            v-if="lessonState === 'scheduled'"
                            class="mb-4 font-serif text-5xl font-bold italic tabular-nums tracking-tighter text-[#D4AF37] md:text-7xl"
                        >
                            {{ countdown }}
                        </div>
                        <div
                            v-else
                            class="mb-4 animate-pulse font-sans text-5xl font-black tracking-tighter text-emerald-600 dark:text-emerald-400 md:text-7xl"
                        >
                            STREAMING
                        </div>

                        <p
                            class="mb-6 border-t border-outline-variant/20 pt-4 text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/40 dark:border-outline-variant/10 dark:text-on-surface-variant/60 md:text-[10px]"
                        >
                            Fecha de clase: {{ currentLesson?.start_time ? new Date(currentLesson.start_time).toLocaleDateString() : 'Pendiente' }}
                        </p>

                        <div v-if="lessonState === 'live'">
                            <a
                                :href="currentLesson?.live_link"
                                target="_blank"
                                @click="emit('completeLesson')"
                                class="group flex transform items-center gap-3 rounded-xl bg-primary px-10 py-4 text-[11px] font-bold uppercase tracking-wider text-white shadow-md transition-all hover:-translate-y-1 hover:bg-on-background dark:bg-primary-fixed dark:text-on-primary-fixed dark:hover:bg-white dark:hover:text-on-background"
                            >
                                <ExternalLink class="h-4 w-4" /> Entrar al Aula Privada
                            </a>
                        </div>
                        <div
                            v-else
                            class="dark:bg-surface-2 rounded-xl border border-outline-variant/20 bg-surface-container-low px-10 py-4 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/40 dark:border-outline-variant/10 dark:text-on-surface/40"
                        >
                            Acceso en Espera
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- PROCESSING -->
        <template v-else-if="lessonState === 'processing'">
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-surface-container p-6 text-center md:p-10">
                <div class="relative mb-6">
                    <div class="flex h-20 w-20 animate-pulse items-center justify-center rounded-full bg-primary/10"></div>
                    <Clock
                        class="absolute left-1/2 top-1/2 h-8 w-8 -translate-x-1/2 -translate-y-1/2 animate-spin text-[#D4AF37]"
                        style="animation-duration: 3s"
                    />
                </div>
                <h2 class="mb-3 font-serif text-2xl font-bold text-on-background dark:text-on-surface md:text-3xl">Procesado de Grabación</h2>
                <p class="mx-auto max-w-xl text-sm leading-relaxed text-on-surface-variant/80 dark:text-on-surface-variant/80 md:text-base">
                    La cátedra en vivo ha concluido con éxito. Nuestro equipo de soporte académico está editando la grabación para su alojamiento
                    definitivo. Estará disponible en minutos.
                </p>
            </div>
        </template>
    </div>

    <!-- Title and Instructor info row -->
    <div class="mb-8 space-y-6">
        <h1 class="font-serif text-2xl font-bold leading-tight tracking-tight text-on-background dark:text-on-surface md:text-3xl lg:text-4xl">
            {{ currentLesson?.title }}
        </h1>

        <div class="border-b border-outline-variant/10 pb-6">
            <!-- Instructor Info -->
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-outline-variant/20 bg-surface-container"
                >
                    <img :src="instructorImage" :alt="instructorName" class="h-full w-full object-cover" />
                </div>
                <div>
                    <p class="text-xs text-on-surface-variant">
                        Instructed by <span class="font-bold text-on-surface">{{ instructorName }}</span>
                    </p>
                    <p class="text-[10px] font-medium text-on-surface-variant/60">{{ instructorTitle }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="flex flex-col">
        <!-- Premium Tabs Navigation -->
        <div
            class="scrollbar-none order-1 mb-8 flex w-full max-w-full items-center gap-8 overflow-x-auto border-b border-outline-variant/10 bg-transparent pb-4"
        >
            <button
                v-for="tab in ['description', 'resources', 'notes', 'questions']"
                :key="tab"
                @click="emit('update:activeTab', tab as any)"
                class="group relative shrink-0 pb-4 text-[11px] font-black uppercase tracking-wider transition-all md:text-[12px]"
                :class="activeTab === tab ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
            >
                <template v-if="tab === 'description'">Descripción</template>
                <template v-else-if="tab === 'resources'">Recursos</template>
                <template v-else-if="tab === 'notes'">Mis Notas</template>
                <template v-else-if="tab === 'questions'">Preguntas Frecuentes</template>

                <span class="absolute bottom-0 left-0 h-[3px] w-full scale-x-0 bg-primary/10 transition-transform group-hover:scale-x-50"></span>
                <span
                    v-if="activeTab === tab"
                    class="absolute bottom-0 left-0 h-[3px] w-full rounded-full bg-primary duration-300 animate-in zoom-in"
                ></span>
            </button>
        </div>

        <!-- Tabs Content -->
        <div class="order-2 min-h-[250px]">
            <!-- 1. Description Tab -->
            <div v-show="activeTab === 'description'" class="space-y-6 duration-500 animate-in fade-in slide-in-from-bottom-4">
                <div class="prose max-w-none font-sans text-sm leading-relaxed text-on-surface-variant/90 md:text-base dark:text-slate-300">
                    <p v-if="currentLesson?.description" class="whitespace-pre-line leading-relaxed">{{ currentLesson.description }}</p>
                    <p v-else class="text-base italic text-on-surface-variant/40 dark:text-slate-400">Descripción en revisión académica.</p>
                </div>
            </div>

            <!-- 2. Resources Tab -->
            <div v-show="activeTab === 'resources'" class="duration-500 animate-in fade-in slide-in-from-bottom-4">
                <slot name="resources"></slot>
            </div>

            <!-- 4. Personal Notes Tab -->
            <div v-show="activeTab === 'notes'" class="space-y-4 duration-500 animate-in fade-in slide-in-from-bottom-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="flex items-center gap-2 text-sm font-bold text-on-background">
                            <FileText class="w-4.5 h-4.5 text-primary" />
                            Notas Académicas Personales
                        </h3>
                        <p class="text-[10px] text-on-surface-variant/60">Tus notas son personales y se guardan automáticamente en este navegador.</p>
                    </div>
                    <div
                        class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider"
                        :class="isSaved ? 'text-emerald-600' : 'text-amber-600'"
                    >
                        <span class="h-1.5 w-1.5 rounded-full" :class="isSaved ? 'bg-emerald-500' : 'animate-pulse bg-amber-500'"></span>
                        {{ isSaved ? 'Guardado' : 'Guardando...' }}
                    </div>
                </div>

                <textarea
                    v-model="personalNotes"
                    @input="handleNotesInput"
                    placeholder="Escribe aquí tus ideas, apuntes y notas clave de la clase actual para repasarlas más tarde..."
                    class="min-h-[250px] w-full resize-y rounded-2xl border border-outline-variant/20 bg-white p-6 font-sans text-sm font-medium leading-relaxed text-on-surface shadow-inner focus:border-primary/40 focus:ring-1 focus:ring-primary/20 dark:bg-surface-2 dark:text-slate-300 dark:placeholder:text-slate-500"
                ></textarea>
            </div>

            <!-- 5. Questions (FAQ) Tab -->
            <div v-show="activeTab === 'questions'" class="space-y-4 pb-12 duration-500 animate-in fade-in slide-in-from-bottom-4">
                <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-on-background">
                    <HelpCircle class="w-4.5 h-4.5 text-primary" />
                    Preguntas Frecuentes
                </h3>

                <div class="space-y-3">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="overflow-hidden rounded-2xl border border-outline-variant/10 bg-white shadow-sm transition-all duration-300 dark:bg-surface-2"
                    >
                        <button
                            @click="toggleFaq(index)"
                            class="flex w-full items-center justify-between px-6 py-4 text-left text-xs font-bold text-on-background transition-colors hover:text-primary focus:outline-none md:text-sm dark:text-slate-300"
                        >
                            <span>{{ faq.q }}</span>
                            <ChevronUp v-if="faq.open" class="h-4 w-4 shrink-0 text-primary" />
                            <ChevronDown v-else class="h-4 w-4 shrink-0 text-on-surface-variant/40" />
                        </button>

                        <div
                            v-show="faq.open"
                            class="border-t border-outline-variant/5 bg-surface-container-low/30 px-6 pb-5 pt-1 text-xs font-medium leading-relaxed text-on-surface-variant/80 duration-200 animate-in slide-in-from-top-1 md:text-sm dark:bg-black/20 dark:text-slate-400"
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
