import { ref, computed, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { MonitorPlay, Briefcase, BookOpen, GraduationCap, FileText } from 'lucide-vue-next';
import type { DbBanner, BannerSlide, BannerSection, SectionSpec } from '@/types/banner';

const sectionSpecs: Record<string, SectionSpec> = {
    home: { minWidth: 1920, minHeight: 700, idealW: 1920, idealH: 1080, label: '1920×1080px (16:9)' },
    consultoria: { minWidth: 1920, minHeight: 560, idealW: 1920, idealH: 600, label: '1920×600px (Panorámica)' },
    masterclass: { minWidth: 1280, minHeight: 400, idealW: 1600, idealH: 500, label: '1600×500px (Tarjeta 16:5)' },
    cursos: { minWidth: 1280, minHeight: 400, idealW: 1600, idealH: 500, label: '1600×500px (Tarjeta 16:5)' },
    publicaciones: { minWidth: 1280, minHeight: 400, idealW: 1600, idealH: 500, label: '1600×500px (Tarjeta 16:5)' },
};

function createSlide(section: string, order: number, extra: Partial<BannerSlide> = {}): BannerSlide {
    return {
        section, order, image_path: null, imagePreview: null, file: null,
        heading: '', subheading: '', button_text: '', button_link: '', show_text: true,
        ...extra,
    };
}

const defaultBanners: BannerSection[] = [
    { id: 'home', title: 'Home (Carrusel)', icon: MonitorPlay, isCarousel: true, slides: [createSlide('home', 1), createSlide('home', 2), createSlide('home', 3)] },
    { id: 'consultoria', title: 'Consultoría', icon: Briefcase, isCarousel: false, slides: [createSlide('consultoria', 1, { whatsapp_number: '', contact_email: '', contact_address: '' })] },
    { id: 'cursos', title: 'Cursos', icon: BookOpen, isCarousel: false, slides: [createSlide('cursos', 1)] },
    { id: 'publicaciones', title: 'Publicaciones', icon: FileText, isCarousel: false, slides: [createSlide('publicaciones', 1)] },
    { id: 'masterclass', title: 'Masterclass', icon: GraduationCap, isCarousel: false, slides: [createSlide('masterclass', 1)] },
];

export function useBannerEditor(dbBanners: DbBanner[]) {
    const banners = ref<BannerSection[]>(JSON.parse(JSON.stringify(defaultBanners)));
    const activeBannerId = ref('home');
    const activeSlideIndex = ref(0);
    const errorMessage = ref('');
    const isSaving = ref(false);
    const fileInput = ref<HTMLInputElement | null>(null);
    let errorTimeout: number | undefined;

    const activeBanner = computed(() => banners.value.find(b => b.id === activeBannerId.value));
    const activeSlide = computed(() => activeBanner.value?.slides[activeSlideIndex.value] ?? null);

    const recommendedSpecs = computed(() => {
        const spec = sectionSpecs[activeBanner.value?.id ?? ''];
        return spec ? `Medidas recomendadas: ${spec.label} | Landscape obligatorio | Max 5MB` : 'Medidas recomendadas: 1920×400px | Max 5MB';
    });

    const bannerRequirements = computed(() => {
        const spec = sectionSpecs[activeBanner.value?.id ?? ''];
        return spec ? { minWidth: spec.minWidth, minHeight: spec.minHeight, max: 4000 } : { minWidth: 1280, minHeight: 400, max: 4000 };
    });

    onMounted(() => {
        if (!dbBanners?.length) return;
        dbBanners.forEach(dbb => {
            const bannerSection = banners.value.find(b => b.id === dbb.section);
            if (!bannerSection) return;
            const slideIdx = bannerSection.slides.findIndex(s => s.order === dbb.order);
            if (slideIdx === -1) return;
            bannerSection.slides[slideIdx] = {
                ...bannerSection.slides[slideIdx],
                image_path: dbb.image_path,
                imagePreview: dbb.image_path,
                heading: dbb.heading || '',
                subheading: dbb.subheading || '',
                button_text: dbb.button_text || '',
                button_link: dbb.button_link || '',
                show_text: dbb.show_text !== undefined ? !!dbb.show_text : true,
                whatsapp_number: dbb.whatsapp_number || '',
                contact_email: dbb.contact_email || '',
                contact_address: dbb.contact_address || '',
            };
        });
    });

    watch(activeBannerId, () => { activeSlideIndex.value = 0; });

    function showError(msg: string) {
        errorMessage.value = msg;
        if (errorTimeout) clearTimeout(errorTimeout);
        errorTimeout = window.setTimeout(() => { errorMessage.value = ''; }, 7000);
    }

    function triggerUpload() { fileInput.value?.click(); }

    function handleFileChange(event: Event) {
        const target = event.target as HTMLInputElement;
        if (!target.files?.length || !activeSlide.value) return;
        const file = target.files[0];
        if (file.size > 5 * 1024 * 1024) { showError('El peso de la imagen excede el límite de 5MB.'); target.value = ''; return; }

        const img = new Image();
        const objectUrl = URL.createObjectURL(file);
        img.onload = () => {
            const { minWidth, minHeight, max } = bannerRequirements.value;
            const spec = sectionSpecs[activeBanner.value?.id ?? ''];
            if (img.width <= img.height) { showError(`Imagen no landscape (${img.width}×${img.height}px). Ideal: ${spec?.label}.`); target.value = ''; URL.revokeObjectURL(objectUrl); return; }
            if (activeBanner.value?.id !== 'home' && img.width / img.height < 2.0) { showError(`Proporción incorrecta. Requiere ratio ≥ 2:1. Ej: ${spec?.label}.`); target.value = ''; URL.revokeObjectURL(objectUrl); return; }
            if (img.width < minWidth || img.height < minHeight) { showError(`Resolución insuficiente. Mínimo ${minWidth}×${minHeight}px.`); target.value = ''; URL.revokeObjectURL(objectUrl); return; }
            if (img.width > max || img.height > max) { showError(`Resolución excesiva. Máximo ${max}×${max}px.`); target.value = ''; URL.revokeObjectURL(objectUrl); return; }
            errorMessage.value = '';
            const slide = activeSlide.value;
            if (slide) { slide.file = file; slide.imagePreview = objectUrl; }
            target.value = '';
        };
        img.onerror = () => { showError('Formato de imagen no válido.'); target.value = ''; URL.revokeObjectURL(objectUrl); };
        img.src = objectUrl;
    }

    function saveChanges() {
        if (!activeSlide.value) return;
        isSaving.value = true;
        errorMessage.value = '';
        const orderToSend = activeBanner.value?.isCarousel ? (activeSlideIndex.value + 1) : 1;
        const formData = new FormData();
        formData.append('section', activeSlide.value.section);
        formData.append('order', orderToSend.toString());
        formData.append('heading', activeSlide.value.heading ?? '');
        formData.append('subheading', activeSlide.value.subheading ?? '');
        formData.append('button_text', activeSlide.value.button_text ?? '');
        formData.append('button_link', activeSlide.value.button_link ?? '');
        formData.append('show_text', activeSlide.value.show_text ? '1' : '0');
        if (activeBannerId.value === 'consultoria') {
            formData.append('whatsapp_number', activeSlide.value.whatsapp_number ?? '');
            formData.append('contact_email', activeSlide.value.contact_email ?? '');
            formData.append('contact_address', activeSlide.value.contact_address ?? '');
        }
        if (activeSlide.value.file) formData.append('image', activeSlide.value.file);

        router.post(route('admin.banners.store'), formData, {
            preserveScroll: true,
            onSuccess: () => { isSaving.value = false; activeSlide.value!.file = null; },
            onError: () => { isSaving.value = false; alert('Ocurrió un error al guardar el banner.'); },
        });
    }

    return {
        banners,
        activeBannerId,
        activeSlideIndex,
        activeBanner,
        activeSlide,
        errorMessage,
        isSaving,
        fileInput,
        recommendedSpecs,
        showError,
        triggerUpload,
        handleFileChange,
        saveChanges,
    };
}
