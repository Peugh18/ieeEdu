<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { 
    Image as ImageIcon, Save, MonitorPlay, Briefcase, BookOpen, GraduationCap, 
    Upload, Edit3, Type, Link as LinkIcon, FileText, Loader2
} from 'lucide-vue-next';

const props = defineProps<{
    dbBanners: any[]
}>();

const defaultBanners = [
    {
        id: 'home',
        title: 'Home (Carrusel)',
        icon: MonitorPlay,
        isCarousel: true,
        slides: [
            { section: 'home', order: 1, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true },
            { section: 'home', order: 2, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true },
            { section: 'home', order: 3, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true }
        ]
    },
    {
        id: 'consultoria',
        title: 'Consultoría',
        icon: Briefcase,
        isCarousel: false,
        slides: [
            { section: 'consultoria', order: 1, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true }
        ]
    },
    {
        id: 'cursos',
        title: 'Cursos',
        icon: BookOpen,
        isCarousel: false,
        slides: [
            { section: 'cursos', order: 1, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true }
        ]
    },
    {
        id: 'publicaciones',
        title: 'Publicaciones',
        icon: FileText,
        isCarousel: false,
        slides: [
            { section: 'publicaciones', order: 1, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true }
        ]
    },
    {
        id: 'masterclass',
        title: 'Masterclass',
        icon: GraduationCap,
        isCarousel: false,
        slides: [
            { section: 'masterclass', order: 1, image_path: null, imagePreview: null, file: null, heading: '', subheading: '', button_text: '', button_link: '', show_text: true }
        ]
    }
];

const banners = ref(JSON.parse(JSON.stringify(defaultBanners)));

// Fusionar datos de la DB con la estructura
onMounted(() => {
    if (props.dbBanners && props.dbBanners.length > 0) {
        props.dbBanners.forEach(dbb => {
            const bannerSection = banners.value.find(b => b.id === dbb.section);
            if (bannerSection) {
                // El order en BD es 1-indexado, slides array es 0-indexado
                const slideIndex = bannerSection.slides.findIndex(s => s.order === dbb.order);
                if (slideIndex !== -1) {
                    bannerSection.slides[slideIndex] = {
                        ...bannerSection.slides[slideIndex],
                        image_path: dbb.image_path,
                        imagePreview: dbb.image_path,
                        heading: dbb.heading || '',
                        subheading: dbb.subheading || '',
                        button_text: dbb.button_text || '',
                        button_link: dbb.button_link || '',
                        show_text: dbb.show_text !== undefined ? !!dbb.show_text : true,
                    };
                }
            }
        });
    }
});

const activeBannerId = ref('home');
const activeSlideIndex = ref(0);

const activeBanner = computed(() => banners.value.find(b => b.id === activeBannerId.value));
const activeSlide = computed(() => activeBanner.value?.slides[activeSlideIndex.value]);

watch(activeBannerId, () => {
    activeSlideIndex.value = 0;
});

const fileInput = ref<HTMLInputElement | null>(null);

function triggerUpload() {
    fileInput.value?.click();
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        activeSlide.value.file = file;
        activeSlide.value.imagePreview = URL.createObjectURL(file);
        // Reset input so same file can be re-selected
        target.value = '';
    }
}

const isSaving = ref(false);

function saveChanges() {
    if (!activeSlide.value) return;
    isSaving.value = true;

    // El order es fijo: para home es el índice+1 del tab, para otros siempre es 1
    const orderToSend = activeBanner.value?.isCarousel ? (activeSlideIndex.value + 1) : 1;

    const formData = new FormData();
    formData.append('section', activeSlide.value.section);
    formData.append('order', orderToSend.toString());
    formData.append('heading', activeSlide.value.heading ?? '');
    formData.append('subheading', activeSlide.value.subheading ?? '');
    formData.append('button_text', activeSlide.value.button_text ?? '');
    formData.append('button_link', activeSlide.value.button_link ?? '');
    formData.append('show_text', activeSlide.value.show_text ? '1' : '0');
    
    if (activeSlide.value.file) {
        formData.append('image', activeSlide.value.file);
    }

    router.post(route('admin.banners.store'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            isSaving.value = false;
            activeSlide.value.file = null;
        },
        onError: () => {
            isSaving.value = false;
            alert('Ocurrió un error al guardar el banner.');
        }
    });
}
</script>

<template>
    <Head title="Gestión de Banners - iieEdu Admin" />

    <AppLayout>
        <div class="max-w-[1400px] mx-auto px-4 py-8 space-y-8">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 rounded-[2.5rem] shadow-sm" style="background-color: var(--elite-surface);">
                <div class="space-y-1">
                    <h1 class="font-serif text-5xl tracking-tight leading-tight" style="color: var(--elite-text);">
                        Gestión de <span class="italic" style="color: var(--elite-olive);">Banners</span>
                    </h1>
                    <p class="font-medium text-lg flex items-center gap-2" style="color: var(--elite-text-muted);">
                        <ImageIcon class="w-4 h-4" />
                        Configura las imágenes de cabecera sincronizadas con la base de datos.
                    </p>
                </div>
                
                <button
                    @click="saveChanges"
                    :disabled="isSaving"
                    class="h-12 inline-flex items-center gap-2.5 rounded-2xl px-7 text-sm font-bold shadow-xl hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed"
                    style="background-color: var(--elite-olive); color: var(--elite-cream);"
                >
                    <Loader2 v-if="isSaving" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ isSaving ? 'Guardando...' : 'Guardar Configuración' }}
                </button>
            </div>

            <!-- Main Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Sidebar Nav -->
                <div class="lg:col-span-1">
                    <div class="rounded-[2rem] p-4 shadow-sm border" style="background-color: var(--elite-surface); border-color: var(--elite-border);">
                        <div class="text-[10px] font-extrabold uppercase tracking-widest mb-4 px-4 pt-2" style="color: var(--elite-text-muted);">Apartados Web</div>
                        <nav class="flex flex-col gap-1.5">
                            <button 
                                v-for="banner in banners" :key="banner.id"
                                @click="activeBannerId = banner.id"
                                :style="activeBannerId === banner.id 
                                    ? 'background-color: var(--elite-olive); color: var(--elite-cream);' 
                                    : 'color: var(--elite-text-muted);'"
                                class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all duration-300 hover:opacity-90"
                            >
                                <component :is="banner.icon" class="w-5 h-5" />
                                {{ banner.title }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Editor -->
                <div class="lg:col-span-3 space-y-6" v-if="activeBanner && activeSlide">
                    
                    <!-- Tab Carrusel (solo Home) -->
                    <div v-if="activeBanner.isCarousel" class="rounded-3xl p-3 shadow-sm border flex flex-wrap gap-2" style="background-color: var(--elite-surface); border-color: var(--elite-border);">
                        <button v-for="(slide, index) in activeBanner.slides" :key="index"
                            @click="activeSlideIndex = index"
                            :style="activeSlideIndex === index 
                                ? 'background-color: var(--elite-olive); color: var(--elite-cream);' 
                                : 'background-color: var(--elite-surface-2); color: var(--elite-text-muted);'"
                            class="flex-1 md:flex-none min-w-[120px] h-12 flex items-center justify-center gap-2 rounded-2xl text-xs font-bold transition-all"
                        >
                            <ImageIcon class="w-4 h-4" />
                            Imagen {{ index + 1 }}
                        </button>
                    </div>
                    
                    <!-- Image Editor -->
                    <div class="rounded-[2.5rem] p-8 shadow-sm border" style="background-color: var(--elite-surface); border-color: var(--elite-border);">
                        <h3 class="text-xl font-serif mb-6 flex items-center gap-2" style="color: var(--elite-text);">
                            <ImageIcon class="w-5 h-5" style="color: var(--elite-olive);" />
                            Fotografía de Cabecera {{ activeBanner.isCarousel ? `(${activeSlideIndex + 1} / 3)` : '' }}
                        </h3>
                        
                        <!-- Input file oculto -->
                        <input type="file" ref="fileInput" class="hidden" accept="image/png,image/jpeg,image/jpg,image/webp" @change="handleFileChange" />

                        <div class="relative group rounded-[2rem] overflow-hidden border-2 border-dashed flex items-center justify-center min-h-[340px] transition-all cursor-pointer"
                            :style="activeSlide.imagePreview ? 'border-color: var(--elite-olive);' : 'border-color: var(--elite-border-strong); background-color: var(--elite-surface-2);'"
                            @click="triggerUpload"
                        >
                            <!-- Preview de imagen -->
                            <img v-if="activeSlide.imagePreview" 
                                :src="activeSlide.imagePreview" 
                                class="absolute inset-0 w-full h-full object-cover" />
                            
                            <!-- Placeholder si no hay imagen -->
                            <div v-else class="flex flex-col items-center justify-center p-8 z-0">
                                <MonitorPlay class="w-12 h-12 mb-4" style="color: var(--elite-text-faint);" />
                                <div class="text-center font-medium" style="color: var(--elite-text-muted);">
                                    Haz clic para seleccionar una fotografía<br>
                                    <span class="text-xs uppercase tracking-widest font-bold" style="color: var(--elite-olive);">{{ activeBanner.title }}</span>
                                </div>
                            </div>

                            <!-- Overlay hover con botón cambiar -->
                            <div class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-sm p-6"
                                :style="activeSlide.imagePreview ? 'background-color: rgba(0,0,0,0.45);' : 'background-color: transparent;'"
                            >
                                <div class="h-12 inline-flex items-center gap-2 rounded-xl px-6 text-sm font-bold shadow-xl"
                                    style="background-color: var(--elite-olive); color: var(--elite-cream);">
                                    <Upload class="w-4 h-4" />
                                    {{ activeSlide.imagePreview ? 'Cambiar Fotografía' : 'Subir Fotografía' }}
                                </div>
                                <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-lg" 
                                    style="color: var(--elite-text-muted); background-color: var(--elite-bg);">
                                    Recomendado: 1920×1080px · WebP/JPG · max 4MB
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="rounded-[2.5rem] p-8 shadow-sm border space-y-6" style="background-color: var(--elite-surface); border-color: var(--elite-border);">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-serif flex items-center gap-2" style="color: var(--elite-text);">
                                <Type class="w-5 h-5" style="color: var(--elite-olive);" />
                                Contenido del Banner
                            </h3>
                            <!-- Toggle mostrar/ocultar texto -->
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <span class="text-xs font-bold uppercase tracking-widest" style="color: var(--elite-text-muted);">
                                    {{ activeSlide.show_text ? 'Texto Visible' : 'Texto Oculto' }}
                                </span>
                                <button
                                    type="button"
                                    @click="activeSlide.show_text = !activeSlide.show_text"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none"
                                    :style="activeSlide.show_text 
                                        ? 'background-color: var(--elite-olive);' 
                                        : 'background-color: var(--elite-border-strong);'"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-300"
                                        :class="activeSlide.show_text ? 'translate-x-6' : 'translate-x-1'"
                                    />
                                </button>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2" style="color: var(--elite-text-muted);">Título Principal</label>
                                <input v-model="activeSlide.heading" type="text" 
                                    class="w-full h-14 rounded-2xl px-5 text-sm font-bold outline-none transition-all border"
                                    style="background-color: var(--elite-surface-2); border-color: var(--elite-border-strong); color: var(--elite-text);"
                                    placeholder="Escribe el título..." />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2" style="color: var(--elite-text-muted);">Subtítulo / Descripción</label>
                                <textarea v-model="activeSlide.subheading" rows="3" 
                                    class="w-full rounded-2xl p-5 text-sm font-medium outline-none transition-all resize-none border"
                                    style="background-color: var(--elite-surface-2); border-color: var(--elite-border-strong); color: var(--elite-text);"
                                    placeholder="Escribe la descripción..."></textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2" style="color: var(--elite-text-muted);">Texto del Botón (Opcional)</label>
                                <div class="relative">
                                    <Edit3 class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--elite-text-muted);" />
                                    <input v-model="activeSlide.button_text" type="text" 
                                        class="w-full h-12 rounded-xl pl-11 pr-4 text-sm font-bold outline-none transition-all border"
                                        style="background-color: var(--elite-surface-2); border-color: var(--elite-border-strong); color: var(--elite-text);"
                                        placeholder="Ej: Explorar Cursos" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2" style="color: var(--elite-text-muted);">Enlace del Botón (Opcional)</label>
                                <div class="relative">
                                    <LinkIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--elite-text-muted);" />
                                    <input v-model="activeSlide.button_link" type="text" 
                                        class="w-full h-12 rounded-xl pl-11 pr-4 text-sm font-bold outline-none transition-all border"
                                        style="background-color: var(--elite-surface-2); border-color: var(--elite-border-strong); color: var(--elite-text);"
                                        placeholder="Ej: /cursos" />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
